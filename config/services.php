<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Service Configuration (UPDATED Dec 15, 2025)
    |--------------------------------------------------------------------------
    |
    | Configuration for WhatsApp notification service using:
    | 1. Node.js Gateway with Baileys (Recommended - FREE)
    | 2. Fonnte API (Paid service)
    |
    */
    'whatsapp' => [
        // ✅ General Settings
        'enabled' => env('WHATSAPP_ENABLED', true),

        // ✅ Gateway Settings (Node.js Baileys - RECOMMENDED)
        'gateway_url' => env('WHATSAPP_GATEWAY_URL', 'http://localhost:3000'),
        'gateway_password' => env('WHATSAPP_GATEWAY_PASSWORD', 'r11223344'),
        'use_gateway' => env('WHATSAPP_USE_GATEWAY', true), // true = Node.js, false = API

        // ✅ API Settings (Fonnte/Twilio - PAID)
        'api_url' => env('WHATSAPP_API_URL', 'https://api.fonnte.com'),
        'api_key' => env('WHATSAPP_API_KEY', ''),

        // ✅ Logging Settings
        'logging_enabled' => env('WHATSAPP_LOGGING_ENABLED', true),
        'log_failed_only' => env('WHATSAPP_LOG_FAILED_ONLY', false),

        // ✅ Admin Phone Numbers (untuk notifikasi ke admin)
        'admin_phone' => env('WHATSAPP_ADMIN_PHONE', '6282129554934'),
        'admin_phones' => array_filter([
            env('WHATSAPP_ADMIN_PHONE', '6282129554934'),
            env('WHATSAPP_ADMIN_PHONE_2', null),
            env('WHATSAPP_ADMIN_PHONE_3', null),
        ]),

        // ✅ Retry Settings
        'max_retries' => env('WHATSAPP_MAX_RETRIES', 3),
        'retry_delay_seconds' => env('WHATSAPP_RETRY_DELAY', 5),
        'timeout_seconds' => env('WHATSAPP_TIMEOUT', 10),

        // ✅ Twilio Specific (optional)
        'account_sid' => env('WHATSAPP_ACCOUNT_SID', ''),
        'auth_token' => env('WHATSAPP_AUTH_TOKEN', ''),
        'from_number' => env('WHATSAPP_FROM_NUMBER', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | STABA Configuration
    |--------------------------------------------------------------------------
    |
    | Contact information for STABA that will be used in notifications
    |
    */
    'staba' => [
        'name' => 'STABA Bandung',
        'phone' => env('STABA_PHONE', '0812-3456-7890'),
        'email' => env('STABA_EMAIL', 'akademik@staba.ac.id'),
        'address' => env('STABA_ADDRESS', 'STABA Bandung'),
        'office_hours' => [
            'weekday' => env('STABA_OFFICE_HOURS_WEEKDAY', '08.00 - 15.00 WIB'),
            'saturday' => env('STABA_OFFICE_HOURS_SATURDAY', '08.00 - 14.00 WIB'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 3-Level Verification Configuration (NEW v3.0)
    |--------------------------------------------------------------------------
    |
    | Configuration for sequential 3-level document verification system
    | Level 1: Ketua Akademik
    | Level 2: Wakil Ketua 3 (Kemahasiswaan)
    | Level 3: Direktur (Final Approval)
    |
    */
    'verification' => [
        // Total verification levels
        'total_levels' => 3,

        // Level configuration
        'levels' => [
            1 => [
                'name' => 'Level 1 - Ketua Akademik',
                'authority_type' => 'ketua_akademik',
                'required' => true,
                'can_skip' => false,
            ],
            2 => [
                'name' => 'Level 2 - Wakil Ketua 3',
                'authority_type' => 'wakil_ketua_3',
                'required' => true,
                'can_skip' => false,
            ],
            3 => [
                'name' => 'Level 3 - Direktur (Final)',
                'authority_type' => 'ketua',
                'required' => true,
                'can_skip' => false,
                'is_final' => true,
            ],
        ],

        // Token settings
        'token_expiry_days' => env('VERIFICATION_TOKEN_EXPIRY_DAYS', 3),
        'token_length' => 64,

        // Auto-progression settings
        'auto_proceed_to_next_level' => true,
        'send_notification_on_auto_proceed' => true,

        // Reminder settings
        'send_reminder_after_hours' => 24,
        'max_reminders' => 3,

        // Progress tracking
        'track_verification_time' => true,
        'log_all_actions' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Signature Authority Configuration (SYNCED with SignatureAuthoritySeeder)
    |--------------------------------------------------------------------------
    |
    | Configuration for Ketua Akademik, Wakil Ketua 3, & Direktur
    | These are the authorities who will verify and digitally sign documents
    |
    | ⚠️ IMPORTANT: Sesuaikan dengan data di SignatureAuthoritySeeder.php
    |
    */
    'signature_authority' => [
        // Level 1: Ketua Akademik
        'academic' => [
            'name' => env('WHATSAPP_KETUA_AKADEMIK_NAME', 'Tubagus Riko Rivanthio, M.Kom'),
            'position' => env('ACADEMIC_AUTHORITY_POSITION', 'Ketua Bagian Akademik'),
            'phone' => env('WHATSAPP_KETUA_AKADEMIK_PHONE', '6282295837826'),
            'email' => env('ACADEMIC_AUTHORITY_EMAIL', 'akademik@staba.ac.id'),
            'level' => 1,
        ],

        // Level 2: Wakil Ketua 3 (Kemahasiswaan)
        'wakil_ketua_3' => [
            'name' => env('WHATSAPP_WAKIL_KETUA_3_NAME', 'Dr. Firman'),
            'position' => env('WAKIL_KETUA_3_POSITION', 'Wakil Ketua 3 - Bidang Kemahasiswaan'),
            'phone' => env('WHATSAPP_WAKIL_KETUA_3_PHONE', '6281563304503'),
            'email' => env('WAKIL_KETUA_3_EMAIL', 'wakilketua3@staba.ac.id'),
            'level' => 2,
        ],

        // Level 3: Direktur/Ketua
        'ketua' => [
            'name' => env('WHATSAPP_KETUA_NAME', 'Bu Rani'),
            'position' => env('KETUA_POSITION', 'Direktur'),
            'phone' => env('WHATSAPP_KETUA_PHONE', '6281317735296'),
            'email' => env('KETUA_EMAIL', 'direktur@staba.ac.id'),
            'level' => 3,
        ],

        // TTD Settings
        'signature_settings' => [
            'max_file_size' => 5120, // 5MB in KB
            'allowed_types' => ['png', 'pdf'],
            'auto_verify' => false, // Manual verification by default
            'reminder_after_hours' => 24, // Send reminder after 24 hours
            'max_retries' => 3, // Max upload retries if rejected
            'require_qr_code' => true, // QR code is required
            'qr_code_max_size' => 1024, // 1MB in KB
        ],
    ],

];
