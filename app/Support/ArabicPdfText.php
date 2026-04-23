<?php

namespace App\Support;

class ArabicPdfText
{
    /**
     * Prepare Arabic text for PDF engines with weak RTL shaping support.
     */
    public static function forPdf(?string $text): string
    {
        $text = trim((string) $text);

        if ($text === '' || ! preg_match('/[\x{0600}-\x{06FF}]/u', $text)) {
            return $text;
        }

        $shaped = self::shape($text);

        return self::reverseUnicode($shaped);
    }

    private static function shape(string $text): string
    {
        $chars = self::chars($text);
        $count = count($chars);
        $out = [];

        for ($i = 0; $i < $count; $i++) {
            $char = $chars[$i];
            $forms = self::forms()[$char] ?? null;

            if (! $forms) {
                $out[] = $char;

                continue;
            }

            $prev = self::findNeighbor($chars, $i, -1);
            $next = self::findNeighbor($chars, $i, 1);

            $connectPrev = $prev !== null
                && self::canConnectLeft($prev)
                && self::canConnectRight($char);

            $connectNext = $next !== null
                && self::canConnectLeft($char)
                && self::canConnectRight($next);

            if ($connectPrev && $connectNext && $forms['medial']) {
                $out[] = $forms['medial'];
            } elseif ($connectPrev && $forms['final']) {
                $out[] = $forms['final'];
            } elseif ($connectNext && $forms['initial']) {
                $out[] = $forms['initial'];
            } else {
                $out[] = $forms['isolated'];
            }
        }

        return implode('', $out);
    }

    private static function findNeighbor(array $chars, int $index, int $step): ?string
    {
        $i = $index + $step;

        while (isset($chars[$i])) {
            if (isset(self::forms()[$chars[$i]])) {
                return $chars[$i];
            }

            if (! preg_match('/[\s\p{P}]/u', $chars[$i])) {
                return null;
            }

            $i += $step;
        }

        return null;
    }

    private static function canConnectLeft(string $char): bool
    {
        $form = self::forms()[$char] ?? null;

        return (bool) ($form['initial'] || $form['medial']);
    }

    private static function canConnectRight(string $char): bool
    {
        $form = self::forms()[$char] ?? null;

        return (bool) ($form['final'] || $form['medial']);
    }

    private static function reverseUnicode(string $text): string
    {
        return implode('', array_reverse(self::chars($text)));
    }

    private static function chars(string $text): array
    {
        return preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    }

