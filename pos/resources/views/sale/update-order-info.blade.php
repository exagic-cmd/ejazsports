@if($result->data->order)

<div class="pos-order-info">
    <div class="order-info">
        <div class="order_row"><label class="row_title">Order ID</label>
            <div class="row_value order_id"> #{{$result->data->order->order_no}}
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </div>
        </div>
        <div class="order_row"><label class="row_title">Order Date</label>
            <div class="row_value"><i class="fa fa-calendar"></i>
                {{date('M d, Y h:i A',strtotime($result->data->order->created_at))}}
            </div>
        </div>
        <div class="order_row"><label class="row_title">Customer Detail</label>
            <div class="row_value"><span class="cust_name">{{$result->data->order->name}}</span>
                <span class="cust_email"><i class="fa fa-phone"></i>
                    {{$result->data->order->phone_number}}
                </span>
            </div>
        </div>

        <div class="order_row"><label class="row_title">Employee Information</label>
            <div class="row_value"><span class="cust_name">{{$result->data->order->employee->name}}</span>
            </div>
        </div>

        <div class="pos-action text-left" style="padding-right: 0px;">
            <a target="_blank" style="color:white" class="btn btn-lg btn-pos-primary" id="print-btn"
                href="{{route('pos.order.print',['id'=>$result->data->order->id])}}"><i class="fa fa-print"></i> Print
                Invoice
            </a>

            <a target="_blank" style="color:white" class="btn btn-lg btn-pos-primary" id="print-btn"
                onclick="editOrder({{$result->data->order->id}})"><i class="fa fa-pencil"></i> Edit Invoice
            </a>
        </div>
    </div>
</div>

<div class="pos-order-totals">
    <div class="order-info">
        <div class="order_row"><label class="row_title">Order Summary</label>
            <div class="row_value">

                @foreach($result->data->order->products as $p)
                    @php
                        // Always skip the bundle parent to show the actual products
                        if (isset($p->is_bundle) && $p->is_bundle == 1) {
                            continue;
                        }
                    @endphp

                    @if($p->returned)
                    <div style="background-color: #ffbebe;">
                    @else
                    <div>
                    @endif
                        <div class="product_info">
                            <span class="product_name">
                                {{ $p->product->title ?? 'Product' }}
                                @if(isset($p->variant) && $p->variant)
                                    <br>{{ $p->variant->shade ?? '' }} - {{ $p->variant->size ?? '' }}
                                @endif
                                <span class="product-attributes"></span>
                            </span>
                            <span class="product_unit">
                                @if($p->returned)
                                    {{$p->qty - $p->return_qty}} Sold
                                    {{$p->return_qty}} Return Unit(s)
                                @else
                                    {{$p->qty}} Unit(s)
                                @endif
                            </span>
                        </div>
                        <div class="price_info">
                            <span class="product_price">
                                Rs. {{number_format($p->price * $p->qty)}}
                            </span>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
        <div>
            <div class="order_row">
                <div class="total_row_value">
                    <div class="total_text">Sub Total</div>
                    <div class="total_value">
                        Rs.
                        {{number_format($result->data->order->total_amount + $result->data->order->discount_amount)}}
                    </div>
                </div>
            </div>
            <div class="order_row">
                <div class="total_row_value">
                    <div class="total_text">
                        Discount (-)
                    </div>
                    <div class="total_value">
                        Rs. {{$result->data->order->discount_amount}}
                    </div>
                </div>
            </div>
            @if($result->data->order->return_amount)
            <div class="order_row">
                <div class="total_row_value">
                    <div class="total_text">
                        Return Amount (-)
                    </div>
                    <div class="total_value">
                        - Rs. {{$result->data->order->return_amount}}
                    </div>
                </div>
            </div>
            @endif
            <div class="order_row" style="border-bottom: none; margin: 0px; padding-bottom: 0px;">
                <div class="total_row_value">
                    <div class="total_text"><b>Total</b></div>
                    <div class="total_value order_id">
                        Rs.
                        {{number_format($result->data->order->total_amount - ($result->data->order->return_amount ? $result->data->order->return_amount : 0))}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endif
