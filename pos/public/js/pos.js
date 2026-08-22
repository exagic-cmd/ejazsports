document.addEventListener('DOMContentLoaded', function () {
    if (!sessionStorage.getItem('cartProducts')) {
        sessionStorage.setItem('cartProducts', JSON.stringify([]));
    }
    if (!sessionStorage.getItem('cartBundles')) {
        sessionStorage.setItem('cartBundles', JSON.stringify([]));
    }

    // Delay to ensure DOM is fully loaded
    setTimeout(() => {
        updateCustomerDisplay();
        bindCartItemEvents();
        updateCartDisplay();
    }, 100);
});

function bindCartItemEvents() {
    document.querySelectorAll('.tPrice').forEach(input => {
        input.removeEventListener('change', handleProductChange);
        input.addEventListener('change', handleProductChange);
    });

    document.querySelectorAll('.bundle-input').forEach(input => {
        input.removeEventListener('change', handleBundleChange);
        input.addEventListener('change', handleBundleChange);
    });
}

function handleProductChange() {
    const productId = this.getAttribute('data-pId');
    const variantId = this.getAttribute('data-vId') || 0;
    addToCartMul(productId, variantId);
}

function handleBundleChange() {
    const bundleId = this.getAttribute('data-bId');
    if (this.id.startsWith('bqty')) {
        updateBundleQty(bundleId);
    } else if (this.id.startsWith('bprice')) {
        updateBundlePrice(bundleId);
    } else if (this.id.startsWith('btPrice')) {
        updateBundleTotal(bundleId);
    }
}

document.querySelectorAll('.related_category').forEach(element => {
    element.addEventListener('click', getBrandData);
});

let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function getBrandData() {
    document.getElementById('app').style.opacity = '0.1';
    const brand_id = this.getAttribute('data-brand-id');

    fetch(config.routes.getBrandData, {
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json, text-plain, */*",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": token
        },
        method: 'post',
        credentials: "same-origin",
        body: JSON.stringify({
            brand_id
        })
    })
        .then(response => response.text())
        .then(html => {
            document.getElementById('product-list').innerHTML = html;
            document.getElementById('app').style.opacity = '1';
            $('.related_category').removeClass("focus-category");
            $(`.related_category[data-brand-id="${brand_id}"]`).addClass('focus-category');
        })
        .catch(error => {
            toastr.error(error.message || 'Failed to fetch brand data');
            document.getElementById('app').style.opacity = '1';
        });
}

function getCategoryData(category_id) {
    document.getElementById('app').style.opacity = '0.1';

    fetch(config.routes.getCategoryData, {
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json, text-plain, */*",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": token
        },
        method: 'post',
        credentials: "same-origin",
        body: JSON.stringify({
            category_id,
            customer_id: sessionStorage.getItem('customer_id'),
            route: $('#route').val()
        })
    })
        .then(response => response.text())
        .then(html => {
            document.getElementById('product-list').innerHTML = html;
            document.getElementById('app').style.opacity = '1';
        })
        .catch(error => {
            toastr.error(error.message || 'Failed to fetch category data');
            document.getElementById('app').style.opacity = '1';
        });
}

$(".search-field").keyup(throttle(function () {
    const query = $('#nav-search').val().trim();
    const route = $('#route').val();

    if (query.length > 2) {
        document.getElementById('app').style.opacity = '0.1';
        fetch(config.routes.getSearchData, {
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json, text-plain, */*",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": token
            },
            method: 'post',
            credentials: "same-origin",
            body: JSON.stringify({
                val: query,
                route,
                customer_id: sessionStorage.getItem('customer_id')
            })
        })
            .then(response => response.text())
            .then(html => {
                document.getElementById('product-list').innerHTML = html;
                document.getElementById('app').style.opacity = '1';
            })
            .catch(error => {
                toastr.error(error.message || 'Failed to search products');
                document.getElementById('app').style.opacity = '1';
            });
    } else {
        document.getElementById('product-list').innerHTML = '';
    }
}, 200));

$(".customer_search_field").keyup(throttle(function () {
    const query = $('#customer_search_field').val().trim();

    if (query.length > 2) {
        document.getElementById('app').style.opacity = '0.1';
        fetch(config.routes.getCustomerSearchData, {
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json, text-plain, */*",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": token
            },
            method: 'post',
            credentials: "same-origin",
            body: JSON.stringify({
                val: query
            })
        })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.text();
            })
            .then(html => {
                if (!html) throw new Error('Empty response');
                document.getElementById('customer-list').innerHTML = html;
                bindCustomerEvents();
                document.getElementById('app').style.opacity = '1';
            })
            .catch(error => {
                toastr.error('Customer search failed: ' + error.message);
                document.getElementById('app').style.opacity = '1';
            });
    } else {
        document.getElementById('customer-list').innerHTML = '';
    }
}, 200));

