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
        
        Schema::create('sold_inventories', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('inventory_id')->constrained('inventories')->onDelete('cascade');
            $table->foreignId('deal_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('zip_code', 50)->nullable();
            $table->text('latitude')->nullable();
            $table->text('longitude')->nullable();
            $table->foreignId('vehicle_make_id')->constrained('vehicle_makes');
            $table->string('title');
            $table->string('year')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('vin')->unique();
            $table->decimal('price', 8, 0)->nullable();
            $table->string('price_rating')->nullable();
            $table->integer('miles')->nullable();
            $table->string('type')->nullable();
            $table->string('trim')->nullable();
            $table->string('stock')->nullable();
            $table->string('transmission')->nullable();
            $table->text('engine_details')->nullable();
            $table->string('fuel')->nullable();
            $table->string('drive_info')->nullable();
            $table->string('mpg')->nullable();
            $table->string('mpg_city')->nullable();
            $table->string('mpg_highway')->nullable();
            $table->string('exterior_color')->nullable();
            $table->string('interior_color')->nullable();
            $table->string('created_date', 100)->nullable();
            $table->string('stock_date_formated')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('payment_price')->nullable();
            $table->string('body_formated')->nullable();
            $table->boolean('is_feature')->nullable();
            $table->tinyInteger('is_lead_feature')->default(0);
            $table->tinyInteger('package')->default(0);
            $table->timestamp('payment_date')->nullable();
            $table->timestamp('active_till')->nullable();
            $table->timestamp('featured_till')->nullable();
            $table->tinyInteger('is_visibility')->default(1);
            $table->integer('batch_no')->nullable();
            $table->tinyInteger('status')->default(0); // Active 1, Inactive 0
            $table->integer('image_count')->nullable();
            $table->string('inventory_status')->nullable();
            $table->text('detail_url')->nullable();
            $table->boolean('img_exist')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sold_inventories');
    }
};
