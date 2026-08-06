<div class="pos-content">
    <div class="cart">
        <div class="cart-header">
            <div class="cart-hold-section">
                <span>Cart Details</span>
                <div class="btn btn-sm btn-pos-default btn-hold">
                    <a href="{{ route('sale.hold') }}"><i class="fa fa-pause"></i> <span class="hold_cart_count" id="hold-count">0</span></a>
                </div>
            </div>
            <div class="cart-count-section">
                <div class="btn btn-sm btn-pos-primary cart-btn" onclick="clearCart()">Clear Cart</div>
            </div>
        </div>
        <div class="pos-nav-content">
            <div id="cart_count_0" class="pos-nav-pane active">
                <ul class="cart_details" style="padding-bottom: 120px;">
                    @php
                        $cartProducts = (array) $result->data->cartProducts;
                        $cartBundles = (array) $result->data->cartBundles ?? [];
                        $carts = $cartProducts;
                        $products = (array) $result->data->products;
                        $variants = (array) $result->data->variants;
                        $bundles = (array) $result->data->bundles ?? [];
                        $price = (array) $result->data->price;
                        $vPrice = (array) $result->data->vPrice;
                        $bundlePrice = (array) $result->data->bundlePrice ?? [];
                    @endphp

                    @if (empty($cartProducts) && empty($cartBundles))
                        <div class="message-alert"><span class="text-danger">Current cart is empty!</span></div>
                    @else
                        <div>
                            {{-- Products --}}
                            @foreach ($carts as $cart)
                                @php
                                    $uniqueId = 'cart-item-' . $cart->id . ($cart->variant_id ?? '0');
                                    $product = $products[$cart->id] ?? null;
                                    $variant = $variants[$cart->variant_id] ?? null;
                                    $unitPrice = $cart->variant_id
                                        ? $vPrice[$cart->variant_id] ?? 0
                                        : $price[$cart->id] ?? 0;
                                @endphp
                                @if ($product)
                                    <li id="{{ $uniqueId }}">
                                        <div class="cart-product-content">
                                            <div class="product-name">
                                                {{ $product->title }}
                                                @if ($cart->variant_id)
                                                    <br>
                                                    <small>{{ $variant->size }} - {{ $variant->shade }}</small>
                                                @endif
                                            </div>
                                            <div class="product-qty">
                                                <span>
                                                    <input class="tPrice" style="width:15%" type="number" min="1"
                                                           data-pId="{{ $cart->id }}"
                                                           data-vId="{{ $cart->variant_id ?? 0 }}"
                                                           id="qty{{ $cart->id }}{{ $cart->variant_id ?? 0 }}"
                                                           value="{{ $cart->qty }}"
                                                           onfocusout="addToCartMul({{ $cart->id }}, {{ $cart->variant_id ?? 0 }})">
                                                    (Unit's)

                                                    @if ($cart->variant_id)
                                                        - <span style="color: #db324d;">
                                                            <input class="tPrice" style="width:25%" type="number" min="0"
                                                                   id="price{{ $cart->id }}{{ $cart->variant_id }}"
                                                                   data-pId="{{ $cart->id }}"
                                                                   data-vId="{{ $cart->variant_id }}"
                                                                   value="{{ $vPrice[$cart->variant_id] }}"
                                                                   onfocusout="addToCartMul({{ $cart->id }}, {{ $cart->variant_id }})">
                                                        </span> per unit
                                                    @else
                                                        - <span style="color: #db324d;">
                                                            <input class="tPrice" style="width:25%" type="number" min="0"
                                                                   id="price{{ $cart->id }}{{ $cart->variant_id ?? 0 }}"
                                                                   data-pId="{{ $cart->id }}"
                                                                   data-vId="{{ $cart->variant_id ?? 0 }}"
                                                                   value="{{ $price[$cart->id] }}"
                                                                   onfocusout="addToCartMul({{ $cart->id }}, {{ $cart->variant_id ?? 0 }})">
                                                        </span> per unit
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        <div class="cart-product-price">
                                            <span><i class="fa fa-times-circle" onclick="removeFromCart({{ $cart->id }}, {{ $cart->variant_id ?? 0 }})"></i></span>
                                            <span>
                                                @if ($cart->variant_id)
                                                    Rs. <input style="width:55px" type="number" min="0"
                                                               id="tPrice{{ $cart->id }}{{ $cart->variant_id }}"
                                                               value="{{ round($vPrice[$cart->variant_id] * $cart->qty) }}"
                                                               onfocusout="addToCartMulT({{ $cart->id }}, {{ $cart->variant_id }})">
                                                @else
                                                    Rs. <input style="width:55px" type="number" min="0"
                                                               id="tPrice{{ $cart->id }}{{ $cart->variant_id ?? 0 }}"
                                                               value="{{ round($price[$cart->id] * $cart->qty) }}"
                                                               onfocusout="addToCartMulT({{ $cart->id }}, {{ $cart->variant_id ?? 0 }})">
                                                @endif
                                            </span>
                                        </div>
                                    </li>
                                @endif
                            @endforeach

                            {{-- Bundles --}}
                            @foreach ($cartBundles as $bundle)
                                @php
                                    $bundleId = $bundle->bundle_id;
                                    $bundleData = $bundles[$bundleId] ?? null;
                                    $unitPrice = $bundlePrice[$bundleId] ?? ($bundleData->additional_price ?? $bundleData->price ?? 0);
                                    $uniqueId = 'bundle-item-' . $bundleId;
                                @endphp
                                @if ($bundleData)
                                    <li id="{{ $uniqueId }}" data-bundle-id="{{ $bundleId }}">
                                        <div class="cart-product-content">
                                            <div class="product-name">
                                                {{ $bundleData->name }}
                                                @if (!empty($bundleData->short_desc))
                                                    <br>
                                                    <small>{{ $bundleData->short_desc }}</small>
                                                @endif
                                            </div>
                                            <div class="product-qty">
                                                <span>
                                                    <input class="bundle-input" style="width:15%" type="number" min="1"
                                                           data-bId="{{ $bundleId }}" id="bqty{{ $bundleId }}"
                                                           value="{{ $bundle->qty }}"
                                                           onfocusout="updateBundleQty({{ $bundleId }})">
                                                    (Unit's)

                                                    - <span style="color: #db324d;">
                                                        <input class="bundle-input" style="width:25%" type="number" min="0"
                                                               id="bprice{{ $bundleId }}"
                                                               data-bId="{{ $bundleId }}"
                                                               value="{{ $unitPrice }}"
                                                               onfocusout="updateBundlePrice({{ $bundleId }})">
                                                    </span> per unit
                                                </span>
                                            </div>
                                        </div>
                                        <div class="cart-product-price">
                                            <span><i class="fa fa-times-circle" onclick="removeFromCart({{ $bundleId }}, 0, true)"></i></span>
                                            <span>
                                                Rs. <input style="width:55px" type="number" min="0" class="bundle-input"
                                                           id="btPrice{{ $bundleId }}"
                                                           value="{{ round($unitPrice * $bundle->qty) }}"
                                                           onfocusout="updateBundleTotal({{ $bundleId }})">
                                            </span>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="cart-total-container">
        <div class="cart-total">
            <div class="pos-table-responsive cart-totals">
                <table class="pos-table">
                    <tbody>
                        <tr>
                            <td class="text-left">Select Employee</td>
                            <td class="text-right">
                                <select id="employee_id" class="form-control">
                                    @foreach ($result->data->employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-left">Grand Total</td>
                            <td class="text-right">Rs. {{ number_format($result->data->subTotal - $result->data->discount) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <style>
        .manual-return-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 15px;
            margin: 10px 15px;
            background-color: #fcf8e3;
            border: 1px solid #faf2cc;
            border-radius: 4px;
        }
        .manual-return-label {
            font-weight: bold;
            color: #8a6d3b;
            font-size: 13px;
            margin-bottom: 0;
        }
        .manual-return-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 22px;
            margin-bottom: 0;
        }
        .manual-return-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .manual-return-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .3s;
            border-radius: 34px;
        }
        .manual-return-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
        }
        #manual_return_checkbox:checked + .manual-return-slider {
            background-color: #d9534f;
        }
        #manual_return_checkbox:checked + .manual-return-slider:before {
            transform: translateX(20px);
        }
        </style>

        <div class="manual-return-container">
            <span class="manual-return-label"><i class="fa fa-warning"></i> Manual Return Mode Only</span>
            <label class="manual-return-switch">
                <input type="checkbox" id="manual_return_checkbox" onchange="toggleManualReturnOnly(this)">
                <span class="manual-return-slider"></span>
            </label>
        </div>

        <div class="cart-button-container pos-action">
            <button type="button" class="btn btn-lg btn-pos-primary customer-btn">
                <a href="{{ route('customer.data') }}"><i class="fa fa-user-circle"></i>
                    <span><b id="customer_name" style="color: white;">Customer</b></span>
                    <i class="fa fa-pencil"></i>
                </a>
            </button>
            <button id="btn-pay" type="button" class="btn btn-lg btn-pos-dark pay-btn" onclick="holdCartModal()">
                <b>Hold</b>
            </button>
            <button type="button" class="btn btn-lg btn-pos-primary hold-btn" onclick="updatePayment()">
                <b>Pay</b>
            </button>
            <button type="button" class="mt-10 btn btn-lg btn-black hold-btn" onclick="mannualReturnForm()">
                <b>Manual Return</b>
            </button>
        </div>
    </div>
</div>
