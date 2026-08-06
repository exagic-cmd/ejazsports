@extends('layouts.app')

@section('content')
    <main id="content" role="main">
        <!-- Slider Section -->
        <div class="mb-5">
            <div class="bg-img-hero" style="background-image: url(./assets/img/1920X422/img1.jpg);">
                <div class="container min-height-420 overflow-hidden">
                    <div class="js-slick-carousel u-slick"
                         data-autoplay="true"
                         data-autoplay-speed="5000"
                         data-pagi-classes="text-center position-absolute right-0 bottom-0 left-0 u-slick__pagination u-slick__pagination--long justify-content-start mb-3 mb-md-4 offset-xl-3 pl-2 pb-1">
                        @forelse ($banners as $banner)
                            <div class="js-slide bg-img-hero-center">
                                <div class="row min-height-420 py-7 py-md-0">
                                    <div class="offset-xl-3 col-xl-4 col-6 mt-md-8">
                                        <h1 class="font-size-64 text-lh-57 font-weight-light" data-scs-animation-in="fadeInUp">
                                            {{ $banner->web_heading }}
                                        </h1>
                                        <h6 class="font-size-15 font-weight-bold mb-3"
                                            data-scs-animation-in="fadeInUp"
                                            data-scs-animation-delay="200">
                                            {{ $banner->web_sub_heading }}
                                        </h6>
                                        <div class="mb-4" data-scs-animation-in="fadeInUp" data-scs-animation-delay="300">
                                            <span class="font-size-13">FROM</span>
                                            <div class="font-size-50 font-weight-bold text-lh-45">
                                                <sup>Rs.</sup>749<sup>99</sup>
                                            </div>
                                        </div>
                                        <a href="#"
                                           class="btn btn-primary transition-3d-hover rounded-lg font-weight-normal py-2 px-md-7 px-3 font-size-16"
                                           data-scs-animation-in="fadeInUp"
                                           data-scs-animation-delay="400">
                                            Start Buying
                                        </a>
                                    </div>
                                    <div class="col-xl-5 col-6 d-flex align-items-center"
                                         data-scs-animation-in="fadeInUp"
                                         data-scs-animation-delay="500">
                                        <img class="img-fluid"
                                             src="{{ !empty($banner->web_image) ? env('BACKEND_IMAGE_URL') . $banner->web_image : env('BACKEND_IMAGE_URL') . 'default.jpeg' }}"
                                             alt="Banner"
                                             onerror="this.onerror=null;this.src='{{ env('BACKEND_IMAGE_URL') . 'default.jpeg' }}';">
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 text-gray-6">
                                <p>No banners to display</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <!-- End Slider Section -->

        <div class="container">
            <!-- Promo Banners - Dynamic Categories -->

            @if($promoCategories->isNotEmpty())
            <div class="mb-5">
                <div class="row">
                    @foreach($promoCategories as $promoCategory)
                    <div class="col-md-6 mb-4 mb-xl-0 col-xl-3">
                        <a href="{{ route('categories.products', $promoCategory->id) }}" class="d-block text-gray-90">
                            <div class="min-height-132 py-1 d-flex bg-gray-1 align-items-center">
                                <div class="col-6 col-xl-5 col-wd-6 pr-0">
                                    <img class="img-fluid"
                                         src="{{ !empty($promoCategory->image) ? env('BACKEND_IMAGE_URL') . $promoCategory->image : env('BACKEND_IMAGE_URL') . 'default.jpeg' }}"
                                         alt="{{ $promoCategory->title }}"
                                         onerror="this.onerror=null;this.src='{{ env('BACKEND_IMAGE_URL') . 'default.jpeg' }}';">
                                </div>
                                <div class="col-6 col-xl-7 col-wd-6">
                                    <div class="mb-2 pb-1 font-size-18 font-weight-light text-ls-n1 text-lh-23">
                                        <strong>{{ $promoCategory->title }}</strong>
                                    </div>
                                    <div class="link text-gray-90 font-weight-bold font-size-15">
                                        Shop now
                                        <span class="link__icon ml-1">
                                            <span class="link__icon-inner"><i class="ec ec-arrow-right-categproes"></i></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            <!-- End Promo Banners -->

            <!-- Special Offer + Product Tabs -->
            <div class="mb-5">
                <div class="row">
                    <!-- Special Offer -->
                    <div class="col-md-auto mb-6 mb-md-0">
                        @php $special = $saleProducts->first() ?? null; @endphp
                        <div class="p-3 border border-width-2 border-primary borders-radius-20 bg-white min-width-auto w-100">
                            <div class="d-flex justify-content-between align-items-center m-1 ml-2">
                                <h3 class="font-size-22 mb-0 font-weight-normal text-lh-28 max-width-120">Special Offer</h3>
                                <div class="d-flex align-items-center flex-column justify-content-center bg-primary rounded-pill height-75 width-75 text-lh-1">
                                    <span class="font-size-12">Save</span>
                                    <div class="font-size-20 font-weight-bold">Rs.120</div>
                                </div>
                            </div>

                            <div class="mb-4 text-center">
                                @if($special)
                                    @php
                                        $specialSlug = $special->slug ?: \Illuminate\Support\Str::slug($special->title);
                                    @endphp
                                    <a href="{{ route('productdetail', $specialSlug) }}">
                                        <img class="img-fluid rounded shadow-sm" style="max-height: 280px; object-fit: contain;"
                                             src="{{ !empty($special->image_url) ? env('BACKEND_IMAGE_URL') . $special->image_url : env('BACKEND_IMAGE_URL') . 'default.jpeg' }}"
                                             alt="{{ $special->title }}"
                                             onerror="this.onerror=null;this.src='{{ env('BACKEND_IMAGE_URL') . 'default.jpeg' }}';">
                                    </a>
                                @else
                                    <img class="img-fluid opacity-50" style="max-height: 280px; object-fit: contain;"
                                         src="{{ env('BACKEND_IMAGE_URL') . 'default.jpeg' }}" alt="No Offer">
                                @endif
                            </div>

                            @if($special)
                                <h5 class="mb-2 font-size-14 text-center mx-auto max-width-180 text-lh-18">
                                    <a href="{{ route('productdetail', $specialSlug) }}" class="text-blue font-weight-bold">
                                        {{ $special->title }}
                                    </a>
                                </h5>
                                <div class="d-flex align-items-center justify-content-center mb-3">
                                    @if($special->discount_status && $special->discount_amount > 0)
                                        <del class="font-size-18 mr-2 text-gray-2">
                                            Rs.{{ number_format($special->effective_price + $special->discount_amount, 2) }}
                                        </del>
                                        <ins class="font-size-30 text-red text-decoration-none">
                                            Rs.{{ number_format($special->effective_price, 2) }}
                                        </ins>
                                    @else
                                        <ins class="font-size-30 text-red text-decoration-none">
                                            Rs.{{ number_format($special->effective_price, 2) }}
                                        </ins>
                                    @endif
                                </div>
                                @if(!$special->in_stock)
                                    <div class="text-center text-danger font-weight-bold mb-3">Out of Stock</div>
                                @endif
                            @endif

                            <div class="mb-3 mx-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Available: <strong>{{ $special->total_stock ?? 'N/A' }}</strong></span>
                                    <span>Already Sold: <strong>28</strong></span>
                                </div>
                                <div class="rounded-pill bg-gray-3 height-20 position-relative">
                                    <span class="position-absolute left-0 top-0 bottom-0 rounded-pill w-30 bg-primary"></span>
                                </div>
                            </div>

                            <div class="mb-2">
                                <h6 class="font-size-15 text-gray-2 text-center mb-3">Hurry Up! Offer ends in:</h6>
                                <div class="js-countdown d-flex justify-content-center" data-end-date="2025/12/31" data-format="%H:%M:%S">
                                    <div class="text-lh-1">
                                        <div class="text-gray-2 font-size-30 bg-gray-4 py-2 px-2 rounded-sm mb-2"><span class="js-cd-hours"></span></div>
                                        <div class="text-gray-2 font-size-12 text-center">HOURS</div>
                                    </div>
                                    <div class="mx-1 pt-1 text-gray-2 font-size-24">:</div>
                                    <div class="text-lh-1">
                                        <div class="text-gray-2 font-size-30 bg-gray-4 py-2 px-2 rounded-sm mb-2"><span class="js-cd-minutes"></span></div>
                                        <div class="text-gray-2 font-size-12 text-center">MINS</div>
                                    </div>
                                    <div class="mx-1 pt-1 text-gray-2 font-size-24">:</div>
                                    <div class="text-lh-1">
                                        <div class="text-gray-2 font-size-30 bg-gray-4 py-2 px-2 rounded-sm mb-2"><span class="js-cd-seconds"></span></div>
                                        <div class="text-gray-2 font-size-12 text-center">SECS</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Special Offer -->

                    <!-- Tab Products -->
                    <div class="col">
                        <div class="position-relative bg-white text-center z-index-2">
                            <ul class="nav nav-classic nav-tab justify-content-center" id="pills-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="pills-one-example1-tab" data-toggle="pill" href="#pills-one-example1">Featured</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-two-example1-tab" data-toggle="pill" href="#pills-two-example1">On Sale</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-three-example1-tab" data-toggle="pill" href="#pills-three-example1">Top Rated</a>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content" id="pills-tabContent">
                            <!-- Featured Products -->
                            <div class="tab-pane fade show active" id="pills-one-example1">
                                <ul class="row list-unstyled products-group no-gutters">
                                    @foreach ($featuredProducts as $product)
                                        <li class="col-6 col-wd-3 col-md-4 product-item">
                                            <div class="product-item__outer h-100">
                                                <div class="product-item__inner px-xl-4 p-3">
                                                    @if(!$product->in_stock)
                                                        <div class="position-absolute top-0 left-0 pt-3 pl-3">
                                                            <span class="badge badge-danger badge-pill">Out of Stock</span>
                                                        </div>
                                                    @endif

                                                    <div class="mb-2">
                                                        <a href="{{ route('brands.products', $product->brand_id) }}" class="font-size-12 text-gray-5">{{ $product->brand_title }}</a>
                                                    </div>
                                                    <h5 class="mb-1 product-item__title">
                                                        @php
                                                            $productSlug = $product->slug ?: \Illuminate\Support\Str::slug($product->title);
                                                        @endphp
                                                        <a href="{{ route('productdetail', $productSlug) }}" class="text-blue font-weight-bold">{{ $product->title }}</a>
                                                    </h5>

                                                    @if($product->have_variants)
                                                        <small class="d-block text-gray-6 mb-2">
                                                            {{ $product->variant_size }} @if($product->variant_shade) / {{ $product->variant_shade }} @endif
                                                        </small>
                                                    @endif

                                                    <div class="mb-2 text-center">
                                                        <a href="{{ route('productdetail', $productSlug) }}">
                                                            <img class="img-fluid"
                                                                 src="{{ !empty($product->image_url) ? env('BACKEND_IMAGE_URL') . $product->image_url : env('BACKEND_IMAGE_URL') . 'default.jpeg' }}"
                                                                 alt="{{ $product->title }}"
                                                                 onerror="this.onerror=null;this.src='{{ env('BACKEND_IMAGE_URL') . 'default.jpeg' }}';">
                                                        </a>
                                                    </div>

                                                    <div class="flex-center-between mb-1">
                                                        <div class="prodcut-price">
                                                            <div class="text-gray-100">Rs.{{ number_format($product->effective_price, 2) }}</div>
                                                        </div>
                                                        {{-- <div class="d-none d-xl-block prodcut-add-cart">
                                                            <button type="button"
                                                                    class="btn btn-primary-dark transition-3d-hover add-to-cart-btn"
                                                                    data-product-id="{{ $product->id }}"
                                                                    data-variant-id="{{ $product->first_variant_id ?? '' }}"
                                                                    {{ !$product->in_stock ? 'disabled' : '' }}>
                                                                <i class="ec ec-add-to-cart mr-2"></i>
                                                            </button>
                                                        </div> --}}
                                                    </div>

                                                    {{-- <div class="product-item__footer">
                                                        <div class="border-top pt-2 flex-center-between flex-wrap">
                                                            <a href="#" class="text-gray-6 font-size-13"><i class="ec ec-compare mr-1 font-size-15"></i> Compare</a>
                                                            <a href="#" class="text-gray-6 font-size-13"><i class="ec ec-favorites mr-1 font-size-15"></i> Wishlist</a>
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- On Sale -->
                            <div class="tab-pane fade" id="pills-two-example1">
                                <ul class="row list-unstyled products-group no-gutters">
                                    @foreach ($saleProducts as $product)
                                        @include('partials.product-card-home', ['product' => $product])
                                    @endforeach
                                </ul>
                            </div>

                            <!-- Top Rated -->
                            <div class="tab-pane fade" id="pills-three-example1">
                                <ul class="row list-unstyled products-group no-gutters">
                                    @foreach ($topratedProducts as $product)
                                        @include('partials.product-card-home', ['product' => $product])
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Special Offer + Tabs -->

            <!-- Brands 4-1-4 Grid -->
            <div class="products-group-4-1-4 space-1 bg-gray-7">
                <div class="container">
                    <div class="position-relative text-center z-index-2 mb-3">
                        <ul class="nav nav-classic nav-tab nav-tab-sm px-md-3 justify-content-start border-md-down-bottom-0 pb-1 pb-lg-0 mb-n1 mb-lg-0"
                            id="pills-tab-1" role="tablist">
                            @foreach ($brands as $index => $brand)
                                <li class="nav-item flex-shrink-0 flex-lg-shrink-1">
                                    <a class="nav-link {{ $index == 0 ? 'active' : '' }}"
                                       id="Tpills-{{ $index + 1 }}-example1-tab"
                                       data-toggle="pill"
                                       href="#Tpills-{{ $index + 1 }}-example1">
                                        {{ $brand->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="tab-content" id="Tpills-tabContent">
                        @foreach ($brands as $index => $brand)
                            @php
                                $first = $firstProducts[$brand->id] ?? null;
                                $secondThird = $productsByBrand[$brand->id]['secondThird'] ?? collect();
                                $lastTwo = $productsByBrand[$brand->id]['lastTwo'] ?? collect();
                            @endphp
                            <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}"
                                 id="Tpills-{{ $index + 1 }}-example1">
                                <div class="row no-gutters">
                                    <!-- Left Column -->
                                    <div class="col-md-3 col-wd-4 d-md-flex d-wd-block">
                                        <ul class="row list-unstyled products-group no-gutters mb-0 flex-xl-column flex-wd-row">
                                            @foreach ($secondThird as $product)
                                                @include('partials.product-card-small', ['product' => $product, 'brand' => $brand])
                                            @endforeach
                                        </ul>
                                    </div>

                                    <!-- Center Main Product -->
                                    <div class="col-md-6 col-wd-4 products-group-1">
                                        @if($first && $first->in_stock)
                                            <ul class="row list-unstyled products-group no-gutters bg-white h-100 mb-0">
                                                <li class="col product-item remove-divider">
                                                    <div class="h-100 w-100 prodcut-box-shadow">
                                                        <div class="bg-white p-3">
                                                            <div class="d-flex flex-column">
                                                                <div class="mb-1">
                                                                    <a href="{{ route('brands.products', $brand->id) }}" class="font-size-12 text-gray-5">{{ $brand->title }}</a>
                                                                </div>
                                                                <h5 class="mb-0 product-item__title">
                                                                    @php
                                                                        $firstSlug = $first->slug ?: \Illuminate\Support\Str::slug($first->title);
                                                                    @endphp
                                                                    <a href="{{ route('productdetail', $firstSlug) }}" class="text-blue font-weight-bold">{{ $first->title }}</a>
                                                                </h5>

                                                                @if($first->have_variants)
                                                                    <small class="text-gray-6 d-block mt-2">
                                                                        {{ $first->variant_size }} @if($first->variant_shade) / {{ $first->variant_shade }} @endif
                                                                    </small>
                                                                @endif

                                                                <div class="mb-1 min-height-4-1-4 text-center my-4">
                                                                    <a href="{{ route('productdetail', $firstSlug) }}">
                                                                        <img class="img-fluid"
                                                                             src="{{ !empty($first->image_url) ? env('BACKEND_IMAGE_URL') . $first->image_url : (!empty($first->image) ? env('BACKEND_IMAGE_URL') . $first->image : env('BACKEND_IMAGE_URL') . 'default.jpeg') }}"
                                                                             alt="{{ $first->title }}"
                                                                             onerror="this.onerror=null;this.src='{{ env('BACKEND_IMAGE_URL') . 'default.jpeg' }}';">
                                                                    </a>
                                                                </div>

                                                                <div class="flex-center-between">
                                                                    <div class="prodcut-price">
                                                                        <div class="text-gray-100">Rs.{{ number_format($first->effective_price, 2) }}</div>
                                                                    </div>
                                                                    {{-- <div class="d-none d-xl-block prodcut-add-cart">
                                                                        <a href="{{ url('/product/' . $first->id) }}"
                                                                           class="btn btn-primary-dark transition-3d-hover">
                                                                            <i class="ec ec-eye"></i>
                                                                        </a>
                                                                    </div> --}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        @else
                                            <div class="h-100 d-flex align-items-center justify-content-center bg-light">
                                                <p class="text-gray-6">No products available</p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Right Column -->
                                    <div class="col-md-3 col-wd-4 d-md-flex d-wd-block">
                                        <ul class="row list-unstyled products-group no-gutters mb-0 flex-xl-column flex-wd-row">
                                            @foreach ($lastTwo as $product)
                                                @include('partials.product-card-small', ['product' => $product, 'brand' => $brand])
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <!-- End Brands Grid -->

            <!-- Bestsellers Carousel -->
            <div class="space-top-2">
                <div class="d-flex justify-content-between border-bottom border-color-1 flex-md-nowrap flex-wrap border-sm-bottom-0">
                    <h3 class="section-title mb-0 pb-2 font-size-22">Bestsellers</h3>
                    <ul id="bestsellers-nav" class="nav nav-pills mb-2 pt-3 pt-md-0 mb-0 border-top border-color-1 border-md-top-0 align-items-center font-size-15 flex-nowrap flex-md-wrap overflow-auto overflow-md-visible">
                        <li class="nav-item flex-shrink-0 flex-md-shrink-1">
                            <a class="nav-link text-gray-90 btn btn-outline-primary border-width-2 rounded-pill py-1 px-4 active" href="#" data-slide-index="0">Top 16</a>
                        </li>
                        @foreach ($top_categories as $category)
                            <li class="nav-item flex-shrink-0 flex-md-shrink-1">
                                <a class="nav-link text-gray-8" href="#" data-slide-index="{{ $loop->index + 1 }}">{{ $category->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div id="bestsellers-carousel" class="js-slick-carousel u-slick u-slick--gutters-2 overflow-hidden u-slick-overflow-visible pt-3 pb-6"
                     data-pagi-classes="text-center right-0 bottom-1 left-0 u-slick__pagination u-slick__pagination--long mb-0 z-index-n1 mt-4">
                    @php
                        $allTopProducts = $top_16_products_first->merge($top_16_products_second);
                        $topChunks = $allTopProducts->chunk(8);
                    @endphp
                    @foreach ($topChunks as $chunk)
                        <div class="js-slide">
                            <ul class="row list-unstyled products-group no-gutters mb-0 overflow-visible">
                                @foreach ($chunk as $product)
                                    <li class="col-wd-3 col-md-4 product-item product-item__card pb-2 mb-2 pb-md-0 mb-md-0 border-bottom border-md-bottom-0">
                                        <div class="product-item__outer h-100">
                                            <div class="product-item__inner p-md-3 row no-gutters">
                                                <div class="col col-lg-auto product-media-left">
                                                    @php
                                                        $productSlug = $product->slug ?: \Illuminate\Support\Str::slug($product->title);
                                                    @endphp
                                                    <a href="{{ route('productdetail', $productSlug) }}" class="max-width-150 d-block">
                                                        <img class="img-fluid"
                                                             src="{{ !empty($product->image_url) ? env('BACKEND_IMAGE_URL') . $product->image_url : env('BACKEND_IMAGE_URL') . 'default.jpeg' }}"
                                                             alt="{{ $product->title }}"
                                                             onerror="this.onerror=null;this.src='{{ env('BACKEND_IMAGE_URL') . 'default.jpeg' }}';">
                                                    </a>
                                                </div>
                                                <div class="col product-item__body pl-2 pl-lg-3 mr-xl-2 mr-wd-1">
                                                    <div class="mb-4">
                                                        <div class="mb-2">
                                                            <a href="#" class="font-size-12 text-gray-5">{{ $product->category_title ?? 'Category' }}</a>
                                                        </div>
                                                        <h5 class="product-item__title">
                                                            @php
                                                                $productSlug = $product->slug ?: \Illuminate\Support\Str::slug($product->title);
                                                            @endphp
                                                            <a href="{{ route('productdetail', $productSlug) }}" class="text-blue font-weight-bold">{{ $product->title }}</a>
                                                        </h5>
                                                    </div>
                                                    <div class="flex-center-between mb-3">
                                                        <div class="prodcut-price">
                                                            <div class="text-gray-100">Rs.{{ number_format($product->effective_price, 2) }}</div>
                                                        </div>
                                                        {{-- <div class="d-none d-xl-block prodcut-add-cart">
                                                                        <a href="{{ url('/product/' . $product->id) }}"
                                                                           class="btn btn-primary-dark transition-3d-hover">
                                                                            <i class="ec ec-eye"></i>
                                                                        </a>
                                                                    </div> --}}
                                                    </div>
                                                    {{-- <div class="product-item__footer">
                                                        <div class="border-top pt-2 flex-center-between flex-wrap">
                                                            <a href="#" class="text-gray-6 font-size-13"><i class="ec ec-compare mr-1 font-size-15"></i> Compare</a>
                                                            <a href="#" class="text-gray-6 font-size-13"><i class="ec ec-favorites mr-1 font-size-15"></i> Wishlist</a>
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach

                    <!-- Category-based bestsellers -->
                    @foreach ($top_categories as $category)
                        @php
                            $products = $category_products[$category->id]['first']->merge($category_products[$category->id]['second']);
                            $chunks = $products->chunk(8);
                        @endphp
                        @foreach ($chunks as $chunk)
                            <div class="js-slide">
                                <ul class="row list-unstyled products-group no-gutters mb-0 overflow-visible">
                                    @foreach ($chunk as $product)
                                        @include('partials.bestseller-card', ['product' => $product])
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
            <!-- End Bestsellers -->

            <!-- Full Banner -->
            <div class="mb-6">
                <a href="#" class="d-block text-gray-90">
                    <div style="background-image: url(assets/img/1400X206/img1.jpg);">
                        <div class="space-top-2-md p-4 pt-6 pt-md-8 pt-lg-6 pt-xl-8 pb-lg-4 px-xl-8 px-lg-6">
                            <div class="flex-horizontal-center mt-lg-3 mt-xl-0 overflow-auto overflow-md-visble">
                                <h1 class="text-lh-38 font-size-32 font-weight-light mb-0 flex-shrink-0 flex-md-shrink-1">
                                    SHOP AND <strong>SAVE BIG</strong> ON HARD BALL ACCESSORIES
                                </h1>
                                <div class="ml-5 flex-content-center flex-shrink-0">
                                    <div class="bg-primary rounded-lg px-6 py-2">
                                        <em class="font-size-14 font-weight-light">STARTING AT</em>
                                        <div class="font-size-30 font-weight-bold text-lh-1">
                                            <sup>Rs.</sup>790
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Recently Viewed -->
            @if($recent_products->isNotEmpty())
                <div class="mb-6">
                    <div class="position-relative">
                        <div class="border-bottom border-color-1 mb-2">
                            <h3 class="section-title mb-0 pb-2 font-size-22">Recently Viewed</h3>
                        </div>
                        <div class="js-slick-carousel u-slick position-static overflow-hidden u-slick-overflow-visble pb-7 pt-2 px-1"
                            data-pagi-classes="text-center right-0 bottom-1 left-0 u-slick__pagination u-slick__pagination--long mb-0 z-index-n1 mt-3 mt-md-0"
                            data-slides-show="7" data-slides-scroll="1"
                            data-arrows-classes="position-absolute top-0 font-size-17 u-slick__arrow-normal top-10"
                            data-arrow-left-classes="fa fa-angle-left right-1"
                            data-arrow-right-classes="fa fa-angle-right right-0"
                            data-responsive='[{
                              "breakpoint": 1400,
                              "settings": {
                                "slidesToShow": 6
                              }
                            }, {
                                "breakpoint": 1200,
                                "settings": {
                                  "slidesToShow": 4
                                }
                            }, {
                              "breakpoint": 992,
                              "settings": {
                                "slidesToShow": 3
                              }
                            }, {
                              "breakpoint": 768,
                              "settings": {
                                "slidesToShow": 2
                              }
                            }, {
                              "breakpoint": 554,
                              "settings": {
                                "slidesToShow": 2
                              }
                            }]'>
                            @foreach ($recent_products as $product)
                                @include('partials.recently-viewed-card', ['product' => $product])
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Brand Carousel -->
            <div class="mb-8">
                <div class="py-2 border-top border-bottom">
                    <div class="js-slick-carousel u-slick my-1"
                         data-slides-show="6"
                         data-slides-scroll="1"
                         data-arrows-classes="d-none d-lg-inline-block u-slick__arrow-normal u-slick__arrow-centered--y"
                         data-arrow-left-classes="fa fa-angle-left u-slick__arrow-classic-inner--left z-index-9"
                         data-arrow-right-classes="fa fa-angle-right u-slick__arrow-classic-inner--right">
                        @foreach ($brands as $brand)
                            <div class="js-slide text-center">
                                <a href="{{ route('brands.products', $brand->id) }}" class="link-hover__brand">
                                    <img class="img-fluid m-auto max-height-50"
                                         src="{{ !empty($brand->image) ? env('BACKEND_IMAGE_URL') . $brand->image : env('BACKEND_IMAGE_URL') . 'default.jpeg' }}"
                                         alt="{{ $brand->title }}"
                                         onerror="this.onerror=null;this.src='{{ env('BACKEND_IMAGE_URL') . 'default.jpeg' }}';">
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
<script>
    // Bestsellers Carousel Navigation
    $(window).on('load', function() {
        var $carousel = $('#bestsellers-carousel');
        var $nav = $('#bestsellers-nav a[data-slide-index]');
        $nav.on('click', function(e) {
            e.preventDefault();
            var idx = parseInt($(this).data('slide-index'), 10);
            $carousel.slick('slickGoTo', idx);
            $nav.removeClass('active');
            $(this).addClass('active');
        });
    });

    // Add to Cart Handler
    $(document).on('click', '.add-to-cart-btn', function(e) {
        e.preventDefault();
        let btn = $(this);
        if (btn.is(':disabled')) return;

        let productId = btn.data('product-id');
        let variantId = btn.data('variant-id') || null;
        let quantity = 1;

        btn.prop('disabled', true).html('<i class="ec ec-add-to-cart mr-2"></i> Adding...');

        $.post('{{ route("cart.add") }}', {
            _token: '{{ csrf_token() }}',
            product_id: productId,
            variant_id: variantId,
            quantity: quantity
        })
        .done(function(res) {
            if (res.success) {
                $('.cart-count').text(res.count);
                // Refresh mini-cart content
                if (res.miniHtml) {
                    $('#mini-cart-content').html(res.miniHtml);
                }
                showToast('Product added to cart!', 'success');
            } else {
                showToast(res.message || 'Failed to add to cart.', 'danger');
            }
        })
        .fail(function() {
            showToast('Network error. Try again.', 'danger');
        })
        .always(function() {
            btn.prop('disabled', false).html('<i class="ec ec-add-to-cart mr-2"></i>');
        });
    });

    function showToast(message, type = 'success') {
        const toast = $(`
            <div class="toast-notification position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
                <div class="alert alert-${type} alert-dismissible fade show shadow-lg">
                    <strong>${message}</strong>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            </div>
        `);
        $('body').append(toast);
        toast.fadeIn(300);
        setTimeout(() => toast.fadeOut(500, () => toast.remove()), 3000);
    }
</script>
@endpush

<style>
    .out-of-stock-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: #dc3545;
        color: white;
        padding: 5px 10px;
        font-size: 12px;
        border-radius: 4px;
        z-index: 10;
    }
</style>