$(".order_search_field").keyup(throttle(function () {
    const query = $('#order_search_field').val().trim();

    if (query.length > 2) {
        document.getElementById('app').style.opacity = '0.1';
        fetch(config.routes.getOrderSearchData, {
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json, text-plain, */*",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": token
            },
            method: 'post',
            credentials: "same-origin",
            body: JSON.stringify({
                val: query
            })
        })
            .then(response => response.text())
            .then(html => {
                document.getElementById('order-list').innerHTML = html;
                document.getElementById('app').style.opacity = '1';
                document.querySelectorAll('.record').forEach(element => {
                    element.addEventListener('click', orderInfo);
                });
            })
            .catch(error => {
                toastr.error(error.message || 'Failed to search orders');
                document.getElementById('app').style.opacity = '1';
            });
    } else {
        document.getElementById('order-list').innerHTML = '';
    }
}, 200));

function throttle(f, delay) {
    let timer = null;
    return function () {
        const context = this,
            args = arguments;
        clearTimeout(timer);
        timer = window.setTimeout(function () {
            f.apply(context, args);
        }, delay || 200);
    };
}

function addToCart(itemId, variantId = 0, isBundle = false) {
    if (isBundle) {
        let cartBundles = JSON.parse(sessionStorage.getItem('cartBundles')) || [];
        const existingBundle = cartBundles.find(b => b.bundle_id == itemId);
        if (existingBundle) {
            existingBundle.qty = parseInt(existingBundle.qty) + 1;
        } else {
            cartBundles.push({
                bundle_id: itemId,
                qty: 1,
                price: 0  // Initialize bundle price as 0, same as products
            });
        }
        sessionStorage.setItem('cartBundles', JSON.stringify(cartBundles));
        toastr.success('Bundle added to cart!');
    } else {
        let cartProducts = JSON.parse(sessionStorage.getItem('cartProducts')) || [];
        const existingProduct = cartProducts.find(p => p.id == itemId && p.variant_id == variantId);
        if (existingProduct) {
            existingProduct.qty = parseInt(existingProduct.qty) + 1;
        } else {
            cartProducts.push({
                id: itemId,
                variant_id: variantId,
                qty: 1,
                price: 0
            });
        }
        sessionStorage.setItem('cartProducts', JSON.stringify(cartProducts));
        toastr.success('Product added to cart!');
    }
    updateCartDisplay();
}


function addToCartMul(productId, variantId) {
    const qtyId = '#qty' + productId + variantId;
    const priceId = '#price' + productId + variantId;
    const qty = parseInt($(qtyId).val()) || 1;
    const price = parseFloat($(priceId).val()) || 0;

    let cartProducts = JSON.parse(sessionStorage.getItem('cartProducts')) || [];
    let existingIndex = variantId == 0 ?
        cartProducts.findIndex(item => item.id == productId) :
        cartProducts.findIndex(item => item.variant_id == variantId);

    if (existingIndex >= 0) {
        cartProducts[existingIndex].qty = qty;
        cartProducts[existingIndex].price = price;
    } else {
        cartProducts.push({
            id: productId,
            variant_id: variantId,
            qty: qty,
            price: price
        });
    }

    sessionStorage.setItem('cartProducts', JSON.stringify(cartProducts));
    updateCartDisplay();
}

// Add similar function for bundles
function addToCartMulBundle(bundleId) {
    const qtyId = '#bqty' + bundleId;
    const priceId = '#bprice' + bundleId;
    const qty = parseInt($(qtyId).val()) || 1;
    const price = parseFloat($(priceId).val()) || 0;

    let cartBundles = JSON.parse(sessionStorage.getItem('cartBundles')) || [];
    let existingIndex = cartBundles.findIndex(item => item.bundle_id == bundleId);

    if (existingIndex >= 0) {
        cartBundles[existingIndex].qty = qty;
        cartBundles[existingIndex].price = price;
    } else {
        cartBundles.push({
            bundle_id: bundleId,
            qty: qty,
            price: price
        });
    }

    sessionStorage.setItem('cartBundles', JSON.stringify(cartBundles));
    updateCartDisplay();
}

function updateCartDisplay() {
    const cartProducts = JSON.parse(sessionStorage.getItem('cartProducts')) || [];
    const cartBundles = JSON.parse(sessionStorage.getItem('cartBundles')) || [];
    const discount_id = sessionStorage.getItem('discount');
    const customer_id = sessionStorage.getItem('customer_id');

    // Validate bundles data
    const validatedBundles = cartBundles.filter(bundle => {
        if (!bundle.bundle_id || !bundle.qty || bundle.price === undefined) {
            console.warn('Invalid bundle data', bundle);
            return false;
        }
        return true;
    });
    sessionStorage.setItem('cartBundles', JSON.stringify(validatedBundles));

    // Save scroll position
    const scrollContainer = document.querySelector('.pos-nav-pane.active');
    const scrollPos = scrollContainer ? scrollContainer.scrollTop : 0;

    fetch(config.routes.getCartData, {
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json, text-plain, */*",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": token
        },
        method: 'post',
        credentials: "same-origin",
        body: JSON.stringify({
            cart: cartProducts,
            bundles: validatedBundles,
            discount_id,
            customer_id,
            manual_return_only: sessionStorage.getItem('manual_return_only') || '0'
        })
    })
        .then(async response => {
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to update cart');
            }
            return response.text();
        })
        .then(html => {
            document.getElementById('pos-cart-container').innerHTML = html;
            
            // Restore scroll position
            const newScrollContainer = document.querySelector('.pos-nav-pane.active');
            if (newScrollContainer) {
                newScrollContainer.scrollTop = scrollPos;
            }

            bindCartItemEvents(); // Re-bind product and bundle events
            updateCustomerDisplay();

            // Sync manual return checkbox and buttons state
            const isManualReturn = sessionStorage.getItem('manual_return_only') === '1';
            const checkbox = document.getElementById('manual_return_checkbox');
            if (checkbox) {
                checkbox.checked = isManualReturn;
            }
            updateButtonStates(isManualReturn);

            toastr.success('Cart updated successfully!');
        })
        .catch(error => {
            toastr.error(error.message || 'Failed to update cart');
            document.getElementById('app').style.opacity = '1';
        });
}
function updateCustomerDisplay() {
    const customerName = sessionStorage.getItem('customer_name');
    const customerNameElement = document.getElementById('customer_name');
    const customerIdElement = document.getElementById('customer_id');

    if (customerName) {
        if (customerNameElement) {
            customerNameElement.textContent = customerName;
            customerNameElement.style.color = '#fff';
        } else {
            console.warn('customer_name element not found');
        }
        if (customerIdElement) {
            customerIdElement.value = sessionStorage.getItem('customer_id') || '';
        } else {
            console.warn('customer_id element not found');
        }
    } else {
        console.warn('No customer_name found in sessionStorage');
    }
}

function updatePayment() {
    const cartProducts = JSON.parse(sessionStorage.getItem('cartProducts')) || [];
    const cartBundles = JSON.parse(sessionStorage.getItem('cartBundles')) || [];
    const customerId = sessionStorage.getItem('customer_id');
    if (cartProducts.length === 0 && cartBundles.length === 0) {
        toastr.error('Warning: Current cart is empty!');
        return;
    }
    document.getElementById('app').style.opacity = '0.1';
    const employeeId = $('#employee_id').val();
    sessionStorage.setItem('employee_id', employeeId);
    const requestData = {
        cart: cartProducts,
        bundles: cartBundles.map(bundle => ({
            bundle_id: bundle.bundle_id,
            qty: parseInt(bundle.qty) || 1,
            price: parseFloat(bundle.price) || 0
        })),
        discount_id: sessionStorage.getItem('discount'),
        customer_id: customerId,
        store_id: 1
    };
    fetch(config.routes.updatePayment, {
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json, text-plain, */*",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": token
        },
        method: 'post',
        credentials: "same-origin",
        body: JSON.stringify(requestData)
    })
        .then(response => response.text())
        .then(html => {
            document.getElementById('pos-content-container').innerHTML = html;
            document.getElementById('app').style.opacity = '1';
            document.querySelectorAll(".cal-li").forEach(btn => {
                btn.addEventListener('click', updateAmount);
            });
        })
        .catch(error => {
            toastr.error(error.message || 'Payment processing failed');
            document.getElementById('app').style.opacity = '1';
        });
}

function clearCart() {
    sessionStorage.removeItem('cartProducts');
    sessionStorage.removeItem('cartBundles');
    sessionStorage.removeItem('discount');
    sessionStorage.removeItem('employee_id');
    sessionStorage.removeItem('customer_id');
    sessionStorage.removeItem('customer_name');
    sessionStorage.removeItem('edit_order');

    fetch(config.routes.getCartData, {
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json, text-plain, */*",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": token
        },
        method: 'post',
        credentials: "same-origin",
        body: JSON.stringify({
            cart: [],
            bundles: []
        })
    })
        .then(response => response.text())
        .then(html => {
            document.getElementById('pos-cart-container').innerHTML = html;
            toastr.success('Cart cleared successfully!');
            window.location.reload();
        })
        .catch(error => {
            toastr.error(error.message || 'Failed to clear cart');
        });
}

