<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\FinanceEntry;
use App\Models\Patient;
use App\Models\PatientVisit;
use App\Models\Setting;
use App\Models\Service;
use App\Support\Code39Barcode;
use App\Support\AccountingService;
use App\Support\PatientRegistry;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class AppointmentController extends Controller
{
    public function __construct(private readonly PatientRegistry $patientRegistry)
    {
    }

    public function index(Request $request)
    {
        $query = Appointment::query()->with(['branch', 'service', 'visit.patient', 'patient']);

        if ($request->filled('q')) {
            $term = trim((string) $request->input('q'));
            $query->where(function ($builder) use ($term): void {
                $builder
                    ->where('patient_name', 'like', "%{$term}%")
                    ->orWhere('patient_phone', 'like', "%{$term}%")
                    ->orWhere('notes', 'like', "%{$term}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', (string) $request->input('status'));
        }

        if ($request->filled('source')) {
            $query->where('source', (string) $request->input('source'));
        }
        if ($request->filled('booking_type')) {
            $query->where('booking_type', (string) $request->input('booking_type'));
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', (int) $request->input('branch_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('preferred_date', '>=', (string) $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('preferred_date', '<=', (string) $request->input('date_to'));
        }

        if ($request->filled('converted')) {
            if ($request->input('converted') === 'yes') {
                $query->whereHas('visit');
            } elseif ($request->input('converted') === 'no') {
                $query->whereDoesntHave('visit');
            }
        }

        $sort = (string) $request->input('sort', 'latest');
        if ($sort === 'oldest') {
            $query->oldest();
        } elseif ($sort === 'date_asc') {
            $query->orderBy('preferred_date')->orderBy('preferred_time');
        } elseif ($sort === 'date_desc') {
            $query->orderByDesc('preferred_date')->orderByDesc('preferred_time');
        } else {
            $query->latest();
        }

        $appointments = $query->paginate(20)->withQueryString();
        $branches = Branch::query()->where('is_active', true)->orderBy('sort_order')->get();

        $statsBase = Appointment::query();
        $stats = [
            'today' => (clone $statsBase)->whereDate('preferred_date', now()->toDateString())->count(),
            'new' => (clone $statsBase)->where('status', 'new')->count(),
            'week' => (clone $statsBase)->whereBetween('preferred_date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])->count(),
            'converted' => (clone $statsBase)->whereHas('visit')->count(),
            'total' => (clone $statsBase)->count(),
            'revenue' => (float) ((clone $statsBase)->sum('price') ?? 0),
        ];

        return view('admin.appointments.index', compact('appointments', 'branches', 'stats'));
    }

    public function create()
    {
        $branches = Branch::query()->where('is_active', true)->orderBy('sort_order')->get();
        $services = Service::query()->where('is_active', true)->orderBy('sort_order')->get();
        $regularPrice = (float) Setting::getValue('appointment_price_regular', 300);
        $vipPrice = (float) Setting::getValue('appointment_price_vip', 600);

        return view('admin.appointments.create', compact('branches', 'services', 'regularPrice', 'vipPrice'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_name' => ['required', 'string', 'max:255'],
            'patient_phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female'],
            'occupation' => ['nullable', 'string', 'max:120'],
            'marital_status' => ['nullable', 'string', 'max:40'],
            'national_id' => ['nullable', 'string', 'max:40'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
            'insurance_company' => ['nullable', 'string', 'max:150'],
            'insurance_number' => ['nullable', 'string', 'max:80'],
            'smoking_status' => ['nullable', 'string', 'max:50'],
            'allergies' => ['nullable', 'string'],
            'chronic_diseases' => ['nullable', 'string'],
            'current_medications' => ['nullable', 'string'],
            'previous_surgeries' => ['nullable', 'string'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'booking_type' => ['required', 'in:regular,vip'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'preferred_date' => ['required', 'date'],
            'preferred_time' => ['required'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'in:new,contacted,completed,canceled'],
            'source' => ['nullable', 'in:website,whatsapp,phone'],
        ]);

        $data['source'] = $data['source'] ?? 'website';
        if (! isset($data['price']) || $data['price'] === null || $data['price'] === '') {
            $data['price'] = $data['booking_type'] === 'vip'
                ? (float) Setting::getValue('appointment_price_vip', 600)
                : (float) Setting::getValue('appointment_price_regular', 300);
        }
        $appointmentData = collect($data)->except([
            'email', 'address', 'date_of_birth', 'gender', 'occupation', 'marital_status',
            'national_id', 'blood_type', 'emergency_contact_name', 'emergency_contact_phone',
            'insurance_company', 'insurance_number', 'smoking_status', 'allergies',
            'chronic_diseases', 'current_medications', 'previous_surgeries',
        ])->all();

        $appointment = Appointment::query()->create($appointmentData);
        $this->patientRegistry->syncAppointment($appointment, $data);
        $this->syncFinanceEntry($appointment);

        return redirect()->route('admin.appointments.index', app()->getLocale())->with('success', 'Saved successfully');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['branch', 'service', 'visit.patient', 'financeEntries']);

        return view('admin.appointments.show', compact('appointment'));
    }

    public function report(Appointment $appointment)
    {
        $appointment->load(['branch', 'service', 'visit.patient']);
        $bookingCode = 'APT-' . str_pad((string) $appointment->id, 6, '0', STR_PAD_LEFT);
        $barcodeSvg = Code39Barcode::toSvg($bookingCode, 90);
        $trackingUrl = URL::signedRoute('front.appointments.confirmation', [
            'locale' => app()->getLocale(),
            'appointment' => $appointment->id,
        ]);
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . urlencode($trackingUrl);

        return view('admin.appointments.report', compact('appointment', 'bookingCode', 'barcodeSvg', 'trackingUrl', 'qrUrl'));
    }

    public function reportPdf(Appointment $appointment)
    {
        $appointment->load(['branch', 'service', 'visit.patient']);
        $bookingCode = 'APT-' . str_pad((string) $appointment->id, 6, '0', STR_PAD_LEFT);
        $barcodeSvg = Code39Barcode::toSvg($bookingCode, 80);
        $trackingUrl = URL::signedRoute('front.appointments.confirmation', [
            'locale' => app()->getLocale(),
            'appointment' => $appointment->id,
        ]);
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . urlencode($trackingUrl);

        $pdf = Pdf::loadView('pdf.admin-appointment-report', compact('appointment', 'bookingCode', 'barcodeSvg', 'trackingUrl', 'qrUrl'))
            ->setPaper('a4');

        return $pdf->download('appointment_report_' . $bookingCode . '_' . now()->format('Ymd_His') . '.pdf');
    }

    public function edit(Appointment $appointment)
    {
        $branches = Branch::query()->where('is_active', true)->orderBy('sort_order')->get();
        $services = Service::query()->where('is_active', true)->orderBy('sort_order')->get();
        $regularPrice = (float) Setting::getValue('appointment_price_regular', 300);
        $vipPrice = (float) Setting::getValue('appointment_price_vip', 600);

        return view('admin.appointments.edit', compact('appointment', 'branches', 'services', 'regularPrice', 'vipPrice'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'patient_name' => ['required', 'string', 'max:255'],
            'patient_phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female'],
            'occupation' => ['nullable', 'string', 'max:120'],
            'marital_status' => ['nullable', 'string', 'max:40'],
            'national_id' => ['nullable', 'string', 'max:40'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
            'insurance_company' => ['nullable', 'string', 'max:150'],
            'insurance_number' => ['nullable', 'string', 'max:80'],
            'smoking_status' => ['nullable', 'string', 'max:50'],
            'allergies' => ['nullable', 'string'],
            'chronic_diseases' => ['nullable', 'string'],
            'current_medications' => ['nullable', 'string'],
            'previous_surgeries' => ['nullable', 'string'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'booking_type' => ['required', 'in:regular,vip'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'preferred_date' => ['required', 'date'],
            'preferred_time' => ['required'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'in:new,contacted,completed,canceled'],
            'source' => ['nullable', 'in:website,whatsapp,phone'],
        ]);

        $data['source'] = $data['source'] ?? 'website';
        if (! isset($data['price']) || $data['price'] === null || $data['price'] === '') {
            $data['price'] = $data['booking_type'] === 'vip'
                ? (float) Setting::getValue('appointment_price_vip', 600)
                : (float) Setting::getValue('appointment_price_regular', 300);
        }
        $appointmentData = collect($data)->except([
            'email', 'address', 'date_of_birth', 'gender', 'occupation', 'marital_status',
            'national_id', 'blood_type', 'emergency_contact_name', 'emergency_contact_phone',
            'insurance_company', 'insurance_number', 'smoking_status', 'allergies',
            'chronic_diseases', 'current_medications', 'previous_surgeries',
        ])->all();

        $appointment->update($appointmentData);
        $this->patientRegistry->syncAppointment($appointment, $data);
        $this->syncFinanceEntry($appointment->fresh());

        return redirect()->route('admin.appointments.index', app()->getLocale())->with('success', 'Updated successfully');
    }

    public function destroy(Appointment $appointment)
    {
        FinanceEntry::query()
            ->where('appointment_id', $appointment->id)
            ->update([
                'record_status' => 'void',
                'notes' => DB::raw("CONCAT(IFNULL(notes, ''), ' [Appointment deleted]')"),
            ]);

        $appointment->delete();

        return redirect()->route('admin.appointments.index', app()->getLocale())->with('success', 'Deleted successfully');
    }

    public function quickUpdateStatus(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,contacted,completed,canceled'],
        ]);

        $appointment->update(['status' => $data['status']]);
        $this->syncFinanceEntry($appointment->fresh());

        return redirect()->route('admin.appointments.index', app()->getLocale())->with('success', 'Status updated successfully');
    }

    public function convertToVisit(Appointment $appointment)
    {
        if ($appointment->visit()->exists()) {
            $patientId = $appointment->visit?->patient_id;

            return redirect()
                ->route('admin.patients.show', [app()->getLocale(), $patientId])
                ->with('success', 'Appointment is already converted to a visit');
        }

        $visit = DB::transaction(function () use ($appointment) {
            $patient = $this->patientRegistry->syncAppointment($appointment);

            $visit = PatientVisit::query()->create([
                'patient_id' => $patient->id,
                'branch_id' => $appointment->branch_id,
                'appointment_id' => $appointment->id,
                'visit_date' => $appointment->preferred_date,
                'visit_time' => $appointment->preferred_time,
                'visit_status' => 'new',
                'chief_complaint' => $appointment->notes,
                'notes' => 'Auto created from appointment #' . $appointment->id,
            ]);

            $patient->update(['last_visit_at' => $visit->visit_date]);

            return $visit;
        });

        return redirect()
            ->route('admin.patients.show', [app()->getLocale(), $visit->patient_id])
            ->with('success', 'Appointment converted to patient visit successfully');
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
