@extends('layouts.app')


@section('css')
@stop

@section('content')

    <div class="row">
        <div class="col-9">
            <div class="content-header">
                <h2 class="content-title">Edit Expense</h2>
                <div>
                    <button onclick="setFormSubmitting();document.getElementById('form').submit()" class="btn btn-md rounded font-sm hover-up">Save</button>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <form action="{{route('expense.update',$expense->id)}}" method="post" id="form" autocomplete="false" enctype="multipart/form-data">
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

                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Store <span style="color: red;">*</span></label>
                            <select class="form-control select2 @error('store_id') is-invalid @enderror" name="store_id">
                                @foreach($stores as $store)
                                    @if($expense->store_id == $store->id)
                                        <option selected value="{{$store->id}}">{{$store->name}}</option>
                                    @else
                                        <option value="{{$store->id}}">{{$store->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('store_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Category <span style="color: red;">*</span></label>
                            <select class="form-control select2 @error('category_id') is-invalid @enderror" name="category_id">
                               @foreach($categories as $cat)
                                    @if($expense->category_id == $cat->id)
                                        <option selected value="{{$cat->id}}">{{$cat->name}}</option>
                                    @else
                                        <option value="{{$cat->id}}">{{$cat->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('category_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Bill # </label>
                            <input  type="text" name="bill_no" placeholder="Type here" class="form-control @error('bill_no') is-invalid @enderror" value="{{ $expense->bill_no }}">
                            @error('bill_no')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Date <span style="color: red;">*</span></label>
                            <input  type="date" name="date" placeholder="Type here" class="form-control @error('date') is-invalid @enderror" value="{{ $expense->date }}">
                            @error('date')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Amount <span style="color: red;">*</span></label>
                            <input  type="text" name="amount" placeholder="Type here" class="form-control @error('amount') is-invalid @enderror" value="{{ $expense->amount }}">
                            @error('amount')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Detail </label>
                            <textarea name="detail" placeholder="Type here" class="form-control @error('detail') is-invalid @enderror">{{$expense->detail}}</textarea>
                            @error('detail')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>


                    </div>
                </div>

        </div>

        <div class="col-lg-3">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Picture </h4>
                </div>
                <div class="card-body">
                    <div class="input-upload">
                        <img src="{{asset('storage/'.$expense->picture)}}" alt="">
                        <input class="form-control" type="file" name="picture">
                        @error('picture')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <!-- card end// -->


            </form>
        </div>



    </div>



@stop

@section('js')
    <script>
        $('.select2').select2();

    </script>

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


@stop

