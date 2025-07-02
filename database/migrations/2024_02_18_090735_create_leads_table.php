<?php

use App\Models\Inventory;
use App\Models\User;
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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete()->nullable();
            $table->foreignIdFor(Inventory::class)->constrained()->cascadeOnDelete()->nullable();
            $table->timestamp('date')->nullable();
            $table->string('description');
            $table->string('year')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('mileage')->nullable();
            $table->string('color')->nullable();
            $table->string('vin')->nullable();
            $table->string('source')->nullable();
            $table->string('lead_type')->nullable();
            $table->bigInteger('dealer_id')->nullable();
            $table->tinyInteger('invoice_status')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
