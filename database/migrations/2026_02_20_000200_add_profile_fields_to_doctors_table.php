<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table): void {
            $table->text('expertise_ar')->nullable()->after('bio_en');
            $table->text('expertise_en')->nullable()->after('expertise_ar');
            $table->text('booking_method_ar')->nullable()->after('expertise_en');
            $table->text('booking_method_en')->nullable()->after('booking_method_ar');
        });
    }

    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table): void {
            $table->dropColumn(['expertise_ar', 'expertise_en', 'booking_method_ar', 'booking_method_en']);
        });
    }
};
