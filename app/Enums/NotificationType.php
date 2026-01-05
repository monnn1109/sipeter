<?php

namespace App\Enums;

enum NotificationType: string
{
    case NEW_REQUEST = 'new_request';
    case REQUEST_APPROVED = 'request_approved';
    case REQUEST_REJECTED = 'request_rejected';
    case DOCUMENT_READY = 'document_ready';
    case DOCUMENT_UPLOADED = 'document_uploaded';
    case DOCUMENT_DOWNLOADED = 'document_downloaded';
    case DOCUMENT_PICKED_UP = 'document_picked_up';
    case DOCUMENT_COMPLETED = 'document_completed';

    case VERIFICATION_REQUESTED = 'verification_requested';
    case VERIFICATION_APPROVED = 'verification_approved';
    case VERIFICATION_REJECTED = 'verification_rejected';

    case SIGNATURE_REQUESTED = 'signature_requested';
    case SIGNATURE_UPLOADED = 'signature_uploaded';
    case SIGNATURE_VERIFIED = 'signature_verified';
    case SIGNATURE_REJECTED = 'signature_rejected';
    case SIGNATURE_REMINDER = 'signature_reminder';

    public function label(): string
    {
        return match($this) {
            self::NEW_REQUEST => 'Permohonan Baru',
            self::REQUEST_APPROVED => 'Permohonan Disetujui',
            self::REQUEST_REJECTED => 'Permohonan Ditolak',
            self::DOCUMENT_READY => 'Dokumen Siap',
            self::DOCUMENT_UPLOADED => 'Dokumen Diupload',
            self::DOCUMENT_DOWNLOADED => 'Dokumen Diunduh',
            self::DOCUMENT_PICKED_UP => 'Dokumen Diambil',
            self::DOCUMENT_COMPLETED => 'Permohonan Selesai',

            self::VERIFICATION_REQUESTED => 'Permintaan Verifikasi Dokumen',
            self::VERIFICATION_APPROVED => 'Verifikasi Disetujui',
            self::VERIFICATION_REJECTED => 'Verifikasi Ditolak',

            self::SIGNATURE_REQUESTED => 'Permintaan Tanda Tangan Digital',
            self::SIGNATURE_UPLOADED => 'TTD Digital Telah Diupload',
            self::SIGNATURE_VERIFIED => 'TTD Digital Diverifikasi',
            self::SIGNATURE_REJECTED => 'TTD Digital Ditolak',
            self::SIGNATURE_REMINDER => 'Reminder: Menunggu TTD Digital',
        };
    }


    public function icon(): string
    {
        return match($this) {
            self::NEW_REQUEST => 'mail',
            self::REQUEST_APPROVED => 'check-circle',
            self::REQUEST_REJECTED => 'x-circle',
            self::DOCUMENT_READY => 'bell',
            self::DOCUMENT_UPLOADED => 'upload-cloud',
            self::DOCUMENT_DOWNLOADED => 'download',
            self::DOCUMENT_PICKED_UP => 'package-check',
            self::DOCUMENT_COMPLETED => 'check-circle-2',

            self::VERIFICATION_REQUESTED => 'shield-question',
            self::VERIFICATION_APPROVED => 'shield-check',
            self::VERIFICATION_REJECTED => 'shield-x',

            self::SIGNATURE_REQUESTED => 'edit-3',
            self::SIGNATURE_UPLOADED => 'upload',
            self::SIGNATURE_VERIFIED => 'check-square',
            self::SIGNATURE_REJECTED => 'x-square',
            self::SIGNATURE_REMINDER => 'bell',
        };
    }


    public function color(): string
    {
        return match($this) {
            self::NEW_REQUEST => 'blue',
            self::REQUEST_APPROVED => 'green',
            self::REQUEST_REJECTED => 'red',
            self::DOCUMENT_READY => 'yellow',
            self::DOCUMENT_UPLOADED => 'purple',
            self::DOCUMENT_DOWNLOADED => 'indigo',
            self::DOCUMENT_PICKED_UP => 'orange',
            self::DOCUMENT_COMPLETED => 'emerald',

            self::VERIFICATION_REQUESTED => 'amber',
            self::VERIFICATION_APPROVED => 'emerald',
            self::VERIFICATION_REJECTED => 'rose',

            self::SIGNATURE_REQUESTED => 'purple',
            self::SIGNATURE_UPLOADED => 'indigo',
            self::SIGNATURE_VERIFIED => 'blue',
            self::SIGNATURE_REJECTED => 'orange',
            self::SIGNATURE_REMINDER => 'yellow',
        };
    }


