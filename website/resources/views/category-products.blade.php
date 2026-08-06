@extends('layouts.app')

@section('content')
<style>
    /* Prevent Browse Categories overflow */
    #sidebarNav {
        max-height: 300px;
        overflow-y: auto;
        scrollbar-width: thin;
    }
    #sidebarNav::-webkit-scrollbar {
        width: 6px;
    }
    #sidebarNav::-webkit-scrollbar-thumb {
        background-color: #ccc;
        border-radius: 3px;
    }
    /* Slider styling */
    .irs--round {
        height: 50px;
    }
    .irs--round .irs-line {
        height: 6px;
        background-color: #e5e5e5;
        border-radius: 3px;
        top: 36px;
    }
    .irs--round .irs-bar {
        height: 6px;
        background-color: #fed700;
        top: 36px;
    }
    .irs--round .irs-handle {
        top: 29px;
        width: 20px;
        height: 20px;
        border: 2px solid #fed700;
        background-color: #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .irs--round .irs-handle:hover, .irs--round .irs-handle.state_hover {
        background-color: #fed700;
    }
    .irs--round .irs-from, .irs--round .irs-to, .irs--round .irs-single {
        background-color: #333e48;
        border-radius: 4px;
        font-size: 13px;
        padding: 4px 8px;
    }
    .irs--round .irs-from:before, .irs--round .irs-to:before, .irs--round .irs-single:before {
        border-top-color: #333e48;
    }
    .range-slider {
        position: relative;
        margin-bottom: 25px;
    }
    .btn-outline-secondary {
        border-color: #6c757d;
        color: #6c757d;
    }
    .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: #fff;
    }
</style>

