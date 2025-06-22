<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('discount_code_order', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_code_id')->constrained()->cascadeOnDelete();
            $table->foreignId('temporary_order_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();

            // Ensure each discount code is only used once per order type
            $table->unique(['discount_code_id', 'temporary_order_id']);
            $table->unique(['discount_code_id', 'order_id']);
        });

        DB::statement("
        ALTER TABLE discount_code_order
        ADD CONSTRAINT discount_code_order_order_xor_temp_order_check
        CHECK (
            (temporary_order_id IS NOT NULL AND order_id IS NULL) OR
            (temporary_order_id IS NULL AND order_id IS NOT NULL)
        )
    ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE discount_code_order DROP CONSTRAINT discount_code_order_order_xor_temp_order_check');
        } catch (\Exception $e) {}
        Schema::dropIfExists('discount_code_order');
    }
};
