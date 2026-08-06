@extends('layouts.app')


@section('content')

    <div class="row">
        <div class="col-9">
            <div class="content-header">
                <h2 class="content-title">Edit Discount</h2>
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
                    <form action="{{route('discounts.update',$discount->id)}}" method="post" id="form">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Name</label>
                            <input  type="text" name="name" placeholder="Type here" class="form-control @error('name') is-invalid @enderror" id="product_name" value="{{ $discount->name }}">
                            @error('name')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Discount Type</label>
                            <select class="form-control" name="type">
                                <option value="{{\App\Models\Discount::PRODUCT}}" {{$discount->type == \App\Models\Discount::PRODUCT ? 'selected' : ''}}>Product Discount</option>
                                <option value="{{\App\Models\Discount::BRAND}} " {{$discount->type == \App\Models\Discount::BRAND ? 'selected' : ''}}>Brand Discount</option>
                                <option value="{{\App\Models\Discount::CATEGORY}}" {{$discount->type == \App\Models\Discount::CATEGORY ? 'selected' : ''}}>Category Discount</option>
                            </select>
                            @error('type')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Is Percentage</label>
                            <input type="checkbox" {{$discount->is_percent ? 'checked' : ''}} value="1" name="is_percent" class="form-check-input">
                            @error('is_percent')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Amount / Percentage</label>
                            <input  type="numeric" name="amount" placeholder="Type here" class="form-control @error('amount') is-invalid @enderror"  value="{{ $discount->amount }}">
                            @error('amount')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-4">
                            <label for="product_name" class="form-label">Max Discount Amount</label>
                            <input  type="numeric" name="max_amount" placeholder="Type here" class="form-control @error('max_amount') is-invalid @enderror"  value="{{ $discount->max_amount }}">
                            @error('max_amount')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Start Date</label>
                            <input  type="date" name="start_date" placeholder="Type here" class="form-control @error('start_date') is-invalid @enderror"  value="{{ $discount->start_date }}">
                            @error('start_date')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">End Date</label>
                            <input  type="date" name="end_date" placeholder="Type here" class="form-control @error('amount') is-invalid @enderror"  value="{{ $discount->end_date }}">
                            @error('end_date')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="role" class="form-label">Status</label>
                            <select class="form-control select2"
                                    name="status" required>

                                <option value="1" {{$discount->status ? 'selected' : ''}}>Active</option>
                                <option value="0" {{$discount->status ? '' : 'selected'}}>InActive</option>

                            </select>
                            @error('status')
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



