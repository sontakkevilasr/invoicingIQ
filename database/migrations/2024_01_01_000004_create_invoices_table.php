<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->enum('status', ['draft', 'sent', 'paid', 'partial', 'overdue', 'cancelled'])->default('draft');
            $table->date('invoice_date');
            $table->date('due_date')->nullable();

            // Customer snapshot (stored at invoice time)
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name')->nullable();
            $table->string('customer_gstin')->nullable();
            $table->text('customer_billing_address')->nullable();
            $table->string('customer_city')->nullable();
            $table->string('customer_state')->nullable();
            $table->string('customer_state_code', 5)->nullable();

            // Supply details
            $table->string('place_of_supply')->nullable();
            $table->string('place_of_supply_code', 5)->nullable();
            $table->boolean('is_intra_state')->default(true); // true = CGST+SGST, false = IGST

            // Discount at invoice level
            $table->enum('discount_type', ['percent', 'flat'])->default('percent');
            $table->decimal('discount_value', 10, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);

            // Totals
            $table->decimal('sub_total', 12, 2)->default(0);
            $table->decimal('total_cgst', 12, 2)->default(0);
            $table->decimal('total_sgst', 12, 2)->default(0);
            $table->decimal('total_igst', 12, 2)->default(0);
            $table->decimal('total_tax', 12, 2)->default(0);
            $table->decimal('round_off', 8, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->decimal('amount_paid', 12, 2)->default(0);

            // Visible columns config (JSON)
            $table->json('visible_columns')->nullable();

            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
