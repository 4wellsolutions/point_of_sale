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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->unsignedInteger('user_id')->constrained('users')->onDelete('cascade');
            $table->string('invoice_no')->unique();
            $table->date('purchase_date');
            $table->decimal('total_amount', 15, 2)->default(0.00);
            $table->decimal('discount_amount', 15, 2)->default(0.00)->nullable();
            $table->decimal('net_amount', 15, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
