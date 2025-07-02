<?php

use App\Models\LocationState;
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
        Schema::create('location_cities', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(LocationState::class)->constrained()->cascadeOnDelete()->nullable();
            $table->string('city_name');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->float('sales_tax')->nullable()->default(0);
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
        Schema::dropIfExists('location_cities');
    }
};
