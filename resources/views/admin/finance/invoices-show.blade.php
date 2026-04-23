@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<style>
    .invoice-sheet { display: grid; gap: 1.5rem; }
    .invoice-toolbar { display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; }
    .invoice-document { border: 1px solid rgba(25,98,88,.12); border-radius: 1.6rem; background: linear-gradient(180deg, rgba(255,255,255,.99), rgba(246,251,250,.97)); box-shadow: 0 24px 60px rgba(18,59,53,.12); overflow: hidden; }
    .invoice-document-head { padding: 1.6rem 1.8rem; background: radial-gradient(circle at top left, rgba(29,143,120,.16), transparent 40%), linear-gradient(135deg, rgba(223,244,238,.9), rgba(255,255,255,.92)); border-bottom: 1px solid rgba(25,98,88,.1); }
    .invoice-document-body { padding: 1.8rem; }
    .invoice-brand-grid, .invoice-party-grid, .invoice-summary-grid, .invoice-meta-grid { display: grid; gap: 1rem; }
    .invoice-brand-grid { grid-template-columns: 1.15fr .85fr; gap: 1.5rem; }
    .invoice-meta-grid { grid-template-columns: repeat(4, minmax(0,1fr)); margin-top: 1.4rem; }
    .invoice-party-grid { grid-template-columns: repeat(2, minmax(0,1fr)); margin-top: 1.5rem; }
    .invoice-summary-grid { grid-template-columns: minmax(0,1.2fr) minmax(280px,.8fr); margin-top: 1.5rem; align-items: start; }
    .invoice-title { font-size: 2rem; font-weight: 900; color: #123b35; margin-bottom: .5rem; }
    .invoice-subtitle { color: #5d746e; max-width: 720px; line-height: 1.8; }
    .invoice-badges { display: flex; gap: .7rem; flex-wrap: wrap; margin-top: 1rem; }
    .invoice-badge { display: inline-flex; align-items: center; padding: .5rem .9rem; border-radius: 999px; background: rgba(29,143,120,.1); color: #156a59; font-size: .82rem; font-weight: 700; }
    .invoice-box { border: 1px solid rgba(25,98,88,.12); border-radius: 1.15rem; background: rgba(255,255,255,.9); padding: 1rem 1.05rem; }
    .invoice-box span { display: block; color: #718980; font-size: .8rem; margin-bottom: .35rem; }
    .invoice-box strong, .invoice-box h6 { color: #123b35; margin: 0; }
    .invoice-party-card h6, .invoice-compliance-card h6 { margin-bottom: .8rem; font-weight: 800; color: #123b35; }
    .invoice-party-card ul, .invoice-compliance-card ul { list-style: none; margin: 0; padding: 0; display: grid; gap: .6rem; }
    .invoice-party-card li, .invoice-compliance-card li { display: flex; justify-content: space-between; gap: 1rem; border-bottom: 1px dashed rgba(25,98,88,.12); padding-bottom: .45rem; color: #406057; }
    .invoice-lines table thead th { background: rgba(223,244,238,.9); color: #123b35; }
    .invoice-total-line { display: flex; justify-content: space-between; gap: 1rem; padding: .45rem 0; color: #426159; }
    .invoice-total-line.is-grand { margin-top: .55rem; padding-top: .8rem; border-top: 1px dashed rgba(25,98,88,.16); font-size: 1.08rem; font-weight: 900; color: #123b35; }
    .invoice-code-wrap { display: grid; gap: 1rem; }
    .invoice-qr-box { display: grid; justify-items: center; gap: .85rem; padding: 1rem; border-radius: 1.15rem; background: rgba(223,244,238,.65); border: 1px solid rgba(25,98,88,.12); }
    .invoice-qr-box img { width: 170px; height: 170px; object-fit: contain; background: #fff; border-radius: 1rem; padding: .55rem; border: 1px solid rgba(25,98,88,.12); }
    .invoice-footer-note, .invoice-disclaimer { margin-top: 1rem; padding: 1rem 1.1rem; border-radius: 1rem; line-height: 1.8; }
    .invoice-footer-note { background: rgba(248,252,251,.95); border: 1px dashed rgba(25,98,88,.16); color: #55726a; }
    .invoice-disclaimer { background: rgba(255,243,205,.84); border: 1px solid rgba(185,137,0,.12); color: #735a00; }
    @media print { .invoice-toolbar,.topbar{display:none !important;} .page-frame,.content-card,.content-card-body{box-shadow:none !important;background:#fff !important;} .invoice-document{border:0;box-shadow:none;} }
    @media (max-width: 991.98px) { .invoice-brand-grid,.invoice-summary-grid,.invoice-meta-grid,.invoice-party-grid { grid-template-columns: 1fr; } }
</style>

<div class="admin-list-page invoice-sheet">
    <div class="invoice-toolbar">
        <div>
            <h4 class="admin-list-title mb-1">{{ $isAr ? 'عرض الفاتورة' : 'Invoice View' }}</h4>
            <p class="admin-list-subtitle mb-0">{{ $isAr ? 'نسخة جاهزة للطباعة والمراجعة المالية والضريبية، مع إجراءات ZATCA.' : 'Print-ready version for finance and tax review, with ZATCA actions.' }}</p>
        </div>
        <div class="admin-list-actions">
            <a class="btn btn-danger" href="{{ route('admin.finance.invoices.pdf', [app()->getLocale(), $invoice]) }}">{{ $isAr ? 'تنزيل PDF احترافي' : 'Download Professional PDF' }}</a>
            <a class="btn btn-outline-secondary" href="{{ route('admin.finance.invoices', app()->getLocale()) }}">{{ $isAr ? 'العودة للفواتير' : 'Back to invoices' }}</a>
            <form method="POST" action="{{ route('admin.finance.invoices.zatca.generate', [app()->getLocale(), $invoice]) }}" class="d-inline">@csrf<button class="btn btn-outline-secondary">{{ $isAr ? 'توليد XML' : 'Generate XML' }}</button></form>
            <form method="POST" action="{{ route('admin.finance.invoices.zatca.validate', [app()->getLocale(), $invoice]) }}" class="d-inline">@csrf<button class="btn btn-outline-secondary">{{ $isAr ? 'تحقق محلي' : 'Validate' }}</button></form>
            <a class="btn btn-outline-secondary" href="{{ route('admin.finance.invoices.zatca.xml', [app()->getLocale(), $invoice]) }}">{{ $isAr ? 'تنزيل XML' : 'Download XML' }}</a>
            <form method="POST" action="{{ route('admin.finance.invoices.zatca.submit', [app()->getLocale(), $invoice]) }}" class="d-inline">@csrf<button class="btn btn-success">{{ $isAr ? 'إرسال إلى ZATCA' : 'Submit to ZATCA' }}</button></form>
            <button class="btn btn-primary" type="button" onclick="window.print()">{{ $isAr ? 'طباعة الفاتورة' : 'Print Invoice' }}</button>
        </div>
    </div>

    <div class="invoice-document">
        <div class="invoice-document-head">
            <div class="invoice-brand-grid">
                <div>
                    <div class="invoice-title">{{ $invoiceSettings['seller_name'] }}</div>
                    <div class="invoice-subtitle">{{ $isAr ? 'فاتورة مالية احترافية جاهزة للعرض والطباعة، مع باركود Code39 ورمز QR وتجهيز دورة ZATCA.' : 'Professional financial invoice ready for display and printing, with Code39 barcode, QR, and a ZATCA workflow setup.' }}</div>
                    <div class="invoice-badges">
                        <span class="invoice-badge">{{ $invoice->invoice_no }}</span>
                        <span class="invoice-badge">{{ strtoupper($invoice->currency_code) }}</span>
                        <span class="invoice-badge">{{ $invoice->invoice_scope === 'simplified' ? ($isAr ? 'فاتورة مبسطة' : 'Simplified Invoice') : ($isAr ? 'فاتورة قياسية' : 'Standard Invoice') }}</span>
                        <span class="invoice-badge">{{ $invoice->status }}</span>
                        <span class="invoice-badge">{{ $invoice->zatca_status }}</span>
                    </div>
                </div>
                <div class="invoice-code-wrap">
                    <div class="invoice-box text-center">
                        <span>{{ $isAr ? 'باركود الفاتورة' : 'Invoice Barcode' }}</span>
                        <div class="d-flex justify-content-center">{!! $barcodeSvg !!}</div>
                    </div>
                    @if($zatcaQrUrl)
                        <div class="invoice-qr-box">
                            <strong>{{ $isAr ? 'QR ضريبي' : 'Tax QR' }}</strong>
                            <img src="{{ $zatcaQrUrl }}" alt="ZATCA QR">
                            <small class="text-center text-muted">{{ $isAr ? 'مبني على TLV للحقول الأساسية المتاحة.' : 'Built from TLV payload using available core fields.' }}</small>
                        </div>
                    @endif
                </div>
            </div>

            <div class="invoice-meta-grid">
                <div class="invoice-box"><span>{{ $isAr ? 'تاريخ الإصدار' : 'Issue Date' }}</span><strong>{{ optional($invoice->issue_date)->format('Y-m-d') }}</strong></div>
                <div class="invoice-box"><span>{{ $isAr ? 'تاريخ التوريد' : 'Supply Date' }}</span><strong>{{ optional($invoice->supply_date)->format('Y-m-d') ?: '-' }}</strong></div>
                <div class="invoice-box"><span>{{ $isAr ? 'تاريخ الاستحقاق' : 'Due Date' }}</span><strong>{{ optional($invoice->due_date)->format('Y-m-d') ?: '-' }}</strong></div>
                <div class="invoice-box"><span>{{ $isAr ? 'UUID' : 'UUID' }}</span><strong>{{ $invoice->uuid ?: '-' }}</strong></div>
            </div>
        </div>

        <div class="invoice-document-body">
            <div class="invoice-party-grid">
                <div class="invoice-box invoice-party-card">
                    <h6>{{ $isAr ? 'بيانات البائع' : 'Seller Details' }}</h6>
                    <ul>
                        <li><span>{{ $isAr ? 'الاسم' : 'Name' }}</span><strong>{{ $invoiceSettings['seller_name'] ?: '-' }}</strong></li>
                        <li><span>{{ $isAr ? 'الرقم الضريبي' : 'VAT Number' }}</span><strong>{{ $invoiceSettings['seller_vat_number'] ?: '-' }}</strong></li>
                        <li><span>{{ $isAr ? 'السجل التجاري' : 'CR Number' }}</span><strong>{{ $invoiceSettings['seller_cr_number'] ?: '-' }}</strong></li>
                        <li><span>{{ $isAr ? 'العنوان' : 'Address' }}</span><strong>{{ $invoiceSettings['seller_address'] ?: '-' }}</strong></li>
                        <li><span>{{ $isAr ? 'الهاتف / البريد' : 'Phone / Email' }}</span><strong>{{ trim($invoiceSettings['seller_phone'] . ' / ' . $invoiceSettings['seller_email'], ' /') ?: '-' }}</strong></li>
                    </ul>
                </div>
                <div class="invoice-box invoice-party-card">
                    <h6>{{ $isAr ? 'بيانات الطرف' : 'Buyer / Supplier Details' }}</h6>
                    <ul>
                        <li><span>{{ $isAr ? 'الاسم' : 'Name' }}</span><strong>{{ $invoice->party?->name ?: '-' }}</strong></li>
                        <li><span>{{ $isAr ? 'النوع' : 'Type' }}</span><strong>{{ $invoice->invoice_type }}</strong></li>
                        <li><span>{{ $isAr ? 'الرقم الضريبي' : 'Tax Number' }}</span><strong>{{ $invoice->party?->tax_number ?: '-' }}</strong></li>
                        <li><span>{{ $isAr ? 'الهاتف' : 'Phone' }}</span><strong>{{ $invoice->party?->phone ?: '-' }}</strong></li>
                        <li><span>{{ $isAr ? 'العنوان' : 'Address' }}</span><strong>{{ $invoice->party?->address ?: '-' }}</strong></li>
                    </ul>
                </div>
            </div>

            <div class="invoice-summary-grid">
                <div class="invoice-box invoice-lines">
                    <h6 class="mb-3">{{ $isAr ? 'تفاصيل البنود' : 'Line Items' }}</h6>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ $isAr ? 'الوصف' : 'Description' }}</th>
                                <th>{{ $isAr ? 'الكمية' : 'Qty' }}</th>
                                <th>{{ $isAr ? 'سعر الوحدة' : 'Unit Price' }}</th>
                                <th>{{ $isAr ? 'الإجمالي' : 'Line Total' }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($invoice->items as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-semibold">{{ $item->description }}</td>
                                    <td>{{ number_format((float) $item->quantity, 2) }}</td>
                                    <td>{{ number_format((float) $item->unit_price, 2) }}</td>
                                    <td>{{ number_format((float) $item->line_total, 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="invoice-box">
                    <h6 class="mb-3">{{ $isAr ? 'الملخص المالي' : 'Financial Summary' }}</h6>
                    <div class="invoice-total-line"><span>{{ $isAr ? 'الإجمالي قبل الخصم' : 'Subtotal' }}</span><strong>{{ number_format((float) $invoice->subtotal, 2) }}</strong></div>
                    <div class="invoice-total-line"><span>{{ $isAr ? 'الخصم' : 'Discount' }}</span><strong>{{ number_format((float) $invoice->discount, 2) }}</strong></div>
                    <div class="invoice-total-line"><span>{{ $isAr ? 'نسبة الضريبة' : 'Tax Rate' }}</span><strong>{{ number_format((float) $invoice->tax_rate, 2) }}%</strong></div>
                    <div class="invoice-total-line"><span>{{ $isAr ? 'قيمة الضريبة' : 'VAT Amount' }}</span><strong>{{ number_format((float) $invoice->tax, 2) }}</strong></div>
                    <div class="invoice-total-line"><span>{{ $isAr ? 'المدفوع' : 'Paid' }}</span><strong>{{ number_format((float) $invoice->paid_amount, 2) }}</strong></div>
                    <div class="invoice-total-line"><span>{{ $isAr ? 'المتبقي' : 'Balance Due' }}</span><strong>{{ number_format((float) $invoice->balance_due, 2) }}</strong></div>
                    <div class="invoice-total-line is-grand"><span>{{ $isAr ? 'الإجمالي النهائي' : 'Grand Total' }}</span><strong>{{ number_format((float) $invoice->total, 2) }} {{ strtoupper($invoice->currency_code) }}</strong></div>
                </div>
            </div>

            <div class="invoice-summary-grid">
                <div class="invoice-box invoice-compliance-card">
                    <h6>{{ $isAr ? 'حالة التحقق المحلي' : 'Local Validation' }}</h6>
                    <ul>
                        <li><span>{{ $isAr ? 'الحالة' : 'Status' }}</span><strong>{{ ($zatcaValidation['valid'] ?? false) ? ($isAr ? 'سليم' : 'Valid') : ($isAr ? 'غير مكتمل' : 'Invalid') }}</strong></li>
                        <li><span>{{ $isAr ? 'الأخطاء' : 'Errors' }}</span><strong>{{ count($zatcaValidation['errors'] ?? []) }}</strong></li>
                        <li><span>{{ $isAr ? 'التحذيرات' : 'Warnings' }}</span><strong>{{ count($zatcaValidation['warnings'] ?? []) }}</strong></li>
                        <li><span>{{ $isAr ? 'XML' : 'XML' }}</span><strong>{{ $invoice->zatca_xml_path ? ($isAr ? 'موجود' : 'Generated') : ($isAr ? 'غير مولد' : 'Not generated') }}</strong></li>
                    </ul>
                </div>
                <div class="invoice-box invoice-compliance-card">
                    <h6>{{ $isAr ? 'حالة ZATCA / Fatoora' : 'ZATCA / Fatoora Status' }}</h6>
                    <ul>
                        <li><span>{{ $isAr ? 'الحالة' : 'Status' }}</span><strong>{{ $invoice->zatca_status ?: '-' }}</strong></li>
                        <li><span>{{ $isAr ? 'Hash الفاتورة' : 'Invoice Hash' }}</span><strong>{{ $invoice->zatca_invoice_hash ? \Illuminate\Support\Str::limit($invoice->zatca_invoice_hash, 24, '...') : '-' }}</strong></li>
                        <li><span>{{ $isAr ? 'آخر كود استجابة' : 'Last Response Code' }}</span><strong>{{ $invoice->zatca_last_response['status'] ?? '-' }}</strong></li>
                        <li><span>{{ $isAr ? 'وقت التقرير' : 'Reported At' }}</span><strong>{{ optional($invoice->zatca_reported_at)->format('Y-m-d H:i') ?: '-' }}</strong></li>
                        <li><span>{{ $isAr ? 'وقت الإجازة' : 'Cleared At' }}</span><strong>{{ optional($invoice->zatca_cleared_at)->format('Y-m-d H:i') ?: '-' }}</strong></li>
                    </ul>
                </div>
            </div>

            @if(!empty($zatcaValidation['errors']))
                <div class="invoice-disclaimer">
                    <strong>{{ $isAr ? 'أخطاء التحقق' : 'Validation Errors' }}</strong>
                    <div class="mt-2">{{ implode(' | ', $zatcaValidation['errors']) }}</div>
                </div>
            @endif

            @if(!empty($zatcaValidation['warnings']))
                <div class="invoice-footer-note">
                    <strong>{{ $isAr ? 'تحذيرات التحقق' : 'Validation Warnings' }}</strong>
                    <div class="mt-2">{{ implode(' | ', $zatcaValidation['warnings']) }}</div>
                </div>
            @endif

            @if($invoice->notes)
                <div class="invoice-footer-note"><strong>{{ $isAr ? 'ملاحظات الفاتورة' : 'Invoice Notes' }}</strong><div class="mt-2">{{ $invoice->notes }}</div></div>
            @endif

            @if($invoiceSettings['footer_note'])
                <div class="invoice-footer-note"><strong>{{ $isAr ? 'تنويه إداري' : 'Administrative Note' }}</strong><div class="mt-2">{{ $invoiceSettings['footer_note'] }}</div></div>
            @endif

            <div class="invoice-disclaimer">
                <strong>{{ $isAr ? 'تنبيه مهم' : 'Important Notice' }}</strong>
                <div class="mt-2">{{ $isAr ? 'النظام الآن يولد UBL/XML، يحسب hash، ويحفظ signature إذا كانت الشهادة موجودة، ويربط Fatoora API عند ضبط بياناته. الاعتماد الرسمي الكامل من ZATCA يتطلب أيضًا شهادة صحيحة، إعداد CSID، وربط البيئة المناسبة حسب مرحلة منشأتك.' : 'The system now generates UBL/XML, computes the hash, stores a signature when certificate data is available, and connects to the Fatoora API once configured. Full official ZATCA approval still requires valid certificate material, CSID setup, and the correct environment for your enforcement phase.' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
