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
        Schema::create('csv_tmp_dealers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('full_address');
                $table->string('address');
                $table->string('city');
                $table->string('state', 10);
                $table->string('zip_code', 10);
                $table->string('phone')->nullable();
                $table->string('dealer_homepage')->nullable();
                $table->boolean('import_status')->default(0);
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('csv_tmp_dealers');
    }
};
