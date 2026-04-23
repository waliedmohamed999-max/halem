<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
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
            'notes' => ['nullable', 'string'],
        ];
    }
}
