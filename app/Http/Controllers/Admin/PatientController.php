<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::query()->withCount(['visits', 'appointments', 'documents'])->latest();

        if ($request->filled('q')) {
            $term = trim((string) $request->string('q'));
            $query->where(function ($builder) use ($term): void {
                $builder
                    ->where('full_name', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                    ->orWhere('national_id', 'like', "%{$term}%");
            });
        }

        $patients = $query->paginate(20)->withQueryString();
        $stats = [
            'total' => (clone $query)->toBase()->getCountForPagination(),
            'with_visits' => (clone $query)->has('visits')->toBase()->getCountForPagination(),
            'with_appointments' => (clone $query)->has('appointments')->toBase()->getCountForPagination(),
            'with_documents' => (clone $query)->has('documents')->toBase()->getCountForPagination(),
        ];

        return view('admin.patients.index', compact('patients', 'stats'));
    }

    public function create()
    {
        return view('admin.patients.create');
    }

    public function store(StorePatientRequest $request)
    {
        Patient::query()->create($request->validated());

        return redirect()->route('admin.patients.index', app()->getLocale())->with('success', 'Patient saved successfully');
    }

    public function show(Request $request, int $patient)
    {
        $patient = Patient::query()->with([
            'visits' => function ($query): void {
                $query->with(['branch', 'doctor', 'attachments', 'appointment'])->latest('visit_date')->latest('visit_time');
            },
            'appointments' => function ($query): void {
                $query->with(['branch', 'service', 'visit'])->latest('preferred_date')->latest('preferred_time');
            },
        ])->findOrFail($patient);

        $documentsQuery = $patient->documents()->with('visit');

        if ($request->filled('doc_type')) {
            $documentsQuery->where('document_type', (string) $request->input('doc_type'));
        }

        if ($request->filled('date_from')) {
            $documentsQuery->whereDate('document_date', '>=', (string) $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $documentsQuery->whereDate('document_date', '<=', (string) $request->input('date_to'));
        }

        if ($request->filled('doc_q')) {
            $term = trim((string) $request->input('doc_q'));
            $documentsQuery->where(function ($query) use ($term): void {
                $query->where('title', 'like', "%{$term}%")->orWhere('notes', 'like', "%{$term}%");
            });
        }

        $sort = $request->input('doc_sort') === 'oldest' ? 'oldest' : 'newest';
        if ($sort === 'oldest') {
            $documentsQuery->orderByRaw('COALESCE(document_date, created_at) asc');
        } else {
            $documentsQuery->orderByRaw('COALESCE(document_date, created_at) desc');
        }

        $documents = $documentsQuery->paginate(12)->withQueryString();

        $allDocuments = $patient->documents()->with('visit')->latest('document_date')->latest('id')->get();
        $medicationHistory = $patient->visits
            ->filter(fn ($visit) => filled($visit->prescription))
            ->values();
        $procedureHistory = $patient->visits
            ->filter(fn ($visit) => filled($visit->procedure_done) || filled($visit->treatment_plan) || filled($visit->diagnosis))
            ->values();
        $documentStats = [
            'all' => $allDocuments->count(),
            'xray' => $allDocuments->where('document_type', 'xray')->count(),
            'lab' => $allDocuments->where('document_type', 'lab')->count(),
            'report' => $allDocuments->where('document_type', 'report')->count(),
            'prescription' => $allDocuments->where('document_type', 'prescription')->count(),
        ];
        $nextAppointment = $patient->appointments
            ->filter(fn ($appointment) => $appointment->status !== 'canceled' && optional($appointment->preferred_date)->isTodayOrAfter())
            ->sortBy(fn ($appointment) => ($appointment->preferred_date?->format('Y-m-d') ?? '9999-12-31') . ' ' . ($appointment->preferred_time ?? '23:59:59'))
            ->first();
        $lastVisit = $patient->visits->first();
        $clinicalAlerts = collect([
            [
                'label' => 'allergies',
                'title_ar' => 'الحساسية',
                'title_en' => 'Allergies',
                'value' => $patient->allergies,
            ],
            [
                'label' => 'chronic_diseases',
                'title_ar' => 'الأمراض المزمنة',
                'title_en' => 'Chronic Diseases',
                'value' => $patient->chronic_diseases,
            ],
            [
                'label' => 'current_medications',
                'title_ar' => 'الأدوية الحالية',
                'title_en' => 'Current Medications',
                'value' => $patient->current_medications,
            ],
            [
                'label' => 'smoking_status',
                'title_ar' => 'التدخين',
                'title_en' => 'Smoking Status',
                'value' => $patient->smoking_status,
            ],
        ])->filter(fn (array $item) => filled($item['value']))->values();

        return view('admin.patients.show', compact(
            'patient',
            'documents',
            'allDocuments',
            'medicationHistory',
            'procedureHistory',
            'documentStats',
            'nextAppointment',
            'lastVisit',
            'clinicalAlerts'
        ));
    }

    public function edit(int $patient)
    {
        $patient = Patient::query()->findOrFail($patient);

        return view('admin.patients.edit', compact('patient'));
    }

    public function update(UpdatePatientRequest $request, int $patient)
    {
        $patient = Patient::query()->findOrFail($patient);
        $patient->update($request->validated());

        return redirect()->route('admin.patients.show', [app()->getLocale(), $patient->id])->with('success', 'Patient updated successfully');
    }

    public function destroy(int $patient)
    {
        $patient = Patient::query()->findOrFail($patient);
        $patient->delete();

        return redirect()->route('admin.patients.index', app()->getLocale())->with('success', 'Patient deleted successfully');
    }

    public function report(int $patient)
    {
        $patient = Patient::query()->with([
            'visits' => function ($query): void {
                $query->with(['branch', 'doctor', 'attachments'])->latest('visit_date')->latest('visit_time');
            },
        ])->findOrFail($patient);

        return view('admin.patients.report', compact('patient'));
    }

    public function downloadAttachmentsZip(int $patient)
    {
        $patient = Patient::query()->with([
            'visits' => function ($query): void {
                $query->with('attachments')->latest('visit_date')->latest('visit_time');
            },
        ])->findOrFail($patient);

        $attachments = $patient->visits
            ->flatMap(fn ($visit) => $visit->attachments)
            ->filter(fn ($attachment) => ! empty($attachment->file_path))
            ->values();

        if ($attachments->isEmpty()) {
            return back()->with('success', 'No attachments found for this patient');
        }

        $zip = new \ZipArchive();
        $tmpZipPath = tempnam(sys_get_temp_dir(), 'patient_zip_');

        if ($tmpZipPath === false || $zip->open($tmpZipPath, \ZipArchive::OVERWRITE) !== true) {
            return back()->with('success', 'Could not create ZIP file');
        }

        foreach ($attachments as $index => $attachment) {
            if (! Storage::disk('public')->exists($attachment->file_path)) {
                continue;
            }

            $fullPath = Storage::disk('public')->path($attachment->file_path);
            $safeName = preg_replace('/[^A-Za-z0-9\.\-_]/', '_', (string) $attachment->file_name);
            $zip->addFile($fullPath, sprintf('%03d_%s', $index + 1, $safeName ?: basename($fullPath)));
        }

        $zip->close();

        $downloadName = 'patient_' . $patient->id . '_attachments.zip';

        return response()->download($tmpZipPath, $downloadName)->deleteFileAfterSend(true);
    }

    public function downloadMedicalDocumentsZip(int $patient)
    {
        $patient = Patient::query()->with('documents')->findOrFail($patient);

        $documents = $patient->documents
            ->whereIn('document_type', ['xray', 'lab'])
            ->filter(fn ($document) => ! empty($document->file_path))
            ->values();

        if ($documents->isEmpty()) {
            return back()->with('success', 'No X-ray or Lab files found for this patient');
        }

        $zip = new \ZipArchive();
        $tmpZipPath = tempnam(sys_get_temp_dir(), 'patient_medical_zip_');

        if ($tmpZipPath === false || $zip->open($tmpZipPath, \ZipArchive::OVERWRITE) !== true) {
            return back()->with('success', 'Could not create ZIP file');
        }

        foreach ($documents as $index => $document) {
            if (! Storage::disk('public')->exists($document->file_path)) {
                continue;
            }

            $fullPath = Storage::disk('public')->path($document->file_path);
            $safeName = preg_replace('/[^A-Za-z0-9\.\-_]/', '_', (string) $document->title);
            $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
            $zip->addFile($fullPath, sprintf('%03d_%s.%s', $index + 1, $safeName ?: 'document', $extension));
        }

        $zip->close();

        $safePatientName = preg_replace('/[^A-Za-z0-9\-_]/', '_', Str::ascii((string) $patient->full_name));
        $safePatientName = trim((string) $safePatientName, '_');
        if ($safePatientName === '') {
            $safePatientName = 'patient_' . $patient->id;
        }
        $today = now()->format('Y-m-d');
        $downloadName = $safePatientName . '_' . $today . '_xray_lab_files.zip';

        return response()->download($tmpZipPath, $downloadName)->deleteFileAfterSend(true);
    }
}
