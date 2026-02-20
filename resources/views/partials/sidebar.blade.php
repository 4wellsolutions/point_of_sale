<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="brand-link" style="text-align:center; padding:10px 5px;">
        <span class="brand-text font-weight-bold"
            style="font-size:16px;">{{ setting('business_name', setting('app_name', 'POS System')) }}</span>
        @if(setting('business_name') && setting('app_name') && setting('business_name') !== setting('app_name'))
            <br><small class="text-muted" style="font-size:11px;">{{ setting('app_name') }}</small>
        @endif
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('home') }}"
                        class="nav-link {{ request()->is('home') || request()->is('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- ── PRODUCTS ── -->
                <li
                    class="nav-item has-treeview {{ request()->is('products*') || request()->is('categories*') || request()->is('flavours*') || request()->is('packings*') || request()->is('locations*') || request()->is('types*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('products*') || request()->is('categories*') || request()->is('flavours*') || request()->is('packings*') || request()->is('locations*') || request()->is('types*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box-open"></i>
                        <p>Products <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('products.index') }}"
                                class="nav-link {{ request()->routeIs('products.index') || request()->routeIs('products.show') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Products</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('products.create') }}"
                                class="nav-link {{ request()->routeIs('products.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Product</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('categories.index') }}"
                                class="nav-link {{ request()->is('categories*') ? 'active' : '' }}">
                                <i class="fas fa-tags nav-icon"></i>
                                <p>Categories</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('flavours.index') }}"
                                class="nav-link {{ request()->is('flavours*') ? 'active' : '' }}">
                                <i class="fas fa-lemon nav-icon"></i>
                                <p>Flavours</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('packings.index') }}"
                                class="nav-link {{ request()->is('packings*') ? 'active' : '' }}">
                                <i class="fas fa-box nav-icon"></i>
                                <p>Packing</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('locations.index') }}"
                                class="nav-link {{ request()->is('locations*') ? 'active' : '' }}">
                                <i class="fas fa-map-marker-alt nav-icon"></i>
                                <p>Locations</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('types.index') }}"
                                class="nav-link {{ request()->is('types*') ? 'active' : '' }}">
                                <i class="fas fa-list nav-icon"></i>
                                <p>Types</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- ── PEOPLE ── -->
                <li class="sidebar-section-header">People</li>

                <!-- Customers -->
                <li class="nav-item has-treeview {{ request()->is('customers*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('customers*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Customers <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('customers.index') }}"
                                class="nav-link {{ request()->routeIs('customers.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Customers</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('customers.create') }}"
                                class="nav-link {{ request()->routeIs('customers.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Customer</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Vendors -->
                <li class="nav-item has-treeview {{ request()->is('vendors*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('vendors*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-truck"></i>
                        <p>Vendors <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('vendors.index') }}"
                                class="nav-link {{ request()->routeIs('vendors.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Vendors</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('vendors.create') }}"
                                class="nav-link {{ request()->routeIs('vendors.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Vendor</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- ── TRANSACTIONS ── -->
                <li class="sidebar-section-header">Transactions</li>

                <!-- Purchases -->
                <li
                    class="nav-item has-treeview {{ request()->is('purchases*') || request()->is('purchase-returns*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('purchases*') || request()->is('purchase-returns*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Purchases <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('purchases.index') }}"
                                class="nav-link {{ request()->routeIs('purchases.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Purchases</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('purchases.create') }}"
                                class="nav-link {{ request()->routeIs('purchases.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>New Purchase</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('purchase-returns.index') }}"
                                class="nav-link {{ request()->routeIs('purchase-returns.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Purchase Returns</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Sales -->
                <li
                    class="nav-item has-treeview {{ request()->is('sales*') || request()->is('sales-returns*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('sales*') || request()->is('sales-returns*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cash-register"></i>
                        <p>Sales <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('sales.index') }}"
                                class="nav-link {{ request()->routeIs('sales.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Sales</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('sales.create') }}"
                                class="nav-link {{ request()->routeIs('sales.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>New Sale</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('sales-returns.index') }}"
                                class="nav-link {{ request()->routeIs('sales-returns.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Sales Returns</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- ── FINANCE ── -->
                <li class="sidebar-section-header">Finance</li>

                <!-- Payment Methods -->
                <li class="nav-item has-treeview {{ request()->is('payment_methods*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('payment_methods*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-wallet"></i>
                        <p>Payment Methods <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('payment_methods.index') }}"
                                class="nav-link {{ request()->routeIs('payment_methods.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Methods</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('payment_methods.create') }}"
                                class="nav-link {{ request()->routeIs('payment_methods.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Method</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Transactions -->
                <li class="nav-item has-treeview {{ request()->is('transactions*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('transactions*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-exchange-alt"></i>
                        <p>Transactions <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('transactions.index') }}"
                                class="nav-link {{ request()->routeIs('transactions.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Transactions</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('transactions.create') }}"
                                class="nav-link {{ request()->routeIs('transactions.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>New Transaction</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Ledger -->
                <li class="nav-item">
                    <a href="{{ route('ledgers.index') }}"
                        class="nav-link {{ request()->is('ledgers*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-book"></i>
                        <p>Ledger</p>
                    </a>
                </li>

                <!-- Expenses -->
                <li
                    class="nav-item has-treeview {{ request()->is('expenses*') || request()->is('expense-types*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('expenses*') || request()->is('expense-types*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>Expenses <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('expenses.index') }}"
                                class="nav-link {{ request()->routeIs('expenses.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Expenses</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('expenses.create') }}"
                                class="nav-link {{ request()->routeIs('expenses.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Expense</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('expense-types.index') }}"
                                class="nav-link {{ request()->routeIs('expense-types.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Expense Types</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- ── INVENTORY ── -->
                <li class="sidebar-section-header">Inventory</li>

                <!-- Stock -->
                <li
                    class="nav-item has-treeview {{ request()->is('inventory*') || request()->is('stock-loss-damage*') || request()->is('stock-alerts*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('inventory*') || request()->is('stock-loss-damage*') || request()->is('stock-alerts*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-warehouse"></i>
                        <p>Inventory <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('inventory.index') }}"
                                class="nav-link {{ request()->routeIs('inventory.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Stock Movements</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('stock-alerts.index') }}"
                                class="nav-link {{ request()->routeIs('stock-alerts.index') ? 'active' : '' }}">
                                <i class="fas fa-exclamation-triangle nav-icon text-warning"></i>
                                <p>Stock Alerts</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('stock-loss-damage.index') }}"
                                class="nav-link {{ request()->routeIs('stock-loss-damage.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Loss / Damage</p>
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
        </nav>

        <!-- Settings at bottom -->
        <nav class="mt-auto mb-3">
            <ul class="nav nav-pills nav-sidebar flex-column">
                <li class="nav-item">
                    <a href="{{ route('settings.index') }}"
                        class="nav-link {{ request()->is('settings*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Settings</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>