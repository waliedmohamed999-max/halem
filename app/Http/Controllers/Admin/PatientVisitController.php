<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientVisitRequest;
use App\Http\Requests\UpdatePatientVisitRequest;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\PatientVisit;
use Illuminate\Support\Facades\Storage;

class PatientVisitController extends Controller
{
    public function create(int $patient)
    {
        $patient = Patient::query()->findOrFail($patient);
        $branches = Branch::query()->orderBy('sort_order')->get();
        $doctors = Doctor::query()->where('is_active', true)->orderBy('sort_order')->get();
        $appointments = Appointment::query()
            ->where('patient_phone', $patient->phone)
            ->latest('preferred_date')
            ->take(30)
            ->get();

        return view('admin.patients.visits.create', compact('patient', 'branches', 'doctors', 'appointments'));
    }

    public function store(StorePatientVisitRequest $request, int $patient)
    {
        $patient = Patient::query()->findOrFail($patient);
        $data = $request->validated();
        $data['patient_id'] = $patient->id;

        $visit = PatientVisit::query()->create($data);
        $this->syncAttachments($request, $visit);
        $patient->update(['last_visit_at' => $visit->visit_date]);

        return redirect()
            ->route('admin.patients.show', [app()->getLocale(), $patient->id])
            ->with('success', 'Visit added successfully');
    }

    public function edit(int $patient, int $visit)
    {
        $patient = Patient::query()->findOrFail($patient);
        $visit = PatientVisit::query()
            ->where('patient_id', $patient->id)
            ->with('attachments')
            ->findOrFail($visit);
        $branches = Branch::query()->orderBy('sort_order')->get();
        $doctors = Doctor::query()->where('is_active', true)->orderBy('sort_order')->get();
        $appointments = Appointment::query()
            ->where('patient_phone', $patient->phone)
            ->latest('preferred_date')
            ->take(30)
            ->get();

        return view('admin.patients.visits.edit', compact('patient', 'visit', 'branches', 'doctors', 'appointments'));
    }

    public function update(UpdatePatientVisitRequest $request, int $patient, int $visit)
    {
        $patient = Patient::query()->findOrFail($patient);
        $visit = PatientVisit::query()->where('patient_id', $patient->id)->findOrFail($visit);
        $visit->update($request->validated());
        $this->syncAttachments($request, $visit);

        $latestVisit = PatientVisit::query()
            ->where('patient_id', $patient->id)
            ->latest('visit_date')
            ->first();

        $patient->update(['last_visit_at' => $latestVisit?->visit_date]);

        return redirect()
            ->route('admin.patients.show', [app()->getLocale(), $patient->id])
            ->with('success', 'Visit updated successfully');
    }

    public function destroy(int $patient, int $visit)
    {
        $patient = Patient::query()->findOrFail($patient);
        $visit = PatientVisit::query()->where('patient_id', $patient->id)->findOrFail($visit);
        $visit->delete();

        $latestVisit = PatientVisit::query()
            ->where('patient_id', $patient->id)
            ->latest('visit_date')
            ->first();

        $patient->update(['last_visit_at' => $latestVisit?->visit_date]);

        return redirect()
            ->route('admin.patients.show', [app()->getLocale(), $patient->id])
            ->with('success', 'Visit deleted successfully');
    }

    public function destroyAttachment(int $patient, int $visit, int $attachment)
    {
        $visit = PatientVisit::query()->where('patient_id', $patient)->findOrFail($visit);
        $attachment = $visit->attachments()->findOrFail($attachment);

        if ($attachment->file_path && Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();

        return back()->with('success', 'Attachment deleted successfully');
    }

    private function syncAttachments(StorePatientVisitRequest|UpdatePatientVisitRequest $request, PatientVisit $visit): void
    {
        $deleteIds = collect($request->input('delete_attachment_ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->all();

        if (! empty($deleteIds)) {
            $toDelete = $visit->attachments()->whereIn('id', $deleteIds)->get();
            foreach ($toDelete as $attachment) {
                if ($attachment->file_path && Storage::disk('public')->exists($attachment->file_path)) {
                    Storage::disk('public')->delete($attachment->file_path);
                }
                $attachment->delete();
            }
        }

        if (! $request->hasFile('attachments')) {
            return;
        }

        foreach ($request->file('attachments', []) as $file) {
            $path = $file->store('patient-visits', 'public');
            $visit->attachments()->create([
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }
    }
}
