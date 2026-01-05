const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const QRCode = require('qrcode');
const fs = require('fs');
const path = require('path');
const { default: makeWASocket, useMultiFileAuthState, DisconnectReason, fetchLatestBaileysVersion } = require('@whiskeysockets/baileys');
const P = require('pino');

// ============================================
// CONFIGURATION
// ============================================
const app = express();
const PORT = process.env.PORT || 3000;
const PASSWORD = process.env.WA_PASSWORD || 'r11223344';

// Middleware
app.use(cors());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Logger
const logger = P({ level: 'silent' });

// ============================================
// GLOBAL VARIABLES
// ============================================
let sock = null;
let isConnected = false;
let qrCodeData = null;

// ============================================
// WHATSAPP CONNECTION FUNCTION
// ============================================
async function connectToWhatsApp() {
    const { state, saveCreds } = await useMultiFileAuthState('auth_info');
    const { version, isLatest } = await fetchLatestBaileysVersion();

    console.log(`Using WA v${version.join('.')}, isLatest: ${isLatest}`);

    sock = makeWASocket({
        version,
        auth: state,
        logger,
        browser: ['SIPETER', 'Chrome', '10.0'],
        connectTimeoutMs: 60000,
        defaultQueryTimeoutMs: 0,
        keepAliveIntervalMs: 10000,
        getMessage: async (key) => {
            return { conversation: 'Hello' };
        }
    });

    // Event: Connection Update
    sock.ev.on('connection.update', async (update) => {
        const { connection, lastDisconnect, qr } = update;

        // QR Code handler - Save as PNG file
        if (qr) {
            qrCodeData = qr;

            // Save QR as PNG file
            const qrPath = path.join(__dirname, 'qr-code.png');
            try {
                await QRCode.toFile(qrPath, qr, {
                    width: 300,
                    margin: 2
                });

                console.log('\n╔════════════════════════════════════════════════════╗');
                console.log('║  🔄 QR CODE GENERATED!                             ║');
                console.log('║                                                    ║');
                console.log('║  📸 QR Code saved as: qr-code.png                  ║');
                console.log('║  📂 Location: whatsapp-gateway/qr-code.png         ║');
                console.log('║                                                    ║');
                console.log('║  🔗 Or open in browser:                            ║');
                console.log(`║     http://localhost:${PORT}/qr-image                  ║`);
                console.log('║                                                    ║');
                console.log('║  📱 Scan dengan WhatsApp Mobile:                   ║');
                console.log('║     WhatsApp → ⋮ → Linked Devices → Link          ║');
                console.log('╚════════════════════════════════════════════════════╝\n');
            } catch (err) {
                console.error('Error saving QR code:', err);
            }
        }

        // Connection status handling
        if (connection === 'close') {
            const statusCode = lastDisconnect?.error?.output?.statusCode;
            const shouldReconnect = statusCode !== DisconnectReason.loggedOut;

            console.log('\n❌ Connection closed');
            console.log('Status Code:', statusCode);
            console.log('Reason:', lastDisconnect?.error?.message || 'Unknown');

            isConnected = false;

            if (shouldReconnect) {
                console.log('🔄 Reconnecting in 5 seconds...\n');
                setTimeout(() => connectToWhatsApp(), 5000);
            } else {
                console.log('⚠️ Logged out. Restart server and scan QR code again.\n');
            }
        } else if (connection === 'open') {
            console.log('\n╔════════════════════════════════════════════════════╗');
            console.log('║  ✅ WhatsApp Connected Successfully!               ║');
            console.log('║  📱 Ready to send messages                         ║');
            console.log('╚════════════════════════════════════════════════════╝\n');

            isConnected = true;
            qrCodeData = null;

            // Delete QR code file after connection
            const qrPath = path.join(__dirname, 'qr-code.png');
            if (fs.existsSync(qrPath)) {
                fs.unlinkSync(qrPath);
                console.log('🗑️  QR code file deleted (no longer needed)\n');
            }
        } else if (connection === 'connecting') {
            console.log('🔄 Connecting to WhatsApp...');
        }
    });

    // Event: Credentials Update
    sock.ev.on('creds.update', saveCreds);
}

// ============================================
// HELPER FUNCTIONS
// ============================================

function formatPhoneNumber(phone) {
    let cleaned = phone.replace(/\D/g, '');

    if (cleaned.startsWith('0')) {
        cleaned = '62' + cleaned.substring(1);
    }

    if (!cleaned.startsWith('62')) {
        cleaned = '62' + cleaned;
    }

    return cleaned + '@s.whatsapp.net';
}

async function sendMessage(phone, message) {
    try {
        if (!isConnected) {
            throw new Error('WhatsApp not connected');
        }

        const jid = formatPhoneNumber(phone);
        await sock.sendMessage(jid, { text: message });

        console.log(`✅ Message sent to ${phone}`);
        return {
            success: true,
            message: 'Message sent successfully',
            to: phone,
            jid: jid,
            timestamp: new Date().toISOString()
        };
    } catch (error) {
        console.error(`❌ Failed to send to ${phone}:`, error.message);
        return {
            success: false,
            error: error.message,
            to: phone,
            timestamp: new Date().toISOString()
        };
    }
}

// ============================================
// REST API ENDPOINTS
// ============================================

app.get('/', (req, res) => {
    res.json({
        service: 'SIPETER WhatsApp Gateway',
        version: '2.1.2',
        status: isConnected ? 'connected' : 'disconnected',
        timestamp: new Date().toISOString()
    });
});

app.get('/status', (req, res) => {
    res.json({
        connected: isConnected,
        hasQR: qrCodeData !== null,
        timestamp: new Date().toISOString()
    });
});

