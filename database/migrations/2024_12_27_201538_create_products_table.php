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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('sku')->unique()->nullable();
            $table->unsignedInteger('flavour_id')->nullable()->constrained('flavours')->onDelete('cascade');
            $table->unsignedInteger('packing_id')->nullable()->constrained('packings')->onDelete('cascade');
            $table->unsignedInteger('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('image')->nullable();
            $table->string('barcode')->nullable()->unique();
            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('volume', 8, 2)->nullable();
            $table->decimal('gst', 5, 2)->default(0.00);
            $table->integer('reorder_level')->default(0);
            $table->integer('max_stock_level')->default(0);
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
