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
        Schema::create('csv_tmp_inventories', function (Blueprint $table) {
            $table->id();
            $table->string('dealer_id')->nullable();
            $table->string('dealer_type')->nullable();
            $table->string('dealer_name')->nullable();
            $table->text('dealer_address')->nullable();
            $table->string('dealer_street')->nullable();
            $table->string('dealer_city')->nullable();
            $table->string('dealer_region')->nullable();
            $table->string('dealer_zip_code')->nullable();
            $table->string('dealer_sales_phone')->nullable();
            $table->float('dealer_rating')->nullable();
            $table->integer('dealer_review')->nullable();
            $table->string('dealer_website')->nullable();
            $table->string('brand_website')->nullable();
            $table->text('seller_note')->nullable();
            $table->string('source_url')->nullable();
            $table->string('titles')->nullable();
            $table->string('trim_name')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('exterior_color')->nullable();
            $table->string('interior_color')->nullable();
            // $table->decimal('price', 10, 2)->nullable();
            $table->string('price')->nullable();
            $table->string('mileage')->nullable();
            $table->string('fuel')->nullable();
            $table->integer('city_mpg')->nullable();
            $table->integer('hwy_mpg')->nullable();
            $table->string('engine')->nullable();
            $table->string('transmission')->nullable();
            $table->year('year')->nullable();
            $table->string('type')->nullable();
            $table->string('stock_number')->nullable();
            $table->string('vin')->nullable();
            $table->string('body_type')->nullable();
            $table->text('feature')->nullable();
            $table->text('options')->nullable();
            $table->string('drive_train')->nullable();
            $table->text('price_history')->nullable();
            $table->string('price_rating')->nullable();
            $table->string('primary_image')->nullable();
            $table->text('all_image')->nullable();
            $table->string('vin_image')->nullable();
            $table->integer('batch_no')->nullable()->index();
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('csv_tmp_inventories');
    }
};
