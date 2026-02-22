<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('opening_balance', 15, 2)->default(0)->after('type_id');
            $table->enum('opening_balance_type', ['debit', 'credit'])->default('debit')->after('opening_balance');
            // debit = customer owes us (receivable), credit = we owe customer
        });
        Schema::table('vendors', function (Blueprint $table) {
            $table->decimal('opening_balance', 15, 2)->default(0)->after('type_id');
            $table->enum('opening_balance_type', ['debit', 'credit'])->default('credit')->after('opening_balance');
            // credit = we owe vendor (payable), debit = vendor owes us
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['opening_balance', 'opening_balance_type']);
        });
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['opening_balance', 'opening_balance_type']);
        });
    }
};
