<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 18px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        .card { border: 1px solid #d1d5db; border-radius: 10px; padding: 16px; }
        .title { font-size: 20px; font-weight: 700; margin-bottom: 6px; }
        .muted { color: #6b7280; }
        .grid { width: 100%; border-collapse: collapse; margin-top: 10px; page-break-inside: auto; }
        .grid td, .grid th { border: 1px solid #e5e7eb; padding: 6px; text-align: left; }
        .grid th { background: #f3f4f6; }
        .kpi { margin-top: 12px; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px; }
        tr { page-break-inside: avoid; page-break-after: auto; }
        thead { display: table-header-group; }
    </style>
</head>
<body>
<div class="card">
    <div class="title">Dashboard Daily Report</div>
    <div class="muted">Date: {{ $today }}</div>

    <div class="kpi">
        <strong>Today Summary</strong>
        <table class="grid">
            <tr>
                <th>Today appointments</th>
                <td>{{ $stats['appointments_today'] }}</td>
                <th>New</th>
                <td>{{ $stats['appointments_new'] }}</td>
            </tr>
            <tr>
                <th>Completed</th>
                <td>{{ $stats['appointments_completed'] }}</td>
                <th>Canceled</th>
                <td>{{ $stats['appointments_canceled'] }}</td>
            </tr>
            <tr>
                <th>Today booking value</th>
                <td>{{ number_format((float) $stats['today_revenue'], 2) }}</td>
                <th>Unread messages</th>
                <td>{{ $stats['messages_unread'] }}</td>
            </tr>
            <tr>
                <th>Overdue appointments</th>
                <td colspan="3">{{ $lateAppointmentsCount }}</td>
            </tr>
        </table>
    </div>

    <div class="kpi">
        <strong>Today Appointments List</strong>
        <table class="grid">
            <thead>
            <tr>
                <th>#</th>
                <th>Time</th>
                <th>Patient</th>
                <th>Branch</th>
                <th>Service</th>
                <th>Status</th>
                <th>Type</th>
                <th>Price</th>
            </tr>
            </thead>
            <tbody>
            @forelse($todayAppointments as $a)
                <tr>
                    <td>{{ $a->id }}</td>
                    <td>{{ $a->preferred_time }}</td>
                    <td>{{ $a->patient_name }}<br>{{ $a->patient_phone }}</td>
                    <td>{{ $a->branch?->name ?? '-' }}</td>
                    <td>{{ $a->service?->title ?? '-' }}</td>
                    <td>{{ $a->status }}</td>
                    <td>{{ $a->booking_type === 'vip' ? 'VIP' : 'Regular' }}</td>
                    <td>{{ number_format((float) $a->price, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="8">No appointments for today</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
