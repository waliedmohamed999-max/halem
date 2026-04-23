<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Patient Report - {{ $patient->full_name }}</title>
    <style>
        body { font-family: Tahoma, Arial, sans-serif; color: #1f2937; margin: 22px; }
        .head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
        .title { font-size: 24px; margin: 0; }
        .muted { color: #6b7280; font-size: 12px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px; }
        .card { border: 1px solid #d1d5db; border-radius: 8px; padding: 10px; }
        .card h4 { margin: 0 0 8px; font-size: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #d1d5db; padding: 8px; vertical-align: top; font-size: 12px; }
        th { background: #f3f4f6; }
        .print-actions { margin-bottom: 12px; }
        .btn { border: 1px solid #2563eb; background: #2563eb; color: #fff; padding: 8px 14px; border-radius: 6px; text-decoration: none; cursor: pointer; }
        @media print {
            .print-actions { display: none; }
            body { margin: 8mm; }
        }
    </style>
</head>
<body>
@php($isAr = app()->getLocale() === 'ar')
<div class="print-actions">
    <button class="btn" onclick="window.print()">{{ $isAr ? 'طباعة' : 'Print' }}</button>
</div>

<div class="head">
    <div>
        <h1 class="title">{{ $isAr ? 'تقرير ملف مريض' : 'Patient Medical Report' }}</h1>
        <div class="muted">{{ $isAr ? 'تاريخ الإنشاء' : 'Generated at' }}: {{ now()->format('Y-m-d H:i') }}</div>
    </div>
    <div><strong>{{ $patient->full_name }}</strong></div>
</div>

<div class="grid">
    <div class="card">
        <h4>{{ $isAr ? 'البيانات الأساسية' : 'Basic Info' }}</h4>
        <div>{{ $isAr ? 'الهاتف' : 'Phone' }}: {{ $patient->phone }}</div>
        <div>{{ $isAr ? 'البريد' : 'Email' }}: {{ $patient->email ?: '-' }}</div>
        <div>{{ $isAr ? 'تاريخ الميلاد' : 'Date of birth' }}: {{ optional($patient->date_of_birth)->format('Y-m-d') ?: '-' }}</div>
        <div>{{ $isAr ? 'النوع' : 'Gender' }}: {{ $patient->gender ?: '-' }}</div>
        <div>{{ $isAr ? 'فصيلة الدم' : 'Blood Type' }}: {{ $patient->blood_type ?: '-' }}</div>
    </div>
    <div class="card">
        <h4>{{ $isAr ? 'التاريخ المرضي' : 'Medical History' }}</h4>
        <div>{{ $isAr ? 'الحساسية' : 'Allergies' }}: {{ $patient->allergies ?: '-' }}</div>
        <div>{{ $isAr ? 'الأمراض المزمنة' : 'Chronic diseases' }}: {{ $patient->chronic_diseases ?: '-' }}</div>
        <div>{{ $isAr ? 'الأدوية الحالية' : 'Current medications' }}: {{ $patient->current_medications ?: '-' }}</div>
        <div>{{ $isAr ? 'ملاحظات عامة' : 'General notes' }}: {{ $patient->notes ?: '-' }}</div>
    </div>
</div>

<table>
    <thead>
    <tr>
        <th>{{ $isAr ? 'التاريخ' : 'Date' }}</th>
        <th>{{ $isAr ? 'الطبيب / الفرع' : 'Doctor / Branch' }}</th>
        <th>{{ $isAr ? 'التشخيص' : 'Diagnosis' }}</th>
        <th>{{ $isAr ? 'الخطة والعلاج' : 'Plan & Procedure' }}</th>
        <th>{{ $isAr ? 'الوصفة' : 'Prescription' }}</th>
        <th>{{ $isAr ? 'مرفقات' : 'Attachments' }}</th>
    </tr>
    </thead>
    <tbody>
    @forelse($patient->visits as $visit)
        <tr>
            <td>{{ optional($visit->visit_date)->format('Y-m-d') }} {{ $visit->visit_time }}</td>
            <td>{{ $visit->doctor?->name ?? '-' }}<br>{{ $visit->branch?->name ?? '-' }}</td>
            <td>{{ $visit->diagnosis ?: '-' }}</td>
            <td>{{ $visit->treatment_plan ?: '-' }}<hr style="border:none;border-top:1px dashed #ddd;">{{ $visit->procedure_done ?: '-' }}</td>
            <td>{{ $visit->prescription ?: '-' }}</td>
            <td>
                @forelse($visit->attachments as $attachment)
                    <div>{{ $attachment->file_name }}</div>
                @empty
                    -
                @endforelse
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" style="text-align:center;color:#6b7280;">{{ $isAr ? 'لا توجد زيارات مسجلة.' : 'No visits found.' }}</td>
        </tr>
    @endforelse
    </tbody>
</table>
</body>
</html>

