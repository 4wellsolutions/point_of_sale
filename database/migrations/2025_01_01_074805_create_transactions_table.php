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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('payment_method_id')->constrained('payment_methods')->onDelete('cascade');
            $table->unsignedInteger('vendor_id')->nullable()->constrained('vendors')->onDelete('cascade'); 
            $table->unsignedInteger('customer_id')->nullable()->constrained('customers')->onDelete('cascade'); 
            $table->unsignedInteger('user_id')->nullable()->constrained('users')->onDelete('cascade'); 
            $table->decimal('amount', 10, 2);
            $table->morphs('transactionable'); // Polymorphic relation to either Purchase or Sale
            $table->enum('transaction_type', ['credit', 'debit']);
            $table->dateTime('transaction_date');
            $table->timestamps();
            $table->softDeletes();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
