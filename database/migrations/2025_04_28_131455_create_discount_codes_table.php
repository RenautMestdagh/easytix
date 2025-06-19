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
        Schema::create('discount_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->nullable()->constrained('events'); // Link to event
            $table->foreignId('organization_id')->constrained('organizations'); // Link to organization in case event is null
            $table->string('code'); // Discount code
            $table->integer('discount_percent')->nullable(); // Percentage discount
            $table->integer('discount_fixed_cents')->nullable(); // Fixed amount discount
            $table->integer('max_uses')->nullable(); // Max uses for the discount code
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['code', 'organization_id']);
        });

        DB::statement("
        ALTER TABLE discount_codes
        ADD CONSTRAINT discount_percent_or_discount_fixed_cents_check
        CHECK (
            (discount_percent IS NOT NULL AND discount_fixed_cents IS NULL) OR
            (discount_percent IS NULL AND discount_fixed_cents IS NOT NULL)
        )
    ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE discount_codes DROP CONSTRAINT discount_percent_or_discount_fixed_cents_check');
        } catch (\Exception $e) {}
        Schema::dropIfExists('discount_codes');
    }
};
