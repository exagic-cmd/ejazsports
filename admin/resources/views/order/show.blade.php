@extends('layouts.app')

@section('content')
    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Order detail</h2>
            <p>Details for Order ID: ES{{ $order->order_no }}</p>
        </div>
    </div>
    <div class="card">
        <header class="card-header">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-6 mb-lg-0 mb-15">
                    <span><i class="material-icons md-calendar_today"></i> <b>{{ date('D, M d, Y, h:i', strtotime($order->created_at)) }}</b></span> <br>
                    <small class="text-muted">Order ID: ES-{{ $order->order_no }}</small>
                </div>
                <div class="col-xl-6 text-md-end">
                    <a target="_blank" class="btn btn-secondary print ms-2" href="{{ route('orders.print', ['id' => $order->id]) }}"><i class="icon material-icons md-print"></i>Print</a>
                    <a target="_blank" class="btn btn-secondary print ms-2" href="{{ route('orders.a4', ['id' => $order->id]) }}"><i class="icon material-icons md-print"></i>Print A4</a>
                    <a target="_blank" class="btn btn-secondary print ms-2" href="{{ route('orders.pdf', ['id' => $order->id]) }}"><i class="icon material-icons md-file_copy"></i>Pdf</a>
                    <form class="d-inline" onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('orders.destroy', $order->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button style="width: min-content;" class="btn btn-instagram d-inline" type="submit">Delete</button>
                    </form>
                </div>
            </div>
        </header>
        <!-- card-header end// -->
        <div class="card-body">
            <div class="row mb-50 mt-20 order-info-wrap">
                <div class="col-md-4">
                    <article class="icontext align-items-start">
                        <span class="icon icon-sm rounded-circle bg-primary-light">
                            <i class="text-primary material-icons md-person"></i>
                        </span>
                        <div class="text">
                            <h6 class="mb-1">Customer</h6>
                            <p class="mb-1">
                                {{ $order->name }}<br>
                                {{ $order->email }} <br>
                                {{ $order->phone_number }}
                            </p>
                            <a href="#">View profile</a>
                        </div>
                    </article>
                </div>
                <!-- col// -->
                <div class="col-md-4">
                    <article class="icontext align-items-start">
                        <span class="icon icon-sm rounded-circle bg-primary-light">
                            <i class="text-primary material-icons md-local_shipping"></i>
                        </span>
                        <div class="text">
                            <h6 class="mb-1">Order info</h6>
                            <p class="mb-1">
                                Pay method: <b>
                                    @if($order->payment_method == \App\Models\Order::CASH)
                                        CASH
                                    @elseif($order->payment_method == \App\Models\Order::ONLINE)
                                        ONLINE
                                    @elseif($order->payment_method == \App\Models\Order::EASYPAISA)
                                        EASYPAISA
                                    @elseif($order->payment_method == \App\Models\Order::JAZZCASH)
                                        JAZZCASH
                                    @elseif($order->payment_method == \App\Models\Order::QISTPAY)
                                        QISTPAY
                                    @endif
                                </b>
                                <br>
                                Status: @if($order->status == \App\Models\Order::PENDING)
                                    <span class="badge rounded-pill alert-primary">PENDING</span>
                                @elseif($order->status == \App\Models\Order::BOOKED)
                                    <span class="badge rounded-pill alert-info">BOOKED</span>
                                @elseif($order->status == \App\Models\Order::SCANNED)
                                    <span class="badge rounded-pill alert-success">SCANNED</span>
                                @elseif($order->status == \App\Models\Order::DISPATCHED)
                                    <span class="badge rounded-pill alert-success">DISPATCHED</span>
                                @elseif($order->status == \App\Models\Order::DELIVERED)
                                    <span class="badge rounded-pill alert-success">DELIVERED</span>
                                @elseif($order->status == \App\Models\Order::RETURNED)
                                    <span class="badge rounded-pill alert-danger">RETURNED</span>
                                @elseif($order->status == \App\Models\Order::CANCELED)
                                    <span class="badge rounded-pill alert-danger">CANCELED</span>
                                @elseif($order->status == \App\Models\Order::COMPLETED)
                                    <span class="badge rounded-pill alert-danger">COMPLETE</span>
                                @endif
                            </p>
                            <p class="mb-1">
                                Additional Notes: <b>{{ $order->additional_notes }}</b>
                            </p>
                            <p class="mb-1">
                                Website Order: <b>{{ $order->is_website_order ? 'Yes' : 'No' }}</b>
                            </p>
                            @if($order->status == 6 || $order->status == 9)
                                <p class="mb-1">
                                    Amt Return Type: <b>{{ $order->return_type == 1 ? 'Adjust in Ledger' : 'Cash Return' }}</b>
                                </p>
                                <p class="mb-1">
                                    Qty Adjust Type: <b>{{ $order->adjust_type == 1 ? 'Adjust in Stock' : 'Adjust in Damage' }}</b>
                                </p>
                            @endif
                        </div>
                    </article>
                </div>
            </div>
            <!-- row // -->
            <div class="row">
                <div class="col-lg-10">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="30%">Product</th>
                                    <th width="15%">Unit Price</th>
                                    <th width="15%">Quantity</th>
                                    <th width="20%">Total</th>
                                    <th width="30%">Purchase Price</th>
                                    <th width="30%">Wholesale Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $s = 0; $p = 0; $w = 0; ?>
                                @foreach($order->products as $pro)
                                    {{-- skip bundle parent rows; show only standalone products and bundle child items --}}
                                    @if(isset($pro->is_bundle) && $pro->is_bundle && ( !isset($pro->is_bundle_item) || !$pro->is_bundle_item ))
                                        @continue
                                    @endif
                                    <?php $netQty = max(0, $pro->qty - ($pro->return_qty ?? 0)); ?>
                                    @if($netQty > 0)
                                        <tr>
                                            <td>
                                                <div class="info" style="@if(isset($pro->is_bundle_item) && $pro->is_bundle_item) padding-left:20px; opacity:0.95; @endif">
                                                    {{ $pro->name }}
                                                    @if($pro->variant)
                                                        <br><small>{{ $pro->variant->shade ?? '' }} {{ $pro->variant->size ? '- '.$pro->variant->size : '' }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ number_format($pro->price, 2) }}</td>
                                            <td>{{ $netQty }}</td>
                                            <td>{{ number_format($pro->price * $netQty) }}</td>
                                            <td>{{ number_format($pro->cost_price) }}<br>Total: {{ number_format($pro->cost_price * $netQty) }}</td>
                                            <td>{{ number_format($pro->wholesale_price) }}<br>Total: {{ number_format($pro->wholesale_price * $netQty) }}</td>
                                        </tr>
                                        <?php
                                        $s += ($pro->price * $netQty);
                                        $p += ($pro->cost_price * $netQty);
                                        $w += ($pro->wholesale_price * $netQty);
                                        ?>
                                    @endif
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><b>{{ number_format($s) }}</b></th>
                                    <th><b>{{ number_format($p) }}</b></th>
                                    <th><b>{{ number_format($w) }}</b></th>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <article class="float-end">
                                            <dl class="dlist">
                                                <dt>Subtotal:</dt>
                                                <dd>{{ number_format($order->total_amount - $order->return_amount + $order->discount_amount) }}</dd>
                                            </dl>
                                            <dl class="dlist">
                                                <dt>Discount Amount:</dt>
                                                <dd>{{ number_format($order->discount_amount) }}</dd>
                                            </dl>
                                            <dl class="dlist">
                                                <dt>Grand total:</dt>
                                                <dd><b class="h5">{{ number_format($order->total_amount - $order->return_amount) }}</b></dd>
                                            </dl>
                                        </article>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- table-responsive// -->
                </div>
                <!-- col// -->
                <div class="col-lg-1"></div>
                <!-- col// -->
            </div>
            <div class="row">
                <div class="col-lg-7">
                    <h4>Return Products</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="40%">Product</th>
                                    <th width="20%">Unit Price</th>
                                    <th width="20%">Quantity</th>
                                    <th width="20%" class="text-end">Total</th>
                                    <th width="20%">Purchase Price</th>
                                    <th width="20%">Wholesale Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->products as $pro)
                                    {{-- skip bundle parent rows in return list as well --}}
                                    @if(isset($pro->is_bundle) && $pro->is_bundle && ( !isset($pro->is_bundle_item) || !$pro->is_bundle_item ))
                                        @continue
                                    @endif
                                    @if(isset($pro->return_qty) && $pro->return_qty > 0)
                                        <tr>
                                            <td>
                                                <div class="info" style="@if(isset($pro->is_bundle_item) && $pro->is_bundle_item) padding-left:20px; opacity:0.95; @endif">
                                                    {{ $pro->name }}
                                                    @if($pro->variant)
                                                        <br><small>{{ $pro->variant->shade ?? '' }} {{ $pro->variant->size ? '- '.$pro->variant->size : '' }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ number_format($pro->price) }}</td>
                                            <td>{{ $pro->return_qty }}</td>
                                            <td class="text-end">{{ number_format($pro->price * $pro->return_qty) }}</td>
                                            <td>{{ number_format($pro->cost_price) }}</td>
                                            <td>{{ number_format($pro->wholesale_price) }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                <tr>
                                    <td colspan="4">
                                        <article class="float-end">
                                            <dl class="dlist">
                                                <dt>Grand total:</dt>
                                                <dd><b class="h5">{{ number_format($order->return_amount) }}</b></dd>
                                            </dl>
                                        </article>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- table-responsive// -->
                </div>
                <!-- col// -->
                <div class="col-lg-1"></div>
                <!-- col// -->
            </div>
        </div>
        <!-- card-body end// -->
    </div>
    <!-- card end// -->
@stop
