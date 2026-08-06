{{-- resources/views/partials/cart-table.blade.php --}}

@if(count($cart) > 0)
    @foreach($cart as $key => $item)
        <tr data-cart-key="{{ $item['key'] }}">
            <td class="text-center">
                <a href="javascript:;" class="text-gray-32 font-size-26 remove-item">×</a>
            </td>
            <td class="d-none d-md-table-cell">
                <a href="{{ route('productdetail', $item['slug'] ?? $item['product_id']) }}">
                    <img class="img-fluid max-width-100 p-1 border border-color-1"
                         src="{{ $item['image'] }}"
                         alt="{{ $item['title'] }}"
                         onerror="this.onerror=null;this.src='{{ env('BACKEND_IMAGE_URL') . 'default.jpeg' }}';">
                </a>
            </td>
            <td data-title="Product">
                <a href="{{ route('productdetail', $item['slug'] ?? $item['product_id']) }}" class="text-gray-90">
                    {{ $item['title'] }}
                    @if(!empty($item['size']) || !empty($item['shade']))
                        <small class="d-block text-gray-6">
                            {{ trim($item['size'] . ' ' . $item['shade']) }}
                        </small>
                    @endif
                </a>
            </td>
            <td data-title="Price">
                <span class="price">Rs. {{ number_format($item['price'], 2) }}</span>
            </td>
            <td data-title="Quantity">
                <div class="border rounded-pill py-1 width-122 w-xl-80 px-3 border-color-1">
                    <div class="js-quantity row align-items-center">
                        <div class="col">
                            <input type="text"
                                   class="js-result form-control h-auto border-0 rounded p-0 shadow-none"
                                   value="{{ $item['quantity'] }}"
                                   data-max-stock="{{ $item['stock'] ?? 999 }}">
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
            </td>
            <td data-title="Total">
                <span class="item-total">Rs. {{ number_format($item['price'] * $item['quantity'], 2) }}</span>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="6" class="text-center py-8">
            <i class="ec ec-shopping-bag font-size-60 text-gray-60 mb-3 d-block"></i>
            <p class="font-size-16 text-gray-90">Your cart is empty.</p>
            <a href="{{ route('home') }}" class="btn btn-primary btn-pill px-6">Continue Shopping</a>
        </td>
    </tr>
@endif

<!-- Totals Row -->
<tr class="border-top">
    <td colspan="6" class="pt-4">
        <div class="cart-total-summary">
            <table class="table mb-4">
                <tbody>
                    <tr class="cart-subtotal">
                        <th>Subtotal</th>
                        <td>Rs. <span id="subtotal">{{ number_format($subtotal, 2) }}</span></td>
                    </tr>
                    <tr class="shipping">
                        <th>Shipping</th>
                        <td>Rs. <span id="shipping">{{ number_format($shipping, 2) }}</span></td>
                    </tr>
                    <tr class="order-total">
                        <th>Total</th>
                        <td><strong>Rs. <span id="total">{{ number_format($total, 2) }}</span></strong></td>
                    </tr>
                </tbody>
            </table>
            <div class="row pt-4 d-none d-md-flex">
                <div class="col-md-6 offset-md-6 text-right">
                    <a href="{{ route('checkout') }}" class="btn btn-primary-dark-w btn-lg px-5">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
        </div>
    </td>
</tr>
