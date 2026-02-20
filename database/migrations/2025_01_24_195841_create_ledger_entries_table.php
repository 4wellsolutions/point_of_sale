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
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            // Polymorphic relationship to Customers and Vendors
            $table->morphs('ledgerable'); // Adds ledgerable_id and ledgerable_type columns

            // Foreign key to transactions table
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->foreign('transaction_id')
                  ->references('id')
                  ->on('transactions')
                  ->onDelete('set null');
            
            $table->unsignedInteger('user_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('date');
            $table->text('description')->nullable();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->decimal('balance', 15, 2);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
    }
};
