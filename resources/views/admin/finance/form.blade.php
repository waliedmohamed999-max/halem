@php
    $isAr = app()->getLocale() === 'ar';
    $isEdit = isset($finance);
@endphp

<style>
    .finance-panel { border: 1px solid #d7e3f5; border-radius: 14px; }
    .finance-panel .card-header { background: #f4f8ff; border-bottom: 1px solid #d7e3f5; font-weight: 700; }
    .quick-chip { border-radius: 999px; }
</style>

<form id="finance-form" method="POST" action="{{ $isEdit ? route('admin.finance.update', [app()->getLocale(), $finance->id]) : route('admin.finance.store', app()->getLocale()) }}">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>{{ $isAr ? 'يوجد أخطاء في النموذج:' : 'There are form errors:' }}</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card finance-panel mb-3">
        <div class="card-header">{{ $isAr ? 'البيانات الأساسية' : 'Basic Info' }}</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">{{ $isAr ? 'نوع الحركة' : 'Entry Type' }} *</label>
                    <select class="form-select @error('entry_type') is-invalid @enderror" id="entry_type" name="entry_type" required>
                        <option value="income" @selected(old('entry_type', $finance->entry_type ?? '') === 'income')>{{ $isAr ? 'إيراد' : 'Income' }}</option>
                        <option value="expense" @selected(old('entry_type', $finance->entry_type ?? '') === 'expense')>{{ $isAr ? 'مصروف' : 'Expense' }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ $isAr ? 'البند' : 'Kind' }} *</label>
                    <select class="form-select @error('entry_kind') is-invalid @enderror" id="entry_kind" name="entry_kind" required>
                        <option value="incoming_invoice" @selected(old('entry_kind', $finance->entry_kind ?? '') === 'incoming_invoice')>{{ $isAr ? 'فاتورة داخلة' : 'Incoming Invoice' }}</option>
                        <option value="outgoing_invoice" @selected(old('entry_kind', $finance->entry_kind ?? '') === 'outgoing_invoice')>{{ $isAr ? 'فاتورة خارجة' : 'Outgoing Invoice' }}</option>
                        <option value="expense" @selected(old('entry_kind', $finance->entry_kind ?? '') === 'expense')>{{ $isAr ? 'مصروف تشغيلي' : 'Operational Expense' }}</option>
                        <option value="other" @selected(old('entry_kind', $finance->entry_kind ?? '') === 'other')>{{ $isAr ? 'أخرى' : 'Other' }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ $isAr ? 'الفرع' : 'Branch' }}</label>
                    <select class="form-select @error('branch_id') is-invalid @enderror" name="branch_id">
                        <option value="">{{ $isAr ? 'بدون' : 'None' }}</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" @selected((string) old('branch_id', $finance->branch_id ?? '') === (string) $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ $isAr ? 'الحالة' : 'Record Status' }} *</label>
                    <select class="form-select @error('record_status') is-invalid @enderror" name="record_status" required>
                        <option value="posted" @selected(old('record_status', $finance->record_status ?? 'posted') === 'posted')>{{ $isAr ? 'مرحل' : 'Posted' }}</option>
                        <option value="void" @selected(old('record_status', $finance->record_status ?? 'posted') === 'void')>{{ $isAr ? 'ملغي' : 'Void' }}</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">{{ $isAr ? 'عنوان الحركة' : 'Title' }} *</label>
                    <input class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $finance->title ?? '') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ $isAr ? 'رقم الفاتورة' : 'Invoice Number' }}</label>
                    <div class="input-group">
                        <input class="form-control @error('invoice_number') is-invalid @enderror" id="invoice_number" name="invoice_number" value="{{ old('invoice_number', $finance->invoice_number ?? '') }}">
                        <button class="btn btn-outline-secondary" type="button" onclick="generateInvoiceNumber()">{{ $isAr ? 'توليد' : 'Generate' }}</button>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ $isAr ? 'الجهة' : 'Counterparty' }}</label>
                    <input class="form-control @error('counterparty') is-invalid @enderror" name="counterparty" value="{{ old('counterparty', $finance->counterparty ?? '') }}">
                </div>
            </div>
        </div>
    </div>

    <div class="card finance-panel mb-3">
        <div class="card-header">{{ $isAr ? 'البيانات المالية' : 'Financial Details' }}</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">{{ $isAr ? 'المبلغ' : 'Amount' }} *</label>
                    <input type="number" step="0.01" min="0" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', $finance->amount ?? '') }}" required>
                    <div class="d-flex flex-wrap gap-1 mt-2">
                        <button type="button" class="btn btn-sm btn-outline-primary quick-chip" onclick="setQuickAmount(100)">100</button>
                        <button type="button" class="btn btn-sm btn-outline-primary quick-chip" onclick="setQuickAmount(250)">250</button>
                        <button type="button" class="btn btn-sm btn-outline-primary quick-chip" onclick="setQuickAmount(500)">500</button>
                        <button type="button" class="btn btn-sm btn-outline-primary quick-chip" onclick="setQuickAmount(1000)">1000</button>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ $isAr ? 'التاريخ' : 'Date' }} *</label>
                    <input type="date" class="form-control @error('entry_date') is-invalid @enderror" id="entry_date" name="entry_date" value="{{ old('entry_date', isset($finance) && $finance->entry_date ? $finance->entry_date->format('Y-m-d') : now()->toDateString()) }}" required>
                    <div class="d-flex gap-1 mt-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-chip" onclick="setDate('today')">{{ $isAr ? 'اليوم' : 'Today' }}</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-chip" onclick="setDate('yesterday')">{{ $isAr ? 'أمس' : 'Yesterday' }}</button>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ $isAr ? 'طريقة الدفع' : 'Payment Method' }}</label>
                    <select class="form-select @error('payment_method') is-invalid @enderror" name="payment_method">
                        <option value="">{{ $isAr ? 'اختياري' : 'Optional' }}</option>
                        <option value="cash" @selected(old('payment_method', $finance->payment_method ?? '') === 'cash')>{{ $isAr ? 'نقدي' : 'Cash' }}</option>
                        <option value="transfer" @selected(old('payment_method', $finance->payment_method ?? '') === 'transfer')>{{ $isAr ? 'تحويل بنكي' : 'Transfer' }}</option>
                        <option value="card" @selected(old('payment_method', $finance->payment_method ?? '') === 'card')>{{ $isAr ? 'بطاقة' : 'Card' }}</option>
                        <option value="wallet" @selected(old('payment_method', $finance->payment_method ?? '') === 'wallet')>{{ $isAr ? 'محفظة' : 'Wallet' }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ $isAr ? 'الأثر على الصافي' : 'Net Impact' }}</label>
                    <div id="impactBox" class="form-control bg-light fw-bold">-</div>
                    <small class="text-muted">{{ $isAr ? 'يُحسب تلقائيًا حسب النوع والمبلغ' : 'Auto-calculated from type and amount' }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card finance-panel mb-3">
        <div class="card-header">{{ $isAr ? 'ملاحظات إضافية' : 'Additional Notes' }}</div>
        <div class="card-body">
            <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" rows="5" placeholder="{{ $isAr ? 'أي تفاصيل إضافية عن القيد...' : 'Any extra details for this entry...' }}">{{ old('notes', $finance->notes ?? '') }}</textarea>
            <div class="alert alert-info mt-3 mb-0">
                {{ $isAr ? 'ملاحظة: اختيار "فاتورة داخلة" يضبط النوع تلقائيًا إلى إيراد، و"فاتورة خارجة/مصروف" يضبطه إلى مصروف.' : 'Note: "Incoming invoice" auto-sets type to income, and "Outgoing/Expense" auto-sets type to expense.' }}
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center gap-2">
        <a class="btn btn-outline-secondary" href="{{ route('admin.finance.index', app()->getLocale()) }}">{{ $isAr ? 'إلغاء' : 'Cancel' }}</a>
        <button class="btn btn-success px-4">{{ $isEdit ? ($isAr ? 'تحديث القيد' : 'Update Entry') : ($isAr ? 'حفظ القيد' : 'Save Entry') }}</button>
    </div>
</form>

<script>
    function generateInvoiceNumber() {
        var now = new Date();
        var pad = function (n) { return String(n).padStart(2, '0'); };
        var serial = now.getFullYear() + pad(now.getMonth() + 1) + pad(now.getDate()) + '-' + pad(now.getHours()) + pad(now.getMinutes()) + pad(now.getSeconds());
        var prefix = document.getElementById('entry_type').value === 'income' ? 'IN' : 'OUT';
        document.getElementById('invoice_number').value = prefix + '-' + serial;
    }

    function syncEntryTypeWithKind() {
        var kind = document.getElementById('entry_kind').value;
        var typeSelect = document.getElementById('entry_type');
        if (kind === 'incoming_invoice') typeSelect.value = 'income';
        if (kind === 'outgoing_invoice' || kind === 'expense') typeSelect.value = 'expense';
        updateImpact();
    }

    function setQuickAmount(value) {
        document.getElementById('amount').value = value;
        updateImpact();
    }

    function setDate(mode) {
        var date = new Date();
        if (mode === 'yesterday') {
            date.setDate(date.getDate() - 1);
        }
        var y = date.getFullYear();
        var m = String(date.getMonth() + 1).padStart(2, '0');
        var d = String(date.getDate()).padStart(2, '0');
        document.getElementById('entry_date').value = y + '-' + m + '-' + d;
    }

    function updateImpact() {
        var type = document.getElementById('entry_type').value;
        var amount = parseFloat(document.getElementById('amount').value || '0');
        var box = document.getElementById('impactBox');
        var sign = type === 'income' ? '+' : '-';
        box.textContent = sign + ' ' + amount.toFixed(2);
        box.classList.remove('text-success', 'text-danger');
        box.classList.add(type === 'income' ? 'text-success' : 'text-danger');
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('entry_kind').addEventListener('change', syncEntryTypeWithKind);
        document.getElementById('entry_type').addEventListener('change', updateImpact);
        document.getElementById('amount').addEventListener('input', updateImpact);
        updateImpact();
    });
</script>
