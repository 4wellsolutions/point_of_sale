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
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_id')->constrained('products')->onDelete('cascade');
            $table->string('batch_no');
            $table->date('purchase_date');
            $table->date('expiry_date')->nullable();
            $table->string('invoice_no');
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint to prevent duplicate batch numbers per product
            $table->unique(['product_id', 'batch_no']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
