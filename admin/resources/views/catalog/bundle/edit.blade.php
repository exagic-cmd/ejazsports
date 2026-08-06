@extends('layouts.app')

@section('css')
<style>
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #ced4da;
        padding: 0.375rem 0.75rem;
    }
    .product-variants-block {
        border: 1px solid #eee;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 5px;
    }
    .variant-checkbox {
        margin-right: 10px;
    }
    .variant-item {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
    }
    .variant-price {
        margin-left: auto;
        font-weight: bold;
        text-align: right;
        min-width: 150px;
    }
    .product-card {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
        background: #f9f9f9;
    }
    .product-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    .remove-product {
        color: #dc3545;
        cursor: pointer;
    }
    .no-variants-notice {
        color: #666;
        font-style: italic;
    }
    .section-content {
        display: none;
    }
    .section-content.active {
        display: block;
    }
    .product-price-display {
        font-weight: bold;
        margin-bottom: 10px;
        padding: 5px;
        background: #f0f0f0;
        border-radius: 4px;
    }
    .price-summary {
        margin-bottom: 10px;
    }
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        width: 300px;
    }
    .image-preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 15px;
    }
    .image-preview {
        position: relative;
        width: 150px;
        height: 150px;
        border: 1px solid #ddd;
        border-radius: 4px;
        overflow: hidden;
    }
    .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .image-preview-remove {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(0,0,0,0.5);
        color: white;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .price-input-group {
        display: flex;
        gap: 15px;
    }
    .price-input {
        flex: 1;
    }
    .existing-image {
        position: relative;
    }
    .existing-image-remove {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(0,0,0,0.5);
        color: white;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="content-header">
            <h2 class="content-title">Edit Bundle</h2>
            <div>
                <button class="btn btn-md rounded font-sm hover-up" onclick="submitForm()">Update Bundle</button>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row gx-5">
                    <aside class="col-lg-3 border-end">
                        <nav class="nav nav-pills flex-column mb-4">
                            <a class="nav-link a-general active" href="#" data-section="general">General</a>
                            <a class="nav-link a-products" href="#" data-section="products">Products</a>
                            <a class="nav-link a-image" href="#" data-section="image">Images</a>
                        </nav>
                    </aside>

                    <div class="col-lg-9">
                        <form action="{{ route('bundles.update', $bundle->id) }}" method="POST" id="bundle-form" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- General Section -->
                            <section class="content-body p-xl-4 section-content active" id="section-general">
                                <div class="row mb-4">
                                    <label class="col-lg-3 col-form-label">Bundle Name <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input type="text" name="title" class="form-control" value="{{ old('title', $bundle->name) }}" required>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label class="col-lg-3 col-form-label">Short Description</label>
                                    <div class="col-lg-9">
                                        <textarea class="form-control" name="short_desc" rows="3">{{ old('short_desc', $bundle->short_desc) }}</textarea>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label class="col-lg-3 col-form-label">Long Description</label>
                                    <div class="col-lg-9">
                                        <textarea class="form-control" name="full_desc" rows="4">{{ old('full_desc', $bundle->full_desc) }}</textarea>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label class="col-lg-3 col-form-label">Status</label>
                                    <div class="col-lg-9">
                                        <label class="form-check">
                                            <input type="hidden" name="status" value="0">
                                            <input type="checkbox" name="status" value="1"
                                                   class="form-check-input"
                                                   {{ old('status', $bundle->status) ? 'checked' : '' }}>
                                        </label>
                                    </div>
                                </div>

                                <button class="btn btn-primary" type="button" data-next-section="products">Continue to Products</button>
                            </section>

                            <!-- Products Section -->
                            <section class="content-body p-xl-4 section-content" id="section-products">
                                <div class="row mb-4">
                                    <label class="col-lg-3 col-form-label">Add Products <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <select id="product-select" class="form-control select2">
                                            <option value="">Select a product...</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}"
                                                    @if(in_array($product->id, old('products', $selectedProducts))) selected @endif>
                                                    {{ $product->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Select products to add to this bundle</small>
                                    </div>
                                </div>

                                <div id="selected-products-container" class="mb-4">
                                    @foreach($selectedProducts as $productId)
                                        @php
                                            $product = $products->firstWhere('id', $productId);
                                            $variants = $product ? $product->variants : collect();
                                            $productVariants = old('variant_ids.'.$productId, $selectedVariants[$productId] ?? []);
                                        @endphp
                                        @if($product)
                                            <div class="product-card" id="product-card-{{ $productId }}">
                                                <div class="product-header">
                                                    <h5>{{ $product->title }}</h5>
                                                    <span class="remove-product" onclick="removeProduct({{ $productId }})">
                                                        <i class="fas fa-times"></i> Remove
                                                    </span>
                                                </div>
                                                <div class="price-summary">
                                                    <div class="product-price-display">
                                                        @if($product->variants->count() > 0)
                                                            Base purchase price: Rs.{{ number_format($product->purchase_price, 2) }}<br>
                                                            Base additional price: Rs.{{ number_format($product->price, 2) }}<br>
                                                            Selected {{ count($productVariants) }} variant(s)
                                                        @else
                                                            Purchase price: Rs.{{ number_format($product->purchase_price, 2) }}<br>
                                                            Additional price: Rs.{{ number_format($product->price, 2) }}
                                                        @endif
                                                    </div>
                                                </div>
                                                @if($product->variants->count() > 0)
                                                    <div id="product-variants-{{ $productId }}">
                                                        <div class="mb-2">Select variants to include:</div>
                                                        @foreach($variants as $variant)
                                                            <div class="variant-item">
                                                                <input type="checkbox"
                                                                    class="variant-checkbox"
                                                                    id="variant-{{ $variant->id }}"
                                                                    onchange="toggleVariant({{ $productId }}, {{ $variant->id }})"
                                                                    @if(in_array($variant->id, $productVariants)) checked @endif>
                                                                <label for="variant-{{ $variant->id }}">
                                                                    {{ $variant->size }} ({{ $variant->shade }})
                                                                </label>
                                                                <span class="variant-price">
                                                                    Purchase: Rs.{{ number_format($variant->purchase_price, 2) }}<br>
                                                                    Additional: Rs.{{ number_format($variant->additional_price, 2) }}
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="alert alert-info">
                                                        This product has no variants. The base product will be included.
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                <div class="row mb-4">
                                    <label class="col-lg-3 col-form-label">Bundle Prices</label>
                                    <div class="col-lg-9">
                                        <div class="price-input-group">
                                            <div class="price-input">
                                                <label>Additional Price (Rs)</label>
                                              <input type="number" name="additional_price" class="form-control"
       value="{{ old('additional_price', $bundle->additional_price) }}" step="0.01" min="0">
                                                <small class="text-muted">Leave empty to calculate automatically from selected variants</small>
                                            </div>
                                            <div class="price-input">
                                                <label>Purchase Price (Rs)</label>
                                                <input type="number" name="purchase_price" class="form-control"
       value="{{ old('purchase_price', $bundle->purchase_price) }}" step="0.01" min="0" readonly>
                                                <small class="text-muted">Calculated automatically</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-9 offset-lg-3">
                                        <button class="btn btn-light" type="button" data-prev-section="general">Back to General</button>
                                        <button class="btn btn-primary" type="button" data-next-section="image">Continue to Images</button>
                                    </div>
                                </div>
                            </section>

                            <!-- Images Section -->
                            <section class="content-body p-xl-4 section-content" id="section-image">
                                <div class="row mb-4">
                                    <label class="col-lg-3 col-form-label">Existing Images</label>
                                    <div class="col-lg-9">
                                        <div class="image-preview-container" id="existing-images-container">
                                            @foreach($bundle->images as $image)
                                                <div class="image-preview existing-image">
                                                    <img src="{{ asset($image->path) }}" alt="Bundle Image">
                                                    <div class="existing-image-remove" onclick="removeExistingImage(this, {{ $image->id }})">
                                                        <i class="fas fa-times"></i>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label class="col-lg-3 col-form-label">Add New Images</label>
                                    <div class="col-lg-9">
                                        <input type="file" name="images[]" id="image-upload" class="form-control" multiple>
                                        <small class="text-muted">Recommended size: 497px × 497px</small>
                                        <div class="image-preview-container mt-3" id="image-preview-container">
                                            <!-- Image previews will be added here -->
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-9 offset-lg-3">
                                        <button class="btn btn-light" type="button" data-prev-section="products">Back to Products</button>
                                        <button class="btn btn-primary" type="submit">Update Bundle</button>
                                    </div>
                                </div>
                            </section>

                            <!-- Hidden field for tracking removed images -->
                            <input type="hidden" name="removed_images" id="removed-images" value="">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="toast-container"></div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>$(document).ready(function() {
    let selectedProducts = @json(old('products', $selectedProducts)) || [];
    let selectedVariants = @json(old('variant_ids', $selectedVariants)) || {};
    let productHasVariants = {};
    let productDataCache = {};
    let removedImages = [];

    // Initialize with bundle's current prices from database
    let currentPurchasePrice = parseFloat('{{ old("purchase_price", $bundle->purchase_price) }}') || 0;
    let currentAdditionalPrice = parseFloat('{{ old("additional_price", $bundle->additional_price) }}') || 0;

    // Set initial values in form fields
    $('input[name="purchase_price"]').val(currentPurchasePrice.toFixed(2));
    if (currentAdditionalPrice > 0) {
        $('input[name="additional_price"]').val(currentAdditionalPrice.toFixed(2));
    }

    // Track if prices are manually entered
    let isPurchasePriceManual = false;
    // FIXED: Only consider it manual if user explicitly types in the field
    let isAdditionalPriceManual = false;
    let userHasEditedAdditionalPrice = false; // Track if user has actually edited the field

    // Normalize variant IDs to integers and ensure proper structure
    Object.keys(selectedVariants).forEach(pid => {
        if (Array.isArray(selectedVariants[pid])) {
            selectedVariants[pid] = selectedVariants[pid].map(id => parseInt(id)).filter(id => !isNaN(id));
        } else {
            selectedVariants[pid] = [];
        }
    });

    // Ensure all selected products have variant entries
    selectedProducts.forEach(pid => {
        if (!selectedVariants[pid]) {
            selectedVariants[pid] = [];
        }
    });

    console.log('Initial state:', {
        selectedProducts,
        selectedVariants,
        currentPurchasePrice,
        currentAdditionalPrice
    });

    // Price formatting helper
    function formatPrice(price) {
        price = parseFloat(price) || 0;
        return price % 1 === 0 ? price.toString() : price.toFixed(2);
    }

    // Select2 Init
    $('#product-select').select2({
        placeholder: "Select a product...",
        allowClear: true,
        width: '100%',
        dropdownParent: $('#section-products')
    });

    // Image preview functionality
    $('#image-upload').on('change', function() {
        const container = $('#image-preview-container');
        Array.from(this.files).forEach(file => {
            if (!file.type.match('image.*')) return;
            const reader = new FileReader();
            reader.onload = (e) => {
                const preview = $(`
                    <div class="image-preview">
                        <img src="${e.target.result}" title="${file.name}">
                        <div class="image-preview-remove" onclick="removeImagePreview(this)">
                            <i class="fas fa-times"></i>
                        </div>
                    </div>
                `);
                container.append(preview);
            };
            reader.readAsDataURL(file);
        });
    });

    // Section navigation
    function showSection(section) {
        if (section !== 'general') {
            const currentSection = $('.section-content.active').attr('id');
            if (currentSection === 'section-general' && !$('input[name="title"]').val().trim()) {
                alert('Please enter a bundle title');
                $('input[name="title"]').focus().addClass('is-invalid');
                return false;
            }
        }
        $('.section-content').removeClass('active');
        $('#section-' + section).addClass('active');
        $('.nav-link').removeClass('active');
        $(`.a-${section}`).addClass('active');
        $('html, body').animate({
            scrollTop: $('#section-' + section).offset().top - 20
        }, 200);
        return true;
    }

    $('[data-section], [data-next-section], [data-prev-section]').on('click', function(e) {
        e.preventDefault();
        const section = $(this).data('section') || $(this).data('next-section') || $(this).data('prev-section');
        showSection(section);
    });

    $('#product-select').on('change', function() {
        const productId = parseInt($(this).val());
        if (!productId) return;
        const productName = $(this).find('option:selected').text();
        addProduct(productId, productName);
        $(this).val('').trigger('change');
    });

    function addProduct(productId, productName) {
        if (selectedProducts.includes(productId)) {
            showToast('warning', 'This product is already added');
            return;
        }

        selectedProducts.push(productId);
        selectedVariants[productId] = [];

        const card = `
            <div class="product-card" id="product-card-${productId}" data-product-id="${productId}">
                <div class="product-header">
                    <h5>${escapeHtml(productName)}</h5>
                    <span class="remove-product" onclick="removeProduct(${productId})">
                        <i class="fas fa-times"></i> Remove
                    </span>
                </div>
                <div class="price-summary" id="price-summary-${productId}"></div>
                <div id="product-variants-${productId}">
                    <div class="text-center py-3">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                </div>
            </div>`;
        $('#selected-products-container').append(card);

        loadProductData(productId, productName);
    }

    function updatePriceSummary(productId) {
        if (!productDataCache[productId]) return;
        const product = productDataCache[productId];
        let html = '';

        const purchase = formatPrice(product.purchase_price || 0);
        const additional = formatPrice(product.product_price || 0);

        if (productHasVariants[productId]) {
            const count = selectedVariants[productId]?.length || 0;
            html = `Base purchase price: Rs.${purchase}<br>
                    Base additional price: Rs.${additional}<br>
                    Selected ${count} variant(s)`;
        } else {
            html = `Purchase price: Rs.${purchase}<br>
                    Additional price: Rs.${additional}`;
        }
        $(`#price-summary-${productId}`).html(`<div class="product-price-display">${html}</div>`);
    }

    function loadProductData(productId, productName) {
        const container = $(`#product-variants-${productId}`);
        container.html(`<div class="text-center py-3">
            <div class="spinner-border text-primary" role="status"></div>
            <div>Loading variants for ${escapeHtml(productName)}...</div>
        </div>`);

        return $.getJSON(`/bundle/products/${productId}/variants`)
            .then(response => {
                console.log(`Product ${productId} data:`, response);

                productDataCache[productId] = response;
                productHasVariants[productId] = response.has_variants;

                // For new products, auto-select all variants if they have variants
                if (response.has_variants && response.variants && response.variants.length > 0) {
                    // Only auto-select if this is a new product (not in selectedVariants or empty)
                    if (!selectedVariants[productId] || selectedVariants[productId].length === 0) {
                        selectedVariants[productId] = response.variants.map(v => parseInt(v.id));
                    }
                } else {
                    // For products without variants, ensure empty array
                    if (!selectedVariants[productId]) {
                        selectedVariants[productId] = [];
                    }
                }

                renderProductVariants(productId, productName, response);
                updatePriceSummary(productId);
                calculateTotalPrices();

            }).catch(error => {
                console.error(`Error loading product ${productId}:`, error);
                container.html(`<div class="alert alert-danger">Failed to load variants.</div>`);
            });
    }

    function calculateTotalPrices() {
        let calculatedPurchase = 0;
        let calculatedAdditional = 0;

        // Reset to zero first
        selectedProducts.forEach(productId => {
            const product = productDataCache[productId];
            if (!product) {
                console.log(`No data found for product ${productId}`);
                return;
            }

            if (productHasVariants[productId] && product.variants && product.variants.length > 0) {
                const selectedIds = selectedVariants[productId] || [];
                console.log(`Product ${productId} selected variants:`, selectedIds);

                if (selectedIds.length > 0) {
                    selectedIds.forEach(variantId => {
                        const variant = product.variants.find(v => parseInt(v.id) === parseInt(variantId));
                        if (variant) {
                            calculatedPurchase += parseFloat(variant.purchase_price) || 0;
                            calculatedAdditional += parseFloat(variant.additional_price) || 0;
                        }
                    });
                }
                // Don't add base price if product has variants but none selected
            } else {
                // Product without variants - use base product prices
                calculatedPurchase += parseFloat(product.purchase_price) || 0;
                calculatedAdditional += parseFloat(product.product_price) || 0;
            }
        });

        console.log(`Final calculated prices: purchase=${calculatedPurchase}, additional=${calculatedAdditional}`);

        // Always update purchase price
        $('input[name="purchase_price"]').val(formatPrice(calculatedPurchase));

        // FIXED: Only update additional price if user hasn't manually edited it
        if (!userHasEditedAdditionalPrice || selectedProducts.length === 0) {
            $('input[name="additional_price"]').val(formatPrice(calculatedAdditional));
            // Reset manual flag if no products
            if (selectedProducts.length === 0) {
                userHasEditedAdditionalPrice = false;
                isAdditionalPriceManual = false;
            }
        }
    }

    // FIXED: Track manual changes to prices more accurately
    $('input[name="additional_price"]').on('input', function() {
        const value = $(this).val().trim();

        // Mark as manually edited when user types
        userHasEditedAdditionalPrice = true;
        isAdditionalPriceManual = value !== '' && value !== '0';

        console.log('Additional price manual entry:', isAdditionalPriceManual, value, 'userEdited:', userHasEditedAdditionalPrice);

        // If manually set to zero, treat as auto-calculate
        if (value === '0') {
            isAdditionalPriceManual = false;
            userHasEditedAdditionalPrice = false;
            calculateTotalPrices();
        }
    });

    // FIXED: Add focus event to detect when user starts editing
    $('input[name="additional_price"]').on('focus', function() {
        // Don't mark as edited just on focus, wait for actual input
    });

    function renderProductVariants(productId, productName, response) {
        const container = $(`#product-variants-${productId}`);
        let html = '';
        productHasVariants[productId] = response.has_variants;

        if (response.has_variants && response.variants && response.variants.length > 0) {
            html += `<div class="mb-2">Select variants to include:</div>`;
            response.variants.forEach(variant => {
                const variantId = parseInt(variant.id);
                const checked = selectedVariants[productId]?.includes(variantId) ? 'checked' : '';
                html += `
                    <div class="variant-item">
                        <input type="checkbox" class="variant-checkbox"
                            id="variant-${variantId}"
                            onchange="toggleVariant(${productId}, ${variantId})"
                            ${checked}>
                        <label for="variant-${variantId}">
                            ${escapeHtml(variant.name || variant.size || 'N/A')} (${escapeHtml(variant.sku || variant.shade || 'N/A')})
                        </label>
                        <span class="variant-price">
                            Purchase: Rs. ${formatPrice(variant.purchase_price || 0)}<br>
                            Additional: Rs.${formatPrice(variant.additional_price || 0)}
                        </span>
                    </div>`;
            });
        } else {
            html = `<div class="alert alert-info">This product has no variants. The base product will be included.</div>`;
            selectedVariants[productId] = [];
        }

        container.html(html);
    }

    // FIXED: toggleVariant function - ensure price recalculation works
    window.toggleVariant = function(productId, variantId) {
        variantId = parseInt(variantId);
        if (!selectedVariants[productId]) {
            selectedVariants[productId] = [];
        }

        const checkbox = $(`#variant-${variantId}`);
        const isChecked = checkbox.is(':checked');

        if (isChecked) {
            if (!selectedVariants[productId].includes(variantId)) {
                selectedVariants[productId].push(variantId);
            }
        } else {
            selectedVariants[productId] = selectedVariants[productId].filter(id => id !== variantId);
        }

        console.log(`Variant ${variantId} toggled for product ${productId}. Selected variants:`, selectedVariants[productId]);

        updatePriceSummary(productId);

        // FIXED: Always recalculate prices when variants change, unless user has manually edited
        calculateTotalPrices();
    };

    window.removeProduct = function(productId) {
        console.log(`Removing product ${productId}`);

        // Remove from arrays
        selectedProducts = selectedProducts.filter(id => id !== productId);
        delete selectedVariants[productId];
        delete productHasVariants[productId];
        delete productDataCache[productId];

        // Remove from DOM
        $(`#product-card-${productId}`).remove();

        // FIXED: Reset manual flags when removing products
        if (selectedProducts.length === 0) {
            isAdditionalPriceManual = false;
            userHasEditedAdditionalPrice = false;
        }

        calculateTotalPrices();
    };

    window.removeImagePreview = function(el) {
        $(el).closest('.image-preview').remove();
    };

    window.removeExistingImage = function(el, id) {
        removedImages.push(id);
        $('#removed-images').val(removedImages.join(','));
        $(el).closest('.existing-image').remove();
    };

    function validateBeforeSubmit() {
        if (!$('input[name="title"]').val().trim()) {
            alert('Please enter a bundle title');
            $('input[name="title"]').focus();
            return false;
        }
        if (selectedProducts.length === 0) {
            alert('Please select at least one product');
            showSection('products');
            return false;
        }

        // Check if products with variants have at least one variant selected
        for (const pid of selectedProducts) {
            if (productHasVariants[pid] && (!selectedVariants[pid] || selectedVariants[pid].length === 0)) {
                alert(`Please select at least one variant for: ${$(`#product-card-${pid} h5`).text()}`);
                showSection('products');
                return false;
            }
        }

        const hasImages = $('#existing-images-container .existing-image').length + $('#image-upload')[0].files.length > 0;
        if (!hasImages) {
            alert('Please upload or keep at least one image');
            showSection('image');
            return false;
        }
        return true;
    }

    function prepareFormData() {
        console.log('Preparing form data...');
        console.log('Final state before submit:', {
            selectedProducts,
            selectedVariants,
            productHasVariants
        });

        // Remove any existing hidden inputs to avoid duplicates
        $('input[name^="products["], input[name^="variant_ids["]').remove();

        // Add products
        selectedProducts.forEach(pid => {
            $('<input>').attr({
                type: 'hidden',
                name: 'products[]',
                value: pid
            }).appendTo('#bundle-form');
        });

        // Add variant IDs
        selectedProducts.forEach(pid => {
            const variants = selectedVariants[pid] || [];

            if (productHasVariants[pid] && variants.length > 0) {
                // Product has variants and some are selected
                variants.forEach(vid => {
                    $('<input>').attr({
                        type: 'hidden',
                        name: `variant_ids[${pid}][]`,
                        value: vid
                    }).appendTo('#bundle-form');
                });
            } else if (productHasVariants[pid] && variants.length === 0) {
                // Product has variants but none selected - add empty array
                $('<input>').attr({
                    type: 'hidden',
                    name: `variant_ids[${pid}][]`,
                    value: ''
                }).appendTo('#bundle-form');
            } else {
                // Product has no variants - add empty array
                $('<input>').attr({
                    type: 'hidden',
                    name: `variant_ids[${pid}][]`,
                    value: ''
                }).appendTo('#bundle-form');
            }
        });

        // Log what we're sending
        console.log('Form data being sent:');
        console.log('Products:', selectedProducts);
        console.log('Variant IDs:', selectedVariants);

        // Show hidden inputs for debugging
        $('input[name^="products["], input[name^="variant_ids["]').each(function() {
            console.log(`${this.name} = ${this.value}`);
        });

        // Final price calculation
        calculateTotalPrices();
    }

    $('#bundle-form').on('submit', function(e) {
        if (!validateBeforeSubmit()) {
            e.preventDefault();
            return false;
        }
        prepareFormData();
        return true;
    });

    window.submitForm = function() {
        if (validateBeforeSubmit()) {
            prepareFormData();
            document.getElementById('bundle-form').submit();
        }
    };

    // Load existing products data
    if (selectedProducts.length > 0) {
        const existingProductPromises = selectedProducts.map(pid => {
            const productOption = $(`#product-select option[value="${pid}"]`);
            if (productOption.length) {
                const productName = productOption.text();
                return loadProductData(pid, productName);
            }
            return Promise.resolve();
        });

        // Wait for all existing products to load, then calculate prices
        Promise.all(existingProductPromises).then(() => {
            console.log('All existing products loaded, calculating initial prices...');
            // FIXED: Don't mark as manually edited when loading existing data
            calculateTotalPrices();
        }).catch(error => {
            console.error('Error loading existing products:', error);
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>"']/g, m => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'}[m]));
    }

    function showToast(type, message) {
        const toast = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
        $('.toast-container').html(toast);
        setTimeout(() => $('.toast-container .alert').alert('close'), 5000);
    }
});
</script>
@endsection
