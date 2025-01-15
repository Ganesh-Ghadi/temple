<?php

namespace App\Helpers;

class NumberToWordsHelper
{
    private $ones = [
        '', 'एक', 'दोन', 'तीन', 'चार', 'पाच', 'सहा', 'सात', 'आठ', 'नऊ'
    ];

    private $tens = [
        '', 'दहा', 'वीस', 'तीस', 'चाळीस', 'पन्नास', 'साठ', 'सत्तरी', 'ऐंशी', 'नव्वद'
    ];

    private $hundreds = [
        '', 'शंभर', 'दोनशे', 'तीनशे', 'चारशे', 'पाचशे', 'सहा शंभर', 'सातशे', 'आठशे', 'नऊशे'
    ];

    private $thousands = [
        '', 'हजार', 'दोन हजार', 'तीन हजार', 'चार हजार', 'पाच हजार', 'सहा हजार', 'सात हजार', 'आठ हजार', 'नऊ हजार'
    ];

    public function convert($number)
    {
        // Split the number into integer (rupees) and decimal (paise) parts
        $number = number_format($number, 2, '.', ''); // Ensure two decimals
        $parts = explode('.', $number);

        $rupees = (int)$parts[0];  // Integer part - rupees
        $paise = (int)$parts[1];   // Decimal part - paise

        $rupeesInWords = $this->convertNumberToWords($rupees);
        $paiseInWords = $this->convertNumberToWords($paise);

        // Construct the final output
        $output = $rupeesInWords . " रुपये";
        if ($paise > 0) {
            $output .= " आणि " . $paiseInWords . " पैसे";
        } else {
            $output .= " आणि शून्य पैसे";
        }

        return $output;
    }

    private function convertNumberToWords($number)
    {
        if ($number == 0) {
            return 'शून्य';
        }

        $words = '';

        if ($number >= 1000) {
            $thousand = floor($number / 1000);
            $words .= $this->thousands[$thousand] . ' ';
            $number %= 1000;
        }

        if ($number >= 100) {
            $hundred = floor($number / 100);
            $words .= $this->hundreds[$hundred] . ' ';
            $number %= 100;
        }

        // Handle numbers from 21 to 29 properly
        if ($number >= 20) {
            $ten = floor($number / 10); // Extract the tens part
            $ones = $number % 10; // Extract the ones part

            if ($ten == 2) { // Special handling for 20 (वीस)
                $words .= 'वीस'; // Handling for 20
                if ($ones > 0) {
                    $words .= ' ' . $this->ones[$ones]; // Adding the ones if not zero
                }
            } else {
                $words .= $this->tens[$ten]; // Regular tens part
                if ($ones > 0) {
                    $words .= ' ' . $this->ones[$ones]; // Adding the ones part if greater than zero
                }
            }
        } else {
            // Handle numbers less than 20
            $words .= $this->ones[$number];
        }

        return trim($words);
    }
}//vfdf