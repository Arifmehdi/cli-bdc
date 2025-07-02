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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('blog_category_id')->nullable()->constrained('blog_categories')->onDelete('cascade');
            $table->foreignId('blog_sub_category_id')->nullable()->constrained('blog_sub_categories')->onDelete('cascade');
            $table->string('owner_name')->nullable();
            $table->string('type',100)->nullable();
            $table->text('title');
            $table->text('slug');
            $table->text('sub_title')->nullable();
            $table->text('description')->nullable();
            $table->text('img');
            $table->text('seo_description')->nullable();
            $table->text('seo_keyword')->nullable();
            $table->text('hash_keyword')->nullable();
            $table->integer('status')->default(0);
            $table->integer('blog_status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
