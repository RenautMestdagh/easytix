<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('payment_id')->constrained('payments');
            $table->timestamps();

            $table->index(['customer_id', 'payment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
