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
            $table->string('code')->unique(); // Discount code
            $table->integer('discount_percent')->nullable(); // Percentage discount
            $table->integer('discount_fixed_cents')->nullable(); // Fixed amount discount
            $table->integer('max_uses')->nullable(); // Max uses for the discount code
            $table->integer('times_used')->default(0); // How many times the code has been used
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_codes');
    }
};
