<?php

// This source code is now part of the PHPLint project, see:
// http://cvs.icosaedro.it:8080/viewvc/public/phplint/stdlib/it/icosaedro/web
// Test code also available under the test/ directory of that project.



namespace app\core\utils;

/**
 * Italian Codice Fiscale normalization, formatting and validation routines.
 * A regular CF is composed by 16 among letters and digits; the last
 * character is always a letter representing the control code.
 * A temporary CF could also be assigned; a temporary CF is composed of
 * 11 digits, the last digit being the control code.
 * Examples: MRORSS00A00A000U, 12345678903.
 * @author Umberto Salsi <salsi@icosaedro.it>
 * @version $Date: 2020/01/23 10:35:20 $
 */
class CodiceFiscale
{

    /**
     * Normalizes a CF by removing white spaces and converting to upper-case.
     * Useful to clean-up user's input and to save the result in the DB.
     * @param string $cf Raw CF, possibly with spaces.
     * @return string Normalized CF.
     */
    static function normalize($cf)
    {
        $cf = (string) str_replace(" ", "", $cf);
        $cf = (string) str_replace("\t", "", $cf);
        $cf = (string) str_replace("\r", "", $cf);
        $cf = (string) str_replace("\n", "", $cf);
        $cf = strtoupper($cf);
        return $cf;
    }

    /**
     * Returns the formatted CF. Currently does nothing but normalization.
     * @param string $cf Raw CF, possibly with spaces.
     * @return string Formatted CF.
     */
    static function format($cf)
    {
        return self::normalize($cf);
    }

    /**
     * Validates a regular CF.
     * @param string $cf Normalized, 16 characters CF.
     * @return string NULL if valid, or string describing why this CF must be
     * rejected.
     */
    private static function validate_regular($cf)
    {
        if (preg_match("/^[0-9A-Z]{16}\$/sD", $cf) !== 1)
            return "Codice Fiscale - uno o più caratteri non validi";
        $s = 0;
        $even_map = "BAFHJNPRTVCESULDGIMOQKWZYX";
        for ($i = 0; $i < 15; $i++) {
            $c = $cf[$i];
            if (ctype_digit($c))
                $n = ord($c) - ord('0');
            else
                $n = ord($c) - ord('A');
            if (($i & 1) == 0)
                $n = ord($even_map[$n]) - ord('A');
            $s += $n;
        }
        if ($s % 26 + ord("A") !== ord($cf[15]))
            return "Codice Fiscale - codice di controllo non valido";
        return NULL;
    }


    /**
     * Verifies the basic syntax, length and control code of the given CF.
     * @param string $cf Raw CF, possibly with spaces.
     * @return string NULL if valid, or string describing why this CF must be
     * rejected.
     */
    static function validate($cf)
    {
        $cf = self::normalize($cf);
        if (strlen($cf) == 16)
            return self::validate_regular($cf);
        else
            return "Codice Fiscale - la lunghezza del non è valida.";
    }
}
