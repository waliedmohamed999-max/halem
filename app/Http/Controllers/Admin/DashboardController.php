<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\ChatConversation;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Subscriber;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $days = (int) $request->input('days', 14);
        if (! in_array($days, [7, 14, 30], true)) {
            $days = 14;
        }

        $today = now()->toDateString();
        $periodStart = now()->subDays($days - 1)->toDateString();
        $weekStart = now()->startOfWeek()->toDateString();
        $weekEnd = now()->endOfWeek()->toDateString();
        $periodAppointments = Appointment::query()->whereBetween('preferred_date', [$periodStart, $today]);

        $stats = [
            'appointments_today' => Appointment::query()->whereDate('preferred_date', $today)->count(),
            'appointments_week' => Appointment::query()->whereBetween('preferred_date', [$weekStart, $weekEnd])->count(),
            'appointments_total' => (clone $periodAppointments)->count(),
            'appointments_new' => (clone $periodAppointments)->where('status', 'new')->count(),
            'appointments_completed' => (clone $periodAppointments)->where('status', 'completed')->count(),
            'appointments_canceled' => (clone $periodAppointments)->where('status', 'canceled')->count(),
            'services_total' => Service::query()->count(),
            'doctors_total' => Doctor::query()->count(),
            'messages_total' => ChatConversation::query()->count(),
            'messages_unread' => ChatConversation::query()->where('admin_unread_count', '>', 0)->count(),
            'subscribers_total' => Subscriber::query()->count(),
            'patients_total' => Patient::query()->count(),
            'revenue_period' => (float) ((clone $periodAppointments)->sum('price') ?? 0),
        ];

        $stats['completion_rate'] = $stats['appointments_total'] > 0
            ? (int) round(($stats['appointments_completed'] / $stats['appointments_total']) * 100)
            : 0;
        $stats['cancellation_rate'] = $stats['appointments_total'] > 0
            ? (int) round(($stats['appointments_canceled'] / $stats['appointments_total']) * 100)
            : 0;

        $lateAppointmentsCount = Appointment::query()
            ->whereDate('preferred_date', '<', $today)
            ->whereIn('status', ['new', 'contacted'])
            ->count();
        $unansweredMessagesCount = ChatConversation::query()->where('admin_unread_count', '>', 0)->count();
        $highCancelRate = $stats['cancellation_rate'] >= 25;

        $alerts = [];
        if ($lateAppointmentsCount > 0) {
            $alerts[] = [
                'level' => 'warning',
                'title' => 'Overdue appointments',
                'title_ar' => 'حجوزات متأخرة',
                'message' => 'There are appointments in past dates still not completed/canceled.',
                'message_ar' => 'يوجد حجوزات بتواريخ سابقة وما زالت بدون إنهاء/إلغاء.',
                'count' => $lateAppointmentsCount,
                'url' => route('admin.appointments.index', [app()->getLocale(), 'date_to' => now()->subDay()->toDateString(), 'status' => 'new']),
            ];
        }
        if ($unansweredMessagesCount > 0) {
            $alerts[] = [
                'level' => 'danger',
                'title' => 'Unread live chats',
                'title_ar' => 'رسائل بدون رد',
                'message' => 'There are live customer conversations waiting for follow-up.',
                'message_ar' => 'توجد رسائل تواصل غير مقروءة تحتاج متابعة.',
                'count' => $unansweredMessagesCount,
                'url' => route('admin.messages.index', app()->getLocale()),
            ];
        }
        if ($highCancelRate) {
            $alerts[] = [
                'level' => 'warning',
                'title' => 'High cancellation rate',
                'title_ar' => 'نسبة إلغاء مرتفعة',
                'message' => 'Cancellation rate in selected period is above threshold (25%).',
                'message_ar' => 'نسبة الإلغاء في الفترة المختارة أعلى من الحد المسموح (25%).',
                'count' => $stats['cancellation_rate'] . '%',
                'url' => route('admin.appointments.index', [app()->getLocale(), 'days' => $days, 'status' => 'canceled']),
            ];
        }

        $todayAppointments = Appointment::query()
            ->with(['branch', 'service'])
            ->whereDate('preferred_date', $today)
            ->orderBy('preferred_time')
            ->limit(8)
            ->get();

        $upcomingAppointments = Appointment::query()
            ->with(['branch', 'service'])
            ->whereDate('preferred_date', '>=', $today)
            ->orderBy('preferred_date')
            ->orderBy('preferred_time')
            ->limit(8)
            ->get();

        $latestMessages = ChatConversation::query()
            ->latest('last_message_at')
            ->latest('id')
            ->limit(6)
            ->get();

        return view('admin.dashboard', compact('stats', 'todayAppointments', 'upcomingAppointments', 'latestMessages', 'days', 'alerts'));
    }

    public function charts(Request $request)
    {
        $days = (int) $request->input('days', 14);
        if (! in_array($days, [7, 14, 30], true)) {
            $days = 14;
        }
        $dateFrom = now()->subDays($days - 1)->startOfDay();

        $branches = Branch::query()->orderBy('sort_order')->get();
        $branchCounts = Appointment::query()
            ->select('branch_id', DB::raw('count(*) as total'))
            ->where('created_at', '>=', $dateFrom)
            ->groupBy('branch_id')
            ->pluck('total', 'branch_id');

        $byBranch = $branches->map(function (Branch $branch) use ($branchCounts): array {
            return [
                'branch' => $branch->name,
                'total' => (int) ($branchCounts[$branch->id] ?? 0),
            ];
        })->values();

        $statusCounts = Appointment::query()
            ->select('status', DB::raw('count(*) as total'))
            ->where('created_at', '>=', $dateFrom)
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusLabels = ['new', 'contacted', 'completed', 'canceled'];
        $byStatus = collect($statusLabels)->map(static function (string $status) use ($statusCounts): array {
            return [
                'status' => $status,
                'total' => (int) ($statusCounts[$status] ?? 0),
            ];
        })->values();

        $rawDays = Appointment::query()
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', $dateFrom)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('day')
            ->pluck('total', 'day');

        $period = CarbonPeriod::create($dateFrom, now()->startOfDay());
        $last14Days = collect($period)
            ->map(static function (Carbon $day) use ($rawDays): array {
                $key = $day->toDateString();
                return [
                    'day' => $key,
                    'total' => (int) ($rawDays[$key] ?? 0),
                ];
            })
            ->values();

        return response()->json([
            'by_branch' => $byBranch,
            'by_status' => $byStatus,
            'last_14_days' => $last14Days,
            'days' => $days,
        ]);
    }

    public function dailyReportPdf()
    {
        $today = now()->toDateString();
        $todayAppointments = Appointment::query()
            ->with(['branch', 'service'])
            ->whereDate('preferred_date', $today)
            ->orderBy('preferred_time')
            ->get();

        $stats = [
            'appointments_today' => $todayAppointments->count(),
            'appointments_new' => $todayAppointments->where('status', 'new')->count(),
            'appointments_completed' => $todayAppointments->where('status', 'completed')->count(),
            'appointments_canceled' => $todayAppointments->where('status', 'canceled')->count(),
            'today_revenue' => (float) ($todayAppointments->sum('price') ?? 0),
            'messages_unread' => ChatConversation::query()->where('admin_unread_count', '>', 0)->count(),
        ];

        $lateAppointmentsCount = Appointment::query()
            ->whereDate('preferred_date', '<', $today)
            ->whereIn('status', ['new', 'contacted'])
            ->count();

        $pdf = Pdf::loadView('pdf.dashboard-daily-report', compact('today', 'todayAppointments', 'stats', 'lateAppointmentsCount'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('dashboard_daily_report_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }
}
