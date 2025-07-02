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
        Schema::create('location_states', function (Blueprint $table) {
            $table->id();
            $table->string('state_name')->unique();
            $table->string('short_name')->unique();
            $table->float('sales_tax', )->nullable()->default(0);
            $table->boolean('status')->default(0);
            $table->boolean('is_read')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_states');
    }
};
