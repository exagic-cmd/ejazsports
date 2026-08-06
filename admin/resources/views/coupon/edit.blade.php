@extends('layouts.app')


@section('content')

    <div class="row">
        <div class="col-9">
            <div class="content-header">
                <h2 class="content-title">Edit Coupon</h2>
                <div>
                    <button onclick="setFormSubmitting();document.getElementById('form').submit()" class="btn btn-md rounded font-sm hover-up">Update</button>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Basic</h4>
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="card-body">
                    <form action="{{route('coupons.update',$coupon->id)}}" method="post" id="form">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Name</label>
                            <input  type="text" name="name" placeholder="Type here" class="form-control @error('name') is-invalid @enderror" id="product_name" value="{{ $coupon->name }}">
                            @error('name')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Coupon Type</label>
                            <select class="form-control" name="type">
                                <option value="{{\App\Models\Coupon::PRODUCT}}" {{$coupon->type == \App\Models\Coupon::PRODUCT ? 'selected' : ''}}>Product Coupon</option>
                                <option value="{{\App\Models\Coupon::BRAND}}" {{$coupon->type == \App\Models\Coupon::BRAND ? 'selected' : ''}}>Brand Coupon</option>
                                <option value="{{\App\Models\Coupon::CATEGORY}}" {{$coupon->type == \App\Models\Coupon::CATEGORY ? 'selected' : ''}}>Category Coupon</option>
                                <option value="{{\App\Models\Coupon::ORDER}}" {{$coupon->type == \App\Models\Coupon::ORDER ? 'selected' : ''}}>Order Coupon</option>
                                <option value="{{\App\Models\Coupon::DELIVERY}}" {{$coupon->type == \App\Models\Coupon::DELIVERY ? 'selected' : ''}}>Delivery Coupon</option>
                            </select>
                            @error('type')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Usage</label>
                            <select class="form-control" name="usage">
                                <option value="{{\App\Models\Coupon::ONCE}}" {{$coupon->usage == \App\Models\Coupon::ONCE ? 'selected' : ''}}>Only One Time</option>
                                <option value="{{\App\Models\Coupon::EACH_CUSTOMER_ONCE}}" {{$coupon->usage == \App\Models\Coupon::EACH_CUSTOMER_ONCE ? 'selected' : ''}}>Each Customer One Time</option>
                                <option value="{{\App\Models\Coupon::LIMITED}}" {{$coupon->usage == \App\Models\Coupon::LIMITED ? 'selected' : ''}}>Limited</option>
                                <option value="{{\App\Models\Coupon::UNLIMITED}}" {{$coupon->usage == \App\Models\Coupon::UNLIMITED ? 'selected' : ''}}>Un Limited</option>
                            </select>
                            @error('type')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Limit Count (if any)</label>
                            <input type="numeric" name="limit_count" value="{{$coupon->limit_count}}" class="form-control">
                            @error('limit_count')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Is Percentage</label>
                            <input type="checkbox" name="is_percent" {{$coupon->is_percent ? 'checked' : ''}}  class="form-check-input" value="1">
                            @error('is_percent')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Amount / Percentage</label>
                            <input  type="numeric" name="discount_amount" placeholder="Type here" class="form-control @error('discount_amount') is-invalid @enderror"  value="{{ $coupon->discount_amount }}">
                            @error('discount_amount')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Min Order Amount</label>
                            <input  type="numeric" name="min_order_amount" placeholder="Type here" class="form-control @error('min_order_amount') is-invalid @enderror"  value="{{ $coupon->min_order_amount }}">
                            @error('min_order_amount')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Max Discount Amount</label>
                            <input  type="numeric" name="max_discount_amount" placeholder="Type here" class="form-control @error('max_discount_amount') is-invalid @enderror"  value="{{ $coupon->max_discount_amount }}">
                            @error('max_discount_amount')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Start Date</label>
                            <input  type="date" name="start_date" placeholder="Type here" class="form-control @error('start_date') is-invalid @enderror"  value="{{ $coupon->start_date }}">
                            @error('start_date')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">End Date</label>
                            <input  type="date" name="end_date" placeholder="Type here" class="form-control @error('end_date') is-invalid @enderror"  value="{{ $coupon->end_date }}">
                            @error('end_date')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Customer (if any)</label>

                            <select class="form-control select2" name="customer_id">
                                <option value="">None</option>
                            </select>

                            @error('customer_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Order (if any)</label>
                            <select class="form-control select2" name="order_id">
                                <option value="">None</option>
                            </select>
                            @error('order_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>


                    </form>
                </div>
            </div>

        </div>

    </div>



@stop

@section('js')

    <script>
        var formSubmitting = false;
        var setFormSubmitting = function() { formSubmitting = true; };

        window.onload = function() {
            window.addEventListener("beforeunload", function (e) {
                if (formSubmitting) {
                    return undefined;
                }

                var confirmationMessage = 'It looks like you have been editing something. '
                    + 'If you leave before saving, your changes will be lost.';

                (e || window.event).returnValue = confirmationMessage; //Gecko + IE
                return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
            });
        };
    </script>

@endsection



