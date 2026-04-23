@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<style>
    .invoice-builder { display: grid; gap: 1.5rem; }
    .invoice-builder-shell { display: grid; grid-template-columns: minmax(0,1.8fr) minmax(320px,.95fr); gap: 1.5rem; }
    .invoice-builder-card { border: 1px solid rgba(25,98,88,.12); border-radius: 1.35rem; background: linear-gradient(180deg, rgba(255,255,255,.98), rgba(244,251,249,.96)); box-shadow: 0 18px 46px rgba(18,59,53,.08); overflow: hidden; }
    .invoice-builder-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; padding: 1.25rem 1.4rem; border-bottom: 1px solid rgba(25,98,88,.1); background: linear-gradient(120deg, rgba(29,143,120,.07), rgba(223,244,238,.76)); }
    .invoice-builder-head h5 { margin: 0; font-size: 1.05rem; font-weight: 800; color: #123b35; }
    .invoice-builder-head p { margin: .35rem 0 0; color: #5b736b; font-size: .93rem; }
    .invoice-builder-body { padding: 1.3rem 1.4rem 1.45rem; }
    .invoice-hero-badge, .invoice-side-badge { display: inline-flex; align-items: center; gap: .45rem; padding: .42rem .8rem; border-radius: 999px; background: rgba(29,143,120,.1); color: #156a59; font-size: .83rem; font-weight: 700; }
    .invoice-meta-grid { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 1rem; }
    .invoice-meta-chip { padding: .95rem 1rem; border-radius: 1rem; border: 1px solid rgba(25,98,88,.1); background: rgba(255,255,255,.85); }
    .invoice-meta-chip span { display: block; margin-bottom: .32rem; color: #6a847c; font-size: .8rem; }
    .invoice-meta-chip strong { color: #123b35; font-size: .98rem; }
    .invoice-items-stack { display: flex; flex-direction: column; gap: .85rem; }
    .invoice-item-row { padding: 1rem; border: 1px solid rgba(25,98,88,.1); border-radius: 1rem; background: rgba(255,255,255,.84); }
    .invoice-summary-card { position: sticky; top: 1.5rem; }
    .invoice-summary-list { display: grid; gap: .75rem; }
    .invoice-summary-line { display: flex; justify-content: space-between; align-items: center; gap: 1rem; color: #34524a; font-size: .95rem; }
    .invoice-summary-line strong { color: #123b35; font-size: 1.04rem; }
    .invoice-summary-line.is-total { margin-top: .35rem; padding-top: .85rem; border-top: 1px dashed rgba(25,98,88,.18); font-size: 1rem; font-weight: 800; }
    .invoice-side-note, .invoice-warning-note { padding: .95rem 1rem; border-radius: 1rem; font-size: .9rem; line-height: 1.7; }
    .invoice-side-note { background: rgba(223,244,238,.72); color: #31574f; }
    .invoice-warning-note { background: rgba(255,243,205,.88); color: #6e5400; border: 1px solid rgba(185,137,0,.12); }
    .quick-party-card { margin-top: .85rem; padding: 1rem; border: 1px dashed rgba(25,98,88,.18); border-radius: 1rem; background: rgba(246,252,250,.95); }
    .quick-party-card[hidden] { display: none !important; }
    .quick-party-head { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: .8rem; }
    .quick-party-head strong { color: #123b35; }
    .quick-party-feedback { display: none; margin-bottom: .8rem; padding: .75rem .9rem; border-radius: .9rem; font-size: .9rem; }
    .quick-party-feedback.is-error { display: block; background: rgba(220,53,69,.08); color: #842029; border: 1px solid rgba(220,53,69,.14); }
    .quick-party-feedback.is-success { display: block; background: rgba(25,135,84,.1); color: #0f5132; border: 1px solid rgba(25,135,84,.14); }
    @media (max-width: 1199.98px) { .invoice-builder-shell { grid-template-columns: 1fr; } .invoice-summary-card { position: static; } }
    @media (max-width: 767.98px) { .invoice-meta-grid { grid-template-columns: 1fr; } }
</style>

<div class="admin-list-page invoice-builder">
    <div class="admin-list-header">
        <div>
            <h4 class="admin-list-title">{{ $isAr ? 'إنشاء فاتورة احترافية' : 'Create Professional Invoice' }}</h4>
            <p class="admin-list-subtitle">{{ $isAr ? 'فاتورة مالية جاهزة للعرض والطباعة مع بيانات ضريبية وتجهيز مهني للعميل أو المورد.' : 'Financial invoice ready for display and printing with tax data and polished customer or supplier presentation.' }}</p>
        </div>
        <div class="admin-list-actions">
            <a class="btn btn-outline-secondary" href="{{ route('admin.finance.invoices', app()->getLocale()) }}">{{ $isAr ? 'العودة للفواتير' : 'Back to invoices' }}</a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.finance.invoices.store', app()->getLocale()) }}" class="invoice-builder-shell">
        @csrf

        <div class="invoice-builder-card">
            <div class="invoice-builder-head">
                <div>
                    <h5>{{ $isAr ? 'بيانات الفاتورة' : 'Invoice Details' }}</h5>
                    <p>{{ $isAr ? 'حدد النوع والطرف والتواريخ والضرائب وبنود الخدمة بشكل واضح.' : 'Define type, party, dates, tax treatment, and service lines clearly.' }}</p>
                </div>
                <span class="invoice-hero-badge">{{ $isAr ? 'نمط مالي احترافي' : 'Finance Professional' }}</span>
            </div>

            <div class="invoice-builder-body">
                <div class="invoice-meta-grid mb-4">
                    <div class="invoice-meta-chip">
                        <span>{{ $isAr ? 'اسم البائع' : 'Seller' }}</span>
                        <strong>{{ $invoiceSettings['seller_name'] ?: ($isAr ? 'غير محدد' : 'Not configured') }}</strong>
                    </div>
                    <div class="invoice-meta-chip">
                        <span>{{ $isAr ? 'الرقم الضريبي' : 'VAT Number' }}</span>
                        <strong>{{ $invoiceSettings['seller_vat_number'] ?: ($isAr ? 'غير محدد' : 'Not configured') }}</strong>
                    </div>
                    <div class="invoice-meta-chip">
                        <span>{{ $isAr ? 'مرجع الإصدار' : 'Invoice Preview' }}</span>
                        <strong>{{ $isAr ? 'سيُنشأ تلقائيًا عند الحفظ' : 'Generated automatically on save' }}</strong>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">{{ $isAr ? 'نوع الفاتورة' : 'Invoice Type' }}</label>
                        <select class="form-select" name="invoice_type" id="invoice_type">
                            <option value="customer" @selected(old('invoice_type', 'customer') === 'customer')>{{ $isAr ? 'فاتورة عميل' : 'Customer Invoice' }}</option>
                            <option value="supplier" @selected(old('invoice_type') === 'supplier')>{{ $isAr ? 'فاتورة مورد' : 'Supplier Invoice' }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ $isAr ? 'نطاق الفاتورة' : 'Invoice Scope' }}</label>
                        <select class="form-select" name="invoice_scope">
                            <option value="simplified" @selected(old('invoice_scope', 'simplified') === 'simplified')>{{ $isAr ? 'مبسطة' : 'Simplified' }}</option>
                            <option value="standard" @selected(old('invoice_scope') === 'standard')>{{ $isAr ? 'قياسية' : 'Standard' }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ $isAr ? 'العملة' : 'Currency' }}</label>
                        <select class="form-select" name="currency_code">
                            <option value="SAR" @selected(old('currency_code', 'SAR') === 'SAR')>SAR</option>
                            <option value="EGP" @selected(old('currency_code') === 'EGP')>EGP</option>
                            <option value="USD" @selected(old('currency_code') === 'USD')>USD</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ $isAr ? 'شروط الدفع' : 'Payment Terms' }}</label>
                        <select class="form-select" name="payment_terms">
                            <option value="credit" @selected(old('payment_terms', 'credit') === 'credit')>{{ $isAr ? 'آجل' : 'Credit' }}</option>
                            <option value="cash" @selected(old('payment_terms') === 'cash')>{{ $isAr ? 'نقدي' : 'Cash' }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ $isAr ? 'الفرع' : 'Branch' }}</label>
                        <select class="form-select" name="branch_id">
                            <option value="">{{ $isAr ? 'غير محدد' : 'Unassigned' }}</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" @selected((string) old('branch_id') === (string) $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ $isAr ? 'مركز التكلفة' : 'Cost Center' }}</label>
                        <select class="form-select" name="cost_center_id">
                            <option value="">{{ $isAr ? 'اختياري' : 'Optional' }}</option>
                            @foreach($costCenters as $center)
                                <option value="{{ $center->id }}" @selected((string) old('cost_center_id') === (string) $center->id)>{{ $center->code }} - {{ $center->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                            <label class="form-label mb-0">{{ $isAr ? 'الطرف' : 'Party' }}</label>
                            <button class="btn btn-sm btn-outline-success" type="button" id="toggle-quick-party">{{ $isAr ? 'إضافة طرف جديد' : 'Add New Party' }}</button>
                        </div>
                        <select class="form-select" name="party_id" id="party_id" required>
                            <option value="">{{ $isAr ? 'اختر الطرف' : 'Select party' }}</option>
                            @foreach($parties as $party)
                                <option value="{{ $party->id }}" @selected((string) old('party_id') === (string) $party->id)>{{ $party->name }} ({{ $party->party_type }})</option>
                            @endforeach
                        </select>

                        <div class="quick-party-card" id="quick-party-card" hidden>
                            <div class="quick-party-head">
                                <strong>{{ $isAr ? 'إضافة عميل أو مورد بسرعة' : 'Quick Party Creation' }}</strong>
                                <button class="btn btn-sm btn-outline-secondary" type="button" id="close-quick-party">{{ $isAr ? 'إغلاق' : 'Close' }}</button>
                            </div>

                            <div class="quick-party-feedback" id="quick-party-feedback"></div>

                            <div id="quick-party-form" data-action="{{ route('admin.finance.parties.store', app()->getLocale()) }}">
                                <input type="hidden" id="quick-party-token" value="{{ csrf_token() }}">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label class="form-label">{{ $isAr ? 'النوع' : 'Type' }}</label>
                                        <select class="form-select" name="party_type" id="quick_party_type">
                                            <option value="customer">{{ $isAr ? 'عميل' : 'Customer' }}</option>
                                            <option value="supplier">{{ $isAr ? 'مورد' : 'Supplier' }}</option>
                                            <option value="both">{{ $isAr ? 'الاثنان' : 'Both' }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">{{ $isAr ? 'الاسم' : 'Name' }}</label>
                                        <input class="form-control" name="name" id="quick_party_name">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ $isAr ? 'الهاتف' : 'Phone' }}</label>
                                        <input class="form-control" name="phone">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ $isAr ? 'البريد' : 'Email' }}</label>
                                        <input class="form-control" name="email" type="email">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ $isAr ? 'الرقم الضريبي' : 'Tax Number' }}</label>
                                        <input class="form-control" name="tax_number">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ $isAr ? 'الرصيد الافتتاحي' : 'Opening Balance' }}</label>
                                        <input class="form-control" name="opening_balance" type="number" step="0.01" value="0">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">{{ $isAr ? 'العنوان' : 'Address' }}</label>
                                        <input class="form-control" name="address">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">{{ $isAr ? 'ملاحظات' : 'Notes' }}</label>
                                        <textarea class="form-control" name="notes" rows="2"></textarea>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <button class="btn btn-outline-secondary" type="button" id="reset-quick-party">{{ $isAr ? 'تفريغ' : 'Reset' }}</button>
                                        <button class="btn btn-success" type="button" id="submit-quick-party">{{ $isAr ? 'حفظ الطرف واختياره' : 'Save and Select Party' }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ $isAr ? 'تاريخ الإصدار' : 'Issue Date' }}</label>
                        <input class="form-control" type="date" name="issue_date" value="{{ old('issue_date', now()->toDateString()) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ $isAr ? 'تاريخ التوريد' : 'Supply Date' }}</label>
                        <input class="form-control" type="date" name="supply_date" value="{{ old('supply_date', now()->toDateString()) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ $isAr ? 'تاريخ الاستحقاق' : 'Due Date' }}</label>
                        <input class="form-control" type="date" name="due_date" value="{{ old('due_date', now()->addDays(30)->toDateString()) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ $isAr ? 'المرجع الخارجي' : 'Reference Number' }}</label>
                        <input class="form-control" name="reference_number" value="{{ old('reference_number') }}" placeholder="{{ $isAr ? 'اختياري' : 'Optional' }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ $isAr ? 'الخصم' : 'Discount' }}</label>
                        <input class="form-control" type="number" step="0.01" min="0" name="discount" id="discount_input" value="{{ old('discount', 0) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ $isAr ? 'نسبة الضريبة %' : 'Tax Rate %' }}</label>
                        <input class="form-control" type="number" step="0.01" min="0" max="100" name="tax_rate" id="tax_rate_input" value="{{ old('tax_rate', 15) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ $isAr ? 'الضريبة المتوقعة' : 'Estimated VAT' }}</label>
                        <input class="form-control" id="estimated_tax_display" value="0.00" readonly>
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ $isAr ? 'ملاحظات الفاتورة' : 'Invoice Notes' }}</label>
                        <textarea class="form-control" rows="3" name="notes" placeholder="{{ $isAr ? 'ملاحظات داخلية أو نص يظهر ضمن الفاتورة' : 'Internal notes or invoice memo' }}">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="invoice-builder-card mt-4">
                    <div class="invoice-builder-head">
                        <div>
                            <h5>{{ $isAr ? 'بنود الفاتورة' : 'Invoice Items' }}</h5>
                            <p>{{ $isAr ? 'أضف الخدمات أو المواد مع الكمية وسعر الوحدة. الملخص يحسب تلقائيًا.' : 'Add services or materials with quantity and unit price. Summary updates automatically.' }}</p>
                        </div>
                        <button class="btn btn-outline-secondary" type="button" id="add-item-row">{{ $isAr ? 'إضافة بند' : 'Add Item' }}</button>
                    </div>
                    <div class="invoice-builder-body">
                        <div id="invoice-items" class="invoice-items-stack">
                            @php($oldItems = old('items', [['description' => '', 'quantity' => 1, 'unit_price' => 0]]))
                            @foreach($oldItems as $i => $item)
                                <div class="invoice-item-row">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-6"><label class="form-label">{{ $isAr ? 'الوصف' : 'Description' }}</label><input class="form-control item-description" name="items[{{ $i }}][description]" value="{{ $item['description'] ?? '' }}" placeholder="{{ $isAr ? 'مثال: تنظيف أسنان' : 'Example: Teeth cleaning' }}"></div>
                                        <div class="col-md-2"><label class="form-label">{{ $isAr ? 'الكمية' : 'Qty' }}</label><input class="form-control item-qty" type="number" step="0.01" min="0" name="items[{{ $i }}][quantity]" value="{{ $item['quantity'] ?? 1 }}"></div>
                                        <div class="col-md-2"><label class="form-label">{{ $isAr ? 'سعر الوحدة' : 'Unit Price' }}</label><input class="form-control item-price" type="number" step="0.01" min="0" name="items[{{ $i }}][unit_price]" value="{{ $item['unit_price'] ?? 0 }}"></div>
                                        <div class="col-md-1"><label class="form-label">{{ $isAr ? 'الإجمالي' : 'Total' }}</label><input class="form-control item-total" value="0.00" readonly></div>
                                        <div class="col-md-1 d-grid"><button class="btn btn-outline-danger remove-item" type="button">{{ $isAr ? 'حذف' : 'Del' }}</button></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="invoice-builder invoice-summary-card">
            <div class="invoice-builder-card">
                <div class="invoice-builder-head">
                    <div>
                        <h5>{{ $isAr ? 'ملخص الفاتورة' : 'Invoice Summary' }}</h5>
                        <p>{{ $isAr ? 'مراجعة فورية قبل الحفظ والترحيل.' : 'Real-time review before saving and posting.' }}</p>
                    </div>
                    <span class="invoice-side-badge">{{ $isAr ? 'مباشر' : 'Live' }}</span>
                </div>
                <div class="invoice-builder-body">
                    <div class="invoice-summary-list">
                        <div class="invoice-summary-line"><span>{{ $isAr ? 'الإجمالي قبل الخصم' : 'Subtotal' }}</span><strong id="summary_subtotal">0.00</strong></div>
                        <div class="invoice-summary-line"><span>{{ $isAr ? 'الخصم' : 'Discount' }}</span><strong id="summary_discount">0.00</strong></div>
                        <div class="invoice-summary-line"><span>{{ $isAr ? 'الوعاء الضريبي' : 'Taxable Amount' }}</span><strong id="summary_taxable">0.00</strong></div>
                        <div class="invoice-summary-line"><span>{{ $isAr ? 'الضريبة' : 'VAT' }}</span><strong id="summary_tax">0.00</strong></div>
                        <div class="invoice-summary-line is-total"><span>{{ $isAr ? 'الإجمالي النهائي' : 'Grand Total' }}</span><strong id="summary_total">0.00</strong></div>
                    </div>

                    <div class="invoice-side-note mt-4">
                        <strong>{{ $isAr ? 'ما الذي سيُضاف عند الحفظ؟' : 'What gets added on save?' }}</strong>
                        <div class="mt-2">{{ $isAr ? 'سيتم إنشاء رقم فاتورة تلقائي، وترحيل مالي ومحاسبي، وصفحة عرض جاهزة للطباعة مع باركود Code39 وQR.' : 'An automatic invoice number, finance/accounting posting, and a print-ready invoice page with Code39 barcode and QR will be generated.' }}</div>
                    </div>

                    @if(blank($invoiceSettings['seller_vat_number']))
                        <div class="invoice-warning-note mt-3">
                            <strong>{{ $isAr ? 'تنبيه ضريبي' : 'Tax Notice' }}</strong>
                            <div class="mt-2">{{ $isAr ? 'الرقم الضريبي للبائع غير مضبوط في الإعدادات. يمكن تجهيز QR، لكن الفاتورة لن تكون مكتملة ضريبيًا قبل إدخال الرقم الضريبي.' : 'Seller VAT number is not configured in settings. The QR can be prepared, but the tax invoice remains incomplete until VAT data is configured.' }}</div>
                        </div>
                    @endif

                    <div class="d-grid gap-2 mt-4">
                        <button class="btn btn-primary btn-lg">{{ $isAr ? 'حفظ وترحيل الفاتورة' : 'Save and Post Invoice' }}</button>
                        <a class="btn btn-outline-secondary" href="{{ route('admin.settings.index', app()->getLocale()) }}">{{ $isAr ? 'إعدادات الضريبة والبائع' : 'Seller & Tax Settings' }}</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('invoice-items');
    const addButton = document.getElementById('add-item-row');
    const discountInput = document.getElementById('discount_input');
    const taxRateInput = document.getElementById('tax_rate_input');
    const estimatedTaxDisplay = document.getElementById('estimated_tax_display');
    const invoiceTypeInput = document.getElementById('invoice_type');
    const partySelect = document.getElementById('party_id');
    const quickPartyCard = document.getElementById('quick-party-card');
    const toggleQuickParty = document.getElementById('toggle-quick-party');
    const closeQuickParty = document.getElementById('close-quick-party');
    const resetQuickParty = document.getElementById('reset-quick-party');
    const quickPartyForm = document.getElementById('quick-party-form');
    const quickPartyToken = document.getElementById('quick-party-token');
    const quickPartyType = document.getElementById('quick_party_type');
    const quickPartyName = document.getElementById('quick_party_name');
    const quickPartyFeedback = document.getElementById('quick-party-feedback');
    const submitQuickParty = document.getElementById('submit-quick-party');
    let index = container.querySelectorAll('.invoice-item-row').length;

    const formatMoney = (value) => Number(value || 0).toFixed(2);

    const recalc = () => {
        let subtotal = 0;
        container.querySelectorAll('.invoice-item-row').forEach((row) => {
            const qty = Number(row.querySelector('.item-qty')?.value || 0);
            const price = Number(row.querySelector('.item-price')?.value || 0);
            const total = qty * price;
            subtotal += total;
            const totalField = row.querySelector('.item-total');
            if (totalField) totalField.value = formatMoney(total);
        });

        const discount = Number(discountInput?.value || 0);
        const taxable = Math.max(subtotal - discount, 0);
        const taxRate = Number(taxRateInput?.value || 0);
        const tax = taxable * (taxRate / 100);
        const grandTotal = taxable + tax;

        document.getElementById('summary_subtotal').textContent = formatMoney(subtotal);
        document.getElementById('summary_discount').textContent = formatMoney(discount);
        document.getElementById('summary_taxable').textContent = formatMoney(taxable);
        document.getElementById('summary_tax').textContent = formatMoney(tax);
        document.getElementById('summary_total').textContent = formatMoney(grandTotal);
        estimatedTaxDisplay.value = formatMoney(tax);
    };

    const syncQuickPartyType = () => {
        if (! quickPartyType || ! invoiceTypeInput) return;
        quickPartyType.value = invoiceTypeInput.value === 'supplier' ? 'supplier' : 'customer';
    };

    const setQuickPartyEnabled = (enabled) => {
        quickPartyForm?.querySelectorAll('input, select, textarea, button').forEach((field) => {
            field.disabled = ! enabled;
        });

        if (quickPartyName) {
            quickPartyName.required = enabled;
        }
    };

    const showQuickParty = () => {
        syncQuickPartyType();
        setQuickPartyEnabled(true);
        quickPartyCard.hidden = false;
    };

    const hideQuickParty = () => {
        quickPartyCard.hidden = true;
        setQuickPartyEnabled(false);
    };

    const setQuickPartyFeedback = (message, type) => {
        if (! quickPartyFeedback) return;
        quickPartyFeedback.className = 'quick-party-feedback';
        if (! message) {
            quickPartyFeedback.textContent = '';
            return;
        }
        quickPartyFeedback.textContent = message;
        quickPartyFeedback.classList.add(type === 'success' ? 'is-success' : 'is-error');
    };

    addButton?.addEventListener('click', function () {
        const row = document.createElement('div');
        row.className = 'invoice-item-row';
        row.innerHTML = `
            <div class="row g-2 align-items-end">
                <div class="col-md-6"><label class="form-label">{{ $isAr ? 'الوصف' : 'Description' }}</label><input class="form-control item-description" name="items[${index}][description]" placeholder="{{ $isAr ? 'مثال: حشو تجميلي' : 'Example: Cosmetic filling' }}"></div>
                <div class="col-md-2"><label class="form-label">{{ $isAr ? 'الكمية' : 'Qty' }}</label><input class="form-control item-qty" type="number" step="0.01" min="0" name="items[${index}][quantity]" value="1"></div>
                <div class="col-md-2"><label class="form-label">{{ $isAr ? 'سعر الوحدة' : 'Unit Price' }}</label><input class="form-control item-price" type="number" step="0.01" min="0" name="items[${index}][unit_price]" value="0"></div>
                <div class="col-md-1"><label class="form-label">{{ $isAr ? 'الإجمالي' : 'Total' }}</label><input class="form-control item-total" value="0.00" readonly></div>
                <div class="col-md-1 d-grid"><button class="btn btn-outline-danger remove-item" type="button">{{ $isAr ? 'حذف' : 'Del' }}</button></div>
            </div>`;
        container.appendChild(row);
        index += 1;
        recalc();
    });

    toggleQuickParty?.addEventListener('click', showQuickParty);
    closeQuickParty?.addEventListener('click', hideQuickParty);
    resetQuickParty?.addEventListener('click', function () {
        quickPartyForm?.querySelectorAll('input:not([type="hidden"]), textarea').forEach((field) => field.value = '');
        syncQuickPartyType();
        setQuickPartyFeedback('', 'success');
    });
    invoiceTypeInput?.addEventListener('change', syncQuickPartyType);

    submitQuickParty?.addEventListener('click', async function () {
        setQuickPartyFeedback('', 'success');
        submitQuickParty.disabled = true;

        try {
            const formData = new FormData();
            formData.append('_token', quickPartyToken?.value || '');
            quickPartyForm?.querySelectorAll('input[name], select[name], textarea[name]').forEach((field) => {
                formData.append(field.name, field.value);
            });

            const response = await fetch(quickPartyForm.dataset.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });

            const payload = await response.json();

            if (! response.ok) {
                const message = payload?.message || Object.values(payload?.errors || {}).flat().join(' | ') || '{{ $isAr ? 'تعذر حفظ الطرف.' : 'Unable to save the party.' }}';
                setQuickPartyFeedback(message, 'error');
                return;
            }

            const party = payload.party;
            const option = document.createElement('option');
            option.value = party.id;
            option.textContent = `${party.name} (${party.party_type})`;
            option.selected = true;
            partySelect.appendChild(option);
            partySelect.value = String(party.id);

            quickPartyForm?.querySelectorAll('input:not([type="hidden"]), textarea').forEach((field) => field.value = '');
            syncQuickPartyType();
            setQuickPartyFeedback('{{ $isAr ? 'تم إضافة الطرف واختياره في الفاتورة.' : 'Party created and selected in the invoice.' }}', 'success');
            hideQuickParty();
        } catch (error) {
            setQuickPartyFeedback('{{ $isAr ? 'حدث خطأ أثناء الاتصال بالخادم.' : 'A server communication error occurred.' }}', 'error');
        } finally {
            submitQuickParty.disabled = false;
        }
    });

    container?.addEventListener('input', recalc);
    container?.addEventListener('click', function (event) {
        if (event.target.classList.contains('remove-item')) {
            event.target.closest('.invoice-item-row')?.remove();
            recalc();
        }
    });
    discountInput?.addEventListener('input', recalc);
    taxRateInput?.addEventListener('input', recalc);

    syncQuickPartyType();
    setQuickPartyEnabled(false);
    recalc();
});
</script>
@endsection
