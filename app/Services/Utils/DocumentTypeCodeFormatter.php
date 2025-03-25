<?php

namespace App\Services\Utils;

class DocumentTypeCodeFormatter
{
    public static function documentTypeCode(string $age, string $documentTypeCode): array
    {
        if ($age >= 18 && $documentTypeCode == 1) {
            $documentTypeCodes = [
                '524491', '524492', '4'
            ];
        } elseif ($age >= 14 && $age < 18 && $documentTypeCode == 1) {
            $documentTypeCodes = [
                '524491', '524492', '4'
            ];
        } elseif ($age < 14 && $documentTypeCode == 1) {
            $documentTypeCodes = [
                '40003'
            ];
        } elseif ($age >= 18 && $documentTypeCode == 2) {
            $documentTypeCodes = [
                '40001', '40002', '4'
            ];
        } elseif ($age >= 14 && $age < 18 && $documentTypeCode == 2) {
            $documentTypeCodes = [
                '40001', '40002', '40004'
            ];
        } elseif ($age < 14 && $documentTypeCode == 2) {
            $documentTypeCodes = [
                '40002', '40004'
            ];
        }
        return $documentTypeCodes;
    }
    public static function documentTypeCodeConfidant(
        string $age,
        string $documentTypeCode,
        string $jointOwnerSignatoryType
    ): array {
        if ($age >= 18 && $documentTypeCode == 1) {
            if ($jointOwnerSignatoryType == 1) {
                $documentTypeCodes = [
                    '524491', '524492', '4', '41001'
                ];
            } else {
                $documentTypeCodes = [
                    '524491', '524492', '4', '41002'
                ];
            }
        } elseif ($age >= 18 && $documentTypeCode == 4) {
            if ($jointOwnerSignatoryType == 1) {
                $documentTypeCodes = [
                    '40001', '40002', '4', '41001'
                ];
            } else {
                $documentTypeCodes = [
                    '40001', '40002', '4', '41002'
                ];
            }
        }
        return $documentTypeCodes;
    }
}
