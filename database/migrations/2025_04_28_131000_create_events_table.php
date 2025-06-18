<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // 2025_04_28_131000_create_events_table.php
    // 2025_04_28_131000_create_events_table.php
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('uniqid')->unique();
            $table->foreignId('organization_id')->constrained('organizations');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->dateTime('date')->index();
            $table->string('event_image')->nullable(); // For line-up, date, location display
            $table->string('header_image')->nullable(); // For purchasing page header
            $table->string('background_image')->nullable(); // Event background image
            $table->integer('max_capacity')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->timestamp('publish_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("
        ALTER TABLE events
        ADD CONSTRAINT events_publish_before_date_check
        CHECK (publish_at IS NULL OR publish_at <= date)
    ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE events DROP CONSTRAINT events_publish_before_date_check');
        } catch (\Exception $e) {}
        Schema::dropIfExists('events');
    }
};
