<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table): void {
            $table->string('address')->nullable()->after('email');
            $table->string('occupation', 120)->nullable()->after('gender');
            $table->string('marital_status', 40)->nullable()->after('occupation');
            $table->string('emergency_contact_name', 255)->nullable()->after('blood_type');
            $table->string('emergency_contact_phone', 30)->nullable()->after('emergency_contact_name');
            $table->string('insurance_company', 150)->nullable()->after('emergency_contact_phone');
            $table->string('insurance_number', 80)->nullable()->after('insurance_company');
            $table->string('smoking_status', 50)->nullable()->after('insurance_number');
            $table->text('previous_surgeries')->nullable()->after('current_medications');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table): void {
            $table->dropColumn([
                'address',
                'occupation',
                'marital_status',
                'emergency_contact_name',
                'emergency_contact_phone',
                'insurance_company',
                'insurance_number',
                'smoking_status',
                'previous_surgeries',
            ]);
        });
    }
};

