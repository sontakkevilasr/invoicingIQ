<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable()->unique();
            $table->string('hsn_sac', 20)->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['goods', 'service'])->default('service');
            $table->decimal('rate', 12, 2)->default(0);
            $table->decimal('gst_rate', 5, 2)->default(18);
            $table->string('unit', 20)->default('Nos');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
