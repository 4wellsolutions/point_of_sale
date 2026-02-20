<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="brand-link">
        <i class="fas fa-cash-register ms-2 me-2"></i>
        <span class="brand-text font-weight-light">POS System</span>
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

                <!-- ── MASTER DATA ── -->
                <li class="sidebar-section-header">Master Data</li>

                <!-- Products -->
                <li class="nav-item has-treeview {{ request()->is('products*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('products*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box-open"></i>
                        <p>Products <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('products.index') }}"
                                class="nav-link {{ request()->routeIs('products.index') ? 'active' : '' }}">
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
                    </ul>
                </li>

                <!-- Categories -->
                <li class="nav-item has-treeview {{ request()->is('categories*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('categories*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>Categories <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('categories.index') }}"
                                class="nav-link {{ request()->routeIs('categories.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Categories</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('categories.create') }}"
                                class="nav-link {{ request()->routeIs('categories.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Category</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Flavours -->
                <li class="nav-item has-treeview {{ request()->is('flavours*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('flavours*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-lemon"></i>
                        <p>Flavours <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('flavours.index') }}"
                                class="nav-link {{ request()->routeIs('flavours.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Flavours</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('flavours.create') }}"
                                class="nav-link {{ request()->routeIs('flavours.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Flavour</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Packing -->
                <li class="nav-item has-treeview {{ request()->is('packings*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('packings*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Packing <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('packings.index') }}"
                                class="nav-link {{ request()->routeIs('packings.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Packings</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('packings.create') }}"
                                class="nav-link {{ request()->routeIs('packings.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Packing</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Locations -->
                <li class="nav-item has-treeview {{ request()->is('locations*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('locations*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-map-marker-alt"></i>
                        <p>Locations <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('locations.index') }}"
                                class="nav-link {{ request()->routeIs('locations.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Locations</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('locations.create') }}"
                                class="nav-link {{ request()->routeIs('locations.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Location</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Types -->
                <li class="nav-item has-treeview {{ request()->is('types*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('types*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-list"></i>
                        <p>Types <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('types.index') }}"
                                class="nav-link {{ request()->routeIs('types.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Types</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('types.create') }}"
                                class="nav-link {{ request()->routeIs('types.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Add Type</p>
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
                    class="nav-item has-treeview {{ request()->is('inventory*') || request()->is('stock-loss-damage*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('inventory*') || request()->is('stock-loss-damage*') ? 'active' : '' }}">
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
    </div>
</aside>