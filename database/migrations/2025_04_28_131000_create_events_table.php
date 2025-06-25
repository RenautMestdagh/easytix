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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('uniqid')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('subdomain')->nullable();
            $table->foreignId('venue_id')->nullable()->constrained('venues')->onDelete('set null');
            $table->boolean('use_venue_capacity')->default(false);
            $table->unsignedInteger('max_capacity')->nullable();
            $table->dateTime('date')->index();
            $table->string('event_image')->nullable(); // For line-up, date, location display
            $table->string('header_image')->nullable(); // For purchasing page header
            $table->string('background_image')->nullable(); // Event background image
            $table->boolean('is_published')->default(false)->index();
            $table->timestamp('publish_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['subdomain', 'organization_id']);
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