function removeQtyFromCart(productId, variantId) {
    let cartProducts = JSON.parse(sessionStorage.getItem('cartProducts')) || [];
    const index = variantId == 0 ?
        cartProducts.findIndex(item => item.id == productId) :
        cartProducts.findIndex(item => item.variant_id == variantId);

    if (index >= 0) {
        if (cartProducts[index].qty == 1) {
            cartProducts.splice(index, 1);
        } else {
            cartProducts[index].qty = parseInt(cartProducts[index].qty) - 1;
        }
        sessionStorage.setItem('cartProducts', JSON.stringify(cartProducts));
        updateCartDisplay();
        toastr.success('Product quantity decreased successfully!');
    } else {
        toastr.error('Product not found!');
    }
}

function removeFromCart(itemId, variantId = 0, isBundle = false) {
    if (isBundle) {
        let cartBundles = JSON.parse(sessionStorage.getItem('cartBundles')) || [];
        const initialLength = cartBundles.length;
        cartBundles = cartBundles.filter(item => item.bundle_id != itemId);
        if (cartBundles.length < initialLength) {
            sessionStorage.setItem('cartBundles', JSON.stringify(cartBundles));
            toastr.success('Bundle removed from cart!');
        } else {
            toastr.error('Bundle not found in cart!');
        }
    } else {
        let cartProducts = JSON.parse(sessionStorage.getItem('cartProducts')) || [];
        const initialLength = cartProducts.length;
        cartProducts = variantId == 0 ?
            cartProducts.filter(item => item.id != itemId) :
            cartProducts.filter(item => item.variant_id != variantId);
        if (cartProducts.length < initialLength) {
            sessionStorage.setItem('cartProducts', JSON.stringify(cartProducts));
            toastr.success('Product removed from cart!');
        } else {
            toastr.error('Product not found in cart!');
        }
    }
    updateCartDisplay();
}


