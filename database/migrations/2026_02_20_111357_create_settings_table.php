<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed default settings
        DB::table('settings')->insert([
            ['key' => 'app_name', 'value' => 'POS System', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'currency_symbol', 'value' => '$', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'currency_code', 'value' => 'USD', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'business_name', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'business_address', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'business_phone', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'business_email', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'tax_number', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
