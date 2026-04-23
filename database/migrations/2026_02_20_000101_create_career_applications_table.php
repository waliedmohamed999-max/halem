<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('career_applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('career_position_id')->nullable()->constrained('career_positions')->nullOnDelete();
            $table->string('full_name');
            $table->string('phone', 50);
            $table->string('email')->nullable();
            $table->string('city')->nullable();
            $table->string('experience_years')->nullable();
            $table->text('cover_letter')->nullable();
            $table->string('cv_file')->nullable();
            $table->string('status')->default('new');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_applications');
    }
};
