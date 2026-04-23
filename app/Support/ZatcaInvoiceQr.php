<?php

namespace App\Support;

class ZatcaInvoiceQr
{
    public static function buildBase64(
        string $sellerName,
        string $sellerVatNumber,
        string $timestamp,
        float $invoiceTotal,
        float $vatTotal
    ): string {
        $payload = self::tlv(1, $sellerName)
            . self::tlv(2, $sellerVatNumber)
            . self::tlv(3, $timestamp)
            . self::tlv(4, number_format($invoiceTotal, 2, '.', ''))
            . self::tlv(5, number_format($vatTotal, 2, '.', ''));

        return base64_encode($payload);
    }

    public static function qrUrl(string $base64Payload, int $size = 220): string
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=' . $size . 'x' . $size . '&data=' . urlencode($base64Payload);
    }

    private static function tlv(int $tag, string $value): string
    {
        $bytes = $value;

        return chr($tag) . chr(strlen($bytes)) . $bytes;
    }
}
