@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h4 class="mb-0">{{ $isAr ? 'الخزنة' : 'Cashbox' }}</h4>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-dark" href="{{ route('admin.finance.accounting', app()->getLocale()) }}">{{ $isAr ? 'المحاسبة العامة' : 'General Accounting' }}</a>
        <a class="btn btn-outline-secondary" href="{{ route('admin.finance.index', app()->getLocale()) }}">{{ $isAr ? 'العودة للمالية' : 'Back to Finance' }}</a>
    </div>
</div>

<div class="row g-2 mb-3">
    <div class="col-md-3"><div class="card card-body"><small>{{ $isAr ? 'رصيد الخزنة الحالي' : 'Current Cash Balance' }}</small><strong class="{{ $balance >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($balance, 2) }}</strong></div></div>
    <div class="col-md-3"><div class="card card-body"><small>{{ $isAr ? 'إجمالي قبض نقدي' : 'Total Cash In' }}</small><strong class="text-success">{{ number_format($cashIn, 2) }}</strong></div></div>
    <div class="col-md-3"><div class="card card-body"><small>{{ $isAr ? 'إجمالي صرف نقدي' : 'Total Cash Out' }}</small><strong class="text-danger">{{ number_format($cashOut, 2) }}</strong></div></div>
    <div class="col-md-3"><div class="card card-body"><small>{{ $isAr ? 'صافي اليوم' : 'Today Net' }}</small><strong>{{ number_format($todayIn - $todayOut, 2) }}</strong></div></div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6>{{ $isAr ? 'إضافة حركة قبض/صرف' : 'Add Cash Movement' }}</h6>
                <form method="POST" action="{{ route('admin.finance.cashbox.store', app()->getLocale()) }}">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">{{ $isAr ? 'نوع الحركة' : 'Movement Type' }}</label>
                            <select class="form-select" name="movement_type" required>
                                <option value="in">{{ $isAr ? 'قبض' : 'Cash In' }}</option>
                                <option value="out">{{ $isAr ? 'صرف' : 'Cash Out' }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ $isAr ? 'المبلغ' : 'Amount' }}</label>
                            <input type="number" step="0.01" min="0.01" class="form-control" name="amount" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">{{ $isAr ? 'العنوان' : 'Title' }}</label>
                            <input class="form-control" name="title" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ $isAr ? 'التاريخ' : 'Date' }}</label>
                            <input type="date" class="form-control" name="entry_date" value="{{ now()->toDateString() }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ $isAr ? 'الفرع' : 'Branch' }}</label>
                            <select class="form-select" name="branch_id">
                                <option value="">{{ $isAr ? 'بدون' : 'None' }}</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">{{ $isAr ? 'الجهة' : 'Counterparty' }}</label>
                            <input class="form-control" name="counterparty">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">{{ $isAr ? 'ملاحظات' : 'Notes' }}</label>
                            <textarea class="form-control" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <button class="btn btn-success mt-3">{{ $isAr ? 'حفظ الحركة' : 'Save Movement' }}</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6>{{ $isAr ? 'آخر حركات الخزنة' : 'Recent Cash Movements' }}</h6>
                <div class="table-responsive" style="max-height:420px;">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ $isAr ? 'تاريخ' : 'Date' }}</th>
                                <th>{{ $isAr ? 'عنوان' : 'Title' }}</th>
                                <th>{{ $isAr ? 'نوع' : 'Type' }}</th>
                                <th>{{ $isAr ? 'مبلغ' : 'Amount' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent as $item)
                                <tr>
                                    <td>{{ optional($item->entry_date)->format('Y-m-d') }}</td>
                                    <td>{{ $item->title }}</td>
                                    <td>{{ $item->entry_type }}</td>
                                    <td class="{{ $item->entry_type === 'income' ? 'text-success' : 'text-danger' }}">
                                        {{ $item->entry_type === 'income' ? '+' : '-' }} {{ number_format((float) $item->amount, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted text-center">{{ $isAr ? 'لا توجد حركات' : 'No movements yet' }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
