<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class ZatcaFatooraClient
{
    public function submit(string $xml, string $invoiceHash, bool $clearance = false): array
    {
        $baseUrl = rtrim((string) config('zatca.api_base_url'), '/');
        $environment = (string) config('zatca.environment', 'sandbox');
        $csid = $clearance ? (string) config('zatca.production_csid') : (string) config('zatca.compliance_csid');
        $secret = $clearance ? (string) config('zatca.production_secret') : (string) config('zatca.compliance_secret');

        if ($baseUrl === '' || $csid === '' || $secret === '') {
            throw new RuntimeException('ZATCA API credentials are not configured.');
        }

        $endpoint = $clearance ? '/invoices/clearance/single' : '/invoices/reporting/single';
        $response = Http::withBasicAuth($csid, $secret)
            ->withHeaders([
                'Accept' => 'application/json',
                'Accept-Version' => 'V2',
                'Content-Type' => 'application/json',
                'Clearance-Status' => $clearance ? '1' : '0',
                'X-ZATCA-Environment' => $environment,
            ])
            ->post($baseUrl . $endpoint, [
                'invoiceHash' => $invoiceHash,
                'invoice' => base64_encode($xml),
            ]);

        return [
            'ok' => $response->successful(),
            'status' => $response->status(),
            'body' => $response->json() ?? ['raw' => $response->body()],
        ];
    }
}
