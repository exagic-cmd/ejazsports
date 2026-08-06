@extends('layouts.app')


@section('content')

    <div class="row">
        <div class="col-9">
            <div class="content-header">
                <h2 class="content-title">Add New Discount</h2>
                <div>
                    <button onclick="setFormSubmitting();document.getElementById('form').submit()" class="btn btn-md rounded font-sm hover-up">Save</button>
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
                    <form action="{{route('discounts.store')}}" method="post" id="form">
                        @csrf

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Name</label>
                            <input  type="text" name="name" placeholder="Type here" class="form-control @error('name') is-invalid @enderror" id="product_name" value="{{ old('name') }}">
                            @error('name')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Discount Type</label>
                            <select class="form-control" name="type">
                                <option value="{{\App\Models\Discount::PRODUCT}}">Product Discount</option>
                                <option value="{{\App\Models\Discount::BRAND}}">Brand Discount</option>
                                <option value="{{\App\Models\Discount::CATEGORY}}">Category Discount</option>
                            </select>
                            @error('type')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Is Percentage</label>
                            <input type="checkbox" name="is_percent" class="form-check-input" value="1">
                            @error('is_percent')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Amount / Percentage</label>
                            <input  type="numeric" name="amount" placeholder="Type here" class="form-control @error('amount') is-invalid @enderror"  value="{{ old('amount') }}">
                            @error('amount')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Max Discount Amount</label>
                            <input  type="numeric" name="max_amount" placeholder="Type here" class="form-control @error('max_amount') is-invalid @enderror"  value="{{ old('max_amount') }}">
                            @error('max_amount')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Start Date</label>
                            <input  type="date" name="start_date" placeholder="Type here" class="form-control @error('start_date') is-invalid @enderror"  value="{{ old('start_date') }}">
                            @error('start_date')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">End Date</label>
                            <input  type="date" name="end_date" placeholder="Type here" class="form-control @error('amount') is-invalid @enderror"  value="{{ old('end_date') }}">
                            @error('end_date')
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



