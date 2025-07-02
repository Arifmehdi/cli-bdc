<?php

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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('image');
            $table->tinyInteger('status')->default(0);
            $table->string('renew')->nullable();
            $table->string('position')->nullable();
            $table->text('description')->nullable();
            // $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete()->nullable();
            $table->string('user_model')->nullable()->comment('User model class name');
            $table->text('url')->nullable();
            $table->boolean('new_window')->default(0)->comment('Open URL in new window');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
