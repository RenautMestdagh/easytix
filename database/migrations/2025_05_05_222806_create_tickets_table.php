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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temporary_order_id')->nullable()->constrained('temporary_orders');
            $table->foreignId('order_id')->nullable()->constrained('orders');
            $table->foreignId('ticket_type_id')->constrained('ticket_types');
            $table->string('qr_code')->unique();
            $table->timestamp('scanned_at')->nullable();
            $table->timestamps();
        });

        DB::statement("
        ALTER TABLE tickets
        ADD CONSTRAINT tickets_order_or_temp_order_check
        CHECK (
            (order_id IS NOT NULL AND temporary_order_id IS NULL) OR
            (order_id IS NULL AND temporary_order_id IS NOT NULL)
        )
    ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE tickets DROP CONSTRAINT tickets_order_or_temp_order_check');
        Schema::dropIfExists('tickets');
    }
};
