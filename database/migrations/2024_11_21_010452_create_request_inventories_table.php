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
        Schema::create('request_inventories', function (Blueprint $table) {
            $table->id();
            $table->string('year')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('vin')->unique();
            $table->decimal('price',8,0)->nullable();
            $table->string('exterior_color')->nullable();
            $table->string('transmission')->nullable();
            $table->integer('miles')->nullable();
            $table->string('type')->nullable();
            $table->string('fuel')->nullable();
            $table->string('drive_info')->nullable();
            $table->text('img_from_url')->nullable();
            $table->boolean('status')->default(0)->nullable()->comment('Active 1, Inactive 0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_inventories');
    }
};
