<?php

namespace App\Support;

class Code39Barcode
{
    /**
     * Generate a simple Code39 SVG barcode.
     */
    public static function toSvg(string $value, int $height = 90): string
    {
        $value = strtoupper(trim($value));
        $encoded = '*' . $value . '*';

        $patterns = self::patterns();
        $x = 0;
        $narrow = 2;
        $wide = 5;
        $elements = [];

        foreach (str_split($encoded) as $char) {
            if (! isset($patterns[$char])) {
                continue;
            }

            $pattern = $patterns[$char];
            for ($i = 0; $i < 9; $i++) {
                $isBar = $i % 2 === 0;
                $width = $pattern[$i] === 'w' ? $wide : $narrow;
                if ($isBar) {
                    $elements[] = sprintf(
                        '<rect x="%d" y="0" width="%d" height="%d" fill="#0f172a"/>',
                        $x,
                        $width,
                        $height
                    );
                }
                $x += $width;
            }
            $x += $narrow; // inter-character gap
        }

        $svgWidth = max($x, 120);

        return sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" width="%d" height="%d" viewBox="0 0 %d %d" role="img" aria-label="barcode">%s</svg>',
            $svgWidth,
            $height,
            $svgWidth,
            $height,
            implode('', $elements)
        );
    }

    /**
     * Code39 narrow/wide pattern map.
     */
    private static function patterns(): array
    {
        return [
            '0' => 'nnnwwnwnn',
            '1' => 'wnnwnnnnw',
            '2' => 'nnwwnnnnw',
            '3' => 'wnwwnnnnn',
            '4' => 'nnnwwnnnw',
            '5' => 'wnnwwnnnn',
            '6' => 'nnwwwnnnn',
            '7' => 'nnnwnnwnw',
            '8' => 'wnnwnnwnn',
            '9' => 'nnwwnnwnn',
            'A' => 'wnnnnwnnw',
            'B' => 'nnwnnwnnw',
            'C' => 'wnwnnwnnn',
            'D' => 'nnnnwwnnw',
            'E' => 'wnnnwwnnn',
            'F' => 'nnwnwwnnn',
            'G' => 'nnnnnwwnw',
            'H' => 'wnnnnwwnn',
            'I' => 'nnwnnwwnn',
            'J' => 'nnnnwwwnn',
            'K' => 'wnnnnnnww',
            'L' => 'nnwnnnnww',
            'M' => 'wnwnnnnwn',
            'N' => 'nnnnwnnww',
            'O' => 'wnnnwnnwn',
            'P' => 'nnwnwnnwn',
            'Q' => 'nnnnnnwww',
            'R' => 'wnnnnnwwn',
            'S' => 'nnwnnnwwn',
            'T' => 'nnnnwnwwn',
            'U' => 'wwnnnnnnw',
            'V' => 'nwwnnnnnw',
            'W' => 'wwwnnnnnn',
            'X' => 'nwnnwnnnw',
            'Y' => 'wwnnwnnnn',
            'Z' => 'nwwnwnnnn',
            '-' => 'nwnnnnwnw',
            '.' => 'wwnnnnwnn',
            ' ' => 'nwwnnnwnn',
            '$' => 'nwnwnwnnn',
            '/' => 'nwnwnnnwn',
            '+' => 'nwnnnwnwn',
            '%' => 'nnnwnwnwn',
            '*' => 'nwnnwnwnn',
        ];
    }
}

