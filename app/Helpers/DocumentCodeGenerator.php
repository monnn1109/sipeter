<?php

namespace App\Helpers;

use App\Models\DocumentType;
use App\Models\DocumentRequest;

class DocumentCodeGenerator
{

    public static function generate($documentTypeOrPrefix, ?string $applicantIdentifier = null): string
    {
        if ($documentTypeOrPrefix instanceof DocumentType) {
            $documentType = $documentTypeOrPrefix;
        }
        elseif (is_numeric($documentTypeOrPrefix)) {
            $documentType = DocumentType::findOrFail($documentTypeOrPrefix);
        }
        else {
            $documentType = DocumentType::where('code_prefix', $documentTypeOrPrefix)
                ->where('is_active', true)
                ->firstOrFail();
        }

        return $documentType->generateDocumentCode();
    }


    public static function parse(string $code): array
    {
        $parts = explode('-', $code);

        if (count($parts) !== 3) {
            return [
                'prefix' => '',
                'year' => '',
                'month' => '',
                'sequence' => ''
            ];
        }

        $yearMonth = $parts[1];
        $year = substr($yearMonth, 0, 4);
        $month = substr($yearMonth, 4, 2);

        return [
            'prefix' => $parts[0],
            'year' => $year,
            'month' => $month,
            'sequence' => $parts[2]
        ];
    }


    public static function isValid(string $code): bool
    {
        $pattern = '/^[A-Z]{2,10}-\d{6}-\d{3,4}$/';
        return preg_match($pattern, $code) === 1;
    }

    public static function getYear(string $code): ?string
    {
        return self::parse($code)['year'] ?: null;
    }


    public static function getMonth(string $code): ?string
    {
        return self::parse($code)['month'] ?: null;
    }

    public static function getSequence(string $code): ?string
    {
        return self::parse($code)['sequence'] ?: null;
    }


    public static function getPrefix(string $code): ?string
    {
        return self::parse($code)['prefix'] ?: null;
    }


    public static function generateById(int $documentTypeId): string
    {
        $documentType = DocumentType::findOrFail($documentTypeId);
        return $documentType->generateDocumentCode();
    }


    public static function generateByPrefix(string $codePrefix): string
    {
        $documentType = DocumentType::where('code_prefix', $codePrefix)
            ->where('is_active', true)
            ->firstOrFail();

        return $documentType->generateDocumentCode();
    }
}