// Update bundle quantity
function updateBundleQty(bundleId) {
    const qty = parseInt($('#bqty' + bundleId).val()) || 1;
    const price = parseFloat($('#bprice' + bundleId).val()) || 0;
    addToCartMulBundle(bundleId);
}

// Update bundle price
function updateBundlePrice(bundleId) {
    const price = parseFloat($('#bprice' + bundleId).val()) || 0;
    const qty = parseInt($('#bqty' + bundleId).val()) || 1;
    addToCartMulBundle(bundleId);
}

// Update bundle total
function updateBundleTotal(bundleId) {
    const total = parseFloat($('#btPrice' + bundleId).val()) || 0;
    const qty = parseInt($('#bqty' + bundleId).val()) || 1;
    const price = total / qty;

    $('#bprice' + bundleId).val(price.toFixed(2));
    addToCartMulBundle(bundleId);
}

function holdCartModal() {
    if (!sessionStorage.getItem('cartProducts') && !sessionStorage.getItem('cartBundles')) {
        toastr.error('Warning: Current cart is empty!');
    } else {
        document.body.classList.add("pos-modal-open");
        document.getElementById('holdCart').style.display = 'block';
    }
}

function closeHoldModal() {
    document.getElementById('holdCart').style.display = 'none';
    document.body.classList.remove("pos-modal-open");
}

function holdCart() {
    const note = $('#note').val();
    if (!note) {
        toastr.error('Please enter a note for the hold cart.');
        return;
    }

    const id = Math.floor(1000 + Math.random() * 9000);
    const time = new Date();
    const cartProducts = sessionStorage.getItem('cartProducts');
    const cartBundles = sessionStorage.getItem('cartBundles');

    let holdCarts = JSON.parse(sessionStorage.getItem('holdCarts')) || [];
    holdCarts.push({
        id,
        time,
        note,
        products: cartProducts,
        bundles: cartBundles
    });
    sessionStorage.setItem('holdCarts', JSON.stringify(holdCarts));

    sessionStorage.removeItem('cartProducts');
    sessionStorage.removeItem('cartBundles');

    fetch(config.routes.getCartData, {
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json, text-plain, */*",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": token
        },
        method: 'post',
        credentials: "same-origin",
        body: JSON.stringify({
            cart: [],
            bundles: []
        })
    })
        .then(response => response.text())
        .then(html => {
            document.getElementById('pos-cart-container').innerHTML = html;
            if (sessionStorage.getItem('holdCarts')) {
                const holdCarts = JSON.parse(sessionStorage.getItem('holdCarts'));
                const holdCountElement = document.getElementsByClassName('hold_cart_count')[0];
                if (holdCountElement) {
                    holdCountElement.innerHTML = holdCarts.length;
                }
            }
            toastr.success('Cart has been added to hold list successfully!');
            window.location.href = '/';
        })
        .catch(error => {
            toastr.error(error.message || 'Failed to hold cart');
        });

    closeHoldModal();
}

function holdList() {
    document.getElementById('app').style.opacity = '0.1';
    const holdCarts = JSON.parse(sessionStorage.getItem('holdCarts')) || [];

    fetch(config.routes.getHoldList, {
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json, text-plain, */*",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": token
        },
        method: 'post',
        credentials: "same-origin",
        body: JSON.stringify({
            cart: holdCarts
        })
    })
        .then(response => response.text())
        .then(html => {
            document.getElementById('pos-content-container').innerHTML = html;
            document.getElementById('app').style.opacity = '1';
        })
        .catch(error => {
            toastr.error(error.message || 'Failed to fetch hold list');
            document.getElementById('app').style.opacity = '1';
        });
}

