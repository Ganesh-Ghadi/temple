<?php
namespace App\Helpers;

class AmountToWordsHelper
{
    public static function amountToWords($no, $currency = 'INR', $formatString = '%UNIT% %WHOLE% आणि %FRACTION% %SUBUNIT%', $option = 0)
    {
        if ($no == 0) return "Zero";

        $words = $formatString;
        $whole = '';
        $fraction = '';

        $wholeInt = floor($no);
        $whole = ($wholeInt == 0) ? 'शून्य' : self::words_big($wholeInt, $option);
        $fracInt = (int) (($no - $wholeInt) * 100) % 100;
        $fraction = ($fracInt == 0) ? 'शून्य' : self::words_units($fracInt);

        // TBD: Lookup unit, subunit for $currency & replace values below
        $curr_unit = '';
        $curr_subunit = 'पैसे';

        $words = str_replace('%WHOLE%', $whole, $words);
        $words = str_replace('%FRACTION%', $fraction, $words);
        $words = str_replace('%UNIT%', $curr_unit, $words);
        $words = str_replace('%SUBUNIT%', $curr_subunit, $words);

        return ucfirst($words);
    }

    protected static function words_big($no, $option)
    {
        $descSingular = $option == 0 ? ["हजार", "लाख", "करोड", "Thousand Crore"] : ["Thousand", "Million", "Billion", "Trillion"];
        $descPlural = $option == 0 ? ["हजार", "लाख", "करोड", "Thousand Crores"] : ["Thousand", "Million", "Billion", "Trillion"];
        $compare = $option == 0 ? [1000, 100000, 10000000, 10000000000] : [1000, 1000000, 1000000000, 1000000000000];

        $divide = count($descSingular);
        $loop = $divide;
        $words = '';

        for ($i = $loop - 1; $i >= 0; $i--) {
            $nos = ($i == $loop - 1) ? $no : $no % $compare[$i + 1];
            $split = (int) floor($nos / $compare[$i]);

            if ($split > 0) {
                if ($split < 100) {
                    $words .= ' ' . self::words_units($split) . ' ' . ($split == 1 ? $descSingular[$i] : $descPlural[$i]);
                } elseif ($split >= 100 && $split < 1000) {
                    $words .= ' ' . self::words_hundreds($split) . ' ' . $descPlural[$i];
                } else if ($split >= 1000) {
                    $words .= ' ' . self::words_big($split, $option) . ' ' . $descPlural[$i];
                }
            }

            if ($i == 0) {
                $hundred = (int) $no % 1000;
                if ($hundred > 0 && $hundred <= 999) {
                    $words .= ' ' . self::words_hundreds($hundred);
                }
            }
        }

        return trim($words);
    }

    protected static function words_hundreds($no)
    {
        $words = '';
        if ($no > 999) throw new \Exception("Invalid call to hundreds function");

        $hundreds = 0;
        if ($no > 99) {
            $hundreds = floor($no / 100);
            $words = self::words_units($hundreds) . "शे";
        }

        $words .= ' ' . self::words_units($no - (100 * $hundreds));
        return trim($words);
    }

    protected static function words_units($no)
    {
        $words = '';
        if ($no > 100) throw new \Exception("Invalid call to Units function");

        $units = [
            1 => "ऎक", 2 => "दोन", 3 => "तीन", 4 => "चार", 5 => "पाच", 6 => "सहा",
            7 => "सात", 8 => "आठ", 9 => "नऊ", 10 => "दहा", 11 => "अकरा", 12 => "बारा",
            13 => "तेरा", 14 => "चौदा", 15 => "पंधरा", 16 => "सोळा", 17 => "सतरा", 18 => "अठरा",
            19 => "ऎकोणिस", 20 => "वीस", 21 => "एकवीस", 22 => "बावीस", 23 => "तेवीस",
            24 => "चोवीस", 25 => "पंचवीस", 26 => "सव्वीस", 27 => "सत्तावीस", 28 => "अठ्ठावीस",
            29 => "एकोणतीस", 30 => "तीस", 31 => "एकतीस", 32 => "बत्तीस", 33 => "तेहतीस", 
            34 => "चौतीस", 35 => "पस्तीस", 36 => "छत्तीस", 37 => "सदोतीस", 38 => "अडोतीस",
            39 => "एकोणचाळीस", 40 => "चाळीस", 41 => "एक्केचाळीस", 42 => "बेचाळीस", 43 => "त्रेचाळीस",
            44 => "चव्वेचाळीस", 45 => "पंचेचाळीस", 46 => "सेहेचाळीस", 47 => "सत्तेचाळीस", 
            48 => "अठ्ठेचाळीस", 49 => "एकोणपन्नास", 50 => "पन्नास", 51 => "एक्कावन्न", 
            52 => "बावन्न", 53 => "त्रेपन्न", 54 => "चोपन्न", 55 => "पंचावन्न", 56 => "छपन्न", 
            57 => "सत्तावन्न", 58 => "अठावन्न", 59 => "एकोणसाठ", 60 => "साठ", 61 => "एकसष्ट", 
            62 => "बासष्ट", 63 => "त्रेसष्ट", 64 => "चौसष्ट", 65 => "पासष्ट", 66 => "सहासष्ट", 
            67 => "सदुसष्ट", 68 => "अडूसष्ट", 69 => "एकोणसत्तर", 70 => "सत्तर", 71 => "एक्काहत्तर", 
            72 => "बाहत्तर", 73 => "त्र्याहत्तर", 74 => "चौत्र्याहत्तर", 75 => "पंचाहत्तर", 
            76 => "श्याहत्तर", 77 => "सत्यात्तर", 78 => "आठयात्तर", 79 => "एकोणऐंशी", 80 => "ऐंशी", 
            81 => "एक्याऐंशी", 82 => "ब्याऐंशी", 83 => "त्र्याऐंशी", 84 => "चौऐंशी", 85 => "पंच्याऐंशी", 
            86 => "शाऐंशी", 87 => "सत्याऐंशी", 88 => "अठयाऐंशी", 89 => "एकोणनव्वद", 90 => "नव्वद", 
            91 => "एक्याण्णव", 92 => "ब्याण्णव", 93 => "त्र्याण्णव", 94 => "चौरयाण्णव", 95 => "पंचाण्णव", 
            96 => "शहाण्णव", 97 => "सत्याण्णव", 98 => "आठ्याण्णव", 99 => "नौव्याण्णव", 100 => "शंभर"
        ];

        if (array_key_exists($no, $units)) {
            $words = $units[$no];
        }

        return $words;
    }
}