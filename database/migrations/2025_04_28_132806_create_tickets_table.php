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
            $table->foreignId('payment_id')->constrained('payments'); // Link to payment
            $table->foreignId('ticket_type_id')->constrained('ticket_types'); // Link to ticket type
            $table->foreignId('customer_id')->constrained('customers'); // Link to customer
            $table->string('qr_code')->unique(); // Unique QR code for each ticket
            $table->timestamp('scanned_at')->nullable(); // When the ticket was scanned (nullable)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
