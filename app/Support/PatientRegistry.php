<?php

namespace App\Support;

use App\Models\Appointment;
use App\Models\Patient;

class PatientRegistry
{
    public function syncAppointment(Appointment $appointment, array $intakeData = []): Patient
    {
        $patient = Patient::query()->firstOrCreate(
            ['phone' => trim((string) $appointment->patient_phone)],
            ['full_name' => trim((string) $appointment->patient_name)]
        );

        $updates = [];

        if (trim((string) $appointment->patient_name) !== '' && $patient->full_name !== trim((string) $appointment->patient_name)) {
            $updates['full_name'] = trim((string) $appointment->patient_name);
        }

        if (trim((string) $appointment->patient_phone) !== '' && $patient->phone !== trim((string) $appointment->patient_phone)) {
            $updates['phone'] = trim((string) $appointment->patient_phone);
        }

        if ($updates !== []) {
            $patient->update($updates);
        }

        $patientUpdates = [];
        $fillableFields = [
            'email',
            'address',
            'date_of_birth',
            'gender',
            'occupation',
            'marital_status',
            'national_id',
            'blood_type',
            'emergency_contact_name',
            'emergency_contact_phone',
            'insurance_company',
            'insurance_number',
            'smoking_status',
            'allergies',
            'chronic_diseases',
            'current_medications',
            'previous_surgeries',
        ];

        foreach ($fillableFields as $field) {
            if (! array_key_exists($field, $intakeData)) {
                continue;
            }

            $value = $intakeData[$field];

            if ($value === null) {
                continue;
            }

            if (is_string($value)) {
                $value = trim($value);
                if ($value === '') {
                    continue;
                }
            }

            $patientUpdates[$field] = $value;
        }

        if (array_key_exists('notes', $intakeData) && is_string($intakeData['notes'])) {
            $intakeNotes = trim($intakeData['notes']);
            if ($intakeNotes !== '') {
                $existingNotes = trim((string) $patient->notes);
                if ($existingNotes === '') {
                    $patientUpdates['notes'] = $intakeNotes;
                } elseif (! str_contains($existingNotes, $intakeNotes)) {
                    $patientUpdates['notes'] = $existingNotes . "\n\n" . $intakeNotes;
                }
            }
        }

        if ($patientUpdates !== []) {
            $patient->update($patientUpdates);
        }

        if ((int) $appointment->patient_id !== (int) $patient->id) {
            $appointment->patient()->associate($patient);
            $appointment->save();
        }

        return $patient;
    }
}
