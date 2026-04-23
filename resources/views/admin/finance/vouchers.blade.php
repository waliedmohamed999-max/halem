@extends('layouts.admin')

@section('content')
@php
    $isAr = app()->getLocale() === 'ar';
    $typeLabels = [
        'receipt' => $isAr ? 'سند قبض' : 'Receipt',
        'payment' => $isAr ? 'سند صرف' : 'Payment',
    ];
    $statusLabels = [
        'posted' => $isAr ? 'مرحل' : 'Posted',
        'draft' => $isAr ? 'مسودة' : 'Draft',
        'void' => $isAr ? 'ملغي' : 'Void',
    ];
@endphp

<style>
    .finance-workspace{display:grid;gap:1rem}
    .finance-workspace .hero-card,.finance-workspace .panel-card{border:1px solid #d7ebe4;border-radius:1.6rem;background:linear-gradient(180deg,rgba(255,255,255,.98) 0%,rgba(245,252,249,.96) 100%);box-shadow:0 18px 38px rgba(16,82,92,.08)}
    .finance-workspace .hero-card{padding:1.2rem 1.25rem}
    .finance-workspace .hero-row,.finance-workspace .panel-head{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap}
    .finance-workspace .hero-kicker{display:inline-flex;align-items:center;gap:.35rem;padding:.35rem .8rem;border-radius:999px;background:#ebfaf5;border:1px solid #d6ece5;color:#13656b;font-size:.78rem;font-weight:800}
    .finance-workspace .hero-title{margin:.55rem 0 .3rem;color:#18485d;font-size:clamp(1.7rem,1.35rem + .7vw,2.3rem);font-weight:900}
    .finance-workspace .hero-copy{margin:0;max-width:64ch;color:#67808b;line-height:1.8}
    .finance-workspace .hero-actions{display:flex;gap:.6rem;flex-wrap:wrap}
    .finance-workspace .stat-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.85rem;margin-top:1rem}
    .finance-workspace .stat-card{padding:.95rem 1rem;border:1px solid #d7ebe4;border-radius:1.1rem;background:rgba(255,255,255,.9)}
    .finance-workspace .stat-card small{display:block;color:#68808b;font-weight:700;margin-bottom:.25rem}
    .finance-workspace .stat-card strong{display:block;color:#155f69;font-size:1.65rem;line-height:1;font-weight:900}
    .finance-workspace .stat-card span{display:block;margin-top:.4rem;color:#81949d;font-size:.78rem}
    .finance-workspace .workspace-grid{display:grid;grid-template-columns:minmax(0,1.7fr) minmax(320px,.95fr);gap:1rem;align-items:start}
    .finance-workspace .panel-card{padding:1rem}
    .finance-workspace .panel-title{margin:0;color:#18485d;font-size:1.15rem;font-weight:850}
    .finance-workspace .panel-subtitle{margin:.15rem 0 0;color:#7a9098;font-size:.83rem}
    .finance-workspace .filter-grid,.finance-workspace .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.8rem}
    .finance-workspace .full{grid-column:1/-1}
    .finance-workspace .form-label{display:block;margin-bottom:.35rem;color:#476471;font-size:.84rem;font-weight:800}
    .finance-workspace .table-shell{border:1px solid #deede7;border-radius:1.2rem;overflow:hidden;background:#fff}
    .finance-workspace .table thead th{background:#edf9f4;color:#285a66;font-size:.82rem;font-weight:800;border-bottom:1px solid #d8ebe4}
    .finance-workspace .table td{vertical-align:middle}
    .finance-workspace .table td strong{color:#1b4556}
    .finance-workspace .table td small{color:#7f939c}
    .finance-workspace .badge-soft{display:inline-flex;align-items:center;padding:.28rem .62rem;border-radius:999px;font-size:.74rem;font-weight:800}
    .finance-workspace .badge-soft.type-receipt{background:#e9faf2;color:#18724e}
    .finance-workspace .badge-soft.type-payment{background:#fff3ed;color:#b45522}
    .finance-workspace .badge-soft.status-posted{background:#e8faf0;color:#1c7c58}
    .finance-workspace .badge-soft.status-draft,.finance-workspace .badge-soft.status-void{background:#f1f5f7;color:#61757d}
    .finance-workspace .sticky-form{position:sticky;top:1rem}
    @media (max-width:1199.98px){.finance-workspace .workspace-grid{grid-template-columns:1fr}.finance-workspace .sticky-form{position:static}}
    @media (max-width:767.98px){.finance-workspace .stat-grid,.finance-workspace .filter-grid,.finance-workspace .form-grid{grid-template-columns:1fr}}
</style>

<div class="finance-workspace">
    <section class="hero-card">
        <div class="hero-row">
            <div>
                <span class="hero-kicker"><i class="bi bi-wallet2"></i> {{ $isAr ? 'النقدية والحركة المباشرة' : 'Cash movement' }}</span>
                <h1 class="hero-title">{{ $isAr ? 'السندات' : 'Vouchers' }}</h1>
                <p class="hero-copy">{{ $isAr ? 'إدارة سندات القبض والصرف وربطها بالفواتير والأطراف ومراكز التكلفة بدون فوضى في العرض.' : 'Manage receipt and payment vouchers with a cleaner operational layout.' }}</p>
            </div>
            <div class="hero-actions">
                <a class="btn btn-outline-secondary" href="{{ route('admin.finance.invoices', app()->getLocale()) }}">{{ $isAr ? 'الفواتير' : 'Invoices' }}</a>
            </div>
        </div>

        <div class="stat-grid">
            <div class="stat-card">
                <small>{{ $isAr ? 'إجمالي المقبوضات' : 'Total receipts' }}</small>
                <strong>{{ number_format((float) ($stats['receipts'] ?? 0), 2) }}</strong>
                <span>{{ $isAr ? 'سندات قبض مرحلة' : 'Posted receipt vouchers' }}</span>
            </div>
            <div class="stat-card">
                <small>{{ $isAr ? 'إجمالي المصروفات' : 'Total payments' }}</small>
                <strong>{{ number_format((float) ($stats['payments'] ?? 0), 2) }}</strong>
                <span>{{ $isAr ? 'سندات صرف مرحلة' : 'Posted payment vouchers' }}</span>
            </div>
            <div class="stat-card">
                <small>{{ $isAr ? 'سندات مرتبطة بفواتير' : 'Linked vouchers' }}</small>
                <strong>{{ number_format((int) ($stats['open_linked'] ?? 0)) }}</strong>
                <span>{{ $isAr ? 'مرتبطة بمرجع فاتورة' : 'Connected to invoice records' }}</span>
            </div>
        </div>
    </section>

    <section class="workspace-grid">
        <div class="panel-card">
            <div class="panel-head">
                <div>
                    <h2 class="panel-title">{{ $isAr ? 'سجل السندات' : 'Voucher register' }}</h2>
                    <p class="panel-subtitle">{{ $isAr ? 'فلترة سريعة وجدول أوضح لحركة السندات الحالية.' : 'Filter current voucher activity in a clearer table.' }}</p>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.finance.vouchers', app()->getLocale()) }}" class="filter-grid mb-3">
                <div>
                    <label class="form-label">{{ $isAr ? 'نوع السند' : 'Voucher type' }}</label>
                    <select class="form-select" name="voucher_type">
                        <option value="">{{ $isAr ? 'كل السندات' : 'All vouchers' }}</option>
                        <option value="receipt" @selected(request('voucher_type') === 'receipt')>{{ $isAr ? 'سند قبض' : 'Receipt' }}</option>
                        <option value="payment" @selected(request('voucher_type') === 'payment')>{{ $isAr ? 'سند صرف' : 'Payment' }}</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ $isAr ? 'الحالة' : 'Status' }}</label>
                    <select class="form-select" name="status">
                        <option value="">{{ $isAr ? 'كل الحالات' : 'All statuses' }}</option>
                        <option value="posted" @selected(request('status') === 'posted')>{{ $isAr ? 'مرحل' : 'Posted' }}</option>
                        <option value="draft" @selected(request('status') === 'draft')>{{ $isAr ? 'مسودة' : 'Draft' }}</option>
                        <option value="void" @selected(request('status') === 'void')>{{ $isAr ? 'ملغي' : 'Void' }}</option>
                    </select>
                </div>
                <div class="full">
                    <label class="form-label">{{ $isAr ? 'الطرف' : 'Party' }}</label>
                    <select class="form-select" name="party_id">
                        <option value="">{{ $isAr ? 'كل الأطراف' : 'All parties' }}</option>
                        @foreach($parties as $party)
                            <option value="{{ $party->id }}" @selected((string) request('party_id') === (string) $party->id)>{{ $party->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="full d-flex gap-2 flex-wrap">
                    <button class="btn btn-primary">{{ $isAr ? 'تصفية' : 'Filter' }}</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.finance.vouchers', app()->getLocale()) }}">{{ $isAr ? 'إعادة' : 'Reset' }}</a>
                </div>
            </form>

            <div class="table-shell">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ $isAr ? 'الرقم' : 'No.' }}</th>
                                <th>{{ $isAr ? 'البيان' : 'Details' }}</th>
                                <th>{{ $isAr ? 'الطرف' : 'Party' }}</th>
                                <th>{{ $isAr ? 'المبلغ' : 'Amount' }}</th>
                                <th>{{ $isAr ? 'الفاتورة' : 'Invoice' }}</th>
                                <th>{{ $isAr ? 'التاريخ' : 'Date' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vouchers as $voucher)
                                <tr>
                                    <td>
                                        <strong>{{ $voucher->voucher_no }}</strong>
                                        <div class="mt-1"><span class="badge-soft type-{{ $voucher->voucher_type }}">{{ $typeLabels[$voucher->voucher_type] ?? $voucher->voucher_type }}</span></div>
                                    </td>
                                    <td>
                                        <strong>{{ $voucher->branch?->name ?? ($isAr ? 'بدون فرع' : 'No branch') }}</strong>
                                        <div><small>{{ $voucher->costCenter?->name_ar ?? $voucher->costCenter?->name_en ?? ($isAr ? 'بدون مركز تكلفة' : 'No cost center') }}</small></div>
                                        <div class="mt-1"><span class="badge-soft status-{{ $voucher->status }}">{{ $statusLabels[$voucher->status] ?? $voucher->status }}</span></div>
                                    </td>
                                    <td>
                                        <strong>{{ $voucher->party?->name ?? ($isAr ? 'بدون طرف' : 'No party') }}</strong>
                                        <div><small>{{ $voucher->payment_method ?: ($isAr ? 'طريقة دفع غير محددة' : 'No payment method') }}</small></div>
                                    </td>
                                    <td><strong>{{ number_format((float) $voucher->amount, 2) }}</strong></td>
                                    <td>{{ $voucher->invoice?->invoice_no ?? ($isAr ? 'غير مرتبط' : 'Not linked') }}</td>
                                    <td>{{ optional($voucher->voucher_date)->format('Y-m-d') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-5 text-muted">{{ $isAr ? 'لا توجد سندات مطابقة للفلاتر الحالية.' : 'No vouchers found for the current filters.' }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-3">{{ $vouchers->links() }}</div>
        </div>

        <div class="sticky-form">
            <div class="panel-card">
                <div class="panel-head">
                    <div>
                        <h2 class="panel-title">{{ $isAr ? 'إضافة سند جديد' : 'Post voucher' }}</h2>
                        <p class="panel-subtitle">{{ $isAr ? 'نموذج مختصر وواضح لإدخال سند قبض أو صرف.' : 'A cleaner side form for posting vouchers.' }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.finance.vouchers.store', app()->getLocale()) }}" class="form-grid">
                    @csrf
                    <div>
                        <label class="form-label">{{ $isAr ? 'نوع السند' : 'Voucher type' }}</label>
                        <select class="form-select" name="voucher_type" required>
                            <option value="receipt" @selected(old('voucher_type', 'receipt') === 'receipt')>{{ $isAr ? 'سند قبض' : 'Receipt' }}</option>
                            <option value="payment" @selected(old('voucher_type') === 'payment')>{{ $isAr ? 'سند صرف' : 'Payment' }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">{{ $isAr ? 'التاريخ' : 'Date' }}</label>
                        <input class="form-control" type="date" name="voucher_date" value="{{ old('voucher_date', now()->format('Y-m-d')) }}" required>
                    </div>
                    <div>
                        <label class="form-label">{{ $isAr ? 'الفرع' : 'Branch' }}</label>
                        <select class="form-select" name="branch_id">
                            <option value="">{{ $isAr ? 'غير محدد' : 'Optional' }}</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" @selected((string) old('branch_id') === (string) $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">{{ $isAr ? 'مركز التكلفة' : 'Cost center' }}</label>
                        <select class="form-select" name="cost_center_id">
                            <option value="">{{ $isAr ? 'اختياري' : 'Optional' }}</option>
                            @foreach($costCenters as $center)
                                <option value="{{ $center->id }}" @selected((string) old('cost_center_id') === (string) $center->id)>{{ $center->code }} - {{ $center->name_ar ?? $center->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="full">
                        <label class="form-label">{{ $isAr ? 'الطرف' : 'Party' }}</label>
                        <select class="form-select" name="party_id">
                            <option value="">{{ $isAr ? 'بدون طرف' : 'No party' }}</option>
                            @foreach($parties as $party)
                                <option value="{{ $party->id }}" @selected((string) old('party_id') === (string) $party->id)>{{ $party->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="full">
                        <label class="form-label">{{ $isAr ? 'الفاتورة المرتبطة' : 'Linked invoice' }}</label>
                        <select class="form-select" name="invoice_id">
                            <option value="">{{ $isAr ? 'بدون فاتورة' : 'No invoice' }}</option>
                            @foreach($invoices as $invoice)
                                <option value="{{ $invoice->id }}" @selected((string) old('invoice_id') === (string) $invoice->id)>{{ $invoice->invoice_no }} - {{ $invoice->party?->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">{{ $isAr ? 'طريقة الدفع' : 'Payment method' }}</label>
                        <input class="form-control" type="text" name="payment_method" value="{{ old('payment_method', $isAr ? 'نقدي' : 'Cash') }}">
                    </div>
                    <div>
                        <label class="form-label">{{ $isAr ? 'المبلغ' : 'Amount' }}</label>
                        <input class="form-control" type="number" min="0.01" step="0.01" name="amount" value="{{ old('amount') }}" required>
                    </div>
                    <div class="full">
                        <label class="form-label">{{ $isAr ? 'ملاحظات' : 'Notes' }}</label>
                        <textarea class="form-control" rows="4" name="notes">{{ old('notes') }}</textarea>
                    </div>
                    <div class="full d-grid">
                        <button class="btn btn-primary">{{ $isAr ? 'ترحيل السند' : 'Post voucher' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
