<?php

namespace Alphaolomi\Azampay;

/**
 * @author Alpha Olomi
 */
class Helpers
{
    /**
     * Cleans the mobile number to remove any whitespace or dashes
     *
     * @param  string  $mobileNumber
     */
    public static function cleanMobileNumber($mobileNumber): string
    {
        $mobileNumber = preg_replace('/[^0-9]/', '', $mobileNumber);

        if (strlen($mobileNumber) < 9 || strlen($mobileNumber) > 12) {
            throw new \Exception('Invalid mobile number');
        }
        if (strlen($mobileNumber) == 9 && $mobileNumber[0] != '0') {
            $mobileNumber = "255{$mobileNumber}";
        } elseif (strlen($mobileNumber) == 10 && $mobileNumber[0] == '0') {
            $mobileNumber = str_replace('0', '255', $mobileNumber, 1);
        }

        return $mobileNumber;
    }

    /**
     * Clean the amount to remove any whitespace or commas
     *
     * @param  string  $amount
     */
    public static function cleanAmount($amount): string
    {
        $amount = trim($amount);
        $amount = str_replace(' ', '', $amount);
        $amount = str_replace(',', '', $amount);

        return $amount;
    }
}
