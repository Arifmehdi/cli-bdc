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
        Schema::create('csv_location_zips', function (Blueprint $table) {
            $table->id();
            $table->string('src_url');
            $table->string('city');
            $table->string('zip_code', 10);
            $table->string('county');
            $table->string('state', 10);
            $table->string('short_name', 10);
            $table->string('country', 100);
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('combine_tax')->nullable();
            $table->integer('batch_no');
            $table->boolean('import_status')->default(0);
            $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('csv_location_zips');
    }
};