    public function getWhatsAppTemplate(): string
    {
        return match($this) {
            self::NEW_REQUEST => "📮 *Permohonan Baru*\n\nKode: {code}\nTipe: {document_type}\nPemohon: {name}\n\nSilakan login untuk memproses.",

            self::REQUEST_APPROVED => "✅ *Permohonan Disetujui*\n\nKode: {code}\nTipe: {document_type}\n\nPermohonan Anda telah disetujui. Dokumen sedang diproses.",

            self::REQUEST_REJECTED => "❌ *Permohonan Ditolak*\n\nKode: {code}\nTipe: {document_type}\nAlasan: {reason}\n\nSilakan hubungi admin untuk informasi lebih lanjut.",

            self::DOCUMENT_READY => "📄 *Dokumen Siap*\n\nKode: {code}\nTipe: {document_type}\n\nDokumen Anda sudah siap untuk {action}.",

            self::DOCUMENT_UPLOADED => "☁️ *Dokumen Telah Diupload*\n\nKode: {code}\nTipe: {document_type}\n\nDokumen telah diupload dan siap untuk diunduh.",

            self::DOCUMENT_DOWNLOADED => "📥 *Dokumen Telah Diunduh*\n\nKode: {code}\nPemohon: {name}\nWaktu: {time}",

            self::DOCUMENT_PICKED_UP => "📦 *Dokumen Telah Diambil*\n\nKode: {code}\nTipe: {document_type}\n\nTerima kasih.",

            self::DOCUMENT_COMPLETED => "✅ *Permohonan Selesai*\n\nKode: {code}\nTipe: {document_type}\n\nTerima kasih telah menggunakan layanan kami.",

            self::VERIFICATION_REQUESTED => "🔍 *Request Verifikasi Dokumen*\n\n" .
                "Yth. {authority_name}\n" .
                "{authority_position}\n\n" .
                "Mohon verifikasi kelayakan dokumen berikut:\n\n" .
                "📋 No. Dokumen: {code}\n" .
                "📄 Jenis: {document_type}\n" .
                "👤 Pemohon: {applicant}\n" .
                "🆔 NIM/NIP: {applicant_id}\n\n" .
                "Silakan verifikasi melalui link berikut:\n" .
                "🔗 {verification_link}\n\n" .
                "Link berlaku 3 hari.\n\n" .
                "Terima kasih.\n\n---\nSIPETER - Universitas XYZ",

            self::VERIFICATION_APPROVED => "✅ *Dokumen Terverifikasi*\n\n" .
                "📋 {code}\n" .
                "✅ Disetujui oleh: {authority_name}\n\n" .
                "Dokumen akan segera diproses untuk penandatanganan.",

            self::VERIFICATION_REJECTED => "❌ *Verifikasi Ditolak*\n\n" .
                "📋 {code}\n" .
                "❌ Ditolak oleh: {authority_name}\n\n" .
                "Alasan:\n{reason}\n\n" .
                "Silakan hubungi admin untuk informasi lebih lanjut.",

            self::SIGNATURE_REQUESTED => "✏️ *Permintaan Tanda Tangan Digital*\n\n" .
                "Yth. {authority_name}\n" .
                "{authority_position}\n\n" .
                "Mohon tanda tangan digital untuk:\n" .
                "Kode: {code}\n" .
                "Jenis Surat: {document_type}\n" .
                "Pemohon: {applicant}\n\n" .
                "Silakan upload TTD digital:\n" .
                "🔗 {signature_link}\n\n" .
                "Link berlaku 7 hari.\n\n" .
                "Terima kasih.",

            self::SIGNATURE_UPLOADED => "📤 *TTD Digital Diupload*\n\n" .
                "Admin yang terhormat,\n\n" .
                "{authority_name} telah mengupload TTD digital untuk:\n" .
                "Kode: {code}\n\n" .
                "Silakan verifikasi di sistem SIPETER.",

            self::SIGNATURE_VERIFIED => "✅ *TTD Digital Diverifikasi*\n\n" .
                "Yth. {authority_name}\n\n" .
                "TTD digital Anda telah diverifikasi untuk:\n" .
                "Kode: {code}\n\n" .
                "Terima kasih atas kerjasamanya.",

            self::SIGNATURE_REJECTED => "⚠️ *TTD Digital Ditolak*\n\n" .
                "Yth. {authority_name}\n\n" .
                "TTD digital Anda untuk dokumen {code} ditolak.\n" .
                "Alasan: {reason}\n\n" .
                "Mohon upload ulang.",

            self::SIGNATURE_REMINDER => "🔔 *Reminder: Menunggu TTD Digital*\n\n" .
                "Yth. {authority_name}\n\n" .
                "Dokumen {code} masih menunggu TTD digital Anda.\n\n" .
                "Terima kasih.",
        };
    }


    public function recipients(): array
    {
        return match($this) {
            self::NEW_REQUEST => ['admin'],
            self::REQUEST_APPROVED => ['user'],
            self::REQUEST_REJECTED => ['user'],
            self::DOCUMENT_READY => ['user'],
            self::DOCUMENT_UPLOADED => ['user'],
            self::DOCUMENT_DOWNLOADED => ['admin', 'user'],
            self::DOCUMENT_PICKED_UP => ['user'],
            self::DOCUMENT_COMPLETED => ['user'],

            self::VERIFICATION_REQUESTED => ['authority'],
            self::VERIFICATION_APPROVED => ['admin', 'user', 'authority'],
            self::VERIFICATION_REJECTED => ['admin', 'user', 'authority'],

            self::SIGNATURE_REQUESTED => ['authority'],
            self::SIGNATURE_UPLOADED => ['admin'],
            self::SIGNATURE_VERIFIED => ['authority'],
            self::SIGNATURE_REJECTED => ['authority'],
            self::SIGNATURE_REMINDER => ['authority'],
        };
    }


    public function shouldSendWhatsApp(): bool
    {
        return true;
    }
}
