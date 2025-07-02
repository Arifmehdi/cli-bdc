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
        Schema::create('cache_commands', function (Blueprint $table) {
            // $table->id();
            // $table->string('name');
            // $table->string('command');
            // $table->string('city')->nullable();
            // $table->string('state');
            // $table->string('county')->nullable();
            // $table->text('description')->nullable();
            // $table->string('cache_file')->nullable(); // Path to cache file if applicable
            // $table->boolean('status')->default(1);
            // $table->timestamps();

            $table->id();
            $table->string('name');
            $table->string('command')->unique();
            $table->string('city')->nullable();
            $table->string('county')->nullable();
            $table->string('state');
            $table->text('zip_codes'); // Changed from description to zip_codes
            $table->string('cache_file');
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache_commands');
    }
};
