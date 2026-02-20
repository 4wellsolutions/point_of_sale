<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Add role_id column to users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        // 2. Create Admin role
        $adminRoleId = DB::table('roles')->insertGetId([
            'name' => 'Admin',
            'is_admin' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Seed all 13 modules
        $modules = [
            ['name' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'order' => 1],
            ['name' => 'products', 'label' => 'Products', 'icon' => 'fas fa-box-open', 'order' => 2],
            ['name' => 'customers', 'label' => 'Customers', 'icon' => 'fas fa-users', 'order' => 3],
            ['name' => 'vendors', 'label' => 'Vendors', 'icon' => 'fas fa-truck', 'order' => 4],
            ['name' => 'purchases', 'label' => 'Purchases', 'icon' => 'fas fa-shopping-cart', 'order' => 5],
            ['name' => 'sales', 'label' => 'Sales', 'icon' => 'fas fa-cash-register', 'order' => 6],
            ['name' => 'payment_methods', 'label' => 'Payment Methods', 'icon' => 'fas fa-wallet', 'order' => 7],
            ['name' => 'transactions', 'label' => 'Transactions', 'icon' => 'fas fa-exchange-alt', 'order' => 8],
            ['name' => 'ledger', 'label' => 'Ledger', 'icon' => 'fas fa-book', 'order' => 9],
            ['name' => 'expenses', 'label' => 'Expenses', 'icon' => 'fas fa-money-bill-wave', 'order' => 10],
            ['name' => 'inventory', 'label' => 'Inventory', 'icon' => 'fas fa-warehouse', 'order' => 11],
            ['name' => 'reports', 'label' => 'Reports', 'icon' => 'fas fa-chart-bar', 'order' => 12],
            ['name' => 'settings', 'label' => 'Settings', 'icon' => 'fas fa-cog', 'order' => 13],
        ];

        $moduleIds = [];
        foreach ($modules as $module) {
            $module['created_at'] = now();
            $module['updated_at'] = now();
            $moduleIds[] = DB::table('modules')->insertGetId($module);
        }

        // 4. Assign all modules to Admin role
        foreach ($moduleIds as $moduleId) {
            DB::table('role_module')->insert([
                'role_id' => $adminRoleId,
                'module_id' => $moduleId,
            ]);
        }

        // 5. Assign Admin role to all existing users
        DB::table('users')->whereNull('role_id')->update(['role_id' => $adminRoleId]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};
