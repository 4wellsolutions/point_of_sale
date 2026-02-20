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
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('purchase_id')->constrained('purchases')->onDelete('cascade');
            $table->unsignedInteger('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->unsignedInteger('user_id')->constrained('users')->onDelete('cascade');
            $table->string('invoice_no')->unique();
            $table->date('return_date');
            $table->decimal('total_amount', 15, 2)->default(0.00);
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            $table->decimal('net_amount', 15, 2)->default(0.00);
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_returns');
    }
};
