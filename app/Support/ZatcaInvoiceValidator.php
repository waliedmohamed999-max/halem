<?php

namespace App\Support;

use App\Models\FinanceInvoice;
use App\Models\Setting;

class ZatcaInvoiceValidator
{
    public function validate(FinanceInvoice $invoice): array
    {
        $invoice->loadMissing(['party', 'items']);

        $errors = [];
        $warnings = [];
        $sellerVat = (string) Setting::getValue('seller_vat_number', '');

        if ($sellerVat === '') {
            $errors[] = 'Seller VAT number is missing in settings.';
        }

        if (! $invoice->uuid) {
            $errors[] = 'Invoice UUID is missing.';
        }

        if (! in_array($invoice->invoice_scope, ['simplified', 'standard'], true)) {
            $errors[] = 'Invoice scope must be simplified or standard.';
        }

        if ($invoice->items->isEmpty()) {
            $errors[] = 'Invoice must contain at least one item.';
        }

        if ((float) $invoice->total < 0 || (float) $invoice->subtotal < 0) {
            $errors[] = 'Invoice totals must be non-negative.';
        }

        $expectedTax = round(max((float) $invoice->subtotal - (float) $invoice->discount, 0) * (((float) $invoice->tax_rate) / 100), 2);
        if (abs($expectedTax - (float) $invoice->tax) > 0.01) {
            $errors[] = 'Stored VAT amount does not match subtotal/discount/tax rate.';
        }

        if ($invoice->invoice_scope === 'standard') {
            if (! $invoice->party?->name) {
                $errors[] = 'Buyer name is required for standard invoices.';
            }

            if (! $invoice->party?->tax_number) {
                $warnings[] = 'Buyer tax number is recommended for standard invoices.';
            }
        }

        if (strtoupper((string) $invoice->currency_code) !== 'SAR') {
            $warnings[] = 'Non-SAR currency may require additional compliance review depending on business flow.';
        }

        if (! $invoice->zatca_xml_path) {
            $warnings[] = 'UBL/XML file has not been generated yet.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }
}
