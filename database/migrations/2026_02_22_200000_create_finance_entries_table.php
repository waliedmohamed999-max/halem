<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('finance_entries', function (Blueprint $table) {
            $table->id();
            $table->enum('entry_type', ['income', 'expense']);
            $table->enum('entry_kind', ['appointment', 'incoming_invoice', 'outgoing_invoice', 'expense', 'other'])->default('other');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('invoice_number')->nullable();
            $table->string('counterparty')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->date('entry_date');
            $table->string('payment_method', 100)->nullable();
            $table->text('notes')->nullable();
            $table->enum('record_status', ['posted', 'void'])->default('posted');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['entry_type', 'entry_kind']);
            $table->index(['entry_date', 'record_status']);
            $table->unique(['appointment_id', 'entry_type', 'entry_kind'], 'finance_appointment_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_entries');
    }
};

