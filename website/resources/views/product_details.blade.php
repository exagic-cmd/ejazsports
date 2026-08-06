@extends('layouts.app')

@section('title', $product->title)

@section('content')
<main id="content" role="main">
    <!-- Breadcrumb -->
    <div class="bg-gray-13 bg-md-transparent">
        <div class="container">
            <div class="my-md-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visible">
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1">
                            <a href="{{ route('home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1">
                            <a href="{{ route('categories.products', $category->id) }}">{{ $category->title }}</a>
                        </li>
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">
                            {{ $product->title }}
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <div class="container">
        <!-- Single Product Body -->
        <div class="mb-xl-14 mb-6">
            <div class="row">
                <!-- Product Images Column -->
                <div class="col-md-5 mb-4 mb-md-0">
                    @php
                        $images = DB::table('product_images')
                            ->where('product_id', $product->id)
                            ->orderBy('id', 'asc')
                            ->get();
                        $defaultImage = env('BACKEND_IMAGE_URL') . 'default.jpeg';
                        $imageCount = $images->count();
                    @endphp

                    {{-- Main Slider --}}
                    <div id="sliderSyncingNav" class="u-slick mb-2">
                        @php $paddedCount = 0; @endphp
                        @if($images->isNotEmpty())
                            @foreach($images as $image)
                                <div class="js-slide">
                                    <img class="img-fluid"
                                         src="{{ !empty($image->url) ? env('BACKEND_IMAGE_URL') . $image->url : $defaultImage }}"
                                         alt="{{ $product->title }}"
                                         onerror="this.onerror=null;this.src='{{ $defaultImage }}';">
                                </div>
                                @php $paddedCount++; @endphp
                            @endforeach
                        @endif

                        {{-- Pad with placeholders if less than 5 (for visual consistency) --}}
                        @while($paddedCount < 5)
                            <div class="js-slide">
                                <img class="img-fluid" src="{{ $defaultImage }}" alt="Placeholder">
                            </div>
                            @php $paddedCount++; @endphp
                        @endwhile
                    </div>

                    {{-- Thumbnail Slider (Always rendered, padded to at least 5) --}}
                    <div id="sliderSyncingThumb" class="u-slick u-slick--slider-syncing u-slick--slider-syncing-size u-slick--gutters-1 u-slick--transform-off">
                        @php $paddedCount = 0; @endphp
                        @if($images->isNotEmpty())
                            @foreach($images as $image)
                                <div class="js-slide" style="cursor: pointer;">
                                    <img class="img-fluid"
                                         src="{{ !empty($image->url) ? env('BACKEND_IMAGE_URL') . $image->url : $defaultImage }}"
                                         alt="{{ $product->title }}"
                                         onerror="this.onerror=null;this.src='{{ $defaultImage }}';">
                                </div>
                                @php $paddedCount++; @endphp
                            @endforeach
                        @endif

                        @while($paddedCount < 5)
                            <div class="js-slide" style="cursor: pointer; opacity: 0.5;">
                                <img class="img-fluid" src="{{ $defaultImage }}" alt="Placeholder">
                            </div>
                            @php $paddedCount++; @endphp
                        @endwhile
                    </div>
                </div>
                <!-- End Product Images Column -->

                <!-- Product Details Column -->
                <div class="col-md-7 mb-md-6 mb-lg-0" style="position: relative; z-index: 10;">
                    <div class="mb-2">
                        <div class="border-bottom mb-3 pb-md-1 pb-3">
                            <a href="{{ route('categories.products', $category->id) }}"
                               class="font-size-12 text-gray-5 mb-2 d-inline-block">{{ $category->title }}</a>
                            <h2 class="font-size-25 text-lh-1dot2">{{ $product->title }}</h2>
                            <div class="mb-2">
                                <a class="d-inline-flex align-items-center small font-size-15 text-lh-1" href="#">
                                    <div class="text-warning mr-2">
                                        <small class="fas fa-star"></small>
                                        <small class="fas fa-star"></small>
                                        <small class="fas fa-star"></small>
                                        <small class="fas fa-star"></small>
                                        <small class="far fa-star text-muted"></small>
                                    </div>
                                    <span class="text-secondary font-size-13">(0 customer reviews)</span>
                                </a>
                            </div>
                            <div class="d-md-flex align-items-center">
                                <div class="ml-md-3 text-gray-9 font-size-14">
                                    Availability:
                                    <span id="stock-display" class="{{ $totalProductStock > 0 ? 'text-green' : 'text-red' }} font-weight-bold">
                                        {{ $totalProductStock > 0 ? $totalProductStock . ' in stock' : 'Out of stock' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex-horizontal-center flex-wrap mb-4">
                            <a href="#" class="text-gray-6 font-size-13 mr-2"><i class="ec ec-favorites mr-1 font-size-15"></i> Wishlist</a>
                            <a href="#" class="text-gray-6 font-size-13 ml-2"><i class="ec ec-compare mr-1 font-size-15"></i> Compare</a>
                        </div>

                        <div class="mb-2">
                            <ul class="font-size-14 pl-3 ml-1 text-gray-110">
                                @if($product->short_description)
                                    {!! nl2br(e($product->short_description)) !!}
                                @else
                                    <li>No description available.</li>
                                @endif
                            </ul>
                        </div>

                        <p>{!! $product->long_description ?? 'No detailed description available.' !!}</p>
                        <p><strong>SKU</strong>: {{ $product->barcode ?? 'N/A' }}</p>

                        <!-- Price Display -->
                        <div class="mb-4">
                            <div class="d-flex align-items-baseline">
                                @php
                                    $displayPrice = ($product->have_variants && $minVariantPrice > 0) ? $minVariantPrice : $product->price;
                                    if ($displayPrice <= 0) $displayPrice = $product->price;
                                @endphp
                                <ins class="font-size-36 text-decoration-none">Rs. {{ number_format($displayPrice, 2) }}</ins>
                                @if($product->discount_amount > 0 && $product->discount_status)
                                    <del class="font-size-20 ml-2 text-gray-6">
                                        Rs. {{ number_format($displayPrice + $product->discount_amount, 2) }}
                                    </del>
                                @endif
                            </div>
                        </div>

                        <!-- Variant Selection -->
                        @if($product->have_variants)
                            <div id="variant-section" class="border-top border-bottom py-3 mb-4">
                                @php
                                    $uniqueShades = $variants->pluck('shade')->filter(fn($value) => !is_null($value) && $value !== '')->unique()->values();
                                    $uniqueSizes = $variants->pluck('size')->unique()->values();
                                    $hasShades = $uniqueShades->isNotEmpty();
                                @endphp

                                <div id="variant-message" class="font-weight-bold mb-2" style="min-height: 20px;"></div>

                                <!-- Hidden Inputs for Cart -->
                                <input type="hidden" id="selected-variant-id" value="">
                                <input type="hidden" id="selected-variant-price" value="">

                                <!-- Colors (Shades) Section -->
                                @if($hasShades)
                                    <div class="mb-4">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="font-size-14 font-weight-bold mr-2">Color:</span>
                                            <span id="selected-shade-text" class="text-gray-6 font-size-14">Select a color</span>
                                        </div>
                                        <div class="d-flex flex-wrap">
                                            @foreach($uniqueShades as $shade)
                                                @php
                                                    $shadeStock = $variants->where('shade', $shade)->sum('available_stock');
                                                    $isOos = $shadeStock <= 0;
                                                @endphp
                                                <div class="shade-option mr-2 mb-2 border rounded p-2 transition-3d-hover {{ $isOos ? 'oos-shade' : '' }}"
                                                     data-shade="{{ $shade }}"
                                                     onclick="window.selectShade('{{ $shade }}', this)"
                                                     style="min-width: 60px; text-align: center; cursor: pointer; {{ $isOos ? 'opacity: 0.5; background-color: #f8f9fa;' : '' }}">
                                                    <span class="font-weight-bold font-size-14 {{ $isOos ? 'text-muted' : '' }}">{{ $shade }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Sizes Section -->
                                <div class="mb-4" id="size-section" style="{{ $hasShades ? 'opacity: 0.5; pointer-events: none;' : '' }}">
                                    <label class="font-size-14 font-weight-bold mb-2">Size:</label>
                                    <select class="form-control js-select" id="size-select"
                                            onchange="window.selectSize(this.value, this)"
                                            style="width: 100%; max-width: 300px;">
                                        <option value="" selected disabled>Select a size</option>
                                        @foreach($uniqueSizes as $size)
                                            <option value="{{ $size }}" data-size="{{ $size }}" class="size-option">{{ $size }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <script>
                                // Variant Data
                                var allVariants       = @json($variants);
                                var hasShades         = {{ $hasShades ? 'true' : 'false' }};
                                var basePrice         = Number("{{ $product->price }}");
                                var initialDisplayPrice = Number("{{ $displayPrice }}");
                                var totalProductStock = {{ $totalProductStock }};

                                var state = { shade: null, size: null };

                                // ── Shade Selection ──────────────────────────────────────────
                                window.selectShade = function(shade, el) {
                                    if (state.shade === shade) return;

                                    var previousSize = state.size;
                                    state.shade = shade;

                                    // Visual update
                                    $('.shade-option').removeClass('bg-primary text-white border-primary').addClass('border text-gray-5');
                                    $(el).removeClass('border text-gray-5').addClass('bg-primary text-white border-primary');
                                    $('#selected-shade-text').text(shade).addClass('font-weight-bold text-dark').removeClass('text-gray-6');

                                    updateSizeAvailability(shade);
                                    $('#size-section').css({'opacity': '1', 'pointer-events': 'auto'});

                                    // Keep size if still valid for new shade
                                    var isPreviousSizeValid = false;
                                    if (previousSize) {
                                        isPreviousSizeValid = allVariants.some(function(v) {
                                            return v.shade === shade && v.size === previousSize;
                                        });
                                    }

                                    if (isPreviousSizeValid) {
                                        $('#size-select').val(previousSize).trigger('change');
                                        findAndSelectVariant();
                                    } else {
                                        state.size = null;
                                        $('#size-select').val('').trigger('change');
                                        $('#selected-variant-id').val('');
                                        updateAddToCartDetails();
                                    }
                                };

                                // ── Size Selection ───────────────────────────────────────────
                                window.selectSize = function(size, el) {
                                    if (!size) return;
                                    state.size = size;
                                    findAndSelectVariant();
                                };

                                // ── DOM Ready ────────────────────────────────────────────────
                                document.addEventListener("DOMContentLoaded", function() {
                                    if (typeof jQuery === 'undefined') return;
                                    if (!hasShades) {
                                        jQuery('#size-section').css({'opacity': '1', 'pointer-events': 'auto'});
                                    }
                                    updateAddToCartDetails();
                                    jQuery('#addToCartBtn').attr('onclick', 'window.productPageAddToCart(this)');
                                });

                                // ── Helpers ──────────────────────────────────────────────────
                                function updateSizeAvailability(selectedShade) {
                                    $('.size-option').prop('disabled', false).text(function() {
                                        return $(this).data('size');
                                    });

                                    if (!selectedShade && hasShades) return;

                                    var validSizes = allVariants
                                        .filter(function(v) { return !hasShades || v.shade === selectedShade; })
                                        .map(function(v) { return v.size; });

                                    $('.size-option').each(function() {
                                        var size = $(this).data('size');
                                        if (!validSizes.includes(size)) {
                                            $(this).prop('disabled', true);
                                        } else {
                                            var variant = allVariants.find(function(v) {
                                                return (!hasShades || v.shade === selectedShade) && v.size === size;
                                            });
                                            if (!variant || variant.available_stock <= 0) {
                                                $(this).text(size + ' (Out of Stock)');
                                            }
                                        }
                                    });

                                    if (typeof $.fn.selectpicker !== 'undefined') {
                                        $('#size-select').selectpicker('refresh');
                                    }
                                }

                                function findAndSelectVariant() {
                                    var variant = allVariants.find(function(v) {
                                        return (!hasShades || v.shade === state.shade) && v.size === state.size;
                                    });
                                    $('#variant-message').text('').removeClass('text-danger');

                                    if (variant) {
                                        var finalPrice = basePrice + parseFloat(variant.additional_price || 0);
                                        $('ins.font-size-36').text('Rs. ' + finalPrice.toFixed(2));
                                        $('#selected-variant-id').val(variant.id);

                                        if (variant.available_stock > 0) {
                                            window.updateStockUI(totalProductStock);
                                            $('#addToCartBtn').prop('disabled', false)
                                                .html('<i class="ec ec-add-to-cart mr-2 font-size-20"></i> Add to Cart');
                                        } else {
                                            window.updateStockUI(0);
                                            $('#variant-message').text('This combination is out of stock.').addClass('text-danger');
                                            $('#addToCartBtn').prop('disabled', true);
                                        }
                                    }
                                }

                                function updateAddToCartDetails() {
                                    if ((hasShades && !state.shade) || !state.size) {
                                        $('#addToCartBtn').prop('disabled', true)
                                            .html('<i class="ec ec-add-to-cart mr-2 font-size-20"></i> Select Options');
                                        $('ins.font-size-36').text('Rs. ' + initialDisplayPrice.toFixed(2));
                                    }
                                }

                                window.updateStockUI = function(stock) {
                                    var $el = $('#stock-display');
                                    if ($el.length) {
                                        if (stock > 0) {
                                            $el.removeClass('text-red').addClass('text-green').html(stock + ' in stock');
                                        } else {
                                            $el.removeClass('text-green').addClass('text-red').html('Out of stock');
                                        }
                                    }
                                };

                                // ── AJAX Add to Cart (variant product) ───────────────────────
                                window.productPageAddToCart = function(btn) {
                                    var variantId = $('#selected-variant-id').val();
                                    var quantity  = parseInt($('.js-result').val()) || 1;

                                    if (!variantId) {
                                        typeof showToast === 'function'
                                            ? showToast('Please select all options.', 'danger')
                                            : alert('Please select all options.');
                                        return;
                                    }

                                    var $btn = $(btn);
                                    var originalText = $btn.html();
                                    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Adding...');

                                    $.ajax({
                                        url: '{{ route("cart.add") }}',
                                        method: 'POST',
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            product_id: {{ $product->id }},
                                            variant_id: variantId,
                                            quantity: quantity
                                        },
                                        success: function(response) {
                                            $btn.prop('disabled', false).html(originalText);
                                            if (response.success) {
                                                if (response.miniHtml) $('#mini-cart-content').html(response.miniHtml);
                                                typeof showToast === 'function'
                                                    ? (showToast(response.message || 'Added to cart!', 'success'), $('.cart-count').text(response.count || 0))
                                                    : alert(response.message || 'Added to cart!');
                                            } else {
                                                typeof showToast === 'function'
                                                    ? showToast(response.message || 'Error adding to cart', 'danger')
                                                    : alert(response.message || 'Error adding to cart');
                                            }
                                        },
                                        error: function(xhr) {
                                            $btn.prop('disabled', false).html(originalText);
                                            var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to add to cart.';
                                            typeof showToast === 'function' ? showToast(msg, 'danger') : alert(msg);
                                        }
                                    });
                                };
                            </script>
                        @endif

                        <!-- GLOBAL Add to Cart (non-variant fallback) -->
                        <script>
                            window.addToCart = function(btn) {
                                var $btn = $(btn);
                                if ($btn.prop('disabled')) return;

                                var quantity = parseInt($('.js-result').val()) || 1;
                                var items    = [];

                                @if($product->have_variants)
                                    var variantId = $('#selected-variant-id').val();
                                    if (!variantId) {
                                        typeof showToast === 'function'
                                            ? showToast('Please select all options.', 'danger')
                                            : alert('Please select all options.');
                                        return;
                                    }
                                    items.push({ product_id: {{ $product->id }}, variant_id: variantId, quantity: quantity });
                                @else
                                    items.push({ product_id: {{ $product->id }}, quantity: quantity });
                                @endif

                                var originalText = $btn.html();
                                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Adding...');

                                $.ajax({
                                    url: '{{ route("cart.add") }}',
                                    type: 'POST',
                                    data: { _token: '{{ csrf_token() }}', items: items },
                                    success: function(response) {
                                        $btn.prop('disabled', false).html(originalText);
                                        if (response.success) {
                                            if (response.miniHtml) $('#mini-cart-content').html(response.miniHtml);
                                            typeof showToast === 'function'
                                                ? (showToast(response.message || 'Added to cart!', 'success'), $('.cart-count').text(response.count || 0))
                                                : alert(response.message || 'Added to cart!');
                                        } else {
                                            typeof showToast === 'function'
                                                ? showToast(response.message || 'Error adding to cart', 'danger')
                                                : alert(response.message || 'Error adding to cart');
                                        }
                                    },
                                    error: function(xhr) {
                                        $btn.prop('disabled', false).html(originalText);
                                        var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error adding to cart';
                                        typeof showToast === 'function' ? showToast(msg, 'danger') : alert(msg);
                                    }
                                });
                            };
                        </script>

                        <!-- Quantity & Add to Cart Button -->
                        <div class="d-md-flex align-items-end mb-3">
                            <div class="max-width-150 mb-4 mb-md-0">
                                <h6 class="font-size-14">Quantity</h6>
                                <div class="border rounded-pill py-2 px-3 border-color-1">
                                    <div class="js-quantity row align-items-center">
                                        <div class="col">
                                            <input class="js-result form-control h-auto border-0 rounded p-0 shadow-none"
                                                   type="text" value="1" name="quantity">
                                        </div>
                                        <div class="col-auto pr-1">
                                            <a class="js-minus btn btn-icon btn-xs btn-outline-secondary rounded-circle border-0" href="javascript:;">
                                                <small class="fas fa-minus btn-icon__inner"></small>
                                            </a>
                                            <a class="js-plus btn btn-icon btn-xs btn-outline-secondary rounded-circle border-0" href="javascript:;">
                                                <small class="fas fa-plus btn-icon__inner"></small>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-md-3">
                                <button type="button"
                                        id="addToCartBtn"
                                        class="btn px-5 btn-primary-dark transition-3d-hover"
                                        onclick="window.addToCart(this)"
                                        {{ ($product->have_variants) ? 'disabled' : (($product->available_stock <= 0) ? 'disabled' : '') }}
                                        style="position: relative; z-index: 20; pointer-events: auto;">
                                    <i class="ec ec-add-to-cart mr-2 font-size-20"></i>
                                    @if($product->have_variants)
                                        Select Options
                                    @elseif($product->available_stock <= 0)
                                        Out of Stock
                                    @else
                                        Add to Cart
                                    @endif
                                </button>
                                <input type="hidden" id="product_id_val" value="{{ $product->id }}">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Product Details Column -->
            </div>
        </div>
        <!-- End Single Product Body -->

        <!-- Product Tabs -->
        <div class="mb-8">
            <div class="position-relative position-md-static px-md-6">
                <ul class="nav nav-classic nav-tab nav-tab-lg justify-content-xl-center flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visible border-0 pb-1 pb-xl-0 mb-n1 mb-xl-0"
                    id="pills-tab-8" role="tablist">
                    <li class="nav-item flex-shrink-0 flex-xl-shrink-1 z-index-2">
                        <a class="nav-link active" id="Jpills-two-example1-tab" data-toggle="pill"
                           href="#Jpills-two-example1" role="tab" aria-controls="Jpills-two-example1" aria-selected="true">
                            Description
                        </a>
                    </li>
                    <li class="nav-item flex-shrink-0 flex-xl-shrink-1 z-index-2">
                        <a class="nav-link" id="Jpills-four-example1-tab" data-toggle="pill"
                           href="#Jpills-four-example1" role="tab" aria-controls="Jpills-four-example1" aria-selected="false">
                            Reviews
                        </a>
                    </li>
                </ul>
            </div>
            <div class="borders-radius-17 border p-4 mt-4 mt-md-0 px-lg-10 py-lg-9">
                <div class="tab-content" id="Jpills-tabContent">

                    <!-- Description Tab -->
                    <div class="tab-pane fade active show" id="Jpills-two-example1" role="tabpanel" aria-labelledby="Jpills-two-example1-tab">
                        <h3 class="font-size-24 mb-3">Product Description</h3>
                        {!! $product->long_description ?? '<p>No detailed description available.</p>' !!}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="pt-lg-8 pt-xl-10">
                                    <h3 class="font-size-24 mb-3">Key Features</h3>
                                    <ul class="font-size-14 pl-3 ml-1 text-gray-110">
                                        @if($product->short_description)
                                            {!! nl2br(e($product->short_description)) !!}
                                        @else
                                            <li>No key features listed.</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            @if($images->isNotEmpty())
                                <div class="col-md-6 text-right">
                                    <img class="img-fluid mr-n4 mr-lg-n10"
                                         src="{{ !empty($images->first()->url) ? env('BACKEND_IMAGE_URL') . $images->first()->url : $defaultImage }}"
                                         alt="{{ $product->title }}"
                                         onerror="this.onerror=null;this.src='{{ $defaultImage }}';">
                                </div>
                            @endif
                        </div>
                        <ul class="nav flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visible mt-3">
                            <li class="nav-item text-gray-111 flex-shrink-0 flex-xl-shrink-1">
                                <strong>SKU:</strong> <span class="sku">{{ $product->barcode ?? 'N/A' }}</span>
                            </li>
                            <li class="nav-item text-gray-111 mx-3 flex-shrink-0 flex-xl-shrink-1">/</li>
                            <li class="nav-item text-gray-111 flex-shrink-0 flex-xl-shrink-1">
                                <strong>Category:</strong>
                                <a href="{{ route('categories.products', $category->id) }}" class="text-blue">{{ $category->title }}</a>
                            </li>
                            <li class="nav-item text-gray-111 mx-3 flex-shrink-0 flex-xl-shrink-1">/</li>
                            <li class="nav-item text-gray-111 flex-shrink-0 flex-xl-shrink-1">
                                <strong>Brand:</strong>
                                <a href="{{ isset($brand->id) ? route('brands.products', $brand->id) : '#' }}" class="text-blue">{{ $brand->title ?? 'N/A' }}</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Reviews Tab -->
                    <div class="tab-pane fade" id="Jpills-four-example1" role="tabpanel" aria-labelledby="Jpills-four-example1-tab">
                        <div class="row mb-8">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h3 class="font-size-18 mb-6">Customer Reviews</h3>
                                    <h2 class="font-size-30 font-weight-bold text-lh-1 mb-0">0.0</h2>
                                    <div class="text-lh-1">No reviews yet</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- End Product Tabs -->

        <!-- Related Products -->
        <div class="mb-6">
            <div class="d-flex justify-content-between align-items-center border-bottom border-color-1 flex-lg-nowrap flex-wrap mb-4">
                <h3 class="section-title mb-0 pb-2 font-size-22">Related Products</h3>
            </div>
            <ul class="row list-unstyled products-group no-gutters">
                @forelse($relatedProducts as $related)
                    <li class="col-6 col-md-3 col-xl-2gdot4-only col-wd-2 product-item">
                        <div class="product-item__outer h-100">
                            <div class="product-item__inner px-xl-4 p-3">
                                <div class="product-item__body pb-xl-2">
                                    <div class="mb-2">
                                        <a href="{{ route('categories.products', $category->id) }}"
                                           class="font-size-12 text-gray-5">{{ $category->title }}</a>
                                    </div>
                                    <h5 class="mb-1 product-item__title">
                                        @php
                                            $relatedSlug = $related->slug ?: \Illuminate\Support\Str::slug($related->title);
                                        @endphp
                                        <a href="{{ route('productdetail', $relatedSlug) }}"
                                           class="text-blue font-weight-bold">{{ $related->title }}</a>
                                    </h5>
                                    <div class="mb-2">
                                        <a href="{{ route('productdetail', $relatedSlug) }}" class="d-block text-center">
                                            @php
                                                $imageUrl = !empty($related->images) && $related->images->isNotEmpty()
                                                    ? env('BACKEND_IMAGE_URL') . $related->images->first()->url
                                                    : env('BACKEND_IMAGE_URL') . 'default.jpeg';
                                            @endphp
                                            <img class="img-fluid" src="{{ $imageUrl }}"
                                                 alt="{{ $related->title }}"
                                                 onerror="this.onerror=null;this.src='{{ env('BACKEND_IMAGE_URL') . 'default.jpeg' }}';">
                                        </a>
                                    </div>
                                    <div class="flex-center-between mb-1">
                                        <div class="prodcut-price">
                                            <div class="text-gray-100">
                                                Rs. {{ number_format($related->price, 2) }}
                                                @if($related->discount_amount > 0 && $related->discount_status)
                                                    <del class="font-size-14 text-gray-6 ml-2">
                                                        Rs. {{ number_format($related->price + $related->discount_amount, 2) }}
                                                    </del>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="product-item__footer"></div>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="col-12 text-center">
                        <p>No related products available.</p>
                    </li>
                @endforelse
            </ul>
        </div>
        <!-- End Related Products -->
    </div>
</main>
@stop

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css" />
<style>
    .variant-option.bg-primary {
        background-color: #377dff !important;
        color: #ffffff !important;
        border-color: #377dff !important;
    }
    .col-md-5 { position: relative; z-index: 1; }
    .col-md-7 { position: relative; z-index: 10; }
    .mb-6, .mb-8 { position: relative; z-index: 5; }
    .variant-option {
        cursor: pointer !important;
        position: relative;
        z-index: 20;
        pointer-events: auto !important;
    }
    .u-slick { z-index: 1; }
    /* Slick thumbnail active state */
    #sliderSyncingThumb .slick-current img {
        border: 2px solid #377dff;
    }
    #sliderSyncingThumb .js-slide img {
        border: 2px solid transparent;
        transition: border-color 0.2s;
    }
</style>
@stop

@section('js')
<script src="{{ asset('assets/vendor/slick-carousel/slick/slick.js') }}"></script>
<script>
$(document).ready(function () {

    var $navSlider   = $('#sliderSyncingNav');
    var $thumbSlider = $('#sliderSyncingThumb');

    // Step 1: Destroy any instance the theme may have auto-initialized
    if ($navSlider.hasClass('slick-initialized'))   { $navSlider.slick('unslick');   }
    if ($thumbSlider.hasClass('slick-initialized')) { $thumbSlider.slick('unslick'); }

    // Step 2: Strip all theme data-attributes to prevent re-auto-init conflicts
    $navSlider.removeAttr('data-nav-for data-arrows-classes data-arrow-left-classes data-arrow-right-classes data-infinite');
    $thumbSlider.removeAttr('data-nav-for data-is-thumbs data-slides-show data-infinite');

    // Initialization (Thumbnails are now always padded up to at least 5)
    $thumbSlider.slick({
        infinite:       true,
        slidesToShow:   5,
        slidesToScroll: 1,
        asNavFor:       '#sliderSyncingNav',
        focusOnSelect:  true,
        arrows:         false,
        accessibility:  false // Fix for initADA error
    });

    $navSlider.slick({
        infinite:  true,
        arrows:    true,
        prevArrow: '<div class="fas fa-arrow-left u-slick__arrow-classic-inner u-slick__arrow-classic-inner--left ml-lg-2 ml-xl-4" style="cursor:pointer;"></div>',
        nextArrow: '<div class="fas fa-arrow-right u-slick__arrow-classic-inner u-slick__arrow-classic-inner--right mr-lg-2 mr-xl-4" style="cursor:pointer;"></div>',
        asNavFor:  '#sliderSyncingThumb',
        accessibility: false // Fix for initADA error
    });

    // ── Quantity Controls ─────────────────────────────────────────────────────
    $('.js-minus').on('click', function (e) {
        e.preventDefault();
        var $input = $(this).closest('.js-quantity').find('.js-result');
        var val = parseInt($input.val()) || 1;
        if (val > 1) $input.val(val - 1);
    });

    $('.js-plus').on('click', function (e) {
        e.preventDefault();
        var $input = $(this).closest('.js-quantity').find('.js-result');
        var val = parseInt($input.val()) || 1;
        $input.val(val + 1);
    });

    $('.js-result').on('change', function () {
        var val = parseInt($(this).val()) || 1;
        if (val < 1) val = 1;
        $(this).val(val);
    });

    // ── Star Rating ───────────────────────────────────────────────────────────
    $('.rate-star').on('click', function () {
        var rating = $(this).data('value');
        $('#rating').val(rating);
        $('.rate-star').each(function () {
            if ($(this).data('value') <= rating) {
                $(this).removeClass('far').addClass('fas');
            } else {
                $(this).removeClass('fas').addClass('far');
            }
        });
    });
});
</script>
@stop