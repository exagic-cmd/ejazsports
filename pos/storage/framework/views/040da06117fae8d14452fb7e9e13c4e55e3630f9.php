<div class="pos-navbar-left" >
    <ul class="pos-menubar">
        
        <?php if(Auth::user()->id == 7): ?>
        
         <li class="pos-menu-item"><a href="<?php echo e(route('dashboard')); ?>" aria-current="page" class="nav-link <?php echo e(home_page() ? 'router-link-exact-active router-link-active' : ''); ?>"><span class="icon fa fa-calculator"></span> <p>POS</p></a></li>
        
     
       
      
       
        <li class="pos-menu-item"><a href="<?php echo e(route('product.data')); ?>" class="nav-link <?php echo e(current_page('products') ? 'router-link-exact-active router-link-active' : ''); ?>"><span class="icon fa fa-product-hunt"></span> <p>Products</p></a></li>
     
        
        
        <?php else: ?>
        <li class="pos-menu-item"><a href="<?php echo e(route('dashboard')); ?>" aria-current="page" class="nav-link <?php echo e(home_page() ? 'router-link-exact-active router-link-active' : ''); ?>"><span class="icon fa fa-calculator"></span> <p>POS</p></a></li>
        
        <li class="pos-menu-item"><a href="<?php echo e(route('sale.hold')); ?>" aria-current="page" class="nav-link <?php echo e(current_page('hold-list') ? 'router-link-exact-active router-link-active' : ''); ?>"><span class="icon fa fa-recycle"></span> <p>Hold Orders</p></a></li>
        
        
       
        <li class="pos-menu-item"><a href="<?php echo e(route('sales.data')); ?>" class="nav-link <?php echo e(current_page('sales') ? 'router-link-exact-active router-link-active' : ''); ?>"><span class="icon fa fa-first-order"></span> <p>Sales</p></a></li>
        
        
       
        <li class="pos-menu-item"><a href="<?php echo e(route('sales.return.orders')); ?>" class="nav-link <?php echo e(current_page('return-orders') ? 'router-link-exact-active router-link-active' : ''); ?>"><span class="icon fa fa-star"></span> <p>Return Orders</p></a></li>
       
        
        <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'Admin')): ?>
        <li class="pos-menu-item"><a href="<?php echo e(route('customer.data')); ?>" class="nav-link <?php echo e(current_page('customers') ? 'router-link-exact-active router-link-active' : ''); ?>"><span class="icon fa fa-user"></span> <p>Customers</p></a></li>
        <?php endif; ?>
        
       
        <li class="pos-menu-item"><a href="<?php echo e(route('product.data')); ?>" class="nav-link <?php echo e(current_page('products') ? 'router-link-exact-active router-link-active' : ''); ?>"><span class="icon fa fa-product-hunt"></span> <p>Products</p></a></li>
        
        <!--<li class="pos-menu-item"><a href="<?php echo e(route('product.out.of.stock')); ?>" class="nav-link <?php echo e(current_page('out-of-stock-products') ? 'router-link-exact-active router-link-active' : ''); ?>"><span class="icon fa fa-product-hunt"></span> <p>Out Of Stock <br>Products</p></a></li>-->
        
       <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'Admin')): ?>
        <!--<li class="pos-menu-item"><a href="<?php echo e(route('expense.data')); ?>" class="nav-link <?php echo e(current_page('expense') ? 'router-link-exact-active router-link-active' : ''); ?>"><span class="icon fa fa-exchange"></span> <p>Expense</p></a></li>-->
        <?php endif; ?>
        
        <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'Admin')): ?>
        <li class="pos-menu-item"><a href="<?php echo e(route('report.data')); ?>" class="nav-link <?php echo e(current_page('reports') ? 'router-link-exact-active router-link-active' : ''); ?>"><span class="icon fa fa-bar-chart"></span> <p>Reports</p></a></li>
        <?php endif; ?>
        
        <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'Admin')): ?>
        <li class="pos-menu-item"><a href="<?php echo e(route('cashier.data')); ?>" class="nav-link <?php echo e(current_page('cashier') ? 'router-link-exact-active router-link-active' : ''); ?>"><span class="icon fa fa-money"></span> <p>Cashier</p></a></li>
        <?php endif; ?>
        <li class="pos-menu-item">&nbsp;</li>
        <li class="pos-menu-item">&nbsp;</li>
        <li class="pos-menu-item">&nbsp;</li>
        
        <?php endif; ?>
    </ul>
</div>
<?php /**PATH C:\xampp\htdocs\EjazSportsCode\ejazsports\pos\resources\views/layouts/navigation.blade.php ENDPATH**/ ?>