@extends('layouts.app')

@section('content')

<!-- Breadcrumb -->
<div class="bg-gray-13 bg-md-transparent">
    <div class="container">
        <div class="my-md-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visible">
                    <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1">
                        <a href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">
                        Cart
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<!-- End Breadcrumb -->

<div class="container">
    <!-- Page Title -->
    <div class="mb-6">
        <h1 class="text-center font-size-30">Your Cart</h1>
    </div>

    <!-- Cart Table -->
    <div class="cart-table mb-10">
        <table class="table table-bordered" cellspacing="0">
            <thead class="bg-gray-13 text-white">
                <tr>
                    <th class="product-remove">&nbsp;</th>
                    <th class="product-thumbnail">&nbsp;</th>
                    <th class="product-name">Product</th>
                    <th class="product-price">Price</th>
                    <th class="product-quantity w-lg-15">Quantity</th>
                    <th class="product-subtotal">Total</th>
                </tr>
            </thead>
            <tbody id="cart-items-body">
                @include('cart-table', [
                    'cart' => session('cart', []),
                    'subtotal' => collect(session('cart', []))->sum(fn($i) => $i['price'] * $i['quantity']),
                    'shipping' => 300,
                    'total' => collect(session('cart', []))->sum(fn($i) => $i['price'] * $i['quantity']) + 300
                ])
            </tbody>
        </table>
    </div>

    <!-- Mobile Checkout Button -->
    <div class="text-center d-md-none mb-6">
        <a href="{{ route('checkout') }}" class="btn btn-primary-dark-w btn-lg px-8">
            Proceed to Checkout
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Only run on cart page
    if (!window.location.pathname.includes('/cart')) return;

    function refreshCart() {
        $.get('{{ route("cart.items") }}', function(res) {
            if (res.success) {
                $('#cart-items-body').html(res.html);
                $('.cart-count').text(res.count || 0);
                // bindCartEvents(); // Not needed due to delegation
            }
        }).fail(function() {
            showToast('Failed to load cart. Please refresh.', 'danger');
        });
    }

    // Event Delegation for dynamic content
    // Increase Quantity
    $('#cart-items-body').on('click', '.js-plus', function() {
        let row = $(this).closest('tr');
        let input = row.find('.js-result');
        let current = parseInt(input.val()) || 1;
        let cartKey = row.data('cart-key');
        input.val(current + 1);
        updateQuantity(cartKey, current + 1);
    });

    // Decrease Quantity
    $('#cart-items-body').on('click', '.js-minus', function() {
        let row = $(this).closest('tr');
        let input = row.find('.js-result');
        let current = parseInt(input.val()) || 1;
        let cartKey = row.data('cart-key');
        if (current > 1) {
            input.val(current - 1);
            updateQuantity(cartKey, current - 1);
        }
    });

    // Remove Item (Table button)
    $('#cart-items-body').on('click', '.remove-item', function() {
        let row = $(this).closest('tr');
        let cartKey = row.data('cart-key');
        updateQuantity(cartKey, 0);
    });

    function updateQuantity(cartKey, quantity) {
        if (!cartKey) {
            showToast('Invalid cart item.', 'danger');
            return;
        }

        $.post('{{ route("cart.update") }}', {
            _token: '{{ csrf_token() }}',
            key: cartKey,
            quantity: quantity
        })
        .done(function(res) {
            if (res.success) {
                // refreshCart(); // Using res.html instead of extra fetch
                if (res.html) {
                    $('#cart-items-body').html(res.html);
                }
                $('.cart-count').text(res.count || 0);
                
                if (res.miniHtml) {
                    $('#mini-cart-content').html(res.miniHtml);
                }
                showToast(
                    quantity === 0 ? 'Item removed from cart' : 'Cart updated successfully',
                    'success'
                );
            } else {
                showToast('Update failed.', 'danger');
            }
        })
        .fail(function() {
            showToast('Network error. Try again.', 'danger');
        });
    }

    // Toast Notification
    function showToast(message, type = 'success') {
        const alertClass = type === 'danger' ? 'alert-danger' :
                          type === 'info'    ? 'alert-info'    : 'alert-success';

        const toast = $(`
            <div class="position-fixed toast-notification" style="top: 20px; right: 20px; z-index: 9999;">
                <div class="alert ${alertClass} alert-dismissible fade show shadow-lg rounded">
                    <strong>${message}</strong>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            </div>
        `);

        $('body').append(toast);
        toast.fadeIn(300);

        setTimeout(() => {
            toast.fadeOut(500, () => toast.remove());
        }, 3500);
    }

    // Initial load
    refreshCart();
});
</script>
@endpush

<style>
.toast-notification .alert {
    min-width: 300px;
    padding: 16px 20px;
    font-size: 15px;
    border: none;
    border-radius: 8px;
}
.toast-notification .close {
    opacity: 0.8;
    font-size: 20px;
}
</style>
