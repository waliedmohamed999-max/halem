@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ $isAr ? 'تفاصيل القيد المالي' : 'Finance Entry Details' }} #{{ $finance->id }}</h4>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-dark" href="{{ route('admin.finance.accounting', app()->getLocale()) }}">{{ $isAr ? 'المحاسبة العامة' : 'General Accounting' }}</a>
        <a class="btn btn-outline-secondary" href="{{ route('admin.finance.index', app()->getLocale()) }}">{{ $isAr ? 'عودة' : 'Back' }}</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4"><div class="card card-body"><strong>{{ $isAr ? 'نوع الحركة' : 'Type' }}</strong><div>{{ $finance->entry_type }}</div></div></div>
    <div class="col-md-4"><div class="card card-body"><strong>{{ $isAr ? 'البند' : 'Kind' }}</strong><div>{{ $finance->entry_kind }}</div></div></div>
    <div class="col-md-4"><div class="card card-body"><strong>{{ $isAr ? 'الحالة' : 'Status' }}</strong><div>{{ $finance->record_status }}</div></div></div>
    <div class="col-md-6"><div class="card card-body"><strong>{{ $isAr ? 'العنوان' : 'Title' }}</strong><div>{{ $finance->title }}</div></div></div>
    <div class="col-md-3"><div class="card card-body"><strong>{{ $isAr ? 'المبلغ' : 'Amount' }}</strong><div>{{ number_format((float) $finance->amount, 2) }}</div></div></div>
    <div class="col-md-3"><div class="card card-body"><strong>{{ $isAr ? 'التاريخ' : 'Date' }}</strong><div>{{ optional($finance->entry_date)->format('Y-m-d') }}</div></div></div>
    <div class="col-md-4"><div class="card card-body"><strong>{{ $isAr ? 'الفرع' : 'Branch' }}</strong><div>{{ $finance->branch?->name ?? '-' }}</div></div></div>
    <div class="col-md-4"><div class="card card-body"><strong>{{ $isAr ? 'رقم الفاتورة' : 'Invoice Number' }}</strong><div>{{ $finance->invoice_number ?: '-' }}</div></div></div>
    <div class="col-md-4"><div class="card card-body"><strong>{{ $isAr ? 'الجهة' : 'Counterparty' }}</strong><div>{{ $finance->counterparty ?: '-' }}</div></div></div>
    <div class="col-md-6"><div class="card card-body"><strong>{{ $isAr ? 'طريقة الدفع' : 'Payment Method' }}</strong><div>{{ $finance->payment_method ?: '-' }}</div></div></div>
    <div class="col-md-6"><div class="card card-body"><strong>{{ $isAr ? 'أدخل بواسطة' : 'Created by' }}</strong><div>{{ $finance->creator?->name ?? '-' }}</div></div></div>
    @if($finance->appointment_id)
        <div class="col-12">
            <div class="alert alert-info mb-0">
                {{ $isAr ? 'هذا القيد مرتبط بالحجز رقم' : 'This entry is linked to appointment #' }}
                <a href="{{ route('admin.appointments.show', [app()->getLocale(), $finance->appointment_id]) }}">#{{ $finance->appointment_id }}</a>
            </div>
        </div>
    @endif
    <div class="col-12"><div class="card card-body"><strong>{{ $isAr ? 'الملاحظات' : 'Notes' }}</strong><div style="white-space:pre-line;">{{ $finance->notes ?: '-' }}</div></div></div>
    @if($finance->journal)
        <div class="col-12">
            <div class="card card-body">
                <strong class="mb-3">{{ $isAr ? 'القيد المحاسبي المرتبط' : 'Linked Accounting Journal' }}</strong>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ $isAr ? 'رقم القيد' : 'Journal No.' }}</th>
                                <th>{{ $isAr ? 'الحساب' : 'Account' }}</th>
                                <th>{{ $isAr ? 'مدين' : 'Debit' }}</th>
                                <th>{{ $isAr ? 'دائن' : 'Credit' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($finance->journal->lines as $line)
                                <tr>
                                    <td>{{ $finance->journal->journal_no }}</td>
                                    <td>{{ $line->account?->code }} - {{ $line->account?->name }}</td>
                                    <td>{{ number_format((float) $line->debit, 2) }}</td>
                                    <td>{{ number_format((float) $line->credit, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
