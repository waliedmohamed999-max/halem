<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['nullable', 'exists:branches,id'],
            'doctor_id' => ['nullable', 'exists:doctors,id'],
            'appointment_id' => ['nullable', 'exists:appointments,id'],
            'visit_date' => ['required', 'date'],
            'visit_time' => ['nullable', 'date_format:H:i'],
            'visit_status' => ['required', 'in:new,follow_up,completed,canceled'],
            'chief_complaint' => ['nullable', 'string'],
            'clinical_findings' => ['nullable', 'string'],
            'diagnosis' => ['nullable', 'string'],
            'treatment_plan' => ['nullable', 'string'],
            'procedure_done' => ['nullable', 'string'],
            'prescription' => ['nullable', 'string'],
            'next_visit_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'attachments' => ['nullable', 'array', 'max:10'],
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
            'delete_attachment_ids' => ['nullable', 'array'],
            'delete_attachment_ids.*' => ['integer'],
        ];
    }
}
