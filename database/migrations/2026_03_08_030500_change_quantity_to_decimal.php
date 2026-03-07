<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sale_items', fn(Blueprint $t) => $t->decimal('quantity', 10, 2)->change());
        Schema::table('purchase_items', fn(Blueprint $t) => $t->decimal('quantity', 10, 2)->change());
        Schema::table('purchase_return_items', fn(Blueprint $t) => $t->decimal('quantity', 10, 2)->change());
        Schema::table('inventory_transactions', fn(Blueprint $t) => $t->decimal('quantity', 10, 2)->change());
        Schema::table('stock_adjustments', fn(Blueprint $t) => $t->decimal('quantity', 10, 2)->change());
        Schema::table('batch_stocks', fn(Blueprint $t) => $t->decimal('quantity', 10, 2)->change());
    }

    public function down(): void
    {
        Schema::table('sale_items', fn(Blueprint $t) => $t->integer('quantity')->change());
        Schema::table('purchase_items', fn(Blueprint $t) => $t->integer('quantity')->change());
        Schema::table('purchase_return_items', fn(Blueprint $t) => $t->integer('quantity')->change());
        Schema::table('inventory_transactions', fn(Blueprint $t) => $t->integer('quantity')->change());
        Schema::table('stock_adjustments', fn(Blueprint $t) => $t->integer('quantity')->change());
        Schema::table('batch_stocks', fn(Blueprint $t) => $t->unsignedInteger('quantity')->change());
    }
};