function salesHistory() {
    document.getElementById('app').style.opacity = '0.1';

    fetch('/pos/salesHistory', {
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json, text-plain, */*",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": token
        },
        method: 'post',
        credentials: "same-origin",
        body: JSON.stringify({})
    })
        .then(response => response.text())
        .then(html => {
            document.getElementById('pos-content-container').innerHTML = html;
            document.getElementById('app').style.opacity = '1';
            document.querySelectorAll('.record').forEach(element => {
                element.addEventListener('click', orderInfo);
            });
        })
        .catch(error => {
            toastr.error(error.message || 'Failed to fetch sales history');
            document.getElementById('app').style.opacity = '1';
        });
}

function removeHoldCart(id) {
    let holdCarts = JSON.parse(sessionStorage.getItem('holdCarts')) || [];
    const index = holdCarts.findIndex(cart => cart.id == id);

    if (index >= 0) {
        holdCarts.splice(index, 1);
        sessionStorage.setItem('holdCarts', JSON.stringify(holdCarts));

        document.getElementById('app').style.opacity = '0.1';
        fetch(config.routes.getHoldList, {
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json, text-plain, */*",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": token
            },
            method: 'post',
            credentials: "same-origin",
            body: JSON.stringify({
                cart: holdCarts
            })
        })
            .then(response => response.text())
            .then(html => {
                document.getElementById('pos-content-container').innerHTML = html;
                toastr.success('Cart deleted successfully!');
                document.getElementById('app').style.opacity = '1';
            })
            .catch(error => {
                toastr.error(error.message || 'Failed to remove hold cart');
                document.getElementById('app').style.opacity = '1';
            });
    } else {
        toastr.error('Cart not found!');
    }
}

function addHoldCart(id) {
    let holdCarts = JSON.parse(sessionStorage.getItem('holdCarts')) || [];
    const holdCart = holdCarts.find(cart => cart.id == id);

    if (holdCart) {
        sessionStorage.setItem('cartProducts', holdCart.products);
        sessionStorage.setItem('cartBundles', holdCart.bundles || JSON.stringify([]));
        holdCarts = holdCarts.filter(cart => cart.id != id);
        sessionStorage.setItem('holdCarts', JSON.stringify(holdCarts));
        toastr.success('Selected cart added to current list!');
        window.location.href = '/';
    } else {
        toastr.error('Cart not found!');
    }
}

