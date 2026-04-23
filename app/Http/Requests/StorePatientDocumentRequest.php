<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_visit_id' => ['nullable', 'exists:patient_visits,id'],
            'document_type' => ['required', 'in:xray,lab,report,prescription,other'],
            'title' => ['required', 'string', 'max:255'],
            'document_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:3000'],
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx', 'max:12288'],
        ];
    }
}

