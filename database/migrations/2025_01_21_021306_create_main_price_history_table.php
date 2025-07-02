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
        Schema::create('main_price_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('main_inventory_id');
            $table->string('change_date');
            $table->string('change_amount');
            $table->decimal('amount',8,2);
            $table->boolean('status');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('main_inventory_id')->references('id')->on('main_inventories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('main_price_history');
    }
};
