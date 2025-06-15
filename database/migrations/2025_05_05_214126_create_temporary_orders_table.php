<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temporary_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events');
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temporary_orders');
    }
};
