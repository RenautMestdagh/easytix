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
            $table->boolean('is_confirmed')->default(false);
            $table->timestamps();

            $table->index(['expires_at', 'is_confirmed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temporary_orders');
    }
};
