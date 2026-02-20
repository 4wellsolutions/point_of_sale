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
        Schema::create('batch_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('batch_id')->constrained('batches')->onDelete('cascade');
            $table->unsignedInteger('product_id')->constrained('products')->onDelete('cascade');
            $table->unsignedInteger('location_id')->constrained('locations')->onDelete('cascade');
            $table->decimal('purchase_price', 8, 2);
            $table->decimal('sale_price', 8, 2);
            $table->unsignedInteger('quantity');
            $table->date('expiry_date')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            // Unique composite index for batch_id and product_id
            $table->unique(['batch_id', 'product_id']); 
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_stocks');
    }
};
