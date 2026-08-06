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
    .price-input input[readonly] {
        background-color: #f8f9fa;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="content-header">
            <h2 class="content-title">Add New Bundle</h2>
            <div>
                <button class="btn btn-md rounded font-sm hover-up" onclick="submitForm()">Publish</button>
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
                        <form action="{{ route('bundles.store') }}" method="POST" id="bundle-form" enctype="multipart/form-data">
                            @csrf

                            <!-- General Section -->
                            <section class="content-body p-xl-4 section-content active" id="section-general">
                                <div class="row mb-4">
                                    <label class="col-lg-3 col-form-label">Bundle Name <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label class="col-lg-3 col-form-label">Short Description</label>
                                    <div class="col-lg-9">
                                        <textarea class="form-control" name="short_desc" rows="3">{{ old('short_desc') }}</textarea>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label class="col-lg-3 col-form-label">Long Description</label>
                                    <div class="col-lg-9">
                                        <textarea class="form-control" name="full_desc" rows="4">{{ old('full_desc') }}</textarea>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label class="col-lg-3 col-form-label">Status</label>
                                    <div class="col-lg-9">
                                        <label class="form-check">
                                            <input type="hidden" name="status" value="0">
                                            <input type="checkbox" name="status" value="1"
                                                   class="form-check-input"
                                                   {{ old('status', 1) ? 'checked' : '' }}>
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
                                                <option value="{{ $product->id }}" @if(in_array($product->id, old('products', []))) selected @endif>
                                                    {{ $product->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Select products to add to this bundle</small>
                                    </div>
                                </div>

                                <div id="selected-products-container" class="mb-4">
                                    @php
                                        $oldProducts = old('products', []);
                                        $oldVariants = old('variant_ids', []);
                                    @endphp

                                    @foreach($oldProducts as $productId)
                                        @php
                                            $product = $products->firstWhere('id', $productId);
                                            $variants = $product ? $product->variants : collect();
                                            $productVariants = $oldVariants[$productId] ?? [];
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
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                <div class="row mb-4">
                                    <label class="col-lg-3 col-form-label">Bundle Prices</label>
                                    <div class="col-lg-9">
                                        <div class="price-input-group">
                                            <div class="price-input">
                                                <label>Additional Price</label>
                                                <input type="number" name="price" class="form-control" value="{{ old('price') }}" step="0.01" min="0">
                                                <small class="text-muted">Leave empty to calculate automatically from selected variants</small>
                                            </div>
                                            <div class="price-input">
                                                <label>Purchase Price</label>
                                                <input type="number" name="purchase_price" class="form-control" value="{{ old('purchase_price') }}" step="0.01" min="0" readonly>
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
                                    <label class="col-lg-3 col-form-label">Images (Multiple) <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input type="file" name="images[]" id="image-upload" class="form-control" multiple required>
                                        <small class="text-muted">Recommended size: 497px × 497px</small>
                                        <div class="image-preview-container mt-3" id="image-preview-container">
                                            <!-- Image previews will be added here -->
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-9 offset-lg-3">
                                        <button class="btn btn-light" type="button" data-prev-section="products">Back to Products</button>
                                        <button class="btn btn-primary" type="submit">Save Bundle</button>
                                    </div>
                                </div>
                            </section>
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
<script>
$(document).ready(function() {
    // Initialize data structures
    let selectedProducts = @json(old('products', []));
    let selectedVariants = @json(old('variant_ids', []));
    let productHasVariants = {};
    let productDataCache = {};

    // Initialize Select2
    $('#product-select').select2({
        placeholder: "Select a product...",
        allowClear: true,
        width: '100%',
        dropdownParent: $('#section-products')
    });

    // Image upload preview
    $('#image-upload').on('change', function() {
        const files = this.files;
        const container = $('#image-preview-container');
        container.empty();

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            if (!file.type.match('image.*')) continue;

            const reader = new FileReader();
            reader.onload = (function(theFile) {
                return function(e) {
                    const preview = $(`
                        <div class="image-preview">
                            <img src="${e.target.result}" title="${theFile.name}">
                            <div class="image-preview-remove" onclick="removeImagePreview(this)">
                                <i class="fas fa-times"></i>
                            </div>
                        </div>
                    `);
                    container.append(preview);
                };
            })(file);
            reader.readAsDataURL(file);
        }
    });

    // SECTION NAVIGATION =============================================

    // Show specific section and handle validation
    function showSection(section) {
        // Validate current section before moving
        if (section !== 'general') {
            const currentSection = $('.section-content.active').attr('id');

            if (currentSection === 'section-general') {
                const titleInput = $('input[name="title"]');
                if (!titleInput.val().trim()) {
                    alert('Please enter a bundle title');
                    titleInput.focus();
                    titleInput.addClass('is-invalid');
                    return false;
                }
            }
        }

        // Hide all sections
        $('.section-content').removeClass('active');

        // Show requested section
        $('#section-' + section).addClass('active');

        // Update active nav link
        $('.nav-link').removeClass('active');
        $(`.a-${section}`).addClass('active');

        // Scroll to top of section
        $('html, body').animate({
            scrollTop: $('#section-' + section).offset().top - 20
        }, 200);

        return true;
    }

    // Navigation event handlers
    $('[data-section], [data-next-section], [data-prev-section]').on('click', function(e) {
        e.preventDefault();
        const section = $(this).data('section') || $(this).data('next-section') || $(this).data('prev-section');
        showSection(section);
    });

    // PRODUCT MANAGEMENT =============================================

    // Product selection handler
    $('#product-select').on('change', function() {
        const productId = parseInt($(this).val());
        if (!productId) return;

        const productName = $(this).find('option:selected').text();
        addProduct(productId, productName);
        $(this).val('').trigger('change');
    });

    // Add product to bundle
    function addProduct(productId, productName) {
        if (selectedProducts.includes(productId)) {
            showToast('warning', 'This product is already added');
            return;
        }

        selectedProducts.push(productId);
        selectedVariants[productId] = selectedVariants[productId] || [];

        const productCard = `
            <div class="product-card" id="product-card-${productId}" data-product-id="${productId}">
                <div class="product-header">
                    <h5>${escapeHtml(productName)}</h5>
                    <span class="remove-product" onclick="removeProduct(${productId})">
                        <i class="fas fa-times"></i> Remove
                    </span>
                </div>
                <div class="price-summary" id="price-summary-${productId}">
                    <!-- Price summary will be updated here -->
                </div>
                <div id="product-variants-${productId}">
                    <div class="text-center py-3">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                </div>
            </div>
        `;

        $('#selected-products-container').append(productCard);
        loadProductData(productId, productName);
    }

    // Update price summary
    function updatePriceSummary(productId) {
        if (!productDataCache[productId]) return;

        let summaryText = '';
        if (productHasVariants[productId]) {
            const selectedCount = selectedVariants[productId]?.length || 0;
            const productPurchasePrice = productDataCache[productId].purchase_price || 0;
            const productAdditionalPrice = productDataCache[productId].product_price || 0;
            summaryText = `
                Base purchase price: Rs.${productPurchasePrice.toFixed(2)}<br>
                Base additional price: Rs.${productAdditionalPrice.toFixed(2)}<br>
                Selected ${selectedCount} variant(s)
            `;
        } else {
            const productPurchasePrice = productDataCache[productId].purchase_price || 0;
            const productAdditionalPrice = productDataCache[productId].product_price || 0;
            summaryText = `
                Purchase price: Rs.${productPurchasePrice.toFixed(2)}<br>
                Additional price: Rs.${productAdditionalPrice.toFixed(2)}
            `;
        }
        $(`#price-summary-${productId}`).html(`<div class="product-price-display">${summaryText}</div>`);
    }

    // Load product variant data
    function loadProductData(productId, productName) {
        const variantsContainer = $(`#product-variants-${productId}`);
        variantsContainer.html(`
            <div class="text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div>Loading variants for ${escapeHtml(productName)}...</div>
            </div>
        `);

        // Clear any previous error state
        variantsContainer.removeClass('alert alert-danger');

        $.ajax({
            url: `/bundle/products/${productId}/variants`,
            type: 'GET',
            dataType: 'json',
            timeout: 10000, // 10 seconds timeout
            success: function(response) {
                if (!response || response.success === false) {
                    throw new Error(response?.message || 'Invalid response from server');
                }

                // Store product data in cache
                productDataCache[productId] = response;
                productHasVariants[productId] = response.has_variants;

                // Initialize selected variants if not already set
                if (!selectedVariants[productId]) {
                    selectedVariants[productId] = response.has_variants ? [] : [null];
                }

                renderProductVariants(productId, productName, response);
                calculateTotalPrices();
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Failed to load product variants.';
                let response = null;

                try {
                    response = xhr.responseJSON || JSON.parse(xhr.responseText);
                    errorMessage = response?.message || errorMessage;
                } catch (e) {
                    errorMessage = xhr.statusText || errorMessage;
                }

                if (xhr.status === 404) {
                    errorMessage = 'Product not found';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error occurred';
                }

                variantsContainer.html(`
                    <div class="alert alert-danger">
                        <strong>Error:</strong> ${errorMessage}
                        <div class="mt-2">
                            <button class="btn btn-sm btn-outline-danger"
                                    onclick="loadProductData(${productId}, '${escapeHtml(productName)}')">
                                <i class="fas fa-sync-alt"></i> Retry
                            </button>
                        </div>
                    </div>
                `);

                // Fallback - allow product without variants
                productHasVariants[productId] = false;
                selectedVariants[productId] = [null];
                updatePriceSummary(productId);
                calculateTotalPrices();
            }
        });
    }

    // Calculate total prices for the bundle
    function calculateTotalPrices() {
        let totalPurchasePrice = 0;
        let totalAdditionalPrice = 0;

        selectedProducts.forEach(productId => {
            if (productDataCache[productId]) {
                if (productHasVariants[productId]) {
                    const selectedVariantIds = selectedVariants[productId] || [];

                    if (selectedVariantIds.length > 0) {
                        selectedVariantIds.forEach(variantId => {
                            const variant = productDataCache[productId].variants.find(v => v.id == variantId);
                            if (variant) {
                                totalPurchasePrice += parseFloat(variant.purchase_price) || 0;
                                totalAdditionalPrice += parseFloat(variant.additional_price) || 0;
                            }
                        });
                    } else {
                        // If product has variants but none selected, use base product price
                        totalPurchasePrice += parseFloat(productDataCache[productId].purchase_price) || 0;
                        totalAdditionalPrice += parseFloat(productDataCache[productId].product_price) || 0;
                    }
                } else {
                    // Product has no variants - use base prices
                    totalPurchasePrice += parseFloat(productDataCache[productId].purchase_price) || 0;
                    totalAdditionalPrice += parseFloat(productDataCache[productId].product_price) || 0;
                }
            }
        });

        // Update the form fields
        $('input[name="purchase_price"]').val(totalPurchasePrice.toFixed(2));

        // Only update additional price if it's empty or hasn't been manually set
        const additionalPriceInput = $('input[name="price"]');
        const isManualInput = additionalPriceInput.data('manual-input');

        if (!isManualInput) {
            additionalPriceInput.val(totalAdditionalPrice.toFixed(2));
        }
    }

    // Calculate just the additional price
    function calculateTotalAdditionalPrice() {
        let total = 0;

        selectedProducts.forEach(productId => {
            if (productDataCache[productId]) {
                if (productHasVariants[productId]) {
                    const selectedVariantIds = selectedVariants[productId] || [];

                    if (selectedVariantIds.length > 0) {
                        selectedVariantIds.forEach(variantId => {
                            const variant = productDataCache[productId].variants.find(v => v.id == variantId);
                            if (variant) {
                                total += parseFloat(variant.additional_price) || 0;
                            }
                        });
                    } else {
                        total += parseFloat(productDataCache[productId].product_price) || 0;
                    }
                } else {
                    total += parseFloat(productDataCache[productId].product_price) || 0;
                }
            }
        });

        return total;
    }

    // Render product variants UI
    function renderProductVariants(productId, productName, response) {
        const variantsContainer = $(`#product-variants-${productId}`);
        productHasVariants[productId] = response.has_variants;

        let html = '';
        if (response.has_variants) {
            html += `<div class="mb-2">Select variants to include:</div>`;

            if (response.variants.length > 0) {
                response.variants.forEach(variant => {
                    const isChecked = selectedVariants[productId] &&
                                    selectedVariants[productId].includes(variant.id);

                    html += `
                        <div class="variant-item">
                            <input type="checkbox"
                                class="variant-checkbox"
                                id="variant-${variant.id}"
                                onchange="toggleVariant(${productId}, ${variant.id})"
                                ${isChecked ? 'checked' : ''}>
                            <label for="variant-${variant.id}">
                                ${escapeHtml(variant.name || 'N/A')} (${escapeHtml(variant.sku || 'N/A')})
                            </label>
                            <span class="variant-price">
                                Purchase: Rs.${(variant.purchase_price || 0).toFixed(2)}<br>
                                Additional: Rs.${(variant.additional_price || 0).toFixed(2)}
                            </span>
                        </div>
                    `;
                });
            } else {
                html += `
                    <div class="alert alert-warning">
                        This product has variants marked in the system but none are currently available.
                    </div>
                `;
                productHasVariants[productId] = false;
                selectedVariants[productId] = [null];
            }
        } else {
            html += `
                <div class="alert alert-info">
                    This product has no variants. The base product will be included.
                </div>
            `;
            selectedVariants[productId] = [null];
        }

        variantsContainer.html(html);
        updatePriceSummary(productId);
    }

    // Toggle variant selection
    window.toggleVariant = function(productId, variantId) {
        const checkbox = $(`#variant-${variantId}`);
        const isChecked = checkbox.is(':checked');

        if (isChecked) {
            if (!selectedVariants[productId].includes(variantId)) {
                selectedVariants[productId].push(variantId);
            }
        } else {
            selectedVariants[productId] = selectedVariants[productId].filter(id => id !== variantId);
        }

        updatePriceSummary(productId);

        // Force update additional price field
        const totalAdditionalPrice = calculateTotalAdditionalPrice();
        $('input[name="price"]').val(totalAdditionalPrice.toFixed(2));

        calculateTotalPrices();
    };

    // Remove product from bundle
    window.removeProduct = function(productId) {
        selectedProducts = selectedProducts.filter(id => id !== productId);
        delete selectedVariants[productId];
        delete productHasVariants[productId];
        $(`#product-card-${productId}`).remove();

        // Force recalculate prices
        const totalAdditionalPrice = calculateTotalAdditionalPrice();
        $('input[name="price"]').val(totalAdditionalPrice.toFixed(2));
        calculateTotalPrices();
    };

    // IMAGE HANDLING =============================================

    // Remove image preview
    window.removeImagePreview = function(element) {
        $(element).closest('.image-preview').remove();
    };

    // FORM SUBMISSION =============================================

    // Validate form before submission
    function validateBeforeSubmit() {
        // Validate title
        if (!$('input[name="title"]').val().trim()) {
            alert('Please enter a bundle title');
            $('input[name="title"]').focus();
            return false;
        }

        // Validate at least one product
        if (selectedProducts.length === 0) {
            alert('Please select at least one product');
            showSection('products');
            return false;
        }

        // Validate variants for products that have them
        for (const productId of selectedProducts) {
            if (productHasVariants[productId] &&
                (!selectedVariants[productId] || selectedVariants[productId].length === 0)) {
                const productName = $(`#product-card-${productId} h5`).text();
                alert(`Please select at least one variant for: ${productName}`);
                showSection('products');
                return false;
            }
        }

        // Validate images
        if (!$('input[name="images[]"]')[0].files.length) {
            alert('Please upload at least one image');
            showSection('image');
            return false;
        }

        return true;
    }

    // Prepare form data for submission
    function prepareFormData() {
        // Clear existing dynamic inputs
        $('input[name^="products["], input[name^="variant_ids["]').remove();

        // Add products as array inputs
        selectedProducts.forEach(productId => {
            $('<input>').attr({
                type: 'hidden',
                name: 'products[]',
                value: productId
            }).appendTo('#bundle-form');
        });

        // Add variants as nested array inputs
        selectedProducts.forEach(productId => {
            const variants = selectedVariants[productId] || [];

            // For products without variants, send null
            if (variants.length === 0 && productHasVariants[productId]) {
                $('<input>').attr({
                    type: 'hidden',
                    name: `variant_ids[${productId}][]`,
                    value: null
                }).appendTo('#bundle-form');
            } else {
                // For products with variants, send all selected variants
                variants.forEach(variantId => {
                    $('<input>').attr({
                        type: 'hidden',
                        name: `variant_ids[${productId}][]`,
                        value: variantId
                    }).appendTo('#bundle-form');
                });
            }
        });
    }

    // Form submission handler
    $('#bundle-form').on('submit', function(e) {
        if (!validateBeforeSubmit()) {
            e.preventDefault();
            return false;
        }

        prepareFormData();
        return true;
    });

    // Submit form programmatically
    window.submitForm = function() {
        if (validateBeforeSubmit()) {
            prepareFormData();
            document.getElementById('bundle-form').submit();
        }
    };

    // Prevent auto-update when user manually enters a price
    $('input[name="price"]').on('input', function() {
        $(this).data('manual-input', true);
    });

    // Initialize existing products from validation errors
    selectedProducts.forEach(productId => {
        if (!$('#product-card-' + productId).length) {
            const product = $('#product-select').find('option[value="' + productId + '"]');
            if (product.length) {
                addProduct(productId, product.text());
            }
        }
    });

    // Helper function to escape HTML
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Show toast message
    function showToast(type, message) {
        const toast = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;

        $('.toast-container').html(toast);

        // Auto-hide after 5 seconds
        setTimeout(() => {
            $('.toast-container .alert').alert('close');
        }, 5000);
    }
});
</script>
@endsection
