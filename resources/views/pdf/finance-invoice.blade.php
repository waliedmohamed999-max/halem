@php
    $isAr = app()->getLocale() === 'ar';
    $renderMode = $renderMode ?? 'dompdf';
    $pdfText = static fn ($text) => ($isAr && $renderMode !== 'browser') ? \App\Support\ArabicPdfText::forPdf((string) $text) : (string) $text;
    $statusText = static function (?string $status) use ($isAr, $pdfText): string {
        $map = [
            'issued' => $isAr ? 'صادرة' : 'Issued',
            'draft' => $isAr ? 'مسودة' : 'Draft',
            'paid' => $isAr ? 'مدفوعة' : 'Paid',
            'partially_paid' => $isAr ? 'مدفوعة جزئيا' : 'Partially Paid',
            'cancelled' => $isAr ? 'ملغاة' : 'Cancelled',
            'validated' => $isAr ? 'متحققة' : 'Validated',
            'invalid' => $isAr ? 'غير مكتملة' : 'Invalid',
            'draft' => $isAr ? 'مسودة' : 'Draft',
            'reported' => $isAr ? 'مبلغ عنها' : 'Reported',
            'cleared' => $isAr ? 'مجازة' : 'Cleared',
            'submitted' => $isAr ? 'مرسلة' : 'Submitted',
        ];

        $value = $map[$status ?? ''] ?? ($status ?: '-');

        return $isAr ? $pdfText($value) : $value;
    };
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isAr ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $invoice->invoice_no }}</title>
    <style>
        @page { size: A4 portrait; margin: 10px 10px 12px 10px; }
        body { font-family: {{ $renderMode === 'browser' ? "'Segoe UI', Tahoma, Arial, sans-serif" : "DejaVu Sans, sans-serif" }}; color: #18352f; font-size: 10px; line-height: 1.35; margin: 0; }
        body.is-ar { direction: rtl; text-align: right; }
        body.is-ar table, body.is-ar .sheet, body.is-ar .card, body.is-ar .soft-box, body.is-ar .note { direction: rtl; }
        .sheet { border: 1px solid #dbe9e4; border-radius: 18px; overflow: hidden; }
        .hero { background: linear-gradient(135deg, #0f766e 0%, #1f9d8b 52%, #d8f0ea 100%); color: #fff; padding: 12px 16px 10px; }
        .hero-table { width: 100%; border-collapse: collapse; }
        .hero-title { font-size: 18px; font-weight: 800; letter-spacing: .2px; }
        .hero-subtitle { font-size: 9px; opacity: .92; margin-top: 2px; }
        .hero-badge { text-align: {{ $isAr ? 'left' : 'right' }}; }
        .hero-badge span { display: inline-block; padding: 6px 10px; border-radius: 999px; background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.28); font-size: 9px; font-weight: 700; }
        .section { padding: 8px 12px 0; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .card { border: 1px solid #dbe9e4; border-radius: 14px; padding: 10px 12px; background: #fbfefd; vertical-align: top; }
        .card-title { font-size: 12px; font-weight: 800; color: #0f5e53; margin-bottom: 8px; }
        .meta-table, .totals-table, .items-table, .kv-table { width: 100%; border-collapse: collapse; }
        .meta-table td { width: 25%; padding: 0 6px 6px 0; vertical-align: top; }
        .label { font-size: 8px; color: #6a867e; margin-bottom: 3px; }
        .value { font-size: 11px; font-weight: 700; color: #18352f; }
        .soft-box { border: 1px solid #d8e7e2; background: #f8fcfb; border-radius: 12px; padding: 8px 10px; min-height: 34px; }
        .kv-table td { padding: 7px 0; border-bottom: 1px dashed #dce9e4; vertical-align: top; }
        .kv-table tr:last-child td { border-bottom: 0; }
        .kv-table td:first-child { width: 42%; color: #55716a; font-size: 9px; font-weight: 700; }
        .kv-table td:last-child { width: 58%; font-weight: 700; color: #18352f; text-align: {{ $isAr ? 'left' : 'right' }}; }
        .items-wrap { margin-top: 6px; }
        .items-table th { background: #e7f5f1; color: #0e544a; font-size: 9px; font-weight: 800; padding: 7px 6px; border: 1px solid #d5e8e2; }
        .items-table td { padding: 7px 6px; border: 1px solid #dce9e4; vertical-align: top; font-size: 9px; }
        .items-table tbody tr:nth-child(even) td { background: #fcfefd; }
        .num { text-align: center; }
        .money { text-align: {{ $isAr ? 'left' : 'right' }}; white-space: nowrap; }
        .summary-panel { background: linear-gradient(180deg, #f9fcfb 0%, #eef8f5 100%); }
        .totals-table td { padding: 5px 0; font-size: 9px; }
        .totals-table .grand td { padding-top: 8px; border-top: 1px dashed #b7d5cd; font-size: 12px; font-weight: 800; color: #0e544a; }
        .status-row { margin-top: 8px; }
        .status-chip { display: inline-block; margin: 0 4px 4px 0; padding: 5px 8px; border-radius: 999px; background: #ebf7f4; border: 1px solid #cfe6df; color: #0f5e53; font-size: 9px; font-weight: 700; }
        .code-box { border: 1px solid #dbe9e4; border-radius: 14px; padding: 10px; background: #fff; text-align: center; min-height: 96px; }
        .barcode-box { padding-top: 6px; }
        .barcode-svg { display: flex; justify-content: center; align-items: center; min-height: 54px; margin: 6px 0 4px; }
        .barcode-svg svg { width: 100%; max-width: 170px; height: 52px; }
        .qr { width: 88px; height: 88px; margin: 6px auto 0; border: 1px solid #dce9e4; border-radius: 10px; background: #fff; padding: 4px; }
        .note { margin-top: 8px; border-radius: 12px; padding: 8px 10px; }
        .note-muted { background: #f7fbfa; border: 1px dashed #c8dfd8; color: #4f6d66; font-size: 8px; }
        .note-warn { background: #fff7df; border: 1px solid #ecd79e; color: #765c0b; font-size: 8px; }
        .footer { padding: 8px 12px 10px; color: #68827a; font-size: 8px; }
        .small { font-size: 8px; color: #708880; }
        .latin { direction: ltr; unicode-bidi: embed; display: inline-block; text-align: left; }
        .rtl-text { direction: rtl; unicode-bidi: embed; text-align: right; }
        body.is-ar .items-table th,
        body.is-ar .items-table td,
        body.is-ar .meta-table td,
        body.is-ar .totals-table td,
        body.is-ar .card-title,
        body.is-ar .label,
        body.is-ar .value,
        body.is-ar .footer { text-align: right; }
        body.is-ar .hero-title,
        body.is-ar .hero-subtitle { text-align: right; }
        body.is-ar .kv-table td:first-child { text-align: right; padding-left: 16px; }
        body.is-ar .kv-table td:last-child { text-align: left; padding-right: 16px; }
        body.is-ar .money,
        body.is-ar .num { direction: ltr; unicode-bidi: embed; }
        .value-block { display: inline-block; max-width: 100%; }
        .page-break-avoid { break-inside: avoid; page-break-inside: avoid; }
        .grid-span-wide { grid-column: span 2; }
        .summary-side { grid-template-columns: minmax(0, 1.05fr) minmax(220px, .95fr); align-items: start; }
        .sheet { page-break-inside: auto; }
        .section, .card, .soft-box, .items-table, .items-table tr, .items-wrap { break-inside: avoid; page-break-inside: avoid; }
        @media print {
            .sheet { border: 0; border-radius: 0; }
        }
    </style>
</head>
<body class="{{ $isAr ? 'is-ar' : 'is-en' }}">
<div class="sheet">
    <div class="hero">
        <table class="hero-table">
            <tr>
                <td>
                    <div class="hero-title">{{ $pdfText($invoiceSettings['seller_name'] ?: 'Dr Halim Dental') }}</div>
                    <div class="hero-subtitle">{{ $isAr ? $pdfText('فاتورة مالية احترافية جاهزة للحفظ والطباعة والأرشفة') : 'Professional financial invoice ready for filing, printing, and sharing' }}</div>
                </td>
                <td class="hero-badge">
                    <span>{{ $isAr ? $pdfText('فاتورة رقم') : 'Invoice No.' }} <span class="latin">{{ $invoice->invoice_no }}</span></span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table class="meta-table">
            <tr>
                <td>
                    <div class="soft-box">
                        <div class="label">{{ $isAr ? $pdfText('تاريخ الإصدار') : 'Issue Date' }}</div>
                        <div class="value"><span class="latin">{{ optional($invoice->issue_date)->format('Y-m-d') ?: '-' }}</span></div>
                    </div>
                </td>
                <td>
                    <div class="soft-box">
                        <div class="label">{{ $isAr ? $pdfText('تاريخ التوريد') : 'Supply Date' }}</div>
                        <div class="value"><span class="latin">{{ optional($invoice->supply_date)->format('Y-m-d') ?: '-' }}</span></div>
                    </div>
                </td>
                <td>
                    <div class="soft-box">
                        <div class="label">{{ $isAr ? $pdfText('تاريخ الاستحقاق') : 'Due Date' }}</div>
                        <div class="value"><span class="latin">{{ optional($invoice->due_date)->format('Y-m-d') ?: '-' }}</span></div>
                    </div>
                </td>
                <td>
                    <div class="soft-box">
                        <div class="label">{{ $isAr ? $pdfText('العملة ونطاق الفاتورة') : 'Currency & Scope' }}</div>
                        <div class="value"><span class="latin">{{ strtoupper($invoice->currency_code) }}</span> / {{ $invoice->invoice_scope === 'simplified' ? ($isAr ? $pdfText('مبسطة') : 'Simplified') : ($isAr ? $pdfText('قياسية') : 'Standard') }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="grid-2">
            <div class="card page-break-avoid">
                        <div class="card-title">{{ $isAr ? $pdfText('بيانات البائع') : 'Seller Information' }}</div>
                        <table class="kv-table">
                            <tr><td>{{ $isAr ? $pdfText('الاسم') : 'Name' }}</td><td><span class="value-block">{{ $pdfText($invoiceSettings['seller_name'] ?: '-') }}</span></td></tr>
                            <tr><td>{{ $isAr ? $pdfText('الرقم الضريبي') : 'VAT Number' }}</td><td><span class="latin">{{ $invoiceSettings['seller_vat_number'] ?: '-' }}</span></td></tr>
                            <tr><td>{{ $isAr ? $pdfText('السجل التجاري') : 'CR Number' }}</td><td><span class="latin">{{ $invoiceSettings['seller_cr_number'] ?: '-' }}</span></td></tr>
                            <tr><td>{{ $isAr ? $pdfText('الهاتف') : 'Phone' }}</td><td><span class="latin">{{ $invoiceSettings['seller_phone'] ?: '-' }}</span></td></tr>
                            <tr><td>{{ $isAr ? $pdfText('البريد') : 'Email' }}</td><td><span class="latin">{{ $invoiceSettings['seller_email'] ?: '-' }}</span></td></tr>
                            <tr><td>{{ $isAr ? $pdfText('العنوان') : 'Address' }}</td><td><span class="value-block">{{ $pdfText($invoiceSettings['seller_address'] ?: '-') }}</span></td></tr>
                        </table>
            </div>
            <div class="card page-break-avoid">
                        <div class="card-title">{{ $isAr ? $pdfText('بيانات العميل / المورد') : 'Customer / Supplier Information' }}</div>
                        <table class="kv-table">
                            <tr><td>{{ $isAr ? $pdfText('الاسم') : 'Name' }}</td><td><span class="value-block">{{ $pdfText($invoice->party?->name ?: '-') }}</span></td></tr>
                            <tr><td>{{ $isAr ? $pdfText('نوع الفاتورة') : 'Invoice Type' }}</td><td><span class="value-block">{{ $invoice->invoice_type === 'supplier' ? ($isAr ? $pdfText('فاتورة مورد') : 'Supplier Invoice') : ($isAr ? $pdfText('فاتورة عميل') : 'Customer Invoice') }}</span></td></tr>
                            <tr><td>{{ $isAr ? $pdfText('الرقم الضريبي') : 'Tax Number' }}</td><td><span class="latin">{{ $invoice->party?->tax_number ?: '-' }}</span></td></tr>
                            <tr><td>{{ $isAr ? $pdfText('الهاتف') : 'Phone' }}</td><td><span class="latin">{{ $invoice->party?->phone ?: '-' }}</span></td></tr>
                            <tr><td>{{ $isAr ? $pdfText('الفرع / مركز التكلفة') : 'Branch / Cost Center' }}</td><td><span class="value-block">{{ $pdfText($invoice->branch?->name ?: '-') }}</span> / <span class="latin">{{ $invoice->costCenter?->code ?: '-' }}</span></td></tr>
                            <tr><td>{{ $isAr ? $pdfText('العنوان') : 'Address' }}</td><td><span class="value-block">{{ $pdfText($invoice->party?->address ?: '-') }}</span></td></tr>
                        </table>
            </div>
        </div>
    </div>

    <div class="section items-wrap">
        <div class="card page-break-avoid">
            <div class="card-title">{{ $isAr ? $pdfText('بنود الفاتورة') : 'Invoice Items' }}</div>
            <table class="items-table">
                <thead>
                <tr>
                    <th width="6%">#</th>
                    <th width="46%">{{ $isAr ? $pdfText('الوصف') : 'Description' }}</th>
                    <th width="14%">{{ $isAr ? $pdfText('الكمية') : 'Qty' }}</th>
                    <th width="17%">{{ $isAr ? $pdfText('سعر الوحدة') : 'Unit Price' }}</th>
                    <th width="17%">{{ $isAr ? $pdfText('الإجمالي') : 'Line Total' }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td class="num">{{ $loop->iteration }}</td>
                        <td>{{ $pdfText($item->description) }}</td>
                        <td class="num">{{ number_format((float) $item->quantity, 2) }}</td>
                        <td class="money">{{ number_format((float) $item->unit_price, 2) }}</td>
                        <td class="money">{{ number_format((float) $item->line_total, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="grid-2 summary-side">
            <div class="card page-break-avoid">
                        <div class="card-title">{{ $isAr ? $pdfText('التتبع والاعتمادات') : 'Tracking & Compliance' }}</div>
                        <div class="status-row">
                            <span class="status-chip">{{ $statusText($invoice->status) }}</span>
                            <span class="status-chip">{{ $statusText($invoice->zatca_status) }}</span>
                            <span class="status-chip">{{ $invoice->payment_terms === 'cash' ? ($isAr ? $pdfText('نقدي') : 'Cash') : ($isAr ? $pdfText('آجل') : 'Credit') }}</span>
                        </div>
                        <div class="grid-2" style="margin-top:10px;">
                                    <div class="code-box barcode-box page-break-avoid">
                                        <div class="small">{{ $isAr ? $pdfText('باركود الفاتورة') : 'Invoice Barcode' }}</div>
                                        <div class="barcode-svg">{!! $barcodeSvg !!}</div>
                                        <div class="small"><span class="latin">{{ $invoice->invoice_no }}</span></div>
                                    </div>
                                    <div class="code-box page-break-avoid">
                                        <div class="small">{{ $isAr ? $pdfText('QR الضريبي') : 'Tax QR' }}</div>
                                        @if($zatcaQrUrl)
                                            <img class="qr" src="{{ $zatcaQrUrl }}" alt="QR">
                                        @elseif(! $supportsPdfQr && !($supportsBrowserQr ?? false) && $zatcaQrPayload)
                                            <div class="note note-muted" style="margin-top:8px;">{{ $isAr ? $pdfText('تم تعطيل صورة QR داخل PDF لأن امتداد GD غير مثبت على الخادم.') : 'QR image was skipped in the PDF because the GD extension is not installed on the server.' }}</div>
                                        @else
                                            <div class="note note-muted" style="margin-top:8px;">{{ $isAr ? $pdfText('يتطلب رقم ضريبي للبائع لإظهار QR.') : 'Seller VAT number is required to render the QR.' }}</div>
                                        @endif
                                    </div>
                        </div>
                        @if($invoice->reference_number)
                            <div class="note note-muted">{{ $isAr ? $pdfText('المرجع الخارجي:') : 'Reference:' }} <span class="latin">{{ $invoice->reference_number }}</span></div>
                        @endif
                        @if(!empty($zatcaValidation['errors']))
                            <div class="note note-warn">{{ $isAr ? $pdfText('ملاحظات التحقق:') : 'Validation Notes:' }} {{ $pdfText(implode(' | ', $zatcaValidation['errors'])) }}</div>
                        @endif
            </div>
            <div class="card summary-panel page-break-avoid">
                        <div class="card-title">{{ $isAr ? $pdfText('الملخص المالي') : 'Financial Summary' }}</div>
                        <table class="totals-table">
                            <tr><td>{{ $isAr ? $pdfText('الإجمالي قبل الخصم') : 'Subtotal' }}</td><td class="money">{{ number_format((float) $invoice->subtotal, 2) }}</td></tr>
                            <tr><td>{{ $isAr ? $pdfText('الخصم') : 'Discount' }}</td><td class="money">{{ number_format((float) $invoice->discount, 2) }}</td></tr>
                            <tr><td>{{ $isAr ? $pdfText('الوعاء الضريبي') : 'Taxable Amount' }}</td><td class="money">{{ number_format(max((float) $invoice->subtotal - (float) $invoice->discount, 0), 2) }}</td></tr>
                            <tr><td>{{ $isAr ? $pdfText('نسبة الضريبة') : 'Tax Rate' }}</td><td class="money">{{ number_format((float) $invoice->tax_rate, 2) }}<span class="latin">%</span></td></tr>
                            <tr><td>{{ $isAr ? $pdfText('قيمة الضريبة') : 'VAT Amount' }}</td><td class="money">{{ number_format((float) $invoice->tax, 2) }}</td></tr>
                            <tr><td>{{ $isAr ? $pdfText('المدفوع') : 'Paid Amount' }}</td><td class="money">{{ number_format((float) $invoice->paid_amount, 2) }}</td></tr>
                            <tr><td>{{ $isAr ? $pdfText('المتبقي') : 'Balance Due' }}</td><td class="money">{{ number_format((float) $invoice->balance_due, 2) }}</td></tr>
                            <tr class="grand"><td>{{ $isAr ? $pdfText('الإجمالي النهائي') : 'Grand Total' }}</td><td class="money">{{ number_format((float) $invoice->total, 2) }} <span class="latin">{{ strtoupper($invoice->currency_code) }}</span></td></tr>
                        </table>
            </div>
        </div>
    </div>

    @if($invoice->notes || $invoiceSettings['footer_note'])
        <div class="section">
            @if($invoice->notes)
                <div class="note note-muted"><strong>{{ $isAr ? $pdfText('ملاحظات الفاتورة:') : 'Invoice Notes:' }}</strong> {{ $pdfText($invoice->notes) }}</div>
            @endif
            @if($invoiceSettings['footer_note'])
                <div class="note note-muted"><strong>{{ $isAr ? $pdfText('تنويه إداري:') : 'Administrative Note:' }}</strong> {{ $pdfText($invoiceSettings['footer_note']) }}</div>
            @endif
        </div>
    @endif

    <div class="footer">
        {{ $isAr ? $pdfText('تم إنشاء هذا الملف تلقائيًا من نظام إدارة العيادة، وهو صالح للأرشفة والمشاركة والطباعة.') : 'This file was generated automatically from the clinic management system and is suitable for filing, sharing, and print use.' }}
    </div>
</div>
</body>
</html>