    private static function forms(): array
    {
        static $forms;

        if ($forms !== null) {
            return $forms;
        }

        $forms = [
            'ء' => ['isolated' => 'ء', 'final' => null, 'initial' => null, 'medial' => null],
            'آ' => ['isolated' => 'ﺁ', 'final' => 'ﺂ', 'initial' => null, 'medial' => null],
            'أ' => ['isolated' => 'ﺃ', 'final' => 'ﺄ', 'initial' => null, 'medial' => null],
            'ؤ' => ['isolated' => 'ﺅ', 'final' => 'ﺆ', 'initial' => null, 'medial' => null],
            'إ' => ['isolated' => 'ﺇ', 'final' => 'ﺈ', 'initial' => null, 'medial' => null],
            'ئ' => ['isolated' => 'ﺉ', 'final' => 'ﺊ', 'initial' => 'ﺋ', 'medial' => 'ﺌ'],
            'ا' => ['isolated' => 'ﺍ', 'final' => 'ﺎ', 'initial' => null, 'medial' => null],
            'ب' => ['isolated' => 'ﺏ', 'final' => 'ﺐ', 'initial' => 'ﺑ', 'medial' => 'ﺒ'],
            'ة' => ['isolated' => 'ﺓ', 'final' => 'ﺔ', 'initial' => null, 'medial' => null],
            'ت' => ['isolated' => 'ﺕ', 'final' => 'ﺖ', 'initial' => 'ﺗ', 'medial' => 'ﺘ'],
            'ث' => ['isolated' => 'ﺙ', 'final' => 'ﺚ', 'initial' => 'ﺛ', 'medial' => 'ﺜ'],
            'ج' => ['isolated' => 'ﺝ', 'final' => 'ﺞ', 'initial' => 'ﺟ', 'medial' => 'ﺠ'],
            'ح' => ['isolated' => 'ﺡ', 'final' => 'ﺢ', 'initial' => 'ﺣ', 'medial' => 'ﺤ'],
            'خ' => ['isolated' => 'ﺥ', 'final' => 'ﺦ', 'initial' => 'ﺧ', 'medial' => 'ﺨ'],
            'د' => ['isolated' => 'ﺩ', 'final' => 'ﺪ', 'initial' => null, 'medial' => null],
            'ذ' => ['isolated' => 'ﺫ', 'final' => 'ﺬ', 'initial' => null, 'medial' => null],
            'ر' => ['isolated' => 'ﺭ', 'final' => 'ﺮ', 'initial' => null, 'medial' => null],
            'ز' => ['isolated' => 'ﺯ', 'final' => 'ﺰ', 'initial' => null, 'medial' => null],
            'س' => ['isolated' => 'ﺱ', 'final' => 'ﺲ', 'initial' => 'ﺳ', 'medial' => 'ﺴ'],
            'ش' => ['isolated' => 'ﺵ', 'final' => 'ﺶ', 'initial' => 'ﺷ', 'medial' => 'ﺸ'],
            'ص' => ['isolated' => 'ﺹ', 'final' => 'ﺺ', 'initial' => 'ﺻ', 'medial' => 'ﺼ'],
            'ض' => ['isolated' => 'ﺽ', 'final' => 'ﺾ', 'initial' => 'ﺿ', 'medial' => 'ﻀ'],
            'ط' => ['isolated' => 'ﻁ', 'final' => 'ﻂ', 'initial' => 'ﻃ', 'medial' => 'ﻄ'],
            'ظ' => ['isolated' => 'ﻅ', 'final' => 'ﻆ', 'initial' => 'ﻇ', 'medial' => 'ﻈ'],
            'ع' => ['isolated' => 'ﻉ', 'final' => 'ﻊ', 'initial' => 'ﻋ', 'medial' => 'ﻌ'],
            'غ' => ['isolated' => 'ﻍ', 'final' => 'ﻎ', 'initial' => 'ﻏ', 'medial' => 'ﻐ'],
            'ف' => ['isolated' => 'ﻑ', 'final' => 'ﻒ', 'initial' => 'ﻓ', 'medial' => 'ﻔ'],
            'ق' => ['isolated' => 'ﻕ', 'final' => 'ﻖ', 'initial' => 'ﻗ', 'medial' => 'ﻘ'],
            'ك' => ['isolated' => 'ﻙ', 'final' => 'ﻚ', 'initial' => 'ﻛ', 'medial' => 'ﻜ'],
            'ل' => ['isolated' => 'ﻝ', 'final' => 'ﻞ', 'initial' => 'ﻟ', 'medial' => 'ﻠ'],
            'م' => ['isolated' => 'ﻡ', 'final' => 'ﻢ', 'initial' => 'ﻣ', 'medial' => 'ﻤ'],
            'ن' => ['isolated' => 'ﻥ', 'final' => 'ﻦ', 'initial' => 'ﻧ', 'medial' => 'ﻨ'],
            'ه' => ['isolated' => 'ﻩ', 'final' => 'ﻪ', 'initial' => 'ﻫ', 'medial' => 'ﻬ'],
            'و' => ['isolated' => 'ﻭ', 'final' => 'ﻮ', 'initial' => null, 'medial' => null],
            'ى' => ['isolated' => 'ﻯ', 'final' => 'ﻰ', 'initial' => null, 'medial' => null],
            'ي' => ['isolated' => 'ﻱ', 'final' => 'ﻲ', 'initial' => 'ﻳ', 'medial' => 'ﻴ'],
            'ﻻ' => ['isolated' => 'ﻻ', 'final' => 'ﻼ', 'initial' => null, 'medial' => null],
        ];

        return $forms;
    }
}
