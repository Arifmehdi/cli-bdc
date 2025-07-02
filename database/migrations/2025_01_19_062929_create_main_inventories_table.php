<?php

use App\Models\VehicleMake;
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
        Schema::create('main_inventories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deal_id')->nullable();
            $table->integer('zip_code')->nullable();
            $table->text('latitude')->nullable();
            $table->text('longitude')->nullable();
            $table->foreignIdFor(VehicleMake::class)->constrained()->cascadeOnDelete()->nullable();
            $table->string('title');
            $table->string('year')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('vin')->unique();
            $table->decimal('price',8,0)->nullable();
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
            $table->string('created_date',100)->nullable();
            $table->string('stock_date_formated')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('payment_price')->nullable();
            $table->string('body_formated')->nullable();
            $table->boolean('is_feature')->nullable();
            $table->tinyInteger('is_lead_feature')->default('0');
            $table->tinyInteger('package')->default(0);
            $table->timestamp('payment_date')->nullable();
            $table->timestamp('active_till')->nullable();  //use for listing page
            $table->timestamp('featured_till')->nullable(); //use for listing page
            $table->tinyInteger('is_visibility')->default(1);
            $table->integer('batch_no')->nullable();
            $table->boolean('status')->default(0)->nullable()->comment('Active 1, Inactive 0');
            $table->integer('image_count')->nullable();
            $table->string('inventory_status')->nullable();
            $table->timestamps();
            $table->softDeletes();
            // Foreign key constraint for deal_id referencing users.id
            $table->foreign('deal_id')->references('id')->on('users')->cascadeOnDelete();
            // $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('main_inventories');
    }
};
