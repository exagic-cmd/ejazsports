<aside class="navbar-aside" id="offcanvas_aside">
    <div class="aside-top">
        <a href="<?php echo e(route('home')); ?>" class="brand-wrap">
            <img src="<?php echo e(asset('imgs/theme/logo-new.jpg')); ?>" style="width: 70px; margin-left: 100%;" alt=" Dashboard" />
        </a>
        <div>
            <button class="btn btn-icon btn-aside-minimize">
                <i class="text-muted material-icons md-menu_open"></i>
            </button>
        </div>
    </div>
    <nav>
        <ul class="menu-aside">
            <li class="menu-item <?php echo e(home_page() ? 'active' : ''); ?>">
                <a class="menu-link" href="<?php echo e(route('home')); ?>">
                    <i class="icon material-icons md-home"></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['List Category','List Brand','List Product'])): ?>
            <li
                class="menu-item has-submenu <?php echo e(request()->routeIs('categories.*','brands.*','products.*','bundles.*','colors.*','sizes.*') ? 'active' : ''); ?>">
                <a class="menu-link">
                    <i class="icon material-icons md-shopping_bag"></i>
                    <span class="text">Manage Catalog</span>
                </a>
                <div class="submenu">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Category')): ?>
                    <a href="<?php echo e(route('categories.index')); ?>"
                        class="<?php echo e(current_page('categories') ? 'active' : ''); ?>">Categories</a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Brand')): ?>
                    <a href="<?php echo e(route('brands.index')); ?>" class="<?php echo e(request()->routeIs('brands.*') ? 'active' : ''); ?>">
                        Brands
                    </a>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Product')): ?>
                    <a href="<?php echo e(route('products.index')); ?>"
                        class="<?php echo e(current_page('products') ? 'active' : ''); ?>">Products</a>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Product')): ?>
                    <a href="<?php echo e(route('colors.index')); ?>" class="<?php echo e(request()->routeIs('colors.*') ? 'active' : ''); ?>">
                        Colors
                    </a>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Product')): ?>
                    <a href="<?php echo e(route('sizes.index')); ?>" class="<?php echo e(request()->routeIs('sizes.*') ? 'active' : ''); ?>">
                        Sizes
                    </a>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Bundle')): ?>
                    <a href="<?php echo e(route('bundles.index')); ?>"
                        class="<?php echo e(current_page('bundles') ? 'active' : ''); ?>">Bundles</a>
                    <?php endif; ?>

                </div>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['List Supplier','List Receiving','List Supplier Payment'])): ?>
            <li class="menu-item has-submenu
                    <?php echo e(request()->routeIs('suppliers.*') ? 'active' : ''); ?>

                    <?php echo e(request()->routeIs('receiving.*') ? 'active' : ''); ?>

                    <?php echo e(request()->routeIs('receiving.incomplete') ? 'active' : ''); ?>

                    <?php echo e(request()->routeIs('supplier-payments.*') ? 'active' : ''); ?>

                    <?php echo e(request()->routeIs('supplier-payments.cheaque') ? 'active' : ''); ?>">

                <a class="menu-link">
                    <i class="icon material-icons md-shopping_bag"></i>
                    <span class="text">Manage Supplier</span>
                </a>

                <div class="submenu">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Supplier')): ?>
                    <a href="<?php echo e(route('suppliers.index')); ?>"
                        class="<?php echo e(request()->routeIs('suppliers.*') ? 'active' : ''); ?>">
                        Suppliers
                    </a>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Receiving')): ?>
                    <a href="<?php echo e(route('receiving.index')); ?>"
                        class="<?php echo e(request()->routeIs('receiving.index') ? 'active' : ''); ?>">
                        Receiving
                    </a>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Incomplete Receiving')): ?>
                    <a href="<?php echo e(route('receiving.incomplete')); ?>"
                        class="<?php echo e(request()->routeIs('receiving.incomplete') ? 'active' : ''); ?>">
                        InComplete Receiving
                    </a>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Supplier Payment')): ?>
                    <a href="<?php echo e(route('supplier-payments.index')); ?>"
                        class="<?php echo e(request()->routeIs('supplier-payments.index') ? 'active' : ''); ?>">
                        List Supplier Payments
                    </a>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Supplier Payment')): ?>
                    <a href="<?php echo e(route('supplier-payments.cheaque')); ?>"
                        class="<?php echo e(request()->routeIs('supplier-payments.cheaque') ? 'active' : ''); ?>">
                        Cheaque Reminders
                    </a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check(['List Customer','List Customer Payment'])): ?>
            <li
                class="menu-item has-submenu <?php echo e(current_page('customers') ? 'active' : ''); ?> <?php echo e(current_page('customer-payments') ? 'active' : ''); ?>">
                <a class="menu-link">
                    <i class="icon material-icons md-payments"></i>
                    <span class="text">Customer Management</span>
                </a>
                <div class="submenu">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Customer')): ?>
                    <a href="<?php echo e(route('customers.index')); ?>" class="<?php echo e(current_page('customers') ? 'active' : ''); ?>">List
                        Customers</a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Customer Payment')): ?>
                    <a href="<?php echo e(route('customer-payments.index')); ?>"
                        class="<?php echo e(current_page('customer-payments') ? 'active' : ''); ?>">List Customer Payments</a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check(['List Customer'])): ?>
            <li class="menu-item has-submenu <?php echo e(request()->routeIs('orders.*') ? 'active' : ''); ?>">
                <a class="menu-link">
                    <i class="icon material-icons md-payments"></i>
                    <span class="text">Order Management</span>
                </a>
                <div class="submenu">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Customer')): ?>
                    <a href="<?php echo e(route('orders.index')); ?>"
                        class="<?php echo e(request()->routeIs('orders.*') ? 'active' : ''); ?>">List Orders</a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Reminder')): ?>
            <li class="menu-item has-submenu <?php echo e(current_page('follow-up') ? 'active' : ''); ?>">
                <a class="menu-link">
                    <i class="icon material-icons md-house"></i>
                    <span class="text">Customer Reminders</span>
                </a>
                <div class="submenu">

                    <a href="<?php echo e(route('followup.auto')); ?>" class="<?php echo e(current_page('/auto') ? 'active' : ''); ?>">Last 15 Days
                        Auto</a>

                    <a href="<?php echo e(route('followup.upcoming')); ?>" class="<?php echo e(current_page('/upcoming') ? 'active' : ''); ?>">Up
                        Coming </a>

                    <a href="<?php echo e(route('followup.expired')); ?>"
                        class="<?php echo e(current_page('/expired') ? 'active' : ''); ?>">Expired </a>

                    <a href="<?php echo e(route('followup.complete')); ?>"
                        class="<?php echo e(current_page('/complete') ? 'active' : ''); ?>">Complete </a>
                </div>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['List Purchase Order','Create Purchase Order'])): ?>
            <li
                class="menu-item has-submenu 
                <?php echo e(request()->routeIs('purchase-orders.*') || request()->routeIs('auto-brand-filter') || request()->routeIs('auto-product-create-form') ? 'active open' : ''); ?>">
                <a class="menu-link">
                    <i class="icon material-icons md-shopping_bag"></i>
                    <span class="text">Manage Purchase Orders</span>
                </a>
                <div class="submenu">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Purchase Order')): ?>
                    <a href="<?php echo e(route('purchase-orders.index')); ?>"
                        class="<?php echo e(request()->routeIs('purchase-orders.index') ? 'active' : ''); ?>">
                        List Purchase Order
                    </a>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create Purchase Order')): ?>
                    <a href="<?php echo e(route('purchase-orders.auto-brand-filter')); ?>"
                        class="<?php echo e(request()->routeIs('purchase-orders.auto-brand-filter') ? 'active' : ''); ?>">
                        Auto PO
                    </a>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create Purchase Order')): ?>
                    <a href="<?php echo e(route('purchase-orders.auto-product-form')); ?>"
                        class="<?php echo e(request()->routeIs('purchase-orders.auto-product-form') ? 'active' : ''); ?>">
                        OOS Product PO
                    </a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Manage Supplier Returns'])): ?>
            <li class="menu-item has-submenu 
                <?php echo e(request()->routeIs('supplier-returns.*') ? 'active' : ''); ?>">
                <a class="menu-link">
                    <i class="icon material-icons md-shopping_bag"></i>
                    <span class="text">Manage Supplier Returns</span>
                </a>
                <div class="submenu">

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Supplier Returns')): ?>
                    <a href="<?php echo e(route('supplier-returns.index')); ?>"
                        class="<?php echo e(request()->routeIs('supplier-returns.index') ? 'active' : ''); ?>">
                        Supplier Returns
                    </a>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Supplier Returns')): ?>
                    <a href="<?php echo e(route('supplier-returns.in')); ?>"
                        class="<?php echo e(request()->routeIs('supplier-returns.in') ? 'active' : ''); ?>">
                        InComplete Returns
                    </a>
                    <?php endif; ?>

                </div>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['List Top Bar Content','List Banner','List Promotion'])): ?>
            <li
                class="menu-item has-submenu <?php echo e(current_page('content') ? 'active' : ''); ?> <?php echo e(current_page('top-bar') ? 'active' : ''); ?> <?php echo e(current_page('ads-screen') ? 'active' : ''); ?> <?php echo e(current_page('banners') ? 'active' : ''); ?> <?php echo e(current_page('promotion') ? 'active' : ''); ?> ">
                <a class="menu-link">
                    <i class="icon material-icons md-view_sidebar"></i>
                    <span class="text">Manage Content</span>
                </a>
                <div class="submenu">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Top Bar Content')): ?>
                    <a href="<?php echo e(route('content.edit')); ?>" class="<?php echo e(current_page('content') ? 'active' : ''); ?>">Website
                        Content</a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Top Bar Content')): ?>
                    <a href="<?php echo e(route('top-bar.index')); ?>" class="<?php echo e(current_page('top-bar') ? 'active' : ''); ?>">Top Bar
                        Text</a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Banner')): ?>
                    <a href="<?php echo e(route('banners.index')); ?>"
                        class="<?php echo e(current_page('banners') ? 'active' : ''); ?>">Banners</a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Promotion')): ?>
                    <a href="<?php echo e(route('promotion.index')); ?>"
                        class="<?php echo e(current_page('promotion') ? 'active' : ''); ?>">Promotion Banners</a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Ads Screen')): ?>
                    <a href="<?php echo e(route('ads-screen.index')); ?>" class="<?php echo e(current_page('ads-screen') ? 'active' : ''); ?>">Ads
                        Screen</a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Ads Screen')): ?>
                    <a href="<?php echo e(route('blogs.index')); ?>" class="<?php echo e(current_page('blogs') ? 'active' : ''); ?>">Blog</a>
                    <?php endif; ?>

                </div>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Store')): ?>
            <li class="menu-item has-submenu <?php echo e(current_page('stores') ? 'active' : ''); ?>">
                <a class="menu-link">
                    <i class="icon material-icons md-house"></i>
                    <span class="text">Store Management</span>
                </a>
                <div class="submenu">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Store')): ?>
                    <a href="<?php echo e(route('stores.index')); ?>" class="<?php echo e(current_page('stores') ? 'active' : ''); ?>">List
                        Stores</a>
                    <?php endif; ?>

                </div>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Priceup')): ?>
            <li class="menu-item has-submenu <?php echo e(current_page('priceup-notification') ? 'active' : ''); ?>">
                <a class="menu-link">
                    <i class="icon material-icons md-house"></i>
                    <span class="text">Priceup Notifications</span>
                </a>
                <div class="submenu">
                    <a href="<?php echo e(route('priceup-notification')); ?>"
                        class="<?php echo e(current_page('priceup-notification') ? 'active' : ''); ?>">List </a>
                </div>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check(['List Discounts'])): ?>
            <li
                class="menu-item has-submenu <?php echo e(current_page('coupons') ? 'active' : ''); ?>  <?php echo e(current_page('discounts') ? 'active' : ''); ?>">
                <a class="menu-link">
                    <i class="icon material-icons md-percentage"></i>
                    <span class="text">Manage Promotions</span>
                </a>
                <div class="submenu">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Discounts')): ?>
                    <a href="<?php echo e(route('discounts.index')); ?>" class="<?php echo e(current_page('discounts') ? 'active' : ''); ?>">List
                        Discounts</a>
                    <?php endif; ?>

                </div>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Employee')): ?>
            <li class="menu-item has-submenu <?php echo e(current_page('employee') ? 'active' : ''); ?>">
                <a class="menu-link">
                    <i class="icon material-icons md-explore"></i>
                    <span class="text">Employee Management</span>
                </a>
                <div class="submenu">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Employee')): ?>
                    <a href="<?php echo e(route('employees.index')); ?>" class="<?php echo e(current_page('employee') ? 'active' : ''); ?>">List
                        Employees</a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Expense')): ?>
            <li class="menu-item has-submenu 
                    <?php echo e(request()->routeIs('expense.index','expense-category.index') ? 'active' : ''); ?>">
                <a class="menu-link">
                    <i class="icon material-icons md-explore"></i>
                    <span class="text">Expense Management</span>
                </a>
                <div class="submenu">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Expense')): ?>
                    <a href="<?php echo e(route('expense.index')); ?>"
                        class="<?php echo e(request()->routeIs('expense.index') ? 'active' : ''); ?>">
                        List Expense
                    </a>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Expense')): ?>
                    <a href="<?php echo e(route('expense-category.index')); ?>"
                        class="<?php echo e(request()->routeIs('expense-category.index') ? 'active' : ''); ?>">
                        List Categories
                    </a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Stock Audit')): ?>
            <li class="menu-item has-submenu 
                    <?php echo e(request()->routeIs('stock-audits.create','stock-audits.index') ? 'active' : ''); ?>">
                <a class="menu-link">
                    <i class="icon material-icons md-track_changes"></i>
                    <span class="text">Stock Audit</span>
                </a>
                <div class="submenu">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create Stock Audit')): ?>
                    <a href="<?php echo e(route('stock-audits.create')); ?>"
                        class="<?php echo e(request()->routeIs('stock-audits.create') ? 'active' : ''); ?>">
                        Add New
                    </a>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('List Stock Audit')): ?>
                    <a href="<?php echo e(route('stock-audits.index')); ?>"
                        class="<?php echo e(request()->routeIs('stock-audits.index') ? 'active' : ''); ?>">
                        List Audits
                    </a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Graph Report','Brand Report','Product Report'])): ?>
            <li class="menu-item has-submenu <?php echo e(current_page('reports') ? 'active' : ''); ?>">
                <a class="menu-link">
                    <i class="icon material-icons md-bar_chart"></i>
                    <span class="text">Reports</span>
                </a>
                <div class="submenu">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Graph Report')): ?>
                    <a href="<?php echo e(route('report.stats.form')); ?>"
                        class="<?php echo e(current_page('reports/stats') ? 'active' : ''); ?>">Stats Report</a>
                    <a href="<?php echo e(route('report.graph')); ?>" class="<?php echo e(current_page('reports/graph') ? 'active' : ''); ?>">Graph
                        Report</a>
                    <a href="<?php echo e(route('report.daily-graph')); ?>"
                        class="<?php echo e(current_page('reports/daily-graph') ? 'active' : ''); ?>">Daily Graph</a>
                    <?php endif; ?>
                    
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Brand Report')): ?>
                    <a href="<?php echo e(route('report.brand.form')); ?>"
                        class="<?php echo e(request()->routeIs('report.brand.form') ? 'active' : ''); ?>">
                        Brands Report
                    </a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Brand Report'])): ?>
                    <a href="<?php echo e(route('report.brand-available-inventory-form')); ?>"
                        class="<?php echo e(current_page('reports/brand-available-inventory') ? 'active' : ''); ?>">Brand
                        Inventory</a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Brand Daily Graph')): ?>
                    <a href="<?php echo e(route('report.brand.graph.form')); ?>"
                        class="<?php echo e(current_page('reports/brand-daily-graph') ? 'active' : ''); ?>">Brand Daily Graph</a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Category Report')): ?>
                    <a href="<?php echo e(route('report.category.form')); ?>"
                        class="<?php echo e(current_page('reports/category') ? 'active' : ''); ?>">Categories Report</a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Product Report')): ?>
                    <a href="<?php echo e(route('report.product.form')); ?>"
                        class="<?php echo e(current_page('reports/product') ? 'active' : ''); ?>">Products Report</a>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Product Report')): ?>
                    <a href="<?php echo e(route('report.out-of-stock.products.form')); ?>"
                        class="<?php echo e(request()->routeIs('report.out-of-stock.products.form') ? 'active' : ''); ?>">
                        OutOfStock Products Report
                    </a>
                    <?php endif; ?>

                </div>
            </li>
            <?php endif; ?>

            <li class="menu-item <?php echo e(current_page('reminders') ? 'active' : ''); ?>">
                <a class="menu-link" href="<?php echo e(route('reminders.index')); ?>">
                    <i class="icon material-icons md-notifications"></i>
                    <span class="text">Daily Reminders</span>
                </a>
            </li>

        </ul>
        <hr />

        <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'Admin')): ?>
        <ul class="menu-aside">
            <li
                class="menu-item has-submenu <?php echo e(current_page('accounts') ? 'active' : ''); ?> <?php echo e(current_page('permissions') ? 'active' : ''); ?> <?php echo e(current_page('roles') ? 'active' : ''); ?>">
                <a class="menu-link" href="#">
                    <i class="icon material-icons md-settings"></i>
                    <span class="text">Settings</span>
                </a>
                <div class="submenu">
                    <a href="<?php echo e(route('accounts.index')); ?>"
                        class="<?php echo e(current_page('accounts') ? 'active' : ''); ?> <?php echo e(current_page('permissions') ? 'active' : ''); ?> <?php echo e(current_page('roles') ? 'active' : ''); ?>">Accounts
                    </a>

                </div>
            </li>

            <li class="menu-item <?php echo e(current_page('activity-log') ? 'active' : ''); ?>">
                <a class="menu-link" href="<?php echo e(route('activity.index')); ?>">
                    <i class="icon material-icons md-access_time"></i>
                    <span class="text">Activity Logs</span>
                </a>
            </li>

        </ul>
        <?php endif; ?>
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
<?php /**PATH C:\xampp\htdocs\EjazSportsCode\ejazsports\admin\resources\views/layouts/navigation.blade.php ENDPATH**/ ?>