<!-- ========== MAIN CONTENT ========== -->
<main id="content" role="main">
    <!-- Breadcrumb -->
    <div class="bg-gray-13 bg-md-transparent">
        <div class="container">
            <div class="my-md-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visible">
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">{{ $category->title }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <div class="container">
        <div class="row mb-8">
            <div class="d-none d-xl-block col-xl-3 col-wd-2gdot5">
                <div class="mb-6 border border-width-2 border-color-3 borders-radius-6">
                    <!-- Categories List -->
                    <ul id="sidebarNav" class="list-unstyled mb-0 sidebar-navbar view-all">
                        <li><div class="dropdown-title">Browse Categories</div></li>
                        @php
                            $visibleCategories = $categories->slice(0, 10);
                            $hiddenCategories = $categories->slice(10);
                        @endphp
                        @foreach($visibleCategories as $cat)
                        <li>
                            <a class="dropdown-toggle dropdown-toggle-collapse {{ $category->id == $cat->id ? '' : 'collapsed' }}"
                               href="javascript:;" role="button" data-toggle="collapse"
                               aria-expanded="{{ $category->id == $cat->id ? 'true' : 'false' }}"
                               aria-controls="sidebarNav{{ $cat->id }}Collapse"
                               data-target="#sidebarNav{{ $cat->id }}Collapse">
                                {{ $cat->title }}<span class="text-gray-25 font-size-12 font-weight-normal"> ({{ $cat->product_count }})</span>
                            </a>
                            <div id="sidebarNav{{ $cat->id }}Collapse" class="collapse {{ $category->id == $cat->id ? 'show' : '' }}"
                                 data-parent="#sidebarNav">
                                <ul class="list-unstyled dropdown-list">
                                    <li><a class="dropdown-item" href="{{ route('categories.products', $cat->id) }}">{{ $cat->title }}<span class="text-gray-25 font-size-12 font-weight-normal"> ({{ $cat->product_count }})</span></a></li>
                                </ul>
                            </div>
                        </li>
                        @endforeach
                        @if($hiddenCategories->isNotEmpty())
                        <div class="collapse" id="collapseCategory">
                            @foreach($hiddenCategories as $cat)
                            <li>
                                <a class="dropdown-toggle dropdown-toggle-collapse {{ $category->id == $cat->id ? '' : 'collapsed' }}"
                                   href="javascript:;" role="button" data-toggle="collapse"
                                   aria-expanded="{{ $category->id == $cat->id ? 'true' : 'false' }}"
                                   aria-controls="sidebarNav{{ $cat->id }}Collapse"
                                   data-target="#sidebarNav{{ $cat->id }}Collapse">
                                    {{ $cat->title }}<span class="text-gray-25 font-size-12 font-weight-normal"> ({{ $cat->product_count }})</span>
                                </a>
                                <div id="sidebarNav{{ $cat->id }}Collapse" class="collapse {{ $category->id == $cat->id ? 'show' : '' }}"
                                     data-parent="#sidebarNav">
                                    <ul class="list-unstyled dropdown-list">
                                        <li><a class="dropdown-item" href="{{ route('categories.products', $cat->id) }}">{{ $cat->title }}<span class="text-gray-25 font-size-12 font-weight-normal"> ({{ $cat->product_count }})</span></a></li>
                                    </ul>
                                </div>
                            </li>
                            @endforeach
                        </div>
                        <a class="link link-collapse small font-size-13 text-gray-27 d-inline-flex mt-2"
                           data-toggle="collapse" href="#collapseCategory" role="button" aria-expanded="false"
                           aria-controls="collapseCategory">
                            <span class="link__icon text-gray-27 bg-white">
                                <span class="link__icon-inner">+</span>
                            </span>
                            <span class="link-collapse__default">Show more</span>
                            <span class="link-collapse__active">Show less</span>
                        </a>
                        @endif
                    </ul>
                    <!-- End Categories List -->
                </div>
                <div class="mb-6">
                    <form id="filterForm" method="get" action="{{ $category->id ? route('categories.products', $category->id) : route('search') }}">
                        <input type="hidden" name="q" value="{{ request()->get('q') }}">
                        <input type="hidden" name="category" value="{{ request()->get('category', $category->id) }}">
                        <div class="border-bottom border-color-1 mb-5">
                            <h3 class="section-title section-title__sm mb-0 pb-2 font-size-18">Filters</h3>
                        </div>
                        <!-- Brand Filter -->
                        <div class="border-bottom pb-4 mb-4">
                            <h4 class="font-size-14 mb-3 font-weight-bold">Brands</h4>
                            @php
                                $visibleBrands = $brands->slice(0, 10);
                                $hiddenBrands = $brands->slice(10);
                            @endphp
                            @foreach($visibleBrands as $brand)
                            <div class="form-group d-flex align-items-center justify-content-between mb-2 pb-1">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="brand{{ $brand->id }}"
                                           name="brand[]" value="{{ $brand->id }}"
                                           {{ in_array($brand->id, request()->input('brand', [])) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="brand{{ $brand->id }}">{{ $brand->title }}
                                        <span class="text-gray-25 font-size-12 font-weight-normal"> ({{ $brand->product_count }})</span>
                                    </label>
                                </div>
                            </div>
                            @endforeach
                            @if($hiddenBrands->isNotEmpty())
                            <div class="collapse" id="collapseBrand">
                                @foreach($hiddenBrands as $brand)
                                <div class="form-group d-flex align-items-center justify-content-between mb-2 pb-1">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="brand{{ $brand->id }}"
                                               name="brand[]" value="{{ $brand->id }}"
                                               {{ in_array($brand->id, request()->input('brand', [])) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="brand{{ $brand->id }}">{{ $brand->title }}
                                            <span class="text-gray-25 font-size-12 font-weight-normal"> ({{ $brand->product_count }})</span>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <a class="link link-collapse small font-size-13 text-gray-27 d-inline-flex mt-2"
                               data-toggle="collapse" href="#collapseBrand" role="button" aria-expanded="false"
                               aria-controls="collapseBrand">
                                <span class="link__icon text-gray-27 bg-white">
                                    <span class="link__icon-inner">+</span>
                                </span>
                                <span class="link-collapse__default">Show more</span>
                                <span class="link-collapse__active">Show less</span>
                            </a>
                            @endif
                            <input type="hidden" name="sort" value="{{ request()->input('sort', 'default') }}">
                        </div>
                        <!-- Price Range Slider -->
                        <div class="range-slider">
                            <h4 class="font-size-14 mb-3 font-weight-bold">Price</h4>
                            <input class="js-range-slider" type="text"
                                   data-extra-classes="u-range-slider u-range-slider-indicator u-range-slider-grid"
                                   data-type="double"
                                   data-grid="false"
                                   data-hide-from-to="true"
                                   data-hide-min-max="true"
                                   data-prefix="Rs."
                                   data-min="0"
                                   data-max="{{ $max_price }}"
                                   data-from="{{ request()->input('min_price', 0) }}"
                                   data-to="{{ request()->input('max_price', $max_price) }}"
                                   data-result-min="#rangeSliderExample3MinResult"
                                   data-result-max="#rangeSliderExample3MaxResult"
                                   aria-label="Price range slider">
                            <div class="mt-1 text-gray-111 d-flex align-items-center mb-4">
                                <span class="font-weight-bold">Rs.</span>
                                <span id="rangeSliderExample3MinResult" class="ml-1">{{ request()->input('min_price', 0) }}</span>
                                <span class="mx-2"> — </span>
                                <span class="font-weight-bold">Rs.</span>
                                <span id="rangeSliderExample3MaxResult" class="ml-1">{{ request()->input('max_price', $max_price) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn px-4 btn-primary-dark-w py-2 rounded-lg">Filter</button>
                                <a href="{{ route('categories.products', $category->id) }}"
                                   class="btn px-4 btn-outline-secondary py-2 rounded-lg">Clear Filters</a>
                            </div>
                            <input type="hidden" name="min_price" id="rangeSliderExample3MinResultHidden"
                                   value="{{ request()->input('min_price', 0) }}">
                            <input type="hidden" name="max_price" id="rangeSliderExample3MaxResultHidden"
                                   value="{{ request()->input('max_price', $max_price) }}">
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-xl-9 col-wd-9gdot5">
                <!-- Shop Control Bar -->
                <div class="bg-gray-1 flex-center-between borders-radius-9 py-1">
                    <div class="d-xl-none">
                        <a id="sidebarNavToggler1" class="btn btn-sm py-1 font-weight-normal" href="javascript:;"
                           role="button" aria-controls="sidebarContent1" aria-haspopup="true" aria-expanded="false"
                           data-unfold-event="click" data-unfold-hide-on-scroll="false"
                           data-unfold-target="#sidebarContent1" data-unfold-type="css-animation"
                           data-unfold-animation-in="fadeInLeft" data-unfold-animation-out="fadeOutLeft"
                           data-unfold-duration="500">
                            <i class="fas fa-sliders-h"></i> <span class="ml-1">Filters</span>
                        </a>
                    </div>
                    <div class="px-3 d-none d-xl-block">
                        <ul class="nav nav-tab-shop" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="pills-one-example1-tab" data-toggle="pill"
                                   href="#pills-one-example1" role="tab" aria-controls="pills-one-example1"
                                   aria-selected="true">
                                    <div class="d-md-flex justify-content-md-center align-items-md-center">
                                        <i class="fa fa-th"></i>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-two-example1-tab" data-toggle="pill"
                                   href="#pills-two-example1" role="tab" aria-controls="pills-two-example1"
                                   aria-selected="false">
                                    <div class="d-md-flex justify-content-md-center align-items-md-center">
                                        <i class="fa fa-align-justify"></i>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-three-example1-tab" data-toggle="pill"
                                   href="#pills-three-example1" role="tab" aria-controls="pills-three-example1"
                                   aria-selected="false">
                                    <div class="d-md-flex justify-content-md-center align-items-md-center">
                                        <i class="fa fa-list"></i>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="d-flex">
                        <form id="sortForm" method="get" action="{{ route('categories.products', $category->id) }}">
                            <select class="js-select selectpicker dropdown-select max-width-200 max-width-160-sm right-dropdown-0 px-2 px-xl-0"
                                    name="sort"
                                    data-style="btn-sm bg-white font-weight-normal py-2 border text-gray-20 bg-lg-down-transparent border-lg-down-0">
                                <option value="default" {{ request('sort') == 'default' || !request('sort') ? 'selected' : '' }}>Default sorting</option>
                                <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>Sort by popularity</option>
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Sort by latest</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Sort by price: low to high</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Sort by price: high to low</option>
                            </select>
                            @foreach(request()->input('brand', []) as $brandId)
                                <input type="hidden" name="brand[]" value="{{ $brandId }}">
                            @endforeach
                            <input type="hidden" name="min_price" value="{{ request()->input('min_price', 0) }}">
                            <input type="hidden" name="max_price" value="{{ request()->input('max_price', $max_price) }}">
                        </form>
                    </div>
                </div>
                <!-- End Shop Control Bar -->
                <!-- Empty Results Message -->
                @if($products->isEmpty())
                    <div class="col-12 text-center my-4">
                        <p class="text-gray-110 font-size-16">No products found matching your filter criteria. Try adjusting the filters.</p>
                    </div>
                @endif
                <!-- Tab Content -->
                <div class="tab-content" id="pills-tabContent">
                    <!-- Grid View -->
                    <div class="tab-pane fade pt-2 show active" id="pills-one-example1" role="tabpanel"
                         aria-labelledby="pills-one-example1-tab" data-target-group="groups">
                        <ul class="row list-unstyled products-group no-gutters">
                            @foreach($products as $p)
                            <li class="col-6 col-md-3 product-item">
                                <div class="product-item__outer h-100 w-100">
                                    <div class="product-item__inner px-xl-4 p-3">
                                        <div class="product-item__body pb-xl-2">
                                            @php $pSlug = $p->slug ?: \Illuminate\Support\Str::slug($p->title); @endphp
                                            <div class="mb-2"><a href="{{ route('productdetail', $pSlug) }}"
                                                                 class="font-size-12 text-gray-5">{{ $category->title }}</a></div>
                                            <h5 class="mb-1 product-item__title"><a href="{{ route('productdetail', $pSlug) }}"
                                                                                   class="text-blue font-weight-bold">{{ $p->title }}</a></h5>
                                            <div class="mb-2">
                                                <a href="{{ route('productdetail', $pSlug) }}" class="d-block text-center">
                                                    <img class="img-fluid" src="{{ !empty($p->image_url) ? env('BACKEND_IMAGE_URL') . $p->image_url : env('BACKEND_IMAGE_URL') . 'default.jpeg' }}"
                                                         alt="{{ $p->title }}" style="height: 250px; width: 100%;" onerror="this.onerror=null;this.src='{{ env('BACKEND_IMAGE_URL') . 'default.jpeg' }}';">
                                                </a>
                                            </div>
                                            <div class="flex-center-between mb-1">
                                                <div class="prodcut-price">
                                                    <div class="text-gray-100">Rs. {{ number_format($p->price + ($p->min_additional ?? 0), 2) }}</div>
                                                </div>
                                                {{-- <div class="d-none d-xl-block prodcut-add-cart">
                                                    <a href="{{ route('cart') }}"
                                                       class="btn-add-cart btn-primary transition-3d-hover"><i
                                                            class="ec ec-add-to-cart"></i></a>
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <!-- List View -->
                    <div class="tab-pane fade pt-2" id="pills-two-example1" role="tabpanel"
                         aria-labelledby="pills-two-example1-tab" data-target-group="groups">
                        <ul class="row list-unstyled products-group no-gutters">
                            @foreach($products as $p)
                            <li class="col-6 col-md-3 product-item">
                                <div class="product-item__outer h-100">
                                    <div class="product-item__inner px-xl-4 p-3">
                                        <div class="product-item__body pb-xl-2">
                                            @php $pSlug = $p->slug ?: \Illuminate\Support\Str::slug($p->title); @endphp
                                            <div class="mb-2"><a href="{{ route('productdetail', $pSlug) }}"
                                                                 class="font-size-12 text-gray-5">{{ $category->title }}</a></div>
                                            <h5 class="mb-1 product-item__title"><a href="{{ route('productdetail', $pSlug) }}"
                                                                                   class="text-blue font-weight-bold">{{ $p->title }}</a></h5>
                                            <div class="mb-2">
                                                <a href="{{ route('productdetail', $pSlug) }}" class="d-block text-center">
                                                    <img class="img-fluid" src="{{ !empty($p->image_url) ? env('BACKEND_IMAGE_URL') . $p->image_url : env('BACKEND_IMAGE_URL') . 'default.jpeg' }}"
                                                         alt="{{ $p->title }}" style="height: 250px; width: 100%;" onerror="this.onerror=null;this.src='{{ env('BACKEND_IMAGE_URL') . 'default.jpeg' }}';">
                                                </a>
                                            </div>
                                            <div class="mb-3">
                                                <a class="d-inline-flex align-items-center small font-size-14" href="#">
                                                    <div class="text-warning mr-2">
                                                        <small class="fas fa-star"></small>
                                                        <small class="fas fa-star"></small>
                                                        <small class="fas fa-star"></small>
                                                        <small class="fas fa-star"></small>
                                                        <small class="far fa-star text-muted"></small>
                                                    </div>
                                                    <span class="text-secondary">(40)</span>
                                                </a>
                                            </div>
                                            <div class="font-size-12 text-gray-110 mb-4">
                                                {{ $p->short_desc ?? 'No description available' }}
                                            </div>
                                            <div class="text-gray-20 mb-2 font-size-12">SKU: {{ $p->slug }}</div>
                                            <div class="flex-center-between mb-1">
                                                <div class="prodcut-price">
                                                    <div class="text-gray-100">Rs. {{ number_format($p->price + ($p->min_additional ?? 0), 2) }}</div>
                                                </div>
                                                {{-- <div class="d-none d-xl-block prodcut-add-cart">
                                                    <a href="{{ route('cart') }}"
                                                       class="btn-add-cart btn-primary transition-3d-hover"><i
                                                            class="ec ec-add-to-cart"></i></a>
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <!-- Detailed List View -->
                    <div class="tab-pane fade pt-2" id="pills-three-example1" role="tabpanel"
                         aria-labelledby="pills-three-example1-tab" data-target-group="groups">
                        <ul class="d-block list-unstyled products-group prodcut-list-view">
                            @foreach($products as $p)
                            <li class="product-item remove-divider">
                                <div class="product-item__outer w-100">
                                    <div class="product-item__inner remove-prodcut-hover py-4 row">
                                        <div class="product-item__header col-6 col-md-4">
                                            <div class="mb-2">
                                                @php $pSlug = $p->slug ?: \Illuminate\Support\Str::slug($p->title); @endphp
                                                <a href="{{ route('productdetail', $pSlug) }}" class="d-block text-center">
                                                    <img class="img-fluid" src="{{ !empty($p->image_url) ? env('BACKEND_IMAGE_URL') . $p->image_url : env('BACKEND_IMAGE_URL') . 'default.jpeg' }}"
                                                         alt="{{ $p->title }}" style="width: 100%; height: 250px;" onerror="this.onerror=null;this.src='{{ env('BACKEND_IMAGE_URL') . 'default.jpeg' }}';">
                                                </a>
                                            </div>
                                        </div>
                                        <div class="product-item__body col-6 col-md-5">
                                            <div class="pr-lg-10">
                                                <div class="mb-2"><a href="{{ route('productdetail', $pSlug) }}"
                                                                     class="font-size-12 text-gray-5">{{ $category->title }}</a></div>
                                                <h5 class="mb-2 product-item__title"><a href="{{ route('productdetail', $pSlug) }}"
                                                                                       class="text-blue font-weight-bold">{{ $p->title }}</a></h5>
                                                <div class="prodcut-price mb-2 d-md-none">
                                                    <div class="text-gray-100">Rs. {{ number_format($p->price + ($p->min_additional ?? 0), 2) }}</div>
                                                </div>
                                                <div class="mb-3 d-none d-md-block">
                                                    <a class="d-inline-flex align-items-center small font-size-14" href="#">
                                                        <div class="text-warning mr-2">
                                                            <small class="fas fa-star"></small>
                                                            <small class="fas fa-star"></small>
                                                            <small class="fas fa-star"></small>
                                                            <small class="fas fa-star"></small>
                                                            <small class="far fa-star text-muted"></small>
                                                        </div>
                                                        <span class="text-secondary">(40)</span>
                                                    </a>
                                                </div>
                                                <div class="font-size-12 text-gray-110 mb-4 d-none d-md-block">
                                                    {{ $p->short_desc ?? 'No description available' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="product-item__footer col-md-3 d-md-block">
                                            <div class="mb-3">
                                                <div class="prodcut-price mb-2">
                                                    <div class="text-gray-100">Rs. {{ number_format($p->price + ($p->min_additional ?? 0), 2) }}</div>
                                                </div>
                                                {{-- <div class="prodcut-add-cart">
                                                    <a href="{{ route('cart') }}"
                                                       class="btn btn-sm btn-block btn-primary-dark btn-wide transition-3d-hover">Add to cart</a>
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <!-- End Tab Content -->
                <!-- Shop Pagination -->
                <nav class="d-md-flex justify-content-between align-items-center border-top pt-3"
                     aria-label="Page navigation example">
                    <div class="text-center text-md-left mb-3 mb-md-0">Showing {{ $products->firstItem() }}–{{ $products->lastItem() }}
                        of {{ $products->total() }} results</div>
                    <ul class="pagination mb-0 pagination-shop justify-content-center justify-content-md-start">
                        @if ($products->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">Prev</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $products->previousPageUrl() }}">Prev</a></li>
                        @endif
                        @php
                            $currentPage = $products->currentPage();
                            $lastPage = $products->lastPage();
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($lastPage, $startPage + 4);
                            if ($endPage - $startPage < 4) {
                                $startPage = max(1, $endPage - 4);
                            }
                        @endphp
                        @foreach (range($startPage, $endPage) as $page)
                            <li class="page-item">
                                <a class="page-link {{ $currentPage == $page ? 'current' : '' }}"
                                   href="{{ $products->url($page) }}">{{ $page }}</a>
                            </li>
                        @endforeach
                        @if ($products->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $products->nextPageUrl() }}">Next</a></li>
                        @else
                            <li class="page-item disabled"><span class="page-link">Next</span></li>
                        @endif
                    </ul>
                </nav>
                <!-- End Shop Pagination -->
            </div>
        </div>
    </div>
</main>
<!-- ========== END MAIN CONTENT ========== -->

@stop

<!-- Mobile Filter Sidebar -->
<aside id="sidebarContent1" class="u-sidebar u-sidebar--left" aria-labelledby="sidebarNavToggler1">
    <div class="u-sidebar__scroller">
        <div class="u-sidebar__container">
            <div class="u-header-sidebar__footer-offset">
                <!-- Toggle Button -->
                <div class="d-flex align-items-center pt-4 px-7">
                    <button type="button" class="close ml-auto"
                        data-unfold-target="#sidebarContent1" data-unfold-type="css-animation">
                        <i class="ec ec-close-remove"></i>
                    </button>
                </div>
                <!-- End Toggle Button -->

                <!-- Content -->
                <div class="js-scrollbar u-sidebar__body">
                    <div class="u-sidebar__content u-header-sidebar__content px-4">
                        <div class="mb-6">
                            <h3 class="section-title section-title__sm mb-4 pb-2 font-size-18">Filters</h3>
                            
                            <form method="get" action="{{ $category->id ? route('categories.products', $category->id) : route('search') }}">
                                <input type="hidden" name="q" value="{{ request()->get('q') }}">
                                <input type="hidden" name="category" value="{{ request()->get('category', $category->id) }}">
                                
                                <div class="border-bottom pb-4 mb-4">
                                    <h4 class="font-size-14 mb-3 font-weight-bold">Brands</h4>
                                    @foreach($brands as $brand)
                                    <div class="form-group d-flex align-items-center justify-content-between mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="mob-brand{{ $brand->id }}"
                                                   name="brand[]" value="{{ $brand->id }}"
                                                   {{ in_array($brand->id, request()->input('brand', [])) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="mob-brand{{ $brand->id }}">{{ $brand->title }}</label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <div class="range-slider mb-4">
                                    <h4 class="font-size-14 mb-3 font-weight-bold">Price</h4>
                                    <div class="d-flex align-items-center">
                                        <input type="number" name="min_price" class="form-control form-control-sm" 
                                               value="{{ request()->input('min_price', 0) }}" placeholder="Min">
                                        <span class="mx-2">—</span>
                                        <input type="number" name="max_price" class="form-control form-control-sm" 
                                               value="{{ request()->input('max_price', $max_price) }}" placeholder="Max">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-block btn-primary-dark-w">Apply Filters</button>
                                <a href="{{ route('categories.products', $category->id) }}" class="btn btn-block btn-outline-secondary mt-2">Clear</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</aside>

@push('scripts')
<script>
    $(document).ready(function() {
        // Debugging: Log to check if jQuery and ionRangeSlider are loaded
        console.log('jQuery loaded:', typeof $ !== 'undefined');
        console.log('ionRangeSlider loaded:', typeof $.fn.ionRangeSlider !== 'undefined');

        // Remove any unwanted collapse classes
        $('#basicsCollapseOne').removeClass('show');

        // Initialize range slider
        try {
            var $rangeSlider = $('.js-range-slider');
            $rangeSlider.ionRangeSlider({
                type: "double",
                min: 0,
                max: {{ $max_price }},
                from: {{ request()->input('min_price', 0) }},
                to: {{ request()->input('max_price', $max_price) }},
                grid: false,
                prefix: "Rs.",
                hide_min_max: true,
                hide_from_to: true,
                skin: "round",
                onStart: function(data) {
                    console.log('Slider initialized with values:', data.from, data.to);
                    // Update display values on start
                    $('#rangeSliderExample3MinResult').text(data.from);
                    $('#rangeSliderExample3MaxResult').text(data.to);
                    $('#rangeSliderExample3MinResultHidden').val(data.from);
                    $('#rangeSliderExample3MaxResultHidden').val(data.to);
                },
                onChange: function(data) {
                    // Update display and hidden fields in real-time
                    $('#rangeSliderExample3MinResult').text(data.from);
                    $('#rangeSliderExample3MaxResult').text(data.to);
                    $('#rangeSliderExample3MinResultHidden').val(data.from);
                    $('#rangeSliderExample3MaxResultHidden').val(data.to);
                },
                onFinish: function(data) {
                    console.log('Slider changed to:', data.from, data.to);
                    // Update hidden fields on finish
                    $('#rangeSliderExample3MinResultHidden').val(data.from);
                    $('#rangeSliderExample3MaxResultHidden').val(data.to);
                    $('#rangeSliderExample3MinResult').text(data.from);
                    $('#rangeSliderExample3MaxResult').text(data.to);
                    // Don't auto-submit - let user click Filter button
                }
            });
        } catch (e) {
            console.error('Error initializing ionRangeSlider:', e);
        }

        // Auto-submit sort form on change
        $('#sortForm select[name="sort"]').on('change', function() {
            console.log('Sort changed to:', $(this).val());
            $('#sortForm').submit();
        });

        // Auto-submit filter form on brand checkbox change
        $('#filterForm input[name="brand[]"]').on('change', function() {
            console.log('Brand filter changed:', $(this).val());
            $('#filterForm').submit();
        });
    });
</script>
@endpush
