<!-- ========== HEADER ========== -->
<header id="header" class="u-header u-header-left-aligned-nav">
    <div class="u-header__section">
        <!-- Topbar -->
        <div class="u-header-topbar py-2 d-none d-xl-block">
            <div class="container">
                <div class="d-flex align-items-center">
                    <div class="topbar-left">
                        <a href="#" class="text-gray-110 font-size-13 u-header-topbar__nav-link">
                            Welcome to EjazSports Online Store
                        </a>
                    </div>
                    <div class="topbar-right ml-auto">
                        <ul class="list-inline mb-0">
                            <li
                                class="list-inline-item mr-0 u-header-topbar__nav-item u-header-topbar__nav-item-border">
                                <a href="{{ route('account.orders.list') }}" class="u-header-topbar__nav-link">
                                    <i class="fa fa-clipboard-list mr-1"></i> My Orders
                                </a>
                            </li>
                            <li
                                class="list-inline-item mr-0 u-header-topbar__nav-item u-header-topbar__nav-item-border">
                                <a href="#" class="u-header-topbar__nav-link">
                                    <i class="ec ec-transport mr-1"></i> Track Your Order
                                </a>
                            </li>
                            <li
                                class="list-inline-item mr-0 u-header-topbar__nav-item u-header-topbar__nav-item-border">
                                @if(session('customer_id'))
                                    <a id="sidebarNavToggler" href="javascript:;" role="button"
                                        class="u-header-topbar__nav-link" aria-controls="sidebarContent"
                                        aria-haspopup="true" aria-expanded="false" data-unfold-event="click"
                                        data-unfold-hide-on-scroll="false" data-unfold-target="#sidebarContent"
                                        data-unfold-type="css-animation" data-unfold-animation-in="fadeInRight"
                                        data-unfold-animation-out="fadeOutRight" data-unfold-duration="500">
                                        <i class="ec ec-user mr-1"></i> Hi, {{ session('customer_first_name') }}
                                    </a>
                                @else
                                    <a id="registerToggler" href="javascript:;" role="button"
                                        class="u-header-topbar__nav-link" aria-controls="sidebarContent"
                                        aria-haspopup="true" aria-expanded="false" data-unfold-event="click"
                                        data-unfold-hide-on-scroll="false" data-unfold-target="#sidebarContent"
                                        data-unfold-type="css-animation" data-unfold-animation-in="fadeInRight"
                                        data-unfold-animation-out="fadeOutRight" data-unfold-duration="500"
                                        onclick="window.lastAuthTrigger = 'register';">
                                        <i class="ec ec-user mr-1"></i> Register
                                    </a>
                                    <span class="text-gray-50">or</span>
                                    <a id="signInToggler" href="javascript:;" role="button"
                                        class="u-header-topbar__nav-link" aria-controls="sidebarContent"
                                        aria-haspopup="true" aria-expanded="false" data-unfold-event="click"
                                        data-unfold-hide-on-scroll="false" data-unfold-target="#sidebarContent"
                                        data-unfold-type="css-animation" data-unfold-animation-in="fadeInRight"
                                        data-unfold-animation-out="fadeOutRight" data-unfold-duration="500"
                                        onclick="window.lastAuthTrigger = 'login';">
                                        Sign in
                                    </a>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Topbar -->

        <!-- Logo-Search-header-icons -->
        <div class="py-2 py-xl-5 bg-primary-down-lg">
            <div class="container my-0dot5 my-xl-0">
                <div class="row align-items-center">
                    <!-- Logo & Mobile Menu -->
                    <div class="col-auto">
                        <nav
                            class="navbar navbar-expand u-header__navbar py-0 justify-content-xl-between max-width-270 min-width-270">
                            <a class="order-1 order-xl-0 navbar-brand u-header__navbar-brand u-header__navbar-brand-center"
                                href="{{ route('home') }}" aria-label="EjazSports">
                                <img class="img-fluid" src="{{ asset('assets/img/logo.png') }}" alt="EjazSports">
                            </a>

                            <button id="sidebarHeaderInvokerMenu" type="button"
                                class="navbar-toggler d-block btn u-hamburger mr-3 mr-xl-0"
                                aria-controls="sidebarHeader1" aria-haspopup="true" aria-expanded="false"
                                data-unfold-event="click" data-unfold-hide-on-scroll="false"
                                data-unfold-target="#sidebarHeader1" data-unfold-type="css-animation"
                                data-unfold-animation-in="fadeInLeft" data-unfold-animation-out="fadeOutLeft"
                                data-unfold-duration="500">
                                <span id="hamburgerTriggerMenu" class="u-hamburger__box">
                                    <span class="u-hamburger__inner"></span>
                                </span>
                            </button>
                        </nav>

                        <!-- Mobile Sidebar -->
                        <aside id="sidebarHeader1" class="u-sidebar u-sidebar--left"
                            aria-labelledby="sidebarHeaderInvoker">
                            <div class="u-sidebar__scroller">
                                <div class="u-sidebar__container">
                                    <div class="u-header-sidebar__footer-offset">
                                        <div class="position-absolute top-0 right-0 z-index-2 pt-4 pr-4 bg-white">
                                            <button type="button" class="close ml-auto"
                                                data-unfold-target="#sidebarHeader1" data-unfold-type="css-animation">
                                                <span aria-hidden="true"><i
                                                        class="ec ec-close-remove text-gray-90 font-size-20"></i></span>
                                            </button>
                                        </div>
                                        <div class="js-scrollbar u-sidebar__body">
                                            <div class="u-sidebar__content u-header-sidebar__content">
                                                <a class="navbar-brand u-header__navbar-brand-center mb-3"
                                                    href="{{ route('home') }}">
                                                    <img class="img-fluid" src="{{ asset('assets/img/logo.png') }}"
                                                        alt="EjazSports">
                                                </a>
                                                <ul class="u-header-collapse__nav">
                                                    <!-- Your mobile menu items here (same as desktop vertical menu) -->
                                                    <li><a class="u-header-collapse__nav-link font-weight-bold"
                                                            href="#">Value of the Day</a></li>
                                                    <li><a class="u-header-collapse__nav-link font-weight-bold"
                                                            href="#">Top 100 Offers</a></li>
                                                    <li><a class="u-header-collapse__nav-link font-weight-bold"
                                                            href="#">New Arrivals</a></li>
                                                    <!-- Add more if needed -->
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <footer class="u-header-sidebar__footer">
                                        <ul class="list-inline mb-0">
                                            <li class="list-inline-item pr-3"><a
                                                    class="u-header-sidebar__footer-link text-gray-90"
                                                    href="#">Privacy</a></li>
                                            <li class="list-inline-item pr-3"><a
                                                    class="u-header-sidebar__footer-link text-gray-90"
                                                    href="#">Terms</a></li>
                                            <li class="list-inline-item"><a
                                                    class="u-header-sidebar__footer-link text-gray-90"
                                                    href="#"><i class="fas fa-info-circle"></i></a></li>
                                        </ul>
                                    </footer>
                                </div>
                            </div>
                        </aside>
                        <!-- End Mobile Sidebar -->
                    </div>
                    <!-- End Logo & Mobile Menu -->

                    <!-- Search Bar -->
                    <div class="col d-none d-xl-block">
                        <form class="js-focus-state" method="GET" action="{{ route('search') }}">
                            <label class="sr-only" for="searchproduct">Search</label>
                            <div class="input-group">
                                <input type="search"
                                    class="form-control py-2 pl-5 font-size-15 border-right-0 height-40 border-width-2 rounded-left-pill border-primary"
                                    name="q" id="searchproduct-item" placeholder="Search for Products"
                                    value="{{ request()->get('q') }}">
                                <div class="input-group-append">
                                    <select name="category"
                                        class="js-select selectpicker dropdown-select custom-search-categories-select"
                                        data-style="btn height-40 text-gray-60 font-weight-normal border-top border-bottom border-left-0 rounded-0 border-primary border-width-2 pl-0 pr-5 py-2">
                                        <option value="">All Categories</option>
                                        @if (isset($grouped) && $grouped)
                                            @foreach ($grouped as $parentId => $cats)
                                                @foreach ($cats as $cat)
                                                    <option value="{{ $cat->category_id }}"
                                                        {{ request()->get('category') == $cat->category_id ? 'selected' : '' }}>
                                                        {{ $cat->category_title }}
                                                    </option>
                                                @endforeach
                                            @endforeach
                                        @endif
                                    </select>
                                    <button class="btn btn-primary height-40 py-2 px-3 rounded-right-pill"
                                        type="submit">
                                        <span class="ec ec-search font-size-24"></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- End Search Bar -->

                    <!-- Header Icons -->
                    <div class="col col-xl-auto text-right text-xl-left pl-0 pl-xl-3 position-static">
                        <div class="d-inline-flex">
                            <ul class="d-flex list-unstyled mb-0 align-items-center">
                                <!-- Mobile Search -->
                                <li class="col d-xl-none px-2 px-sm-3 position-static">
                                    <a id="searchClassicInvoker" class="font-size-22 text-gray-90 btn-text-secondary"
                                        href="javascript:;">
                                        <span class="ec ec-search"></span>
                                    </a>
                                    <div id="searchClassic"
                                        class="dropdown-menu dropdown-unfold dropdown-menu-right left-0 mx-2">
                                        <form class="js-focus-state input-group px-3" method="GET"
                                            action="{{ route('search') }}">
                                            <input class="form-control" type="search" name="q"
                                                placeholder="Search Product" value="{{ request()->get('q') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary px-3" type="submit"><i
                                                        class="font-size-18 ec ec-search"></i></button>
                                            </div>
                                        </form>
                                    </div>
                                </li>

                                <!-- Mobile Cart -->
                                <li class="col d-xl-none px-2 px-sm-3 position-static">
                                    <a href="{{ route('cart') }}" class="text-gray-90 position-relative d-flex">
                                        <i class="font-size-22 ec ec-shopping-bag"></i>
                                        <span class="bg-lg-down-black width-22 height-22 bg-primary position-absolute d-flex align-items-center justify-content-center rounded-circle left-12 top-8 font-weight-bold font-size-12 cart-count">
                                            {{ count(session('cart', [])) }}
                                        </span>
                                    </a>
                                </li>

                                <!-- Compare & Wishlist -->
                                <li class="col d-none d-xl-block"><a href="#" class="text-gray-90"
                                        title="Compare"><i class="font-size-22 ec ec-compare"></i></a></li>
                                <li class="col d-none d-xl-block"><a href="#" class="text-gray-90"
                                        title="Wishlist"><i class="font-size-22 ec ec-favorites"></i></a></li>

                                <!-- Cart Dropdown -->
                                <li class="col pr-xl-0 px-2 px-sm-3 d-none d-xl-block">
                                    <div id="basicDropdownHoverInvoker" class="text-gray-90 position-relative d-flex " data-toggle="tooltip" data-placement="top" title="Cart"
                                        aria-controls="basicDropdownHover"
                                        aria-haspopup="true"
                                        aria-expanded="false"
                                        data-unfold-event="click"
                                        data-unfold-target="#basicDropdownHover"
                                        data-unfold-type="css-animation"
                                        data-unfold-duration="300"
                                        data-unfold-delay="300"
                                        data-unfold-hide-on-scroll="true"
                                        data-unfold-animation-in="slideInUp"
                                        data-unfold-animation-out="fadeOut">
                                        <i class="font-size-22 ec ec-shopping-bag"></i>
                                        <span class="bg-lg-down-black width-22 height-22 bg-primary position-absolute d-flex align-items-center justify-content-center rounded-circle left-12 top-8 font-weight-bold font-size-12 cart-count">
                                            {{ count(session('cart', [])) }}
                                        </span>
                                    </div>
                                    <div id="basicDropdownHover" class="cart-dropdown dropdown-menu dropdown-unfold border-top border-top-primary mt-3 border-width-2 border-left-0 border-right-0 border-bottom-0 left-auto right-0 u-shadow-v1-4 w-100 w-md-350" aria-labelledby="basicDropdownHoverInvoker">
                                        <div id="mini-cart-content">
                                            @include('mini-cart', [
                                                'cart' => session('cart', []),
                                            ])
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- End Header Icons -->
                </div>
            </div>
        </div>
        <!-- End Logo-Search-header-icons -->

        <!-- Vertical Menu + Secondary Menu (Desktop Only) -->
        <div class="d-none d-xl-block container">
            <div class="row">
                <!-- Vertical Menu (All Departments) -->
                <div class="col-md-auto d-none d-xl-block">
                    <div class="max-width-270 min-width-270">
                        <div id="basicsAccordion">
                            <div class="card border-0">
                                <div class="card-header card-collapse border-0" id="basicsHeadingOne">
                                    <button type="button"
                                        class="btn-link btn-remove-focus btn-block d-flex card-btn py-3 text-lh-1 px-4 shadow-none btn-primary rounded-top-lg border-0 font-weight-bold text-gray-90"
                                        data-toggle="collapse" data-target="#basicsCollapseOne" aria-expanded="false"
                                        aria-controls="basicsCollapseOne">
                                        <span class="ml-0 text-gray-90 mr-2"><span
                                                class="fa fa-list-ul"></span></span>
                                        <span class="pl-1 text-gray-90">All Departments</span>
                                    </button>
                                </div>
                                <div id="basicsCollapseOne" class="collapse vertical-menu">
                                    <div class="card-body p-0">
                                        <nav
                                            class="js-mega-menu navbar navbar-expand-xl u-header__navbar u-header__navbar--no-space">
                                            <div class="collapse navbar-collapse u-header__navbar-collapse">
                                                <ul class="navbar-nav u-header__navbar-nav">
                                                    @if (isset($grouped) && $grouped)
                                                        @foreach ($grouped as $parentId => $children)
                                                            <li class="nav-item hs-has-mega-menu u-header__nav-item"
                                                                data-event="hover">
                                                                <a class="nav-link u-header__nav-link u-header__nav-link-toggle"
                                                                    href="javascript:;" aria-haspopup="true"
                                                                    aria-expanded="false">
                                                                    {{ $children->first()->parent_title ?? 'Category' }}
                                                                </a>
                                                                <div class="hs-mega-menu vmm-tfw u-header__sub-menu">
                                                                    <div class="row u-header__mega-menu-wrapper">
                                                                        <div class="col">
                                                                            <span class="u-header__sub-menu-title">
                                                                                {{ $children->first()->parent_title ?? 'Category' }}
                                                                            </span>
                                                                            <ul class="u-header__sub-menu-nav-group">
                                                                                @foreach ($children as $cat)
                                                                                    <li>
                                                                                        <a class="nav-link u-header__sub-menu-nav-link"
                                                                                            href="{{ route('categories.products', $cat->category_id) }}">
                                                                                            {{ $cat->category_title }}
                                                                                        </a>
                                                                                    </li>
                                                                                @endforeach
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    @endif
                                                </ul>
                                            </div>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Vertical Menu -->

                <!-- Secondary Menu -->
                <div class="col">
                    <nav class="js-mega-menu navbar navbar-expand-md u-header__navbar u-header__navbar--no-space">
                        <div class="collapse navbar-collapse u-header__navbar-collapse">
                            <ul class="navbar-nav u-header__navbar-nav">
                                <li class="nav-item"><a class="nav-link u-header__nav-link" href="#">Super
                                        Deals</a></li>
                                <li class="nav-item"><a class="nav-link u-header__nav-link" href="#">Featured
                                        Brands</a></li>
                                <li class="nav-item"><a class="nav-link u-header__nav-link" href="#">Trending
                                        Styles</a></li>
                                <li class="nav-item"><a class="nav-link u-header__nav-link" href="#">Gift
                                        Cards</a></li>
                                <li class="nav-item u-header__nav-last-item ml-auto">
                                    <a class="text-gray-90" href="#">Free Shipping on Orders Rs.50+</a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
                <!-- End Secondary Menu -->
            </div>
        </div>
        <!-- End Vertical + Secondary Menu -->
    </div>
</header>
<!-- ========== END HEADER ========== -->

@push('scripts')
<script>
$(document).on('click', '.remove-from-cart', function(e) {
    e.preventDefault();
    let btn = $(this);
    let cartKey = btn.data('key');

    if (!cartKey) return;

    $.post('{{ route("cart.remove") }}', {
        _token: '{{ csrf_token() }}',
        key: cartKey
    })
    .done(function(res) {
        if (res.success) {
            $('.cart-count').text(res.count || 0);
            if (res.miniHtml) {
                $('#mini-cart-content').html(res.miniHtml);
            }
            
            // If on main cart page, update the main table directly
            if (window.location.pathname.indexOf('/cart') !== -1 && res.html) {
                if ($('#cart-items-body').length) {
                    $('#cart-items-body').html(res.html);
                }
            } else if (window.location.pathname.indexOf('/cart') !== -1) {
                 location.reload(); // Fallback if no HTML returned but we are on cart page
            }

            if (typeof showToast === 'function') {
                showToast('Item removed from cart', 'success');
            }
        }
    })
    .fail(function() {
        if (typeof showToast === 'function') {
            showToast('Failed to remove item', 'danger');
        }
    });
});
</script>
@endpush
