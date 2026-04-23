<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table): void {
            $table->foreignId('patient_id')->nullable()->after('id')->constrained('patients')->nullOnDelete();
        });

        $now = now();
        $appointments = DB::table('appointments')
            ->select('id', 'patient_name', 'patient_phone')
            ->whereNull('patient_id')
            ->orderBy('id')
            ->get();

        foreach ($appointments as $appointment) {
            $patientId = DB::table('patients')
                ->where('phone', $appointment->patient_phone)
                ->value('id');

            if (! $patientId) {
                $patientId = DB::table('patients')->insertGetId([
                    'full_name' => $appointment->patient_name ?: 'Patient #' . $appointment->id,
                    'phone' => $appointment->patient_phone,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            DB::table('appointments')
                ->where('id', $appointment->id)
                ->update(['patient_id' => $patientId]);
        }
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('patient_id');
        });
    }
};
