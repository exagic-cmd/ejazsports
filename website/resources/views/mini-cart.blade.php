@if(count($cart) > 0)
    <div class="mini-cart-header px-3 pt-3 pb-2 border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="font-size-16 font-weight-bold mb-0">Shopping Cart ({{ count($cart) }})</h5>
            <span class="font-size-14 text-gray-60">
                Rs. {{ number_format(collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']), 2) }}
            </span>
        </div>
    </div>

    <div class="mini-cart-body js-scrollbar" style="max-height: 300px; overflow-y: auto;">
        <ul class="list-unstyled px-3 mb-0">
            @foreach($cart as $cartKey => $item)
                <li class="border-bottom py-2">
                    <div class="d-flex align-items-center">
                        <div class="mr-2 flex-shrink-0">
                            <img class="img-fluid rounded" 
                                 src="{{ $item['image'] ?? env('BACKEND_IMAGE_URL') . 'default.jpeg' }}" 
                                 alt="{{ $item['title'] }}" 
                                 style="width: 50px; height: 50px; object-fit: cover;"
                                 onerror="this.onerror=null;this.src='{{ env('BACKEND_IMAGE_URL') . 'default.jpeg' }}';">
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <div class="text-blue font-size-13 mb-0 text-truncate">
                                <a href="{{ route('productdetail', $item['slug'] ?? $item['product_id'] ?? $cartKey) }}">
                                    {{ Str::limit($item['title'], 25) }}
                                </a>
                            </div>
                            <div class="d-flex align-items-baseline">
                                <span class="font-size-12 text-gray-90 mr-2">{{ $item['quantity'] }} ×</span>
                                <span class="font-size-12 font-weight-bold">Rs. {{ number_format($item['price'], 2) }}</span>
                            </div>
                        </div>
                        <div class="ml-2 flex-shrink-0">
                            <a href="javascript:;" class="text-gray-40 hover-text-danger transition-3d-hover remove-from-cart" 
                               data-key="{{ $cartKey }}" 
                               data-toggle="tooltip" 
                               title="Remove">
                                <i class="ec ec-close-remove font-size-14"></i>
                            </a>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="mini-cart-footer px-3 py-3 border-top bg-white">
        <div class="d-flex justify-content-between mb-3">
            <span class="font-weight-bold">Subtotal:</span>
            <span class="font-weight-bold font-size-16">Rs. {{ number_format(collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']), 2) }}</span>
        </div>
        <div class="d-flex">
            <a href="{{ route('cart') }}" class="btn btn-soft-secondary btn-sm flex-fill mr-2 transition-3d-hover">View Cart</a>
            <a href="{{ route('checkout') }}" class="btn btn-primary btn-sm flex-fill ml-2 transition-3d-hover">Checkout</a>
        </div>
    </div>
@else
    <div class="text-center py-6 px-4">
        <div class="mb-3">
            <i class="ec ec-shopping-bag font-size-50 text-gray-30"></i>
        </div>
        <p class="font-size-14 text-gray-90 mb-4">No products in the cart.</p>
        <a href="{{ route('home') }}" class="btn btn-primary btn-sm btn-pill px-4 transition-3d-hover">Start Shopping</a>
    </div>
@endif
