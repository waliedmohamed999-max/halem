<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\FinanceEntry;
use App\Models\Setting;
use App\Models\Service;
use App\Models\WorkingHour;
use App\Support\Code39Barcode;
use App\Support\AccountingService;
use App\Support\PatientRegistry;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class AppointmentController extends Controller
{
    public function __construct(private readonly PatientRegistry $patientRegistry)
    {
    }

    public function create()
    {
        $branches = Branch::query()->where('is_active', true)->orderBy('sort_order')->get();
        $services = Service::query()->where('is_active', true)->orderBy('sort_order')->get();
        $hours = WorkingHour::query()->whereNull('branch_id')->orderBy('day_of_week')->get();
        $emergency = WorkingHour::query()
            ->whereNull('branch_id')
            ->where('is_emergency', true)
            ->whereNotNull('emergency_text')
            ->latest('id')
            ->first();
        $whatsappUrl = Setting::getValue('whatsapp_url');
        $sitePhone = Setting::getValue('site_phone', '01028234921');
        $regularPrice = (float) Setting::getValue('appointment_price_regular', 300);
        $vipPrice = (float) Setting::getValue('appointment_price_vip', 600);

        return view('front.appointments.create', compact('branches', 'services', 'hours', 'emergency', 'whatsappUrl', 'sitePhone', 'regularPrice', 'vipPrice'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_name' => ['required', 'string', 'max:255'],
            'patient_phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female'],
            'occupation' => ['nullable', 'string', 'max:120'],
            'marital_status' => ['nullable', 'string', 'max:40'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'allergies' => ['nullable', 'string'],
            'chronic_diseases' => ['nullable', 'string'],
            'current_medications' => ['nullable', 'string'],
            'previous_surgeries' => ['nullable', 'string'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
            'branch_id' => ['required', 'exists:branches,id'],
            'service_id' => ['required', 'exists:services,id'],
            'booking_type' => ['required', 'in:regular,vip'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'preferred_date' => ['required', 'date', 'after_or_equal:today'],
            'preferred_time' => ['required'],
            'notes' => ['nullable', 'string', 'max:3000'],
            'visit_type' => ['nullable', 'string', 'in:first_visit,follow_up,emergency,consultation'],
            'pain_level' => ['nullable', 'integer', 'between:0,10'],
            'preferred_contact' => ['nullable', 'string', 'in:phone,whatsapp'],
            'symptoms' => ['nullable', 'array'],
            'symptoms.*' => ['string', 'max:120'],
        ]);

        $slotTaken = Appointment::query()
            ->where('branch_id', $data['branch_id'])
            ->whereDate('preferred_date', $data['preferred_date'])
            ->whereTime('preferred_time', $data['preferred_time'])
            ->where('status', '!=', 'canceled')
            ->exists();

        if ($slotTaken) {
            return back()
                ->withInput()
                ->withErrors([
                    'preferred_time' => app()->getLocale() === 'ar'
                        ? 'هذا الموعد محجوز بالفعل، اختر وقتًا آخر.'
                        : 'This time slot is already booked. Please choose another slot.',
                ]);
        }

        $doctorBrief = [];
        if (!empty($data['visit_type'])) {
            $doctorBrief[] = 'Visit Type: ' . $data['visit_type'];
        }
        if (isset($data['pain_level']) && $data['pain_level'] !== null && $data['pain_level'] !== '') {
            $doctorBrief[] = 'Pain Level: ' . $data['pain_level'] . '/10';
        }
        if (!empty($data['preferred_contact'])) {
            $doctorBrief[] = 'Preferred Contact: ' . $data['preferred_contact'];
        }
        if (!empty($data['symptoms'])) {
            $doctorBrief[] = 'Symptoms: ' . implode(', ', $data['symptoms']);
        }

        $baseNotes = trim((string) ($data['notes'] ?? ''));
        $notes = $baseNotes;
        if ($doctorBrief !== []) {
            $notes = trim($baseNotes . "\n\n--- Intake Summary ---\n" . implode("\n", $doctorBrief));
        }

        $defaultPrice = $data['booking_type'] === 'vip'
            ? (float) Setting::getValue('appointment_price_vip', 600)
            : (float) Setting::getValue('appointment_price_regular', 300);
        $finalPrice = isset($data['price']) && $data['price'] !== null && $data['price'] !== ''
            ? (float) $data['price']
            : $defaultPrice;

        $appointment = Appointment::create([
            'patient_name' => $data['patient_name'],
            'patient_phone' => $data['patient_phone'],
            'branch_id' => $data['branch_id'],
            'service_id' => $data['service_id'],
            'booking_type' => $data['booking_type'],
            'price' => $finalPrice,
            'preferred_date' => $data['preferred_date'],
            'preferred_time' => $data['preferred_time'],
            'notes' => $notes !== '' ? $notes : null,
            'status' => 'new',
            'source' => 'website',
        ]);
        $this->patientRegistry->syncAppointment($appointment, $data);
        $this->syncFinanceEntry($appointment);

        return redirect()->signedRoute('front.appointments.confirmation', [
            'locale' => app()->getLocale(),
            'appointment' => $appointment->id,
        ]);
    }

    public function confirmation(Appointment $appointment)
    {
        $appointment->load(['branch', 'service']);
        $bookingCode = 'APT-' . str_pad((string) $appointment->id, 6, '0', STR_PAD_LEFT);
        $barcodeSvg = Code39Barcode::toSvg($bookingCode, 86);
        $trackingUrl = route('front.appointments.tracking', [
            'locale' => app()->getLocale(),
            'code' => $bookingCode,
        ]);
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . urlencode($trackingUrl);

        return view('front.appointments.confirmation', compact('appointment', 'bookingCode', 'barcodeSvg', 'trackingUrl', 'qrUrl'));
    }

    public function confirmationPdf(Appointment $appointment)
    {
        $appointment->load(['branch', 'service']);
        $bookingCode = 'APT-' . str_pad((string) $appointment->id, 6, '0', STR_PAD_LEFT);
        $barcodeSvg = Code39Barcode::toSvg($bookingCode, 80);
        $trackingUrl = route('front.appointments.tracking', [
            'locale' => app()->getLocale(),
            'code' => $bookingCode,
        ]);
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . urlencode($trackingUrl);

        $pdf = Pdf::loadView('pdf.appointment-confirmation', compact('appointment', 'bookingCode', 'barcodeSvg', 'trackingUrl', 'qrUrl'))
            ->setPaper('a4');

        return $pdf->download('appointment_confirmation_' . $bookingCode . '_' . now()->format('Ymd_His') . '.pdf');
    }

    public function tracking(Request $request)
    {
        $appointment = null;
        $bookingCode = strtoupper(trim((string) $request->query('code', '')));
        $phone = trim((string) $request->query('phone', ''));

        if ($bookingCode !== '' && $phone !== '') {
            $appointment = $this->findAppointmentByCodeAndPhone($bookingCode, $phone);
            if (! $appointment) {
                return view('front.appointments.tracking', compact('appointment', 'bookingCode', 'phone'))
                    ->withErrors([
                        'tracking' => app()->getLocale() === 'ar'
                            ? 'لم يتم العثور على حجز مطابق. تأكد من رقم الحجز ورقم الهاتف.'
                            : 'No matching appointment found. Please verify booking code and phone.',
                    ]);
            }
        }

        return view('front.appointments.tracking', compact('appointment', 'bookingCode', 'phone'));
    }

    public function trackingSearch(Request $request)
    {
        $data = $request->validate([
            'booking_code' => ['required', 'string', 'max:50'],
            'patient_phone' => ['required', 'string', 'max:50'],
        ]);

        $bookingCode = strtoupper(trim((string) $data['booking_code']));
        $phone = trim((string) $data['patient_phone']);
        $appointment = $this->findAppointmentByCodeAndPhone($bookingCode, $phone);

        if (! $appointment) {
            return back()
                ->withInput()
                ->withErrors([
                    'tracking' => app()->getLocale() === 'ar'
                        ? 'لم يتم العثور على حجز مطابق. تأكد من رقم الحجز ورقم الهاتف.'
                        : 'No matching appointment found. Please verify booking code and phone.',
                ]);
        }

        return redirect()->route('front.appointments.tracking', [
            'locale' => app()->getLocale(),
            'code' => $bookingCode,
            'phone' => $phone,
        ]);
    }

    public function availability(Request $request)
    {
        $data = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $date = Carbon::parse($data['date']);
        $workingHour = $this->resolveWorkingHour((int) $data['branch_id'], $date);

        if (! $workingHour || ! $workingHour->is_open || ! $workingHour->open_at || ! $workingHour->close_at) {
            return response()->json([
                'is_open' => false,
                'message' => app()->getLocale() === 'ar' ? 'الفرع مغلق في هذا اليوم.' : 'Branch is closed on this day.',
                'slots' => [],
                'booked_times' => [],
            ]);
        }

        $slots = $this->buildTimeSlots((string) $workingHour->open_at, (string) $workingHour->close_at, 30);
        $bookedTimes = Appointment::query()
            ->where('branch_id', $data['branch_id'])
            ->whereDate('preferred_date', $date->toDateString())
            ->where('status', '!=', 'canceled')
            ->pluck('preferred_time')
            ->map(static function ($time): string {
                return substr((string) $time, 0, 5);
            })
            ->unique()
            ->values()
            ->all();

        $slotRows = collect($slots)
            ->map(static function (string $slot) use ($bookedTimes): array {
                return [
                    'time' => $slot,
                    'is_booked' => in_array($slot, $bookedTimes, true),
                ];
            })
            ->values()
            ->all();

        return response()->json([
            'is_open' => true,
            'message' => app()->getLocale() === 'ar' ? 'اختر موعدًا متاحًا.' : 'Choose an available slot.',
            'slots' => $slotRows,
            'booked_times' => $bookedTimes,
        ]);
    }

    private function resolveWorkingHour(int $branchId, Carbon $date): ?WorkingHour
    {
        return WorkingHour::query()
            ->where('day_of_week', $date->dayOfWeek)
            ->where(function ($query) use ($branchId): void {
                $query->where('branch_id', $branchId)->orWhereNull('branch_id');
            })
            ->orderByRaw('CASE WHEN branch_id IS NULL THEN 1 ELSE 0 END')
            ->first();
    }

    private function buildTimeSlots(string $openAt, string $closeAt, int $intervalMinutes = 30): array
    {
        $slots = [];
        $cursor = Carbon::createFromFormat('H:i:s', substr($openAt, 0, 8));
        $end = Carbon::createFromFormat('H:i:s', substr($closeAt, 0, 8));

        while ($cursor->lt($end)) {
            $slots[] = $cursor->format('H:i');
            $cursor->addMinutes($intervalMinutes);
        }

        return $slots;
    }

    private function findAppointmentByCodeAndPhone(string $bookingCode, string $phone): ?Appointment
    {
        if (! preg_match('/APT-(\d+)/i', $bookingCode, $matches)) {
            return null;
        }

        $appointmentId = (int) ($matches[1] ?? 0);
        if ($appointmentId <= 0) {
            return null;
        }

        $appointment = Appointment::query()->with(['branch', 'service'])->find($appointmentId);
        if (! $appointment) {
            return null;
        }

        $normalize = static fn (string $value): string => preg_replace('/\D+/', '', $value) ?? '';
        if ($normalize((string) $appointment->patient_phone) !== $normalize($phone)) {
            return null;
        }

        return $appointment;
    }

    private function syncFinanceEntry(Appointment $appointment): void
    {
        $accounting = app(AccountingService::class);
        $party = $accounting->customerParty($appointment->patient_name, $appointment->patient_phone);

        $entry = FinanceEntry::query()->updateOrCreate(
            [
                'appointment_id' => $appointment->id,
                'entry_type' => 'income',
                'entry_kind' => 'appointment',
            ],
            [
                'branch_id' => $appointment->branch_id,
                'party_id' => $party?->id,
                'cost_center_id' => $accounting->costCenterForBranch($appointment->branch_id)?->id,
                'created_by' => auth()->id(),
                'ledger_context' => 'appointment',
                'source_type' => 'appointment',
                'source_id' => $appointment->id,
                'title' => 'Appointment #' . $appointment->id . ' - ' . $appointment->patient_name,
                'invoice_number' => 'APT-' . str_pad((string) $appointment->id, 6, '0', STR_PAD_LEFT),
                'counterparty' => $appointment->patient_name,
                'amount' => (float) ($appointment->price ?? 0),
                'entry_date' => $appointment->preferred_date ?: now()->toDateString(),
                'payment_method' => 'cash',
                'notes' => $appointment->notes,
                'record_status' => $appointment->status === 'canceled' ? 'void' : 'posted',
            ]
        );

        $accounting->syncFinanceEntry($entry);
    }
}