function customerInfo(id) {
    document.getElementById('app').style.opacity = '0.1';

    fetch(config.routes.getSpecificCustomerData, {
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json, text-plain, */*",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": token
        },
        method: 'post',
        credentials: "same-origin",
        body: JSON.stringify({
            customer_id: id
        })
    })
        .then(response => response.text())
        .then(html => {
            document.getElementById('customer-detail').innerHTML = html;
            document.getElementById('app').style.opacity = '1';
        })
        .catch(error => {
            toastr.error(error.message || 'Failed to fetch customer info');
            document.getElementById('app').style.opacity = '1';
        });
}

function selectCustomer(id, name) {
    try {
        if (!id || !name) throw new Error('Invalid customer data');
        console.log('Selecting customer:', {
            id,
            name
        });
        sessionStorage.setItem('customer_id', id);
        sessionStorage.setItem('customer_name', name);
        updateCustomerDisplay();
        closeModal('customerModal');
        updateCartDisplay();
        toastr.success(`Customer selected: ${name}`);
        window.location.href = '/';
    } catch (error) {
        console.error('selectCustomer error:', error);
        toastr.error('Failed to select customer: ' + error.message);
    }
}

function mannualReturnForm() {
    const cartProducts = JSON.parse(sessionStorage.getItem('cartProducts')) || [];
    const cartBundles = JSON.parse(sessionStorage.getItem('cartBundles')) || [];
    
    if (cartProducts.length === 0 && cartBundles.length === 0) {
        toastr.error('Warning: Current cart is empty!');
        return;
    }

    document.getElementById('app').style.opacity = '0.1';
    const employeeId = $('#employee_id').val();
    sessionStorage.setItem('employee_id', employeeId);

    fetch(config.routes.mannualReturnForm, {
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json, text-plain, */*",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": token
        },
        method: 'post',
        credentials: "same-origin",
        body: JSON.stringify({
            cart: cartProducts,
            bundles: cartBundles,
            discount_id: sessionStorage.getItem('discount'),
            customer_id: sessionStorage.getItem('customer_id'),
            manual_return_only: sessionStorage.getItem('manual_return_only') || '0',
            store_id: 1
        })
    })
        .then(response => response.text())
        .then(html => {
            document.getElementById('pos-content-container').innerHTML = html;
            document.getElementById('app').style.opacity = '1';
            document.querySelectorAll(".cal-li").forEach(btn => {
                btn.addEventListener('click', updateAmount);
            });
        })
        .catch(error => {
            toastr.error(error.message || 'Failed to load manual return form');
            document.getElementById('app').style.opacity = '1';
        });
}

function updateAmount() {
    if ($(this).html() === 'C') {
        $('#btn-pay').prop('disabled', true);
        $('#tendered').html(0);
        $('#change').html(0);
    } else {
        let tendered = parseInt($('#tendered').html()) || 0;
        if (tendered === 0) {
            $('#tendered').html($(this).html());
        } else {
            $('#tendered').html(tendered + $(this).html());
        }

        tendered = parseInt($('#tendered').html());
        const total = parseInt($('#aftotal').val());
        const change = tendered - total;

        if (change >= 0) {
            $('#change').html(change);
            $('#btn-pay').prop('disabled', false);
        }
    }
}

function createSaleWithPrint() {
    const btnPay = document.getElementById('btn-pay');
    btnPay.disabled = true;
    btnPay.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';

    const cartProducts = JSON.parse(sessionStorage.getItem('cartProducts')) || [];
    const cartBundles = JSON.parse(sessionStorage.getItem('cartBundles')) || [];

    const validatedBundles = cartBundles.filter(bundle => {
        if (!bundle.bundle_id || !bundle.qty || bundle.price === undefined) {
            console.warn('Invalid bundle data', bundle);
            return false;
        }
        return true;
    });

    if (cartProducts.length === 0 && validatedBundles.length === 0) {
        toastr.error('Cart is empty!');
        btnPay.disabled = false;
        btnPay.innerHTML = '<i class="fa fa-money"></i> Confirm Payment';
        return;
    }

    fetch(config.routes.createSale, {
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json, text-plain, */*",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": token
        },
        method: 'post',
        credentials: "same-origin",
        body: JSON.stringify({
            cart: cartProducts,
            bundles: validatedBundles,
            comment: $('#comment').val(),
            customer_id: $('#customer_id').val(),
            sub_total: $('#total').val(),
            discount_id: sessionStorage.getItem('discount'),
            employee_id: sessionStorage.getItem('employee_id'),
            margin: $('#margin').val(),
            pay_amount: parseInt($('#tendered').html()),
            order_id: sessionStorage.getItem('edit_order') || 0,
            store_id: 1
        })
    })
        .then(async response => {
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to create order');
            }
            return response.json();
        })
        .then(data => {
            if (!data.success || !data.data.order.id) {
                throw new Error(data.message || 'Order creation failed');
            }

            sessionStorage.clear();
            window.open('/print/order/' + data.data.order.id, '_self');
        })
        .catch(error => {
            toastr.error(error.message || 'Failed to process order');
            btnPay.disabled = false;
            btnPay.innerHTML = '<i class="fa fa-money"></i> Confirm Payment';
        });
}

function createReturn() {
    const cartProducts = JSON.parse(sessionStorage.getItem('cartProducts')) || [];
    const cartBundles = JSON.parse(sessionStorage.getItem('cartBundles')) || [];
    
    if (cartProducts.length === 0 && cartBundles.length === 0) {
        toastr.error('Cart is empty!');
        return;
    }

    fetch(config.routes.createReturn, {
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json, text-plain, */*",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": token
        },
        method: 'post',
        credentials: "same-origin",
        body: JSON.stringify({
            cart: cartProducts,
            bundles: cartBundles,
            comment: $('#comment').val(),
            customer_id: $('#customer_id').val(),
            sub_total: $('#total').val(),
            discount_id: sessionStorage.getItem('discount'),
            employee_id: sessionStorage.getItem('employee_id'),
            margin: $('#margin').val(),
            return_type: $('#return_type').val(),
            adjust_type: $('#adjust_type').val(),
            mannual_return: $('#mannual_return').val(),
            manual_return_only: sessionStorage.getItem('manual_return_only') || '0',
            store_id: 1
        })
    })
        .then(response => response.json())
        .then(data => {

            if (!data) {
                throw new Error('Failed to create return order');
            }

            sessionStorage.clear();
            window.open('/print/order/' + data, '_self');
        })
        .catch(error => {
            toastr.error(error.message || 'Failed to process return');
        });
}

