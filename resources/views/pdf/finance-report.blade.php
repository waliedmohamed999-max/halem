<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Finance Report</title>
    <style>
        @page { margin: 18px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .head { margin-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; }
        .meta { color: #444; margin-top: 4px; }
        .cards { margin: 10px 0 14px 0; width: 100%; border-collapse: collapse; }
        .cards td { border: 1px solid #d8d8d8; padding: 8px; width: 33.33%; }
        .cards .v { font-size: 14px; font-weight: bold; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; page-break-inside: auto; }
        tr { page-break-inside: avoid; page-break-after: auto; }
        thead { display: table-header-group; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; vertical-align: top; }
        th { background: #f5f5f5; }
        .income { color: #0a7e2d; font-weight: bold; }
        .expense { color: #b42318; font-weight: bold; }
    </style>
</head>
<body>
<div class="head">
    <div class="title">Finance Report - {{ $period === 'month' ? 'Monthly' : 'Daily' }}</div>
    <div class="meta">Generated at: {{ $generatedAt->format('Y-m-d H:i') }}</div>
    <div class="meta">Entries: {{ $entries->count() }}</div>
</div>

<table class="cards">
    <tr>
        <td>
            <div>Total Income</div>
            <div class="v income">{{ number_format($income, 2) }}</div>
        </td>
        <td>
            <div>Total Expense</div>
            <div class="v expense">{{ number_format($expense, 2) }}</div>
        </td>
        <td>
            <div>Net</div>
            <div class="v">{{ number_format($net, 2) }}</div>
        </td>
    </tr>
</table>

<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Date</th>
        <th>Type</th>
        <th>Kind</th>
        <th>Title</th>
        <th>Invoice</th>
        <th>Branch</th>
        <th>Amount</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    @foreach($entries as $entry)
        <tr>
            <td>{{ $entry->id }}</td>
            <td>{{ optional($entry->entry_date)->format('Y-m-d') }}</td>
            <td>{{ $entry->entry_type }}</td>
            <td>{{ $entry->entry_kind }}</td>
            <td>{{ $entry->title }}</td>
            <td>{{ $entry->invoice_number ?: '-' }}</td>
            <td>{{ $entry->branch?->name ?: '-' }}</td>
            <td class="{{ $entry->entry_type === 'income' ? 'income' : 'expense' }}">
                {{ $entry->entry_type === 'income' ? '+' : '-' }} {{ number_format((float) $entry->amount, 2) }}
            </td>
            <td>{{ $entry->record_status }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
