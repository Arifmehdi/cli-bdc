<?php

use App\Models\MainInventory;
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
        Schema::create('additional_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(MainInventory::class)->constrained()->cascadeOnDelete()->nullable();
            // $table->text('dealer_brand_website')->nullable();
            $table->text('detail_url')->nullable();
            $table->text('img_from_url')->nullable();
            $table->text('local_img_url')->nullable();
            $table->text('vehicle_feature_description')->nullable();
            $table->text('vehicle_additional_description')->nullable();
            $table->text('seller_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('additional_inventories');
    }
};