function orderInfo() {
    document.getElementById('app').style.opacity = '0.1';
    const order_id = this.getAttribute('data-order-id');

    fetch(config.routes.orderInfo, {
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json, text-plain, */*",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": token
        },
        method: 'post',
        credentials: "same-origin",
        body: JSON.stringify({
            order_id
        })
    })
        .then(response => response.text())
        .then(html => {
            document.getElementById('pos-order-view').innerHTML = html;
            document.getElementById('app').style.opacity = '1';
            $('.record').removeClass('active');

            $(`.record[data-order-id="${order_id}"]`).addClass('active');
        })
        .catch(error => {
            toastr.error(error.message || 'Failed to fetch order info');
            document.getElementById('app').style.opacity = '1';
        });
}

function editOrder(orderId) {
    document.getElementById('app').style.opacity = '0.1';

    fetch(config.routes.orderDetail, {
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json, text-plain, */*",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": token
        },
        method: 'post',
        credentials: "same-origin",
        body: JSON.stringify({
            order_no: orderId
        })
    })
        .then(response => response.json())
        .then(data => {
            if (!data.result.data.order) {
                throw new Error('Invalid order data');
            }

            const order = data.result.data.order;
            sessionStorage.clear();

            const cartProducts = order.products.map(product => ({
                id: product.product_id,
                variant_id: product.variant_id || 0,
                qty: product.qty,
                price: product.price
            }));

            sessionStorage.setItem('cartProducts', JSON.stringify(cartProducts));
            sessionStorage.setItem('discount', order.discount_amount);
            sessionStorage.setItem('employee_id', order.employee_id);

            if (order.customer_id != 1) {
                sessionStorage.setItem('customer_id', order.customer_id);
                sessionStorage.setItem('customer_name', order.name);
            }

            sessionStorage.setItem('edit_order', order.id);
            window.location.href = '/';
        })
        .catch(error => {
            toastr.error(error.message || 'Failed to load order details');
            document.getElementById('app').style.opacity = '1';
        });
}

function productDetail(productId) {
    document.getElementById('app').style.opacity = '0.1';

    fetch(config.routes.productDetail, {
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json, text-plain, */*",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": token
        },
        method: 'post',
        credentials: "same-origin",
        body: JSON.stringify({
            product_id: productId
        })
    })
        .then(response => response.text())
        .then(html => {
            document.getElementById('product-modal-body').innerHTML = html;
            document.getElementById('product-modal').style.display = 'block';
            document.getElementById('app').style.opacity = '1';
            $('#table1, #table2').DataTable({
                ordering: false,
                paging: false,
                info: false,
                searching: false
            });
        })
        .catch(error => {
            toastr.error(error.message || 'Failed to fetch product details');
            document.getElementById('app').style.opacity = '1';
        });
}

function productVariantDetail(variantId) {
    document.getElementById('app').style.opacity = '0.1';

    fetch(config.routes.productVariantDetail, {
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json, text-plain, */*",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": token
        },
        method: 'post',
        credentials: "same-origin",
        body: JSON.stringify({
            variant_id: variantId
        })
    })
        .then(response => response.text())
        .then(html => {
            document.getElementById('product-modal-body').innerHTML = html;
            document.getElementById('product-modal').style.display = 'block';
            document.getElementById('app').style.opacity = '1';
            $('#table1, #table2').DataTable({
                ordering: false,
                paging: false,
                info: false,
                searching: false
            });
        })
        .catch(error => {
            toastr.error(error.message || 'Failed to fetch variant details');
            document.getElementById('app').style.opacity = '1';
        });
}

$('.selectDropdown').on('change', function () {
    const vId = $("select option:selected").data('valuea');
    const pId = $("select option:selected").val();
    addToCart(pId, vId);
});