// Serve QR Code image
app.get('/qr-image', (req, res) => {
    const qrPath = path.join(__dirname, 'qr-code.png');

    if (fs.existsSync(qrPath)) {
        res.sendFile(qrPath);
    } else if (isConnected) {
        res.send(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>WhatsApp Connected</title>
                <style>
                    body { font-family: Arial; text-align: center; padding: 50px; }
                    .success { color: green; font-size: 24px; }
                </style>
            </head>
            <body>
                <div class="success">✅ WhatsApp Already Connected!</div>
                <p>No QR code needed. Gateway is ready to send messages.</p>
            </body>
            </html>
        `);
    } else {
        res.send(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>QR Code Not Ready</title>
                <style>
                    body { font-family: Arial; text-align: center; padding: 50px; }
                    .warning { color: orange; font-size: 20px; }
                </style>
            </head>
            <body>
                <div class="warning">⏳ QR Code Not Ready Yet</div>
                <p>Please wait a moment and refresh this page...</p>
                <button onclick="location.reload()">Refresh</button>
            </body>
            </html>
        `);
    }
});

app.post('/send-message', async (req, res) => {
    try {
        // ✅ Debug: Log incoming request
        console.log('\n📥 Incoming Request:', {
            body: req.body,
            contentType: req.headers['content-type']
        });

        // ✅ Support both 'phone' and 'number' field names
        const { phone, number, message, password } = req.body;
        const phoneNumber = phone || number;

        // ✅ Debug: Log extracted values
        console.log('📋 Extracted values:', {
            phoneNumber,
            hasMessage: !!message,
            hasPassword: !!password
        });

        // Validate password
        if (password !== PASSWORD) {
            console.log('❌ Invalid password');
            return res.status(401).json({
                success: false,
                error: 'Invalid password'
            });
        }

        // Validate phone and message
        if (!phoneNumber || !message) {
            console.log('❌ Missing required fields:', {
                phoneNumber,
                message: message ? 'exists' : 'missing'
            });
            return res.status(400).json({
                success: false,
                error: 'Phone and message are required',
                debug: {
                    receivedPhone: phoneNumber,
                    receivedMessage: message ? 'yes' : 'no'
                }
            });
        }

        // Check connection
        if (!isConnected) {
            console.log('❌ WhatsApp not connected');
            return res.status(503).json({
                success: false,
                error: 'WhatsApp not connected. Scan QR code first.'
            });
        }

        // Send message
        console.log(`📤 Attempting to send to ${phoneNumber}...`);
        const result = await sendMessage(phoneNumber, message);

        console.log('📊 Send result:', result);
        res.json(result);

    } catch (error) {
        console.error('💥 Error in /send-message:', error);
        res.status(500).json({
            success: false,
            error: error.message
        });
    }
});

app.post('/send-bulk', async (req, res) => {
    try {
        const { phones, message, password } = req.body;

        if (password !== PASSWORD) {
            return res.status(401).json({
                success: false,
                error: 'Invalid password'
            });
        }

        if (!phones || !Array.isArray(phones) || phones.length === 0 || !message) {
            return res.status(400).json({
                success: false,
                error: 'Phones (array) and message are required'
            });
        }

        if (!isConnected) {
            return res.status(503).json({
                success: false,
                error: 'WhatsApp not connected'
            });
        }

        const results = [];
        for (const phone of phones) {
            const result = await sendMessage(phone, message);
            results.push(result);
            await new Promise(resolve => setTimeout(resolve, 1000));
        }

        res.json({
            success: true,
            total: phones.length,
            results: results
        });
    } catch (error) {
        console.error('Error in /send-bulk:', error);
        res.status(500).json({
            success: false,
            error: error.message
        });
    }
});

app.get('/qr', (req, res) => {
    if (isConnected) {
        return res.json({
            connected: true,
            message: 'Already connected to WhatsApp'
        });
    }

    if (qrCodeData) {
        return res.json({
            connected: false,
            qrCode: qrCodeData,
            message: 'Scan this QR code with WhatsApp'
        });
    }

    res.json({
        connected: false,
        message: 'Waiting for QR code...'
    });
});

// ============================================
// START SERVER
// ============================================
app.listen(PORT, () => {
    console.log('\n╔════════════════════════════════════════════════════╗');
    console.log('║   SIPETER v2.1.2 - WhatsApp Gateway                ║');
    console.log('║   Powered by Baileys Library                       ║');
    console.log('╚════════════════════════════════════════════════════╝\n');
    console.log(`🚀 Server: http://localhost:${PORT}`);
    console.log('📱 Endpoints:');
    console.log('   GET  /          - Health check');
    console.log('   GET  /status    - Connection status');
    console.log('   GET  /qr        - Get QR code (JSON)');
    console.log('   GET  /qr-image  - Get QR code (PNG Image) ⭐ NEW');
    console.log('   POST /send-message - Send message');
    console.log('   POST /send-bulk    - Send bulk');
    console.log(`\n🔐 Password: ${PASSWORD}`);
    console.log('\n📋 Starting WhatsApp connection...\n');

    connectToWhatsApp();
});

// ============================================
// GRACEFUL SHUTDOWN
// ============================================
process.on('SIGINT', () => {
    console.log('\n🛑 Shutting down...');

    // Delete QR code file on shutdown
    const qrPath = path.join(__dirname, 'qr-code.png');
    if (fs.existsSync(qrPath)) {
        fs.unlinkSync(qrPath);
    }

    if (sock) {
        sock.end();
    }
    process.exit(0);
});
