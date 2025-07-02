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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('fname')->nullable();
            $table->string('lname')->nullable();
            $table->string('name')->nullable();
            $table->string('dealer_id', 255)->nullable();
            $table->string('state', 255)->nullable();
            $table->string('dealer_full_address', 255)->nullable();
            $table->text('dealer_iframe_map')->nullable();
            $table->string('hashkey')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('gender')->nullable();
            $table->string('city')->nullable();
            $table->string('zip')->nullable();
            $table->string('country')->nullable();
            $table->string('facebook')->nullable();
            $table->string('google')->nullable();
            $table->string('twitter')->nullable();
            $table->string('pinterest')->nullable();
            $table->string('about_me')->nullable();
            $table->string('image')->nullable();
            $table->string('salesperson')->nullable();
            $table->string('phone_type')->nullable();
            $table->string('contact_type')->nullable();
            $table->string('password_reset_otp')->nullable();
            $table->tinyInteger('role_id')->default(0); // admin = 1, normal user = 0, dealer = 2
            $table->integer('membership_id')->default(1);
            $table->timestamp('email_verified_at')->nullable();
            $table->tinyInteger('is_verify_email')->default(0);
            $table->string('password')->nullable();
            $table->string('adf_email')->nullable();
            $table->tinyInteger('status')->default(1)->comment('A 1, I 0');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
