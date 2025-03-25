<?php

namespace App\Services\Utils;

class AgeFormatter
{
    /**
     * Get the age category based on the provided age.
     *
     * @param int $age The age to categorize.
     *
     * @return string Returns 'adult' if age is 18 or above, 'teen' if age is between 14 and 17,
     *                and 'child' if age is below 14.
     */
    public static function getAgeCategory(string $age): string
    {
        if ($age >= 18) {
            return 'adult';
        }

        if ($age >= 14 && $age < 18) {
            return 'teen';
        }

        if ($age < 14 && $age != 0) {
            return 'child';
        }

        if ($age == 0) {
            return 'undefined';
        }
    }

    public static function isRus(string $ageCategory, string $documentTypeCode): bool
    {
        if (($ageCategory == 'teen' || $ageCategory == 'adult') &&  $documentTypeCode == 1) {
            $isRus = true;
        } elseif ($ageCategory == 'teen' || $ageCategory == 'adult' && $documentTypeCode == 4) {
            $isRus = false;
        }
        return $isRus;
    }
}
