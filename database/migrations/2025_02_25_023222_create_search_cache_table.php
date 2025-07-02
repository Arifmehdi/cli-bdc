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
        Schema::create('search_cache', function (Blueprint $table) {
            $table->id();
            $table->string('ip');
            $table->string('search_key')->unique();
            $table->longText('search_results');
            $table->text('filter_options');
            // $table->dateTime('last_updated_at');
            $table->integer('count');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_cache');
    }
};
