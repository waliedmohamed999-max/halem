@extends('layouts.admin')

@section('content')
@php
    $isAr = app()->getLocale() === 'ar';
    $categoryLabels = [
        'tool' => $isAr ? 'أداة' : 'Tool',
        'supply' => $isAr ? 'مستهلك' : 'Supply',
        'medicine' => $isAr ? 'دواء' : 'Medicine',
        'other' => $isAr ? 'أخرى' : 'Other',
    ];
    $movementLabels = [
        'receipt' => $isAr ? 'إضافة مخزنية' : 'Receipt',
        'issue' => $isAr ? 'صرف' : 'Issue',
        'adjustment_in' => $isAr ? 'تسوية بالزيادة' : 'Adjustment In',
        'adjustment_out' => $isAr ? 'تسوية بالنقص' : 'Adjustment Out',
    ];
@endphp

<style>
    .inventory-workspace{display:grid;gap:1rem}
    .inventory-workspace .hero-card,.inventory-workspace .panel-card{border:1px solid #d7ebe4;border-radius:1.6rem;background:linear-gradient(180deg,rgba(255,255,255,.98) 0%,rgba(245,252,249,.96) 100%);box-shadow:0 18px 38px rgba(16,82,92,.08);padding:1rem 1.1rem}
    .inventory-workspace .hero-row,.inventory-workspace .panel-head{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap}
    .inventory-workspace .hero-kicker{display:inline-flex;align-items:center;gap:.35rem;padding:.35rem .8rem;border-radius:999px;background:#ebfaf5;border:1px solid #d6ece5;color:#13656b;font-size:.78rem;font-weight:800}
    .inventory-workspace .hero-title{margin:.55rem 0 .3rem;color:#18485d;font-size:clamp(1.7rem,1.35rem + .7vw,2.3rem);font-weight:900}
    .inventory-workspace .hero-copy,.inventory-workspace .panel-subtitle{margin:0;color:#69808b;line-height:1.8}
    .inventory-workspace .stats-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.85rem;margin-top:1rem}
    .inventory-workspace .stat-card{padding:.95rem 1rem;border:1px solid #d7ebe4;border-radius:1.1rem;background:rgba(255,255,255,.9)}
    .inventory-workspace .stat-card small{display:block;color:#68808b;font-weight:700;margin-bottom:.25rem}
    .inventory-workspace .stat-card strong{display:block;color:#155f69;font-size:1.55rem;line-height:1;font-weight:900}
    .inventory-workspace .workspace-grid{display:grid;grid-template-columns:minmax(0,1.6fr) minmax(340px,.95fr);gap:1rem;align-items:start}
    .inventory-workspace .stack{display:grid;gap:1rem}
    .inventory-workspace .panel-title{margin:0;color:#18485d;font-size:1.14rem;font-weight:850}
    .inventory-workspace .table-shell{border:1px solid #deede7;border-radius:1.15rem;overflow:hidden;background:#fff}
    .inventory-workspace .table thead th{background:#edf9f4;color:#285a66;font-size:.82rem;font-weight:800;border-bottom:1px solid #d8ebe4}
    .inventory-workspace .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.8rem}
    .inventory-workspace .full{grid-column:1/-1}
    .inventory-workspace .form-label{display:block;margin-bottom:.35rem;color:#476471;font-size:.84rem;font-weight:800}
    .inventory-workspace .chip{display:inline-flex;align-items:center;padding:.28rem .62rem;border-radius:999px;background:#edf9f4;color:#15656b;font-size:.74rem;font-weight:800}
    .inventory-workspace .sticky-side{position:sticky;top:1rem}
    @media (max-width:1199.98px){.inventory-workspace .workspace-grid{grid-template-columns:1fr}.inventory-workspace .sticky-side{position:static}}
    @media (max-width:767.98px){.inventory-workspace .stats-grid,.inventory-workspace .form-grid{grid-template-columns:1fr}}
</style>

<div class="inventory-workspace">
    <section class="hero-card">
        <div class="hero-row">
            <div>
                <span class="hero-kicker"><i class="bi bi-box-seam"></i> {{ $isAr ? 'التخزين والحركة' : 'Warehousing' }}</span>
                <h1 class="hero-title">{{ $isAr ? 'المخزون والمستودع' : 'Inventory & Warehouse' }}</h1>
                <p class="hero-copy">{{ $isAr ? 'واجهة أوضح لإدارة المستودعات والأصناف والحركات المخزنية بدون تمدد عشوائي في الصفحة.' : 'A clearer layout for warehouses, items, and stock movement operations.' }}</p>
            </div>
            <div>
                <a class="btn btn-outline-secondary" href="{{ route('admin.finance.reports', app()->getLocale()) }}">{{ $isAr ? 'التقارير' : 'Reports' }}</a>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card"><small>{{ $isAr ? 'عدد المستودعات' : 'Warehouses' }}</small><strong>{{ number_format((int) ($summary['warehouses'] ?? 0)) }}</strong></div>
            <div class="stat-card"><small>{{ $isAr ? 'عدد الأصناف' : 'Items' }}</small><strong>{{ number_format((int) ($summary['items'] ?? 0)) }}</strong></div>
            <div class="stat-card"><small>{{ $isAr ? 'أصناف منخفضة' : 'Low stock' }}</small><strong>{{ number_format((int) ($summary['low_stock'] ?? 0)) }}</strong></div>
            <div class="stat-card"><small>{{ $isAr ? 'القيمة التقديرية' : 'Estimated stock value' }}</small><strong>{{ number_format((float) ($summary['stock_value'] ?? 0), 2) }}</strong></div>
        </div>
    </section>

    <section class="workspace-grid">
        <div class="stack">
            <div class="panel-card">
                <div class="panel-head mb-3">
                    <div>
                        <h2 class="panel-title">{{ $isAr ? 'المستودعات' : 'Warehouses' }}</h2>
                        <p class="panel-subtitle">{{ $isAr ? 'قائمة سريعة للمستودعات والفروع المرتبطة بها.' : 'Clean warehouse list with branch assignment.' }}</p>
                    </div>
                </div>
                <div class="table-shell">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead><tr><th>{{ $isAr ? 'الكود' : 'Code' }}</th><th>{{ $isAr ? 'الاسم' : 'Name' }}</th><th>{{ $isAr ? 'الفرع' : 'Branch' }}</th></tr></thead>
                            <tbody>
                                @forelse($warehouses as $warehouse)
                                    <tr><td><strong>{{ $warehouse->code }}</strong></td><td>{{ $warehouse->name_ar ?: $warehouse->name_en }}</td><td>{{ $warehouse->branch?->name ?? ($isAr ? 'بدون فرع' : 'No branch') }}</td></tr>
                                @empty
                                    <tr><td colspan="3" class="text-center py-5 text-muted">{{ $isAr ? 'لا توجد مستودعات مسجلة.' : 'No warehouses available.' }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="panel-card">
                <div class="panel-head mb-3">
                    <div>
                        <h2 class="panel-title">{{ $isAr ? 'الأصناف' : 'Inventory items' }}</h2>
                        <p class="panel-subtitle">{{ $isAr ? 'عرض أوضح للأصناف مع التصنيف والمخزون الحالي.' : 'Item list with category, current stock, and average cost.' }}</p>
                    </div>
                </div>
                <div class="table-shell">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead><tr><th>{{ $isAr ? 'الصنف' : 'Item' }}</th><th>{{ $isAr ? 'المستودع' : 'Warehouse' }}</th><th>{{ $isAr ? 'التصنيف' : 'Category' }}</th><th>{{ $isAr ? 'الرصيد' : 'Stock' }}</th><th>{{ $isAr ? 'المتوسط' : 'Avg. cost' }}</th></tr></thead>
                            <tbody>
                                @forelse($items as $item)
                                    <tr>
                                        <td><strong>{{ $item->name_ar ?: $item->name_en }}</strong><div><small>{{ $item->item_code }}</small></div></td>
                                        <td>{{ $item->warehouse?->name_ar ?? $item->warehouse?->name_en ?? '-' }}</td>
                                        <td><span class="chip">{{ $categoryLabels[$item->category] ?? $item->category }}</span></td>
                                        <td>{{ number_format((float) $item->current_stock, 2) }} {{ $item->unit }}</td>
                                        <td>{{ number_format((float) $item->average_cost, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-5 text-muted">{{ $isAr ? 'لا توجد أصناف مخزنية.' : 'No inventory items found.' }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-3">{{ $items->links() }}</div>
            </div>

            <div class="panel-card">
                <div class="panel-head mb-3">
                    <div>
                        <h2 class="panel-title">{{ $isAr ? 'الحركات المخزنية' : 'Stock movements' }}</h2>
                        <p class="panel-subtitle">{{ $isAr ? 'آخر الحركات مع النوع والتاريخ والتكلفة الإجمالية.' : 'Recent inventory movements with date and total cost.' }}</p>
                    </div>
                </div>
                <div class="table-shell">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead><tr><th>{{ $isAr ? 'الصنف' : 'Item' }}</th><th>{{ $isAr ? 'النوع' : 'Type' }}</th><th>{{ $isAr ? 'الكمية' : 'Qty' }}</th><th>{{ $isAr ? 'الفرع' : 'Branch' }}</th><th>{{ $isAr ? 'التاريخ' : 'Date' }}</th></tr></thead>
                            <tbody>
                                @forelse($movements as $movement)
                                    <tr>
                                        <td><strong>{{ $movement->item?->name_ar ?? $movement->item?->name_en ?? '-' }}</strong><div><small>{{ $movement->warehouse?->name_ar ?? $movement->warehouse?->name_en ?? '-' }}</small></div></td>
                                        <td><span class="chip">{{ $movementLabels[$movement->movement_type] ?? $movement->movement_type }}</span></td>
                                        <td>{{ number_format((float) $movement->quantity, 2) }}</td>
                                        <td>{{ $movement->branch?->name ?? ($isAr ? 'غير محدد' : 'N/A') }}</td>
                                        <td>{{ optional($movement->movement_date)->format('Y-m-d') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-5 text-muted">{{ $isAr ? 'لا توجد حركات مخزنية حالياً.' : 'No stock movements yet.' }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-3">{{ $movements->links() }}</div>
            </div>
        </div>

        <div class="stack sticky-side">
            <div class="panel-card">
                <div class="panel-head mb-3">
                    <div>
                        <h2 class="panel-title">{{ $isAr ? 'إضافة مستودع' : 'Add warehouse' }}</h2>
                        <p class="panel-subtitle">{{ $isAr ? 'تعريف مستودع جديد بشكل مختصر وواضح.' : 'Create a warehouse with a compact side form.' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.finance.inventory.warehouses.store', app()->getLocale()) }}" class="form-grid">
                    @csrf
                    <div class="full"><label class="form-label">{{ $isAr ? 'الفرع' : 'Branch' }}</label><select class="form-select" name="branch_id"><option value="">{{ $isAr ? 'بدون فرع' : 'No branch' }}</option>@foreach($branches as $branch)<option value="{{ $branch->id }}" @selected((string) old('branch_id') === (string) $branch->id)>{{ $branch->name }}</option>@endforeach</select></div>
                    <div><label class="form-label">{{ $isAr ? 'الكود' : 'Code' }}</label><input class="form-control" type="text" name="code" value="{{ old('code') }}" required></div>
                    <div><label class="form-label">{{ $isAr ? 'الاسم العربي' : 'Arabic name' }}</label><input class="form-control" type="text" name="name_ar" value="{{ old('name_ar') }}" required></div>
                    <div class="full"><label class="form-label">{{ $isAr ? 'الاسم الإنجليزي' : 'English name' }}</label><input class="form-control" type="text" name="name_en" value="{{ old('name_en') }}" required></div>
                    <div class="full"><label class="form-label">{{ $isAr ? 'ملاحظات' : 'Notes' }}</label><textarea class="form-control" rows="3" name="notes">{{ old('notes') }}</textarea></div>
                    <div class="full d-grid"><button class="btn btn-primary">{{ $isAr ? 'حفظ المستودع' : 'Save warehouse' }}</button></div>
                </form>
            </div>

            <div class="panel-card">
                <div class="panel-head mb-3">
                    <div>
                        <h2 class="panel-title">{{ $isAr ? 'إضافة صنف' : 'Add item' }}</h2>
                        <p class="panel-subtitle">{{ $isAr ? 'تعريف صنف جديد وربطه بالمستودع والتصنيف.' : 'Create an inventory item with warehouse and category.' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.finance.inventory.items.store', app()->getLocale()) }}" class="form-grid">
                    @csrf
                    <div class="full"><label class="form-label">{{ $isAr ? 'المستودع' : 'Warehouse' }}</label><select class="form-select" name="warehouse_id"><option value="">{{ $isAr ? 'اختياري' : 'Optional' }}</option>@foreach($warehouses as $warehouse)<option value="{{ $warehouse->id }}" @selected((string) old('warehouse_id') === (string) $warehouse->id)>{{ $warehouse->code }} - {{ $warehouse->name_ar ?: $warehouse->name_en }}</option>@endforeach</select></div>
                    <div><label class="form-label">{{ $isAr ? 'كود الصنف' : 'Item code' }}</label><input class="form-control" type="text" name="item_code" value="{{ old('item_code') }}" required></div>
                    <div><label class="form-label">{{ $isAr ? 'الوحدة' : 'Unit' }}</label><input class="form-control" type="text" name="unit" value="{{ old('unit', 'unit') }}" required></div>
                    <div class="full"><label class="form-label">{{ $isAr ? 'الاسم العربي' : 'Arabic name' }}</label><input class="form-control" type="text" name="name_ar" value="{{ old('name_ar') }}" required></div>
                    <div class="full"><label class="form-label">{{ $isAr ? 'الاسم الإنجليزي' : 'English name' }}</label><input class="form-control" type="text" name="name_en" value="{{ old('name_en') }}" required></div>
                    <div><label class="form-label">{{ $isAr ? 'التصنيف' : 'Category' }}</label><select class="form-select" name="category" required>@foreach($categoryLabels as $value => $label)<option value="{{ $value }}" @selected(old('category', 'supply') === $value)>{{ $label }}</option>@endforeach</select></div>
                    <div><label class="form-label">{{ $isAr ? 'الحد الأدنى' : 'Min stock' }}</label><input class="form-control" type="number" min="0" step="0.01" name="min_stock" value="{{ old('min_stock', 0) }}"></div>
                    <div class="full"><label class="form-label">{{ $isAr ? 'متوسط التكلفة' : 'Average cost' }}</label><input class="form-control" type="number" min="0" step="0.01" name="average_cost" value="{{ old('average_cost', 0) }}"></div>
                    <div class="full"><label class="form-label">{{ $isAr ? 'ملاحظات' : 'Notes' }}</label><textarea class="form-control" rows="3" name="notes">{{ old('notes') }}</textarea></div>
                    <div class="full d-grid"><button class="btn btn-primary">{{ $isAr ? 'حفظ الصنف' : 'Save item' }}</button></div>
                </form>
            </div>

            <div class="panel-card">
                <div class="panel-head mb-3">
                    <div>
                        <h2 class="panel-title">{{ $isAr ? 'تسجيل حركة مخزنية' : 'Post movement' }}</h2>
                        <p class="panel-subtitle">{{ $isAr ? 'تسجيل الإضافة أو الصرف أو التسوية من نفس العمود الجانبي.' : 'Record receipts, issues, and adjustments from one side form.' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.finance.inventory.movements.store', app()->getLocale()) }}" class="form-grid">
                    @csrf
                    <div class="full"><label class="form-label">{{ $isAr ? 'المستودع' : 'Warehouse' }}</label><select class="form-select" name="warehouse_id" required><option value="">{{ $isAr ? 'اختر المستودع' : 'Select warehouse' }}</option>@foreach($warehouses as $warehouse)<option value="{{ $warehouse->id }}" @selected((string) old('warehouse_id') === (string) $warehouse->id)>{{ $warehouse->code }} - {{ $warehouse->name_ar ?: $warehouse->name_en }}</option>@endforeach</select></div>
                    <div class="full"><label class="form-label">{{ $isAr ? 'الصنف' : 'Item' }}</label><select class="form-select" name="item_id" required><option value="">{{ $isAr ? 'اختر الصنف' : 'Select item' }}</option>@foreach($items as $item)<option value="{{ $item->id }}" @selected((string) old('item_id') === (string) $item->id)>{{ $item->item_code }} - {{ $item->name_ar ?: $item->name_en }}</option>@endforeach</select></div>
                    <div><label class="form-label">{{ $isAr ? 'النوع' : 'Movement type' }}</label><select class="form-select" name="movement_type" required>@foreach($movementLabels as $value => $label)<option value="{{ $value }}" @selected(old('movement_type', 'receipt') === $value)>{{ $label }}</option>@endforeach</select></div>
                    <div><label class="form-label">{{ $isAr ? 'الفرع' : 'Branch' }}</label><select class="form-select" name="branch_id"><option value="">{{ $isAr ? 'اختياري' : 'Optional' }}</option>@foreach($branches as $branch)<option value="{{ $branch->id }}" @selected((string) old('branch_id') === (string) $branch->id)>{{ $branch->name }}</option>@endforeach</select></div>
                    <div><label class="form-label">{{ $isAr ? 'الكمية' : 'Quantity' }}</label><input class="form-control" type="number" min="0.01" step="0.01" name="quantity" value="{{ old('quantity') }}" required></div>
                    <div><label class="form-label">{{ $isAr ? 'تكلفة الوحدة' : 'Unit cost' }}</label><input class="form-control" type="number" min="0" step="0.01" name="unit_cost" value="{{ old('unit_cost', 0) }}"></div>
                    <div class="full"><label class="form-label">{{ $isAr ? 'التاريخ' : 'Date' }}</label><input class="form-control" type="date" name="movement_date" value="{{ old('movement_date', now()->format('Y-m-d')) }}" required></div>
                    <div class="full"><label class="form-label">{{ $isAr ? 'ملاحظات' : 'Notes' }}</label><textarea class="form-control" rows="3" name="notes">{{ old('notes') }}</textarea></div>
                    <div class="full d-grid"><button class="btn btn-primary">{{ $isAr ? 'حفظ الحركة' : 'Save movement' }}</button></div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
