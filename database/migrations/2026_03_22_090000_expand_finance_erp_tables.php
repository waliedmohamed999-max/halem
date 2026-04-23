<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_cost_centers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code')->unique();
            $table->string('name_ar');
            $table->string('name_en');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('finance_parties', function (Blueprint $table): void {
            $table->id();
            $table->enum('party_type', ['customer', 'supplier', 'both'])->default('customer');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('address')->nullable();
            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('finance_invoices', function (Blueprint $table): void {
            $table->id();
            $table->enum('invoice_type', ['customer', 'supplier']);
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cost_center_id')->nullable()->constrained('finance_cost_centers')->nullOnDelete();
            $table->foreignId('party_id')->constrained('finance_parties')->restrictOnDelete();
            $table->foreignId('finance_entry_id')->nullable()->constrained('finance_entries')->nullOnDelete();
            $table->string('invoice_no')->unique();
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->enum('payment_terms', ['cash', 'credit'])->default('credit');
            $table->enum('status', ['draft', 'issued', 'partially_paid', 'paid', 'cancelled'])->default('issued');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('balance_due', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('finance_invoice_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('invoice_id')->constrained('finance_invoices')->cascadeOnDelete();
            $table->string('description');
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('line_total', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('finance_vouchers', function (Blueprint $table): void {
            $table->id();
            $table->enum('voucher_type', ['receipt', 'payment']);
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cost_center_id')->nullable()->constrained('finance_cost_centers')->nullOnDelete();
            $table->foreignId('party_id')->nullable()->constrained('finance_parties')->nullOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained('finance_invoices')->nullOnDelete();
            $table->foreignId('finance_entry_id')->nullable()->constrained('finance_entries')->nullOnDelete();
            $table->string('voucher_no')->unique();
            $table->date('voucher_date');
            $table->string('payment_method')->nullable();
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['posted', 'void'])->default('posted');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('accounting_period_closings', function (Blueprint $table): void {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->string('period_key')->unique();
            $table->enum('status', ['closed', 'reopened'])->default('closed');
            $table->decimal('income_total', 12, 2)->default(0);
            $table->decimal('expense_total', 12, 2)->default(0);
            $table->decimal('net_profit', 12, 2)->default(0);
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_warehouses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code')->unique();
            $table->string('name_ar');
            $table->string('name_en');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('warehouse_id')->nullable()->constrained('inventory_warehouses')->nullOnDelete();
            $table->string('item_code')->unique();
            $table->string('name_ar');
            $table->string('name_en');
            $table->enum('category', ['tool', 'supply', 'medicine', 'other'])->default('supply');
            $table->string('unit')->default('unit');
            $table->decimal('min_stock', 12, 2)->default(0);
            $table->decimal('current_stock', 12, 2)->default(0);
            $table->decimal('average_cost', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('inventory_warehouses')->restrictOnDelete();
            $table->foreignId('item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('movement_type', ['receipt', 'issue', 'adjustment_in', 'adjustment_out']);
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->decimal('total_cost', 12, 2)->default(0);
            $table->date('movement_date');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('finance_entries', function (Blueprint $table): void {
            $table->foreignId('party_id')->nullable()->after('branch_id')->constrained('finance_parties')->nullOnDelete();
            $table->foreignId('cost_center_id')->nullable()->after('party_id')->constrained('finance_cost_centers')->nullOnDelete();
            $table->string('ledger_context')->nullable()->after('entry_kind');
            $table->string('source_type')->nullable()->after('ledger_context');
            $table->unsignedBigInteger('source_id')->nullable()->after('source_type');
            $table->json('meta')->nullable()->after('notes');
            $table->index(['source_type', 'source_id']);
        });

        Schema::table('accounting_journals', function (Blueprint $table): void {
            $table->foreignId('party_id')->nullable()->after('finance_entry_id')->constrained('finance_parties')->nullOnDelete();
            $table->foreignId('cost_center_id')->nullable()->after('party_id')->constrained('finance_cost_centers')->nullOnDelete();
        });

        Schema::table('accounting_journal_lines', function (Blueprint $table): void {
            $table->foreignId('party_id')->nullable()->after('branch_id')->constrained('finance_parties')->nullOnDelete();
            $table->foreignId('cost_center_id')->nullable()->after('party_id')->constrained('finance_cost_centers')->nullOnDelete();
        });

        $this->seedSupportData();
    }

    public function down(): void
    {
        Schema::table('accounting_journal_lines', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('cost_center_id');
            $table->dropConstrainedForeignId('party_id');
        });

        Schema::table('accounting_journals', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('cost_center_id');
            $table->dropConstrainedForeignId('party_id');
        });

        Schema::table('finance_entries', function (Blueprint $table): void {
            $table->dropIndex(['source_type', 'source_id']);
            $table->dropConstrainedForeignId('cost_center_id');
            $table->dropConstrainedForeignId('party_id');
            $table->dropColumn(['ledger_context', 'source_type', 'source_id', 'meta']);
        });

        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('inventory_warehouses');
        Schema::dropIfExists('accounting_period_closings');
        Schema::dropIfExists('finance_vouchers');
        Schema::dropIfExists('finance_invoice_items');
        Schema::dropIfExists('finance_invoices');
        Schema::dropIfExists('finance_parties');
        Schema::dropIfExists('finance_cost_centers');
    }

    private function seedSupportData(): void
    {
        $branches = DB::table('branches')->select('id', 'name_ar', 'name_en')->get();

        foreach ($branches as $branch) {
            DB::table('finance_cost_centers')->updateOrInsert(
                ['code' => 'BR-' . str_pad((string) $branch->id, 3, '0', STR_PAD_LEFT)],
                [
                    'branch_id' => $branch->id,
                    'name_ar' => 'مركز تكلفة ' . ($branch->name_ar ?: ('فرع ' . $branch->id)),
                    'name_en' => 'Cost Center ' . ($branch->name_en ?: ('Branch ' . $branch->id)),
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            DB::table('inventory_warehouses')->updateOrInsert(
                ['code' => 'WH-' . str_pad((string) $branch->id, 3, '0', STR_PAD_LEFT)],
                [
                    'branch_id' => $branch->id,
                    'name_ar' => 'مستودع ' . ($branch->name_ar ?: ('فرع ' . $branch->id)),
                    'name_en' => ($branch->name_en ?: ('Branch ' . $branch->id)) . ' Warehouse',
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        foreach ([
            ['code' => '1150', 'name_ar' => 'المخزون', 'name_en' => 'Inventory', 'category' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '5115', 'name_ar' => 'تكلفة المستلزمات الطبية', 'name_en' => 'Medical Supplies Expense', 'category' => 'expense', 'normal_balance' => 'debit'],
        ] as $account) {
            DB::table('accounting_accounts')->updateOrInsert(
                ['code' => $account['code']],
                $account + ['is_system' => true, 'is_active' => true, 'notes' => null]
            );
        }
    }
};
