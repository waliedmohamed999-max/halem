<?php

namespace App\Support;

use App\Models\FinanceInvoice;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ZatcaInvoiceWorkflowService
{
    public function __construct(
        private readonly ZatcaInvoiceValidator $validator,
        private readonly ZatcaInvoiceXmlBuilder $xmlBuilder,
        private readonly ZatcaInvoiceSigner $signer,
        private readonly ZatcaFatooraClient $client,
    ) {
    }

    public function generate(FinanceInvoice $invoice): FinanceInvoice
    {
        if (! $invoice->uuid) {
            $invoice->uuid = (string) Str::uuid();
        }

        $validation = $this->validator->validate($invoice);
        $xml = $this->xmlBuilder->build($invoice);
        $hash = $this->signer->hash($xml);
        $signature = $this->signer->sign($xml);

        $path = 'zatca/invoices/' . $invoice->invoice_no . '.xml';
        Storage::disk('public')->put($path, $xml);

        $invoice->forceFill([
            'zatca_status' => $validation['valid'] ? 'validated' : 'invalid',
            'zatca_xml_path' => $path,
            'zatca_invoice_hash' => $hash,
            'zatca_signature' => $signature,
            'zatca_validation' => $validation,
        ])->save();

        return $invoice->fresh();
    }

    public function submit(FinanceInvoice $invoice): FinanceInvoice
    {
        $invoice = $invoice->fresh();
        if (! $invoice->zatca_xml_path || ! Storage::disk('public')->exists($invoice->zatca_xml_path)) {
            $invoice = $this->generate($invoice);
        }

        $validation = $invoice->zatca_validation ?? $this->validator->validate($invoice);
        if (! ($validation['valid'] ?? false)) {
            throw new RuntimeException('Invoice failed local ZATCA validation.');
        }

        $xml = (string) Storage::disk('public')->get($invoice->zatca_xml_path);
        $clearance = $invoice->invoice_scope === 'standard';
        $response = $this->client->submit($xml, (string) $invoice->zatca_invoice_hash, $clearance);

        $invoice->forceFill([
            'zatca_last_response' => $response,
            'zatca_status' => $response['ok']
                ? ($clearance ? 'cleared' : 'reported')
                : 'failed',
            'zatca_reported_at' => $response['ok'] ? now() : $invoice->zatca_reported_at,
            'zatca_cleared_at' => $response['ok'] && $clearance ? now() : $invoice->zatca_cleared_at,
        ])->save();

        return $invoice->fresh();
    }
}
