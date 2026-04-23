<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_accounts', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name_ar');
            $table->string('name_en');
            $table->enum('category', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->enum('normal_balance', ['debit', 'credit']);
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('accounting_journals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('finance_entry_id')->nullable()->constrained('finance_entries')->nullOnDelete();
            $table->string('journal_no', 40)->unique();
            $table->date('journal_date');
            $table->string('reference_type', 80)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('description');
            $table->enum('status', ['draft', 'posted', 'void'])->default('posted');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['journal_date', 'status']);
            $table->index(['reference_type', 'reference_id']);
        });

        Schema::create('accounting_journal_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('journal_id')->constrained('accounting_journals')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('accounting_accounts')->restrictOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('description')->nullable();
            $table->decimal('debit', 14, 2)->default(0);
            $table->decimal('credit', 14, 2)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['account_id', 'branch_id']);
        });

        $now = now();
        DB::table('accounting_accounts')->insert([
            ['code' => '1110', 'name_ar' => 'الصندوق النقدي', 'name_en' => 'Cash On Hand', 'category' => 'asset', 'normal_balance' => 'debit', 'is_system' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => '1120', 'name_ar' => 'البنك', 'name_en' => 'Bank', 'category' => 'asset', 'normal_balance' => 'debit', 'is_system' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => '1130', 'name_ar' => 'محافظ إلكترونية', 'name_en' => 'Digital Wallets', 'category' => 'asset', 'normal_balance' => 'debit', 'is_system' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => '1140', 'name_ar' => 'العملاء والذمم المدينة', 'name_en' => 'Accounts Receivable', 'category' => 'asset', 'normal_balance' => 'debit', 'is_system' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => '2110', 'name_ar' => 'الموردون والذمم الدائنة', 'name_en' => 'Accounts Payable', 'category' => 'liability', 'normal_balance' => 'credit', 'is_system' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => '3110', 'name_ar' => 'رأس المال', 'name_en' => 'Owner Equity', 'category' => 'equity', 'normal_balance' => 'credit', 'is_system' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => '4110', 'name_ar' => 'إيرادات الحجوزات', 'name_en' => 'Appointment Revenue', 'category' => 'revenue', 'normal_balance' => 'credit', 'is_system' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => '4120', 'name_ar' => 'إيرادات أخرى', 'name_en' => 'Other Revenue', 'category' => 'revenue', 'normal_balance' => 'credit', 'is_system' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => '5110', 'name_ar' => 'مصروفات تشغيلية', 'name_en' => 'Operating Expenses', 'category' => 'expense', 'normal_balance' => 'debit', 'is_system' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => '5120', 'name_ar' => 'مصروفات عامة', 'name_en' => 'General Expenses', 'category' => 'expense', 'normal_balance' => 'debit', 'is_system' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_journal_lines');
        Schema::dropIfExists('accounting_journals');
        Schema::dropIfExists('accounting_accounts');
    }
};
