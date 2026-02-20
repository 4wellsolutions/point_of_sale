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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_id')->constrained('products')->onDelete('cascade');
            $table->unsignedInteger('batch_id')->constrained('batches')->onDelete('cascade');
            $table->unsignedInteger('location_id')->constrained('locations')->onDelete('cascade');
            $table->enum('type', ['increase', 'decrease']);
            $table->enum('category', ['adjustment', 'damage', 'loss'])->default('adjustment');
            $table->integer('quantity');
            $table->string('reason');
            $table->date('date');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
