@extends('layouts.app')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('content')

    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Brand Available Inventory Store Wise</h2>
            <p>Select the specific Store</p>
        </div>

    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card card-body mb-4">
                <form method="post" action="{{route('report.brand-available-inventory')}}">
                    @csrf


                    <div class="row mb-4">
                        <label class="col-lg-3 col-form-label">Store<span style="color: red;"> *</span></label>
                        <div class="col-lg-9">
                            <select class="form-control select2" name="store_id">
                                <option value="0">All Stores</option>
                                 @unlessrole('Supplier Portal')
                                @foreach($stores as $s)
                                    <option value="{{$s->id}}">{{$s->name}}</option>
                                @endforeach
                                @endunlessrole
                            </select>
                            @error('store_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- col.// -->
                    </div>

                    <div class="form-actions" style="text-align: right">
                        <button type="submit" class=" btn btn-success-light"> <i class="fa fa-check" ></i> Generate</button>

                    </div>
                </form>
            </div>
        </div>
    </div>

@stop

@section('js')

@stop