$(document).on('change', '#tDiscount', function () {
    const discount = parseFloat($('#tDiscount').val()) || 0;
    sessionStorage.setItem('discount', discount);

    const total = parseFloat($('#total').val()) || 0;
    const amount = total - discount;

    $('#tAmountF').html('RS. ' + amount.toFixed(2));
    $('#aftotal').val(amount.toFixed(2));

    let tendered = parseInt($('#tendered').html()) || 0;
    const change = tendered - parseInt(amount.toFixed(2));

    if (change >= 0) {
        $('#change').html(change);
        $('#btn-pay').prop('disabled', false);
    } else {
        $('#change').html(0);
        $('#btn-pay').prop('disabled', true);
    }
});

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.classList.remove('pos-modal-open');
    } else {
        console.warn(`Modal with ID ${modalId} not found`);
    }
}

function bindCustomerEvents() {
    document.querySelectorAll('.customer-item').forEach(item => {
        item.addEventListener('click', () => {
            const id = item.getAttribute('data-customer-id');
            const name = item.getAttribute('data-customer-name');
            if (id && name) {
                selectCustomer(id, name);
            }
        });
    });
}

function searchOrder() {
    let url = config.routes.searchOrder;

    document.getElementById('app').style.opacity = '0.1';

    fetch(url, {
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json, text-plain, */*",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": token
        },
        method: 'post',
        credentials: "same-origin",
        body: JSON.stringify({
            order_no: $('#order_no').val(),

        })
    })
        .then(function (response) {
            return response.text();
        })
        .then(function (html) {
            document.getElementById('pos-order-totals').innerHTML = html;
            document.getElementById('app').style.opacity = '1';

        })
        .catch(function (error) {
            toastr.error(error);
            document.getElementById('wait').style.display = 'none';
            document.getElementById('app').style.opacity = '1';
        });
}

function completeReturnOrder(order_id) {

    let url = config.routes.completeReturnOrder;
    document.getElementById('app').style.opacity = '0.1';

    type = $('#return_type').val();

    fetch(url, {
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json, text-plain, */*",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": token
        },
        method: 'post',
        credentials: "same-origin",
        body: JSON.stringify({
            order_id: order_id,
            type: type

        })
    })
        .then(function (response) {
            return response.text();
        })
        .then(function (html) {

            window.location.href = '/return-orders';
            document.getElementById('app').style.opacity = '1';

        })
        .catch(function (error) {
            toastr.error(error);
            document.getElementById('wait').style.display = 'none';
            document.getElementById('app').style.opacity = '1';
        });
}

function partiallyReturnOrder(order_id, count) {

    let url = config.routes.partiallyReturnOrder;

    document.getElementById('app').style.opacity = '0.1';
    productIds = [];
    returnQty = [];
    type = $('#return_type').val();

    $('input[name="product_ids"]:checked').each(function () {

        productIds.push(this.value);
        rQty = '#product_qty' + this.value;

        returnQty.push($(rQty).val());
    });

    if (productIds.length == 0) {
        document.getElementById('app').style.opacity = '1';
        toastr.error('Please select any product.');
        return false;
    }

    // if(productIds.length == count) {
    //     completeReturnOrder(order_id);
    //     return false;
    // }

    fetch(url, {
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json, text-plain, */*",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": token
        },
        method: 'post',
        credentials: "same-origin",
        body: JSON.stringify({
            order_id: order_id,
            product_ids: productIds,
            return_qty: returnQty,
            type: type

        })
    })
        .then(function (response) {
            return response.text();
        })
        .then(function (html) {
            window.location.href = '/return-orders';
            document.getElementById('app').style.opacity = '1';

        })
        .catch(function (error) {
            toastr.error(error);
            document.getElementById('wait').style.display = 'none';
            document.getElementById('app').style.opacity = '1';
        });
}

function toggleManualReturnOnly(checkbox) {
    const isChecked = checkbox.checked ? '1' : '0';
    sessionStorage.setItem('manual_return_only', isChecked);
    updateButtonStates(checkbox.checked);
    updateCartDisplay();
}

function updateButtonStates(isManualReturn) {
    const holdBtn = document.querySelector('button[onclick="holdCartModal()"]');
    const payBtn = document.querySelector('button[onclick="updatePayment()"]');
    const manualBtn = document.querySelector('button[onclick="mannualReturnForm()"]');

    if (isManualReturn) {
        if (holdBtn) holdBtn.disabled = true;
        if (payBtn) payBtn.disabled = true;
        if (manualBtn) manualBtn.disabled = false;
    } else {
        if (holdBtn) holdBtn.disabled = false;
        if (payBtn) payBtn.disabled = false;
    }
}
