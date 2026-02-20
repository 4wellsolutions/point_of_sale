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
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('sale_id')->constrained('sales')->onDelete('cascade');
            $table->unsignedBigInteger('product_id')->constrained('products')->onDelete('cascade');
            $table->unsignedBigInteger('location_id')->constrained('locations')->onDelete('cascade')->nullable(); 
            $table->unsignedBigInteger('batch_id')->constrained('batches')->onDelete('cascade')->nullable(); 
            
            $table->integer('quantity');
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('sale_price', 10, 2);
            $table->decimal('total_amount', 10, 2);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
