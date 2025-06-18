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
            $table->boolean('is_published')->default(false)->index();
            $table->timestamp('publish_at')->nullable();
            $table->boolean('publish_with_event')->default(false);
            $table->timestamps();
        });

        DB::statement("
            ALTER TABLE ticket_types
            ADD CONSTRAINT ticket_types_not_both_check
            CHECK (
                NOT (publish_at IS NOT NULL AND publish_with_event = true)
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE ticket_types DROP CONSTRAINT ticket_types_not_both_check');
        } catch (\Exception $e) {}
        Schema::dropIfExists('ticket_types');
    }
};
