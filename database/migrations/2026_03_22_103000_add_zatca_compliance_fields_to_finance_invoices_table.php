<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finance_invoices', function (Blueprint $table): void {
            $table->uuid('uuid')->nullable()->after('invoice_no');
            $table->string('zatca_status', 40)->default('draft')->after('status');
            $table->string('zatca_xml_path')->nullable()->after('zatca_status');
            $table->string('zatca_invoice_hash', 255)->nullable()->after('zatca_xml_path');
            $table->longText('zatca_signature')->nullable()->after('zatca_invoice_hash');
            $table->longText('zatca_qr_payload')->nullable()->after('zatca_signature');
            $table->json('zatca_validation')->nullable()->after('zatca_qr_payload');
            $table->json('zatca_last_response')->nullable()->after('zatca_validation');
            $table->timestamp('zatca_reported_at')->nullable()->after('zatca_last_response');
            $table->timestamp('zatca_cleared_at')->nullable()->after('zatca_reported_at');
        });
    }

    public function down(): void
    {
        Schema::table('finance_invoices', function (Blueprint $table): void {
            $table->dropColumn([
                'uuid',
                'zatca_status',
                'zatca_xml_path',
                'zatca_invoice_hash',
                'zatca_signature',
                'zatca_qr_payload',
                'zatca_validation',
                'zatca_last_response',
                'zatca_reported_at',
                'zatca_cleared_at',
            ]);
        });
    }
};
