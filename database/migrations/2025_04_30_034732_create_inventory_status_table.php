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
        // this table for only track or update main_inventories after insert or update
        Schema::create('inventory_status', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Vehicle Details
            $table->string('vin', 255)->unique(); // VIN is unique
            $table->string('year', 255)->nullable();
            $table->string('make', 255)->nullable();
            $table->string('model', 255)->nullable();
            $table->string('trim', 255)->nullable();

            // Pricing and Status
            $table->decimal('price', 20, 0)->nullable(); // Matches main_inventories
            $table->unsignedBigInteger('dealer_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('inventory_status', 255)->nullable(); // ok/update/sold

            // Timestamps
            $table->timestamps(); // created_at and updated_at

            // Indexes
            $table->index('vin'); // Faster lookups by VIN
            $table->index('updated_at'); // Optimize 1-minute duplicate checks
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_status');
    }
};
