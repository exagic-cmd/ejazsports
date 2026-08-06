<aside class="navbar-aside" id="offcanvas_aside">
    <div class="aside-top">
        <a href="{{route('home')}}" class="brand-wrap">
            <img src="{{asset('imgs/theme/logo-new.jpg')}}" style="width: 70px; margin-left: 100%;" alt=" Dashboard" />
        </a>
        <div>
            <button class="btn btn-icon btn-aside-minimize">
                <i class="text-muted material-icons md-menu_open"></i>
            </button>
        </div>
    </div>
    <nav>
        <ul class="menu-aside">
            <li class="menu-item {{home_page() ? 'active' : '' }}">
                <a class="menu-link" href="{{route('home')}}">
                    <i class="icon material-icons md-home"></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>

            @canany(['List Category','List Brand','List Product'])
            <li
                class="menu-item has-submenu {{ request()->routeIs('categories.*','brands.*','products.*','bundles.*','colors.*','sizes.*') ? 'active' : '' }}">
                <a class="menu-link">
                    <i class="icon material-icons md-shopping_bag"></i>
                    <span class="text">Manage Catalog</span>
                </a>
                <div class="submenu">
                    @can('List Category')
                    <a href="{{route('categories.index')}}"
                        class="{{current_page('categories') ? 'active' : '' }}">Categories</a>
                    @endcan
                    @can('List Brand')
                    <a href="{{ route('brands.index') }}" class="{{ request()->routeIs('brands.*') ? 'active' : '' }}">
                        Brands
                    </a>
                    @endcan

                    @can('List Product')
                    <a href="{{route('products.index')}}"
                        class="{{current_page('products') ? 'active' : '' }}">Products</a>
                    @endcan

                    @can('List Product')
                    <a href="{{route('colors.index')}}" class="{{ request()->routeIs('colors.*') ? 'active' : '' }}">
                        Colors
                    </a>
                    @endcan

                    @can('List Product')
                    <a href="{{route('sizes.index')}}" class="{{ request()->routeIs('sizes.*') ? 'active' : '' }}">
                        Sizes
                    </a>
                    @endcan

                    @can('List Bundle')
                    <a href="{{route('bundles.index')}}"
                        class="{{current_page('bundles') ? 'active' : '' }}">Bundles</a>
                    @endcan

                </div>
            </li>
            @endcanany

            @canany(['List Supplier','List Receiving','List Supplier Payment'])
            <li class="menu-item has-submenu
                    {{ request()->routeIs('suppliers.*') ? 'active' : '' }}
                    {{ request()->routeIs('receiving.*') ? 'active' : '' }}
                    {{ request()->routeIs('receiving.incomplete') ? 'active' : '' }}
                    {{ request()->routeIs('supplier-payments.*') ? 'active' : '' }}
                    {{ request()->routeIs('supplier-payments.cheaque') ? 'active' : '' }}">

                <a class="menu-link">
                    <i class="icon material-icons md-shopping_bag"></i>
                    <span class="text">Manage Supplier</span>
                </a>

                <div class="submenu">
                    @can('List Supplier')
                    <a href="{{ route('suppliers.index') }}"
                        class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                        Suppliers
                    </a>
                    @endcan

                    @can('List Receiving')
                    <a href="{{ route('receiving.index') }}"
                        class="{{ request()->routeIs('receiving.index') ? 'active' : '' }}">
                        Receiving
                    </a>
                    @endcan

                    @can('Manage Incomplete Receiving')
                    <a href="{{ route('receiving.incomplete') }}"
                        class="{{ request()->routeIs('receiving.incomplete') ? 'active' : '' }}">
                        InComplete Receiving
                    </a>
                    @endcan

                    @can('List Supplier Payment')
                    <a href="{{ route('supplier-payments.index') }}"
                        class="{{ request()->routeIs('supplier-payments.index') ? 'active' : '' }}">
                        List Supplier Payments
                    </a>
                    @endcan

                    @can('List Supplier Payment')
                    <a href="{{ route('supplier-payments.cheaque') }}"
                        class="{{ request()->routeIs('supplier-payments.cheaque') ? 'active' : '' }}">
                        Cheaque Reminders
                    </a>
                    @endcan
                </div>
            </li>
            @endcanany

            @can(['List Customer','List Customer Payment'])
            <li
                class="menu-item has-submenu {{current_page('customers') ? 'active' : '' }} {{current_page('customer-payments') ? 'active' : '' }}">
                <a class="menu-link">
                    <i class="icon material-icons md-payments"></i>
                    <span class="text">Customer Management</span>
                </a>
                <div class="submenu">
                    @can('List Customer')
                    <a href="{{route('customers.index')}}" class="{{current_page('customers') ? 'active' : '' }}">List
                        Customers</a>
                    @endcan
                    @can('List Customer Payment')
                    <a href="{{route('customer-payments.index')}}"
                        class="{{current_page('customer-payments') ? 'active' : '' }}">List Customer Payments</a>
                    @endcan
                </div>
            </li>
            @endcan

            @can(['List Customer'])
            <li class="menu-item has-submenu {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <a class="menu-link">
                    <i class="icon material-icons md-payments"></i>
                    <span class="text">Order Management</span>
                </a>
                <div class="submenu">
                    @can('List Customer')
                    <a href="{{ route('orders.index') }}"
                        class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">List Orders</a>
                    @endcan
                </div>
            </li>
            @endcan

            @can('Manage Reminder')
            <li class="menu-item has-submenu {{current_page('follow-up') ? 'active' : '' }}">
                <a class="menu-link">
                    <i class="icon material-icons md-house"></i>
                    <span class="text">Customer Reminders</span>
                </a>
                <div class="submenu">

                    <a href="{{route('followup.auto')}}" class="{{current_page('/auto') ? 'active' : '' }}">Last 15 Days
                        Auto</a>

                    <a href="{{route('followup.upcoming')}}" class="{{current_page('/upcoming') ? 'active' : '' }}">Up
                        Coming </a>

                    <a href="{{route('followup.expired')}}"
                        class="{{current_page('/expired') ? 'active' : '' }}">Expired </a>

                    <a href="{{route('followup.complete')}}"
                        class="{{current_page('/complete') ? 'active' : '' }}">Complete </a>
                </div>
            </li>
            @endcan

            @canany(['List Purchase Order','Create Purchase Order'])
            <li
                class="menu-item has-submenu 
                {{ request()->routeIs('purchase-orders.*') || request()->routeIs('auto-brand-filter') || request()->routeIs('auto-product-create-form') ? 'active open' : '' }}">
                <a class="menu-link">
                    <i class="icon material-icons md-shopping_bag"></i>
                    <span class="text">Manage Purchase Orders</span>
                </a>
                <div class="submenu">
                    @can('List Purchase Order')
                    <a href="{{ route('purchase-orders.index') }}"
                        class="{{ request()->routeIs('purchase-orders.index') ? 'active' : '' }}">
                        List Purchase Order
                    </a>
                    @endcan

                    @can('Create Purchase Order')
                    <a href="{{ route('purchase-orders.auto-brand-filter') }}"
                        class="{{ request()->routeIs('purchase-orders.auto-brand-filter') ? 'active' : '' }}">
                        Auto PO
                    </a>
                    @endcan

                    @can('Create Purchase Order')
                    <a href="{{ route('purchase-orders.auto-product-form') }}"
                        class="{{ request()->routeIs('purchase-orders.auto-product-form') ? 'active' : '' }}">
                        OOS Product PO
                    </a>
                    @endcan
                </div>
            </li>
            @endcanany

            @canany(['Manage Supplier Returns'])
            <li class="menu-item has-submenu 
                {{ request()->routeIs('supplier-returns.*') ? 'active' : '' }}">
                <a class="menu-link">
                    <i class="icon material-icons md-shopping_bag"></i>
                    <span class="text">Manage Supplier Returns</span>
                </a>
                <div class="submenu">

                    @can('Manage Supplier Returns')
                    <a href="{{ route('supplier-returns.index') }}"
                        class="{{ request()->routeIs('supplier-returns.index') ? 'active' : '' }}">
                        Supplier Returns
                    </a>
                    @endcan

                    @can('Manage Supplier Returns')
                    <a href="{{ route('supplier-returns.in') }}"
                        class="{{ request()->routeIs('supplier-returns.in') ? 'active' : '' }}">
                        InComplete Returns
                    </a>
                    @endcan

                </div>
            </li>
            @endcanany

            @canany(['List Top Bar Content','List Banner','List Promotion'])
            <li
                class="menu-item has-submenu {{current_page('content') ? 'active' : '' }} {{current_page('top-bar') ? 'active' : '' }} {{current_page('ads-screen') ? 'active' : '' }} {{current_page('banners') ? 'active' : '' }} {{current_page('promotion') ? 'active' : '' }} ">
                <a class="menu-link">
                    <i class="icon material-icons md-view_sidebar"></i>
                    <span class="text">Manage Content</span>
                </a>
                <div class="submenu">
                    @can('List Top Bar Content')
                    <a href="{{route('content.edit')}}" class="{{current_page('content') ? 'active' : '' }}">Website
                        Content</a>
                    @endcan
                    @can('List Top Bar Content')
                    <a href="{{route('top-bar.index')}}" class="{{current_page('top-bar') ? 'active' : '' }}">Top Bar
                        Text</a>
                    @endcan
                    @can('List Banner')
                    <a href="{{route('banners.index')}}"
                        class="{{current_page('banners') ? 'active' : '' }}">Banners</a>
                    @endcan
                    @can('List Promotion')
                    <a href="{{route('promotion.index')}}"
                        class="{{current_page('promotion') ? 'active' : '' }}">Promotion Banners</a>
                    @endcan
                    @can('List Ads Screen')
                    <a href="{{route('ads-screen.index')}}" class="{{current_page('ads-screen') ? 'active' : '' }}">Ads
                        Screen</a>
                    @endcan
                    @can('List Ads Screen')
                    <a href="{{route('blogs.index')}}" class="{{current_page('blogs') ? 'active' : '' }}">Blog</a>
                    @endcan

                </div>
            </li>
            @endcanany

            @can('List Store')
            <li class="menu-item has-submenu {{current_page('stores') ? 'active' : '' }}">
                <a class="menu-link">
                    <i class="icon material-icons md-house"></i>
                    <span class="text">Store Management</span>
                </a>
                <div class="submenu">
                    @can('List Store')
                    <a href="{{route('stores.index')}}" class="{{current_page('stores') ? 'active' : '' }}">List
                        Stores</a>
                    @endcan

                </div>
            </li>
            @endcan

            @can('Manage Priceup')
            <li class="menu-item has-submenu {{current_page('priceup-notification') ? 'active' : '' }}">
                <a class="menu-link">
                    <i class="icon material-icons md-house"></i>
                    <span class="text">Priceup Notifications</span>
                </a>
                <div class="submenu">
                    <a href="{{route('priceup-notification')}}"
                        class="{{current_page('priceup-notification') ? 'active' : '' }}">List </a>
                </div>
            </li>
            @endcan

            @can(['List Discounts'])
            <li
                class="menu-item has-submenu {{current_page('coupons') ? 'active' : '' }}  {{current_page('discounts') ? 'active' : '' }}">
                <a class="menu-link">
                    <i class="icon material-icons md-percentage"></i>
                    <span class="text">Manage Promotions</span>
                </a>
                <div class="submenu">
                    @can('List Discounts')
                    <a href="{{route('discounts.index')}}" class="{{current_page('discounts') ? 'active' : '' }}">List
                        Discounts</a>
                    @endcan

                </div>
            </li>
            @endcan

            @can('List Employee')
            <li class="menu-item has-submenu {{current_page('employee') ? 'active' : '' }}">
                <a class="menu-link">
                    <i class="icon material-icons md-explore"></i>
                    <span class="text">Employee Management</span>
                </a>
                <div class="submenu">
                    @can('List Employee')
                    <a href="{{route('employees.index')}}" class="{{current_page('employee') ? 'active' : '' }}">List
                        Employees</a>
                    @endcan
                </div>
            </li>
            @endcan

            @can('List Expense')
            <li class="menu-item has-submenu 
                    {{ request()->routeIs('expense.index','expense-category.index') ? 'active' : '' }}">
                <a class="menu-link">
                    <i class="icon material-icons md-explore"></i>
                    <span class="text">Expense Management</span>
                </a>
                <div class="submenu">
                    @can('List Expense')
                    <a href="{{ route('expense.index') }}"
                        class="{{ request()->routeIs('expense.index') ? 'active' : '' }}">
                        List Expense
                    </a>
                    @endcan

                    @can('List Expense')
                    <a href="{{ route('expense-category.index') }}"
                        class="{{ request()->routeIs('expense-category.index') ? 'active' : '' }}">
                        List Categories
                    </a>
                    @endcan
                </div>
            </li>
            @endcan

            @can('List Stock Audit')
            <li class="menu-item has-submenu 
                    {{ request()->routeIs('stock-audits.create','stock-audits.index') ? 'active' : '' }}">
                <a class="menu-link">
                    <i class="icon material-icons md-track_changes"></i>
                    <span class="text">Stock Audit</span>
                </a>
                <div class="submenu">
                    @can('Create Stock Audit')
                    <a href="{{ route('stock-audits.create') }}"
                        class="{{ request()->routeIs('stock-audits.create') ? 'active' : '' }}">
                        Add New
                    </a>
                    @endcan

                    @can('List Stock Audit')
                    <a href="{{ route('stock-audits.index') }}"
                        class="{{ request()->routeIs('stock-audits.index') ? 'active' : '' }}">
                        List Audits
                    </a>
                    @endcan
                </div>
            </li>
            @endcan

            @canany(['Graph Report','Brand Report','Product Report'])
            <li class="menu-item has-submenu {{current_page('reports') ? 'active' : '' }}">
                <a class="menu-link">
                    <i class="icon material-icons md-bar_chart"></i>
                    <span class="text">Reports</span>
                </a>
                <div class="submenu">
                    @can('Graph Report')
                    <a href="{{route('report.stats.form')}}"
                        class="{{current_page('reports/stats') ? 'active' : '' }}">Stats Report</a>
                    <a href="{{route('report.graph')}}" class="{{current_page('reports/graph') ? 'active' : '' }}">Graph
                        Report</a>
                    <a href="{{route('report.daily-graph')}}"
                        class="{{current_page('reports/daily-graph') ? 'active' : '' }}">Daily Graph</a>
                    @endcan
                    {{-- @can('Brand Report')
                    <a href="{{route('report.brand.form')}}"
                    class="{{current_page('reports/brands') ? 'active' : '' }}">Brands Report</a>
                    @endcan --}}
                    @can('Brand Report')
                    <a href="{{ route('report.brand.form') }}"
                        class="{{ request()->routeIs('report.brand.form') ? 'active' : '' }}">
                        Brands Report
                    </a>
                    @endcan
                    @canany(['Brand Report'])
                    <a href="{{route('report.brand-available-inventory-form')}}"
                        class="{{current_page('reports/brand-available-inventory') ? 'active' : '' }}">Brand
                        Inventory</a>
                    @endcanany
                    @can('Brand Daily Graph')
                    <a href="{{route('report.brand.graph.form')}}"
                        class="{{current_page('reports/brand-daily-graph') ? 'active' : '' }}">Brand Daily Graph</a>
                    @endcan
                    @can('Category Report')
                    <a href="{{route('report.category.form')}}"
                        class="{{current_page('reports/category') ? 'active' : '' }}">Categories Report</a>
                    @endcan
                    @can('Product Report')
                    <a href="{{route('report.product.form')}}"
                        class="{{current_page('reports/product') ? 'active' : '' }}">Products Report</a>
                    @endcan

                    @can('Product Report')
                    <a href="{{ route('report.out-of-stock.products.form') }}"
                        class="{{ request()->routeIs('report.out-of-stock.products.form') ? 'active' : '' }}">
                        OutOfStock Products Report
                    </a>
                    @endcan

                </div>
            </li>
            @endcanany

        </ul>
        <hr />

        @role('Admin')
        <ul class="menu-aside">
            <li
                class="menu-item has-submenu {{current_page('accounts') ? 'active' : '' }} {{current_page('permissions') ? 'active' : '' }} {{current_page('roles') ? 'active' : '' }}">
                <a class="menu-link" href="#">
                    <i class="icon material-icons md-settings"></i>
                    <span class="text">Settings</span>
                </a>
                <div class="submenu">
                    <a href="{{route('accounts.index')}}"
                        class="{{current_page('accounts') ? 'active' : '' }} {{current_page('permissions') ? 'active' : '' }} {{current_page('roles') ? 'active' : '' }}">Accounts
                    </a>

                </div>
            </li>

            <li class="menu-item {{current_page('activity-log') ? 'active' : '' }}">
                <a class="menu-link" href="{{route('activity.index')}}">
                    <i class="icon material-icons md-access_time"></i>
                    <span class="text">Activity Logs</span>
                </a>
            </li>

        </ul>
        @endrole
        <br />
        <br />
    </nav>
</aside>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const sidebar = document.getElementById("offcanvas_aside");

        // Restore scroll position on page load
        if (localStorage.getItem("sidebarScroll")) {
            sidebar.scrollTop = parseInt(localStorage.getItem("sidebarScroll"));
        }

        // Save scroll position on scroll
        sidebar.addEventListener("scroll", function () {
            localStorage.setItem("sidebarScroll", sidebar.scrollTop);
        });
    });

</script>
