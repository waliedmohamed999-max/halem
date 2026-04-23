<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientDocumentRequest;
use App\Models\Patient;
use App\Models\PatientDocument;
use Illuminate\Support\Facades\Storage;

class PatientDocumentController extends Controller
{
    public function store(StorePatientDocumentRequest $request, int $patient)
    {
        $patient = Patient::query()->findOrFail($patient);
        $data = $request->validated();

        if (! empty($data['patient_visit_id'])) {
            $belongsToPatient = $patient->visits()->whereKey((int) $data['patient_visit_id'])->exists();
            if (! $belongsToPatient) {
                return back()
                    ->withInput()
                    ->withErrors(['patient_visit_id' => 'The selected visit does not belong to this patient.']);
            }
        }

        $file = $request->file('file');
        $path = $file->store('patient-documents', 'public');

        $patient->documents()->create([
            'patient_visit_id' => $data['patient_visit_id'] ?? null,
            'document_type' => $data['document_type'],
            'title' => $data['title'],
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'document_date' => $data['document_date'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return back()->with('success', 'Document uploaded successfully');
    }

    public function destroy(int $patient, int $document)
    {
        $patient = Patient::query()->findOrFail($patient);
        $document = $patient->documents()->findOrFail($document);

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return back()->with('success', 'Document deleted successfully');
    }
}
