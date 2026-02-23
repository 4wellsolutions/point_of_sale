<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('sale_id')->nullable()->after('status');
        });

        // Extend the status enum to include 'converted'
        // MySQL approach via DB statement
        \DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending','completed','cancelled','converted') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('sale_id');
        });
        \DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending','completed','cancelled') NOT NULL DEFAULT 'pending'");
    }
};
