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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            
            $table->string('invoice_no')->nullable()->unique();
            $table->date('sale_date');

            $table->decimal('total_amount', 10, 2);
            $table->decimal('discount_amount', 12, 2);
            $table->decimal('net_amount', 10, 2)->nullable();
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
        Schema::dropIfExists('sales');
    }
};
