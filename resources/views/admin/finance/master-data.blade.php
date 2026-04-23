@extends('layouts.admin')

@section('content')
@php
    $isAr = app()->getLocale() === 'ar';
    $partyTypeLabels = [
        'customer' => $isAr ? 'عميل' : 'Customer',
        'supplier' => $isAr ? 'مورد' : 'Supplier',
        'both' => $isAr ? 'عميل ومورد' : 'Customer & Supplier',
    ];
@endphp

<style>
    .master-grid{display:grid;gap:1rem}
    .master-grid .hero-card,.master-grid .panel-card{border:1px solid #d7ebe4;border-radius:1.6rem;background:linear-gradient(180deg,rgba(255,255,255,.98) 0%,rgba(245,252,249,.96) 100%);box-shadow:0 18px 38px rgba(16,82,92,.08);padding:1rem 1.1rem}
    .master-grid .hero-row,.master-grid .panel-head{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap}
    .master-grid .hero-kicker{display:inline-flex;align-items:center;gap:.35rem;padding:.35rem .8rem;border-radius:999px;background:#ebfaf5;border:1px solid #d6ece5;color:#13656b;font-size:.78rem;font-weight:800}
    .master-grid .hero-title{margin:.55rem 0 .3rem;color:#18485d;font-size:clamp(1.7rem,1.35rem + .7vw,2.3rem);font-weight:900}
    .master-grid .hero-copy,.master-grid .panel-subtitle{margin:0;color:#69808b;line-height:1.8}
    .master-grid .workspace-grid{display:grid;grid-template-columns:minmax(0,1.55fr) minmax(340px,.95fr);gap:1rem;align-items:start}
    .master-grid .stack{display:grid;gap:1rem}
    .master-grid .panel-title{margin:0;color:#18485d;font-size:1.14rem;font-weight:850}
    .master-grid .table-shell{border:1px solid #deede7;border-radius:1.15rem;overflow:hidden;background:#fff}
    .master-grid .table thead th{background:#edf9f4;color:#285a66;font-size:.82rem;font-weight:800;border-bottom:1px solid #d8ebe4}
    .master-grid .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.8rem}
    .master-grid .full{grid-column:1/-1}
    .master-grid .form-label{display:block;margin-bottom:.35rem;color:#476471;font-size:.84rem;font-weight:800}
    .master-grid .type-badge{display:inline-flex;align-items:center;padding:.28rem .62rem;border-radius:999px;background:#ebfaf5;color:#14666c;font-size:.74rem;font-weight:800}
    .master-grid .sticky-side{position:sticky;top:1rem}
    @media (max-width:1199.98px){.master-grid .workspace-grid{grid-template-columns:1fr}.master-grid .sticky-side{position:static}}
    @media (max-width:767.98px){.master-grid .form-grid{grid-template-columns:1fr}}
</style>

<div class="master-grid">
    <section class="hero-card">
        <div class="hero-row">
            <div>
                <span class="hero-kicker"><i class="bi bi-diagram-3"></i> {{ $isAr ? 'تعريفات النظام المالية' : 'Finance references' }}</span>
                <h1 class="hero-title">{{ $isAr ? 'البيانات المرجعية' : 'Master Data' }}</h1>
                <p class="hero-copy">{{ $isAr ? 'تنظيم مراكز التكلفة والعملاء والموردين في شاشة أوضح وأسهل للإدارة اليومية.' : 'Keep cost centers and parties in a cleaner operational workspace.' }}</p>
            </div>
            <div>
                <a class="btn btn-outline-secondary" href="{{ route('admin.finance.index', app()->getLocale()) }}">{{ $isAr ? 'العودة للمالية' : 'Back to finance' }}</a>
            </div>
        </div>
    </section>

    <section class="workspace-grid">
        <div class="stack">
            <div class="panel-card">
                <div class="panel-head mb-3">
                    <div>
                        <h2 class="panel-title">{{ $isAr ? 'مراكز التكلفة الحالية' : 'Cost centers' }}</h2>
                        <p class="panel-subtitle">{{ $isAr ? 'عرض مضغوط لأكواد المراكز وربطها بالفروع.' : 'Compact list of cost center codes and branch links.' }}</p>
                    </div>
                </div>
                <div class="table-shell">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead><tr><th>{{ $isAr ? 'الكود' : 'Code' }}</th><th>{{ $isAr ? 'الاسم' : 'Name' }}</th><th>{{ $isAr ? 'الفرع' : 'Branch' }}</th></tr></thead>
                            <tbody>
                                @forelse($costCenters as $center)
                                    <tr><td><strong>{{ $center->code }}</strong></td><td>{{ $center->name_ar ?: $center->name_en }}</td><td>{{ $center->branch?->name ?? ($isAr ? 'بدون ربط مباشر' : 'No direct branch') }}</td></tr>
                                @empty
                                    <tr><td colspan="3" class="text-center py-5 text-muted">{{ $isAr ? 'لا توجد مراكز تكلفة حالياً.' : 'No cost centers available.' }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="panel-card">
                <div class="panel-head mb-3">
                    <div>
                        <h2 class="panel-title">{{ $isAr ? 'العملاء والموردون' : 'Customers & suppliers' }}</h2>
                        <p class="panel-subtitle">{{ $isAr ? 'قائمة أوضح للأطراف مع النوع وبيانات التواصل.' : 'Clearer list of parties with type and contact details.' }}</p>
                    </div>
                </div>
                <div class="table-shell">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead><tr><th>{{ $isAr ? 'الاسم' : 'Name' }}</th><th>{{ $isAr ? 'النوع' : 'Type' }}</th><th>{{ $isAr ? 'التواصل' : 'Contact' }}</th><th>{{ $isAr ? 'الرصيد الافتتاحي' : 'Opening balance' }}</th></tr></thead>
                            <tbody>
                                @forelse($parties as $party)
                                    <tr>
                                        <td><strong>{{ $party->name }}</strong><div><small>{{ $party->email ?: ($isAr ? 'بدون بريد' : 'No email') }}</small></div></td>
                                        <td><span class="type-badge">{{ $partyTypeLabels[$party->party_type] ?? $party->party_type }}</span></td>
                                        <td>{{ $party->phone ?: ($isAr ? 'غير متوفر' : 'N/A') }}</td>
                                        <td>{{ number_format((float) $party->opening_balance, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-5 text-muted">{{ $isAr ? 'لا توجد أطراف مضافة حتى الآن.' : 'No parties added yet.' }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-3">{{ $parties->links() }}</div>
            </div>
        </div>

        <div class="stack sticky-side">
            <div class="panel-card">
                <div class="panel-head mb-3">
                    <div>
                        <h2 class="panel-title">{{ $isAr ? 'إضافة مركز تكلفة' : 'Add cost center' }}</h2>
                        <p class="panel-subtitle">{{ $isAr ? 'نموذج مرتب لتعريف مركز جديد وربطه بفرع إن لزم.' : 'Create a new center and optionally link it to a branch.' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.finance.cost-centers.store', app()->getLocale()) }}" class="form-grid">
                    @csrf
                    <div class="full"><label class="form-label">{{ $isAr ? 'الفرع' : 'Branch' }}</label><select class="form-select" name="branch_id"><option value="">{{ $isAr ? 'بدون ربط مباشر' : 'No direct branch' }}</option>@foreach($branches as $branch)<option value="{{ $branch->id }}" @selected((string) old('branch_id') === (string) $branch->id)>{{ $branch->name }}</option>@endforeach</select></div>
                    <div><label class="form-label">{{ $isAr ? 'الكود' : 'Code' }}</label><input class="form-control" type="text" name="code" value="{{ old('code') }}" required></div>
                    <div><label class="form-label">{{ $isAr ? 'الاسم العربي' : 'Arabic name' }}</label><input class="form-control" type="text" name="name_ar" value="{{ old('name_ar') }}" required></div>
                    <div class="full"><label class="form-label">{{ $isAr ? 'الاسم الإنجليزي' : 'English name' }}</label><input class="form-control" type="text" name="name_en" value="{{ old('name_en') }}" required></div>
                    <div class="full"><label class="form-label">{{ $isAr ? 'ملاحظات' : 'Notes' }}</label><textarea class="form-control" rows="3" name="notes">{{ old('notes') }}</textarea></div>
                    <div class="full d-grid"><button class="btn btn-primary">{{ $isAr ? 'حفظ مركز التكلفة' : 'Save cost center' }}</button></div>
                </form>
            </div>

            <div class="panel-card">
                <div class="panel-head mb-3">
                    <div>
                        <h2 class="panel-title">{{ $isAr ? 'إضافة عميل أو مورد' : 'Add party' }}</h2>
                        <p class="panel-subtitle">{{ $isAr ? 'تعريف الطرف المالي ببياناته الأساسية بشكل أوضح.' : 'Create a customer or supplier with cleaner data grouping.' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.finance.parties.store', app()->getLocale()) }}" class="form-grid">
                    @csrf
                    <div><label class="form-label">{{ $isAr ? 'النوع' : 'Type' }}</label><select class="form-select" name="party_type" required><option value="customer" @selected(old('party_type', 'customer') === 'customer')>{{ $isAr ? 'عميل' : 'Customer' }}</option><option value="supplier" @selected(old('party_type') === 'supplier')>{{ $isAr ? 'مورد' : 'Supplier' }}</option><option value="both" @selected(old('party_type') === 'both')>{{ $isAr ? 'عميل ومورد' : 'Customer & Supplier' }}</option></select></div>
                    <div><label class="form-label">{{ $isAr ? 'الرصيد الافتتاحي' : 'Opening balance' }}</label><input class="form-control" type="number" step="0.01" name="opening_balance" value="{{ old('opening_balance', 0) }}"></div>
                    <div class="full"><label class="form-label">{{ $isAr ? 'الاسم' : 'Name' }}</label><input class="form-control" type="text" name="name" value="{{ old('name') }}" required></div>
                    <div><label class="form-label">{{ $isAr ? 'الهاتف' : 'Phone' }}</label><input class="form-control" type="text" name="phone" value="{{ old('phone') }}"></div>
                    <div><label class="form-label">{{ $isAr ? 'الرقم الضريبي' : 'Tax number' }}</label><input class="form-control" type="text" name="tax_number" value="{{ old('tax_number') }}"></div>
                    <div class="full"><label class="form-label">{{ $isAr ? 'البريد الإلكتروني' : 'Email' }}</label><input class="form-control" type="email" name="email" value="{{ old('email') }}"></div>
                    <div class="full"><label class="form-label">{{ $isAr ? 'العنوان' : 'Address' }}</label><input class="form-control" type="text" name="address" value="{{ old('address') }}"></div>
                    <div class="full"><label class="form-label">{{ $isAr ? 'ملاحظات' : 'Notes' }}</label><textarea class="form-control" rows="3" name="notes">{{ old('notes') }}</textarea></div>
                    <div class="full d-grid"><button class="btn btn-primary">{{ $isAr ? 'حفظ الطرف' : 'Save party' }}</button></div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
