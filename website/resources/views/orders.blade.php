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
                         <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">My Orders</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- End breadcrumb -->

    <div class="container mb-8">
        <div class="mb-4">
            <h1 class="font-size-25">My Orders</h1>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('account.orders.list') }}" class="row align-items-end">
                    <div class="col-md-3 mb-3 mb-md-0">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="{{ \App\Models\Order::PENDING }}" {{ request('status') == \App\Models\Order::PENDING ? 'selected' : '' }}>Pending</option>
                            <option value="{{ \App\Models\Order::BOOKED }}" {{ request('status') == \App\Models\Order::BOOKED ? 'selected' : '' }}>Booked</option>
                            <option value="{{ \App\Models\Order::SCANNED }}" {{ request('status') == \App\Models\Order::SCANNED ? 'selected' : '' }}>Scanned</option>
                            <option value="{{ \App\Models\Order::DISPATCHED }}" {{ request('status') == \App\Models\Order::DISPATCHED ? 'selected' : '' }}>Dispatched</option>
                            <option value="{{ \App\Models\Order::DELIVERED }}" {{ request('status') == \App\Models\Order::DELIVERED ? 'selected' : '' }}>Delivered</option>
                            <option value="{{ \App\Models\Order::RETURNED }}" {{ request('status') == \App\Models\Order::RETURNED ? 'selected' : '' }}>Returned</option>
                            <option value="{{ \App\Models\Order::CANCELED }}" {{ request('status') == \App\Models\Order::CANCELED ? 'selected' : '' }}>Canceled</option>
                            <option value="{{ \App\Models\Order::COMPLETED }}" {{ request('status') == \App\Models\Order::COMPLETED ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary-dark-w mr-2">Filter</button>
                        <a href="{{ route('account.orders.list') }}" class="btn btn-secondary">Clear</a>
                    </div>
                </form>
            </div>
        </div>
        <!-- End Filters -->

        @if(isset($orders) && count($orders) > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>#{{ $order->order_no ?? $order->id }}</td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
                            <td>
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
                                <span class="badge {{ $badge }}">{{ $status }}</span>
                            </td>
                            <td>Rs. {{ number_format($order->total_amount, 2) }}</td>
                            <td><a href="{{ route('account.order', $order->id) }}" class="btn btn-sm btn-primary-dark-w">View</a></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($orders->hasPages())
                <div class="mt-4 d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} orders
                    </div>
                    <div>
                        {{ $orders->links('pagination::bootstrap-4') }}
                    </div>
                </div>
                @endif
            </div>
        </div>
        @else
            <div class="alert alert-info">
                @if(request()->hasAny(['status', 'date_from', 'date_to']))
                    No orders found matching your filters. <a href="{{ route('account.orders.list') }}">Clear filters</a>
                @else
                    You haven't placed any orders yet. <a href="{{ route('home') }}">Start shopping!</a>
                @endif
            </div>
        @endif
    </div>
</main>
@endsection
