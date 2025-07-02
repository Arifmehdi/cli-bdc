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
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_title')->nullable();
            $table->text('image')->nullable();
            $table->text('fav_image')->nullable();
            $table->text('site_map')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->integer('pagination')->nullable();
            $table->string('separator')->nullable();
            $table->string('timezone')->nullable();
            $table->decimal('apr_rate',8,2)->nullable()->default(0);
            $table->string('language')->nullable();
            $table->text('slider_image')->nullable();
            $table->string('date_formate')->nullable();
            $table->string('time_formate')->nullable();
            $table->string('slider_title');
            $table->string('slider_subtitle');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_settings');
    }
};
