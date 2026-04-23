<?php

namespace App\Support;

class ZatcaInvoiceSigner
{
    public function hash(string $xml): string
    {
        return base64_encode(hash('sha256', $xml, true));
    }

    public function sign(string $xml): ?string
    {
        $privateKey = trim((string) config('zatca.private_key'));
        if ($privateKey === '') {
            return null;
        }

        $resource = openssl_pkey_get_private($privateKey);
        if (! $resource) {
            return null;
        }

        $signature = '';
        $ok = openssl_sign($xml, $signature, $resource, OPENSSL_ALGO_SHA256);
        openssl_free_key($resource);

        return $ok ? base64_encode($signature) : null;
    }
}
