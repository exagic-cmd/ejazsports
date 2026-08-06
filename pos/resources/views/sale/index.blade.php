@extends('layouts.app')

@section('content')
<div class="pos-content-container" id="pos-content-container">
    <div class="pos-sales-main">
        <div class="pos-nav-container">
            <ul class="pos-nav-lists">
                <li label="menu_count_0" class="pos-nav">
                    <a href="#!" aria-current="page" class="nav-link router-link-exact-active router-link-active">
                        Sale History
                    </a>
                </li>
                <li label="menu_count_1" class="pos-nav">
                    <a href="#!" onclick="holdList()" class="nav-link">
                        <span class="hold_cart_count" id="hold-count">0</span> Hold Sale
                    </a>
                </li>
            </ul>
        </div>
        <div class="pos-nav-content" style="height: 700px;">
            <div class="sale-history-panel">
                <div class="order-search-main" style="height: 700px;">
                    <div class="pos-order-list">
                        <div class="order_search">
                            <i class="fa fa-search"></i>
                            <input type="text" placeholder="Search Order By Order No." id="order_search_field" class="control_disabled order_search_field">
                        </div>
                        <ul class="order_list" style="height: 650px;" id="order-list">
                            @foreach ($result->data->orders as $o)
                                <li class="record {{ $loop->first ? 'active' : '' }}" data-order-id="{{ $o->id }}">
                                    <div class="order_id">
                                        #{{ $o->order_no }}
                                    </div>
                                    <div class="order_date">
                                        {{ date('M d, Y', strtotime($o->created_at)) }}
                                        <br>
                                        {{ date('h:i A', strtotime($o->created_at)) }}
                                    </div>
                                    <div class="order_total">
                                        Rs. {{ number_format($o->total_amount) }}
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="pos-order-view" id="pos-order-view">
                    @if ($result->data->latestOrder)
                        <div class="pos-order-info">
                            <div class="order-info">
                                <div class="order_row">
                                    <label class="row_title">Order ID</label>
                                    <div class="row_value order_id"> #{{ $result->data->latestOrder->order_no }} </div>
                                </div>
                                <div class="order_row">
                                    <label class="row_title">Order Date</label>
                                    <div class="row_value">
                                        <i class="fa fa-calendar"></i>
                                        {{ date('M d, Y h:i A', strtotime($result->data->latestOrder->created_at)) }}
                                    </div>
                                </div>
                                <div class="order_row">
                                    <label class="row_title">Customer Detail</label>
                                    <div class="row_value">
                                        <span class="cust_name">{{ $result->data->latestOrder->name }}</span>
                                        <span class="cust_email">
                                            <i class="fa fa-phone"></i>
                                            {{ $result->data->latestOrder->phone_number }}
                                        </span>
                                    </div>
                                </div>
                                <div class="pos-action text-left" style="padding-right: 0px;">
                                    <a target="_blank" style="color:white" class="btn btn-lg btn-pos-primary" id="print-btn"
                                       href="{{ route('pos.order.print', ['id' => $result->data->latestOrder->id]) }}">
                                        <i class="fa fa-print"></i> Print Invoice
                                    </a>
                                    <a target="_blank" style="color:white" class="btn btn-lg btn-pos-primary" id="edit-btn"
                                       onclick="editOrder({{ $result->data->latestOrder->id }})">
                                        <i class="fa fa-pencil"></i> Edit Invoice
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="pos-order-totals" id="pos-order-totals">
                            <div class="order-info">
                                <div class="order_row">
                                    <label class="row_title">Order Summary</label>
                                    <div class="row_value">
                                        @php
                                            $totalsaleprice = 0;
                                            $total_return_qty = 0;
                                            $return_total = 0;
                                        @endphp
                                        {{-- SALE ITEMS --}}
                                        @foreach ($result->data->latestOrder->products as $p)
                                            @php
                                                // Skip the main bundle row (it's covered by child items displaying)
                                                if (isset($p->is_bundle) && $p->is_bundle == 1) continue;

                                                $totalsaleprice += $p->price * ($p->qty - $p->return_qty);
                                                $total_return_qty += $p->return_qty;
                                                $return_total += $p->price * $p->return_qty;

                                                // Check if this is a bundle or regular product
                                                $isBundle = $p->bundle_id && $p->bundle_id != 0 && !$p->is_bundle_item;
                                            @endphp
                                            @if ($p->qty - $p->return_qty > 0)
                                                <div>
                                                    <div class="product_info">
                                                        <span class="product_name">
                                                            @if ($isBundle)
                                                                {{-- Display Bundle Name --}}

                                                                {{ $p->bundle->name ?? DB::table('bundles')->where('id', $p->bundle_id)->value('name') ?? 'Bundle' }}
                                                            @else
                                                                {{-- Display Regular Product --}}
                                                                {{ $p->product?->title ?? 'Product' }}
                                                                @if ($p->variant)
                                                                    - {{ $p->variant->shade }}
                                                                    - {{ $p->variant->size }}
                                                                @endif
                                                            @endif
                                                        </span>
                                                        <span class="product_unit">
                                                            {{ $p->qty - $p->return_qty }} Unit(s)
                                                        </span>
                                                    </div>
                                                    <div class="price_info">
                                                        <span class="product_price">
                                                            Rs. {{ number_format($p->price * ($p->qty - $p->return_qty)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                        {{-- SUB TOTAL --}}
                                        <div class="order_row">
                                            <div class="total_row_value">
                                                <div class="total_text">Sub Total</div>
                                                <div class="total_value">
                                                    Rs. {{ number_format($totalsaleprice) }}
                                                </div>
                                            </div>
                                        </div>
                                        {{-- DISCOUNT --}}
                                        <div class="order_row">
                                            <div class="total_row_value">
                                                <div class="total_text">Discount (-)</div>
                                                <div class="total_value">
                                                    Rs. {{ number_format($result->data->latestOrder->discount_amount) }}
                                                </div>
                                            </div>
                                        </div>
                                        {{-- RETURN ITEMS --}}
                                        @foreach ($result->data->latestOrder->products as $p)
                                            @if ($p->return_qty > 0)
                                                @php
                                                    // Skip the bundle parent in returned items too
                                                    if (isset($p->is_bundle) && $p->is_bundle == 1) continue;
                                                    
                                                    $isBundle = $p->bundle_id && $p->bundle_id != 0 && !$p->is_bundle_item;
                                                @endphp
                                                <div>
                                                    <div class="product_info">
                                                        <span class="product_name text-danger">
                                                            [RETURN]
                                                            @if ($isBundle)
                                                                <span class="badge badge-info">Bundle</span>
                                                                {{ $p->bundle->name ?? 'Bundle' }}
                                                            @else
                                                                {{ $p->product?->title ?? 'Product' }}
                                                                @if ($p->variant)
                                                                    - {{ $p->variant->shade }}
                                                                    - {{ $p->variant->size }}
                                                                @endif
                                                            @endif
                                                        </span>
                                                        <span class="product_unit text-danger">
                                                            {{ $p->return_qty }} Unit(s)
                                                        </span>
                                                    </div>
                                                    <div class="price_info">
                                                        <span class="product_price text-danger">
                                                            - Rs. {{ number_format($p->price * $p->return_qty) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                        {{-- RETURN TOTAL --}}
                                        @if ($return_total > 0)
                                            <div class="order_row">
                                                <div class="total_row_value">
                                                    <div class="total_text">Return Amount</div>
                                                    <div class="total_value">
                                                        - Rs. {{ number_format($return_total) }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        {{-- FINAL TOTAL --}}
                                        <div class="order_row" style="border-bottom: none; margin: 0; padding-bottom: 0;">
                                            <div class="total_row_value">
                                                <div class="total_text"><b>Total</b></div>
                                                <div class="total_value order_id">
                                                    Rs. {{ number_format($totalsaleprice - $result->data->latestOrder->discount_amount) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Initialize hold cart count
    if (sessionStorage.getItem('holdCarts') !== null) {
        const holdCarts = JSON.parse(sessionStorage.getItem('holdCarts'));
        document.getElementById('hold-count').innerHTML = holdCarts.length;
    }

    // Hide search inputs if they exist
    const search1 = document.getElementById('search1');
    const search2 = document.getElementById('search2');
    if (search1) search1.style.display = 'none';
    if (search2) search2.style.display = 'none';

    // Bind click events to order list items on page load
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.record').forEach(element => {
            // Remove existing listeners to prevent duplicates
            element.removeEventListener('click', orderInfo);
            // Add new click event listener
            element.addEventListener('click', orderInfo);
        });
    });
</script>
@endsection
