<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('set_setting')) {
    function set_setting(string $key, mixed $value): void
    {
        Setting::set($key, $value);
    }
}

if (! function_exists('number_to_words')) {
    function number_to_words(float $num): string
    {
        $num = (int) round($num);
        if ($num === 0) return 'Zero Rupees Only';

        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
                 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
                 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        $h = function (int $n) use (&$h, $ones, $tens): string {
            if ($n === 0) return '';
            if ($n < 20) return $ones[$n] . ' ';
            if ($n < 100) return $tens[(int)($n / 10)] . ' ' . ($n % 10 ? $ones[$n % 10] . ' ' : '');
            return $ones[(int)($n / 100)] . ' Hundred ' . ($n % 100 ? $h($n % 100) : '');
        };

        $result = '';
        if ($num >= 10000000) { $result .= $h((int)($num / 10000000)) . 'Crore '; $num %= 10000000; }
        if ($num >= 100000)   { $result .= $h((int)($num / 100000))   . 'Lakh ';  $num %= 100000; }
        if ($num >= 1000)     { $result .= $h((int)($num / 1000))     . 'Thousand '; $num %= 1000; }
        $result .= $h($num);

        return 'Rupees ' . trim($result) . ' Only';
    }
}

if (! function_exists('fmt_inr')) {
    function fmt_inr(float $amount, bool $symbol = true): string
    {
        $formatted = number_format($amount, 2, '.', ',');
        // Apply Indian number system (after formatting, re-group)
        [$int, $dec] = explode('.', $formatted);
        $int = str_replace(',', '', $int);
        if (strlen($int) > 3) {
            $last3 = substr($int, -3);
            $rest  = substr($int, 0, -3);
            $rest  = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
            $int   = $rest . ',' . $last3;
        }
        return ($symbol ? '₹' : '') . $int . '.' . $dec;
    }
}
