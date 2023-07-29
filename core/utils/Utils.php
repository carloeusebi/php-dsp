<?php

namespace app\core\utils;

use DateTime;

class Utils
{
    /**
     * Calculates age based on birthday;
     */
    static function calculateAge(string $birthday): int
    {
        $today = new DateTime();
        $birth_date = new DateTime($birthday);
        $age_interval = $today->diff($birth_date);
        return $age_interval->y;
    }
}
