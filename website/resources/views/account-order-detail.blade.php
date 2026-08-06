@extends('layouts.app')

@section('content')
<main id="content" role="main">
    <!-- breadcrumb -->
    <div class="bg-gray-13 bg-md-transparent">
        <div class="container">
            <div class="my-md-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble">
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1"><a href="{{ route('account') }}">My Account</a></li>
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">Order #{{ $order->order_no ?? $order->id }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- End breadcrumb -->

    <div class="container mb-8">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h1 class="font-size-25">Order Details</h1>
            <a href="{{ route('account.orders.list') }}" class="btn btn-sm btn-soft-secondary"><i class="fas fa-arrow-left mr-1"></i> Back to Orders</a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Order Items</h5>
                    </div>
                    <div class="card-body">
                         <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->products as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="mr-3">
                                                    @php
                                                        $imgUrl = $item->product && $item->product->images->count() > 0 
                                                            ? env('BACKEND_IMAGE_URL') . $item->product->images->first()->url 
                                                            : env('BACKEND_IMAGE_URL') . 'default.jpeg';
                                                    @endphp
                                                    <img class="img-fluid" src="{{ $imgUrl }}" alt="{{ $item->product->title ?? 'Product' }}" style="width: 60px; height: 60px; object-fit: cover; border: 1px solid #eee;" onerror="this.src='{{ env('BACKEND_IMAGE_URL') . 'default.jpeg' }}'">
                                                </div>
                                                <div>
                                                    <a href="{{ route('productdetail', $item->product_id) }}" class="text-dark font-weight-bold">{{ $item->product->title ?? 'Unknown Product' }}</a>
                                                    @if($item->variant)
                                                        <small class="d-block text-muted">
                                                            {{ trim(($item->variant->size ?? '') . ' ' . ($item->variant->shade ?? '')) }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>Rs. {{ number_format($item->price, 2) }}</td>
                                        <td>{{ $item->qty }}</td>
                                        <td class="text-right">Rs. {{ number_format($item->price * $item->qty, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Order ID:</span>
                                <span class="font-weight-bold">#{{ $order->order_no ?? $order->id }}</span>
                            </li>
                            <li class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Date:</span>
                                <span>{{ $order->created_at->format('d M, Y h:i A') }}</span>
                            </li>
                            <li class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Status:</span>
                                @php
                                    switch($order->status) {
                                        case \App\Models\Order::PENDING: $status = 'Pending'; $badge = 'badge-warning'; break;
                                        case \App\Models\Order::BOOKED: $status = 'Booked'; $badge = 'badge-info'; break;
                                        case \App\Models\Order::SCANNED: $status = 'Scanned'; $badge = 'badge-info'; break;
                                        case \App\Models\Order::DISPATCHED: $status = 'Dispatched'; $badge = 'badge-primary'; break;
                                        case \App\Models\Order::DELIVERED: $status = 'Delivered'; $badge = 'badge-success'; break;
                                        case \App\Models\Order::RETURNED: $status = 'Returned'; $badge = 'badge-danger'; break;
                                        case \App\Models\Order::CANCELED: $status = 'Canceled'; $badge = 'badge-danger'; break;
                                        case \App\Models\Order::COMPLETED: $status = 'Completed'; $badge = 'badge-success'; break;
                                        default: $status = 'Unknown'; $badge = 'badge-secondary';
                                    }
                                @endphp
                                <span class="badge {{ $badge }} px-2 py-1">{{ $status }}</span>
                            </li>
                            <li class="border-top my-3"></li>
                            <li class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal:</span>
                                <span>Rs. {{ number_format($order->total_amount - $order->delivery_charges, 2) }}</span>
                            </li>
                            <li class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Shipping:</span>
                                <span>Rs. {{ number_format($order->delivery_charges, 2) }}</span>
                            </li>
                            @if($order->discount_amount > 0)
                            <li class="d-flex justify-content-between mb-2 text-success">
                                <span>Discount:</span>
                                <span>- Rs. {{ number_format($order->discount_amount, 2) }}</span>
                            </li>
                            @endif
                            <li class="border-top my-3"></li>
                            <li class="d-flex justify-content-between font-weight-bold font-size-18">
                                <span>Total:</span>
                                <span>Rs. {{ number_format($order->total_amount, 2) }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Shipping Address</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">
                            <strong>{{ $order->name }}</strong><br>
                            {{ $order->address }}<br>
                            {{ $order->city }}<br>
                            @if($order->phone_number)
                                <i class="fas fa-phone mr-1"></i> {{ $order->phone_number }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
