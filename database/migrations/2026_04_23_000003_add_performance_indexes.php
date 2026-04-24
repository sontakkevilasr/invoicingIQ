<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // invoices — filtered/sorted on almost every query
        Schema::table('invoices', function (Blueprint $table) {
            $table->index('status');
            $table->index('invoice_date');
            $table->index('due_date');
            $table->index('customer_id');          // already FK but add explicit index
            $table->index(['status', 'invoice_date']);  // dashboard + reports filter+sort
            $table->index(['status', 'due_date']);      // overdue detection
        });

        // invoice_items — heavy in GST reports (GROUP BY hsn_sac, gst_rate)
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->index('hsn_sac');
            $table->index('gst_rate');
            $table->index(['invoice_id', 'sort_order']); // ordered relationship load
        });

        // payments — ordered per-invoice relationship
        Schema::table('payments', function (Blueprint $table) {
            $table->index(['invoice_id', 'payment_date']);
        });

        // customers — search scope uses orWhere on all four columns
        Schema::table('customers', function (Blueprint $table) {
            $table->index('name');
            $table->index('gstin');
            $table->index('email');
            $table->index('billing_state');     // stats distinct count
            $table->index(['is_active', 'name']); // dropdown in invoice create
        });

        // items — search + active filter
        Schema::table('items', function (Blueprint $table) {
            $table->index('name');
            $table->index(['is_active', 'name']);
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['invoice_date']);
            $table->dropIndex(['due_date']);
            $table->dropIndex(['customer_id']);
            $table->dropIndex(['status', 'invoice_date']);
            $table->dropIndex(['status', 'due_date']);
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropIndex(['hsn_sac']);
            $table->dropIndex(['gst_rate']);
            $table->dropIndex(['invoice_id', 'sort_order']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['invoice_id', 'payment_date']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['gstin']);
            $table->dropIndex(['email']);
            $table->dropIndex(['billing_state']);
            $table->dropIndex(['is_active', 'name']);
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['is_active', 'name']);
        });
    }
};
