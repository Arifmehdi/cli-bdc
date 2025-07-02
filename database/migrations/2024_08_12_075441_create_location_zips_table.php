<?php

use App\Models\LocationCity;
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
        Schema::create('location_zips', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(LocationCity::class)->constrained()->cascadeOnDelete()->nullable();
            $table->string('county');
            $table->decimal('latitude', 10, 5)->nullable();
            $table->decimal('longitude', 11, 5)->nullable();
            $table->string('zip_code');
            $table->integer('sales_tax')->default(0);
            $table->text('src_url')->nullable();
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
        Schema::dropIfExists('location_zips');
    }
};
