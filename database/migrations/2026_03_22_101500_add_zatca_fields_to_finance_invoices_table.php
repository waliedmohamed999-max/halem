<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finance_invoices', function (Blueprint $table): void {
            $table->string('currency_code', 10)->default('SAR')->after('invoice_no');
            $table->decimal('tax_rate', 5, 2)->default(15)->after('discount');
            $table->enum('invoice_scope', ['simplified', 'standard'])->default('simplified')->after('payment_terms');
            $table->string('reference_number')->nullable()->after('invoice_scope');
            $table->date('supply_date')->nullable()->after('issue_date');
        });
    }

    public function down(): void
    {
        Schema::table('finance_invoices', function (Blueprint $table): void {
            $table->dropColumn(['currency_code', 'tax_rate', 'invoice_scope', 'reference_number', 'supply_date']);
        });
    }
};
