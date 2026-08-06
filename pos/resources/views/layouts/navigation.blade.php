<div class="pos-navbar-left" >
    <ul class="pos-menubar">
        
        @if(Auth::user()->id == 7)
        
         <li class="pos-menu-item"><a href="{{route('dashboard')}}" aria-current="page" class="nav-link {{home_page() ? 'router-link-exact-active router-link-active' : '' }}"><span class="icon fa fa-calculator"></span> <p>POS</p></a></li>
        
     
       
      
       
        <li class="pos-menu-item"><a href="{{route('product.data')}}" class="nav-link {{current_page('products') ? 'router-link-exact-active router-link-active' : '' }}"><span class="icon fa fa-product-hunt"></span> <p>Products</p></a></li>
     
        
        
        @else
        <li class="pos-menu-item"><a href="{{route('dashboard')}}" aria-current="page" class="nav-link {{home_page() ? 'router-link-exact-active router-link-active' : '' }}"><span class="icon fa fa-calculator"></span> <p>POS</p></a></li>
        
        <li class="pos-menu-item"><a href="{{route('sale.hold')}}" aria-current="page" class="nav-link {{current_page('hold-list') ? 'router-link-exact-active router-link-active' : '' }}"><span class="icon fa fa-recycle"></span> <p>Hold Orders</p></a></li>
        
        
       
        <li class="pos-menu-item"><a href="{{route('sales.data')}}" class="nav-link {{current_page('sales') ? 'router-link-exact-active router-link-active' : '' }}"><span class="icon fa fa-first-order"></span> <p>Sales</p></a></li>
        
        
       
        <li class="pos-menu-item"><a href="{{route('sales.return.orders')}}" class="nav-link {{current_page('return-orders') ? 'router-link-exact-active router-link-active' : '' }}"><span class="icon fa fa-star"></span> <p>Return Orders</p></a></li>
       
        
        @role('Admin')
        <li class="pos-menu-item"><a href="{{route('customer.data')}}" class="nav-link {{current_page('customers') ? 'router-link-exact-active router-link-active' : '' }}"><span class="icon fa fa-user"></span> <p>Customers</p></a></li>
        @endrole
        
       
        <li class="pos-menu-item"><a href="{{route('product.data')}}" class="nav-link {{current_page('products') ? 'router-link-exact-active router-link-active' : '' }}"><span class="icon fa fa-product-hunt"></span> <p>Products</p></a></li>
        
        <!--<li class="pos-menu-item"><a href="{{route('product.out.of.stock')}}" class="nav-link {{current_page('out-of-stock-products') ? 'router-link-exact-active router-link-active' : '' }}"><span class="icon fa fa-product-hunt"></span> <p>Out Of Stock <br>Products</p></a></li>-->
        
       @role('Admin')
        <!--<li class="pos-menu-item"><a href="{{route('expense.data')}}" class="nav-link {{current_page('expense') ? 'router-link-exact-active router-link-active' : '' }}"><span class="icon fa fa-exchange"></span> <p>Expense</p></a></li>-->
        @endrole
        
        @role('Admin')
        <li class="pos-menu-item"><a href="{{route('report.data')}}" class="nav-link {{current_page('reports') ? 'router-link-exact-active router-link-active' : '' }}"><span class="icon fa fa-bar-chart"></span> <p>Reports</p></a></li>
        @endrole
        
        @role('Admin')
        <li class="pos-menu-item"><a href="{{route('cashier.data')}}" class="nav-link {{current_page('cashier') ? 'router-link-exact-active router-link-active' : '' }}"><span class="icon fa fa-money"></span> <p>Cashier</p></a></li>
        @endrole
        <li class="pos-menu-item">&nbsp;</li>
        <li class="pos-menu-item">&nbsp;</li>
        <li class="pos-menu-item">&nbsp;</li>
        
        @endif
    </ul>
</div>
