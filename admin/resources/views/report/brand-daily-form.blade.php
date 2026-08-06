@extends('layouts.app')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('content')

    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Brand Daily Graph</h2>
            <p>Select the specific brand</p>
        </div>

    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card card-body mb-4">
                <form method="post" action="{{route('report.brand.graph')}}">
                    @csrf


                    <div class="row mb-4">
                        <label class="col-lg-3 col-form-label">Brand<span style="color: red;"> *</span></label>
                        <div class="col-lg-9">
                            <select class="form-control select2" name="brand_id">
                                <option value="">All Brands</option>
                                @foreach($brands as $b)
                                    <option value="{{$b->id}}">{{$b->title}}</option>
                                @endforeach
                            </select>
                            @error('brand_id')
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

