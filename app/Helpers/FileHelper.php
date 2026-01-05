<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileHelper
{

    public const ALLOWED_EXTENSIONS = ['pdf', 'doc', 'docx'];


    public const MAX_FILE_SIZE = 5120;


    public static function validateFile(UploadedFile $file): array
    {
        if (!$file->isValid()) {
            return [
                'valid' => false,
                'error' => 'File tidak valid atau gagal diupload'
            ];
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            return [
                'valid' => false,
                'error' => 'Format file tidak diizinkan. Gunakan: ' . implode(', ', self::ALLOWED_EXTENSIONS)
            ];
        }

        $sizeInKB = $file->getSize() / 1024;
        if ($sizeInKB > self::MAX_FILE_SIZE) {
            return [
                'valid' => false,
                'error' => 'Ukuran file terlalu besar. Maksimal ' . self::MAX_FILE_SIZE . ' KB'
            ];
        }

        $mimeType = $file->getMimeType();
        $allowedMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];

        if (!in_array($mimeType, $allowedMimes)) {
            return [
                'valid' => false,
                'error' => 'Tipe file tidak valid'
            ];
        }

        return ['valid' => true, 'error' => null];
    }


    public static function storeFile(UploadedFile $file, string $requestCode, string $folder = 'mahasiswa'): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('YmdHis');
        $randomString = Str::random(8);

        $fileName = sprintf(
            '%s_%s_%s.%s',
            $requestCode,
            $timestamp,
            $randomString,
            $extension
        );

        $path = "documents/{$folder}";

        $file->storeAs($path, $fileName, 'public');

        return "{$path}/{$fileName}";
    }


    public static function deleteFile(string $filePath): bool
    {
        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->delete($filePath);
        }
        return false;
    }


    public static function fileExists(string $filePath): bool
    {
        return Storage::disk('public')->exists($filePath);
    }


    public static function getFileSize(string $filePath): string
    {
        if (!self::fileExists($filePath)) {
            return 'N/A';
        }

        $bytes = Storage::disk('public')->size($filePath);

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }


    public static function getFileUrl(string $filePath): ?string
    {
        if (!self::fileExists($filePath)) {
            return null;
        }

        return Storage::disk('public')->url($filePath);
    }


    public static function downloadFile(string $filePath, ?string $downloadName = null)
    {
        if (!self::fileExists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        $downloadName = $downloadName ?? basename($filePath);

        return Storage::disk('public')->download($filePath, $downloadName);
    }


    public static function getFileExtension(string $filePath): string
    {
        return pathinfo($filePath, PATHINFO_EXTENSION);
    }


    public static function sanitizeFileName(string $originalName): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $filename = pathinfo($originalName, PATHINFO_FILENAME);

        $filename = Str::slug($filename);

        return $filename . '.' . $extension;
    }


    public static function getAllowedExtensionsString(): string
    {
        return implode(', ', self::ALLOWED_EXTENSIONS);
    }


    public static function getMaxFileSizeMB(): float
    {
        return self::MAX_FILE_SIZE / 1024;
    }


    public static function isPDF(string $filePath): bool
    {
        return strtolower(self::getFileExtension($filePath)) === 'pdf';
    }

    public static function isWord(string $filePath): bool
    {
        $extension = strtolower(self::getFileExtension($filePath));
        return in_array($extension, ['doc', 'docx']);
    }
}
