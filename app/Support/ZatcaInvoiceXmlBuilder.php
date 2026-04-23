<?php

namespace App\Support;

use App\Models\FinanceInvoice;
use App\Models\Setting;
use DOMDocument;

class ZatcaInvoiceXmlBuilder
{
    public function build(FinanceInvoice $invoice): string
    {
        $invoice->loadMissing(['party', 'items']);

        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;

        $root = $doc->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:Invoice-2', 'Invoice');
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
        $doc->appendChild($root);

        $this->appendText($doc, $root, 'cbc:ProfileID', 'reporting:1.0');
        $this->appendText($doc, $root, 'cbc:ID', $invoice->invoice_no);
        $this->appendText($doc, $root, 'cbc:UUID', (string) $invoice->uuid);
        $this->appendText($doc, $root, 'cbc:IssueDate', $invoice->issue_date?->format('Y-m-d'));
        $this->appendText($doc, $root, 'cbc:IssueTime', '00:00:00');
        $this->appendText($doc, $root, 'cbc:InvoiceTypeCode', $invoice->invoice_scope === 'simplified' ? '388' : '388');
        $this->appendText($doc, $root, 'cbc:DocumentCurrencyCode', strtoupper((string) $invoice->currency_code));
        $this->appendText($doc, $root, 'cbc:TaxCurrencyCode', strtoupper((string) $invoice->currency_code));

        $supplier = $doc->createElement('cac:AccountingSupplierParty');
        $supplierParty = $doc->createElement('cac:Party');
        $supplierName = (string) Setting::getValue('site_name', 'Dr Halim Dental');
        $sellerVat = (string) Setting::getValue('seller_vat_number', '');
        $this->appendParty($doc, $supplierParty, $supplierName, $sellerVat, (string) Setting::getValue('seller_address_ar', ''));
        $supplier->appendChild($supplierParty);
        $root->appendChild($supplier);

        $customer = $doc->createElement('cac:AccountingCustomerParty');
        $customerParty = $doc->createElement('cac:Party');
        $this->appendParty($doc, $customerParty, (string) ($invoice->party?->name ?? 'Customer'), (string) ($invoice->party?->tax_number ?? ''), (string) ($invoice->party?->address ?? ''));
        $customer->appendChild($customerParty);
        $root->appendChild($customer);

        $taxTotal = $doc->createElement('cac:TaxTotal');
        $taxAmount = $doc->createElement('cbc:TaxAmount', number_format((float) $invoice->tax, 2, '.', ''));
        $taxAmount->setAttribute('currencyID', strtoupper((string) $invoice->currency_code));
        $taxTotal->appendChild($taxAmount);
        $root->appendChild($taxTotal);

        $legalTotal = $doc->createElement('cac:LegalMonetaryTotal');
        $this->appendAmount($doc, $legalTotal, 'cbc:LineExtensionAmount', (float) $invoice->subtotal, (string) $invoice->currency_code);
        $this->appendAmount($doc, $legalTotal, 'cbc:TaxExclusiveAmount', max((float) $invoice->subtotal - (float) $invoice->discount, 0), (string) $invoice->currency_code);
        $this->appendAmount($doc, $legalTotal, 'cbc:TaxInclusiveAmount', (float) $invoice->total, (string) $invoice->currency_code);
        $this->appendAmount($doc, $legalTotal, 'cbc:AllowanceTotalAmount', (float) $invoice->discount, (string) $invoice->currency_code);
        $this->appendAmount($doc, $legalTotal, 'cbc:PayableAmount', (float) $invoice->total, (string) $invoice->currency_code);
        $root->appendChild($legalTotal);

        foreach ($invoice->items as $idx => $item) {
            $line = $doc->createElement('cac:InvoiceLine');
            $this->appendText($doc, $line, 'cbc:ID', (string) ($idx + 1));
            $this->appendText($doc, $line, 'cbc:InvoicedQuantity', number_format((float) $item->quantity, 2, '.', ''));
            $this->appendAmount($doc, $line, 'cbc:LineExtensionAmount', (float) $item->line_total, (string) $invoice->currency_code);

            $itemElement = $doc->createElement('cac:Item');
            $this->appendText($doc, $itemElement, 'cbc:Name', $item->description);
            $line->appendChild($itemElement);

            $price = $doc->createElement('cac:Price');
            $this->appendAmount($doc, $price, 'cbc:PriceAmount', (float) $item->unit_price, (string) $invoice->currency_code);
            $line->appendChild($price);

            $root->appendChild($line);
        }

        return $doc->saveXML() ?: '';
    }

    private function appendParty(DOMDocument $doc, \DOMElement $party, string $name, string $taxNumber, string $address): void
    {
        $partyName = $doc->createElement('cac:PartyName');
        $this->appendText($doc, $partyName, 'cbc:Name', $name);
        $party->appendChild($partyName);

        if ($taxNumber !== '') {
            $taxScheme = $doc->createElement('cac:PartyTaxScheme');
            $this->appendText($doc, $taxScheme, 'cbc:CompanyID', $taxNumber);
            $party->appendChild($taxScheme);
        }

        if ($address !== '') {
            $postal = $doc->createElement('cac:PostalAddress');
            $this->appendText($doc, $postal, 'cbc:StreetName', $address);
            $party->appendChild($postal);
        }
    }

    private function appendAmount(DOMDocument $doc, \DOMElement $parent, string $name, float $amount, string $currency): void
    {
        $element = $doc->createElement($name, number_format($amount, 2, '.', ''));
        $element->setAttribute('currencyID', strtoupper($currency));
        $parent->appendChild($element);
    }

    private function appendText(DOMDocument $doc, \DOMElement $parent, string $name, ?string $value): void
    {
        $parent->appendChild($doc->createElement($name, $value ?? ''));
    }
}
