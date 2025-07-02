<?php

use App\Models\Lead;
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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->foreignIdFor(Lead::class)->nullable()->constrained()->cascadeOnDelete();
            $table->string('generated_id')->nullable();
            $table->integer('inventory_id')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('is_cart')->default(0);
            $table->double('discount')->nullable();
            $table->double('price')->nullable();
            $table->string('package')->nullable();
            $table->double('cost')->nullable();
            $table->double('subtotal')->nullable();
            $table->double('total')->nullable();
            $table->integer('total_count')->nullable();
            $table->string('type')->nullable();
            $table->tinyInteger('is_lead_feature')->default(0);
            $table->integer('old_membership')->nullable();
            $table->timestamp('create_date')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
