<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // 2025_04_28_131945_create_ticket_types_table.php
    public function up(): void
    {
        Schema::create('ticket_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events');
            $table->string('name');
            $table->integer('price_cents');
            $table->integer('available_quantity')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('publish_at')->nullable();
            $table->boolean('publish_with_event')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['event_id']);
            $table->index(['is_published']);
            $table->index(['publish_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_types');
    }
};
