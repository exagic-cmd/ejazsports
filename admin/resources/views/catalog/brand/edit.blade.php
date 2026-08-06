@extends('layouts.app')


@section('css')
    <style>
        .tooltip1 {
            position: relative;
        }

        .tooltip1 .tooltiptext {
            visibility: hidden;
            width: 190px;
            background-color: #eefef0;
            color: black;
            text-align: center;
            border-radius: 6px;
            padding: 5px 0;
            position: absolute;
            z-index: 1;
            bottom:60%;
            left: 50%;
            margin-left: -60px;
            opacity: 0;
            transition: opacity 0.3s;
            max-height: 140px;
            overflow-y: auto;
        }

        .tooltip1 .tooltiptext::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #555 transparent transparent transparent;
        }

        .tooltip1:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }
    </style>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.7.0/css/all.css'>



    <script src="{{asset('js/multiselect.js')}}" ></script>
@stop

@section('content')

    <div class="row">
        <div class="col-9">
            <div class="content-header">
                <h2 class="content-title">Edit Brand</h2>
                <div>
                    <button onclick="setFormSubmitting();document.getElementById('form').submit()" class="btn btn-md rounded font-sm hover-up">Save</button>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <form action="{{route('brands.update',$brand->id)}}" method="post" id="form" autocomplete="false" enctype="multipart/form-data">
                @csrf
                @method('PUT')
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
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Title <span style="color: red;">*</span></label>
                            <input  type="text" name="title" placeholder="Type here" class="form-control @error('title') is-invalid @enderror" value="{{ $brand->title }}">
                            @error('title')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                       
                        <div class="mb-4 tooltip1">
                            <label for="product_name" class="form-label">Serial No.</label>

                            <input  type="number" name="serial_no" placeholder="Type here" class="form-control @error('serial_no') is-invalid @enderror" value="{{ $brand->serial_no }}">
                            @error('serial_no')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                       


                        <div class="mb-4">
                            <label for="product_name" class="form-label">Shown in Menu</label>
                            <input class="form-check-input" value="1" type="checkbox" {{$brand->show_in_menu ? 'checked' : ''}} name="show_in_menu" id="flexSwitchCheckChecked" >
                            @error('show_in_menu')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-4">
                            <label class="col-lg-3 col-form-label">Discount </label>

                            <div class="col-lg-9">

                                <select   name="discount_id" class="form-control select2 @error('discount_id') is-invalid @enderror">
                                    <option value="" selected>None</option>
                                    @foreach($discounts as $d)
                                        @if($brand->discount_id == $d->id)
                                            <option selected value="{{$d->id}}">{{$d->name}} - {{$d->amount}} {{$d->is_percent ? ' %' : ''}}</option>
                                        @else
                                            <option value="{{$d->id}}">{{$d->name}} - {{$d->amount}} {{$d->is_percent ? ' %' : ''}}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('discount_id')
                                <div @class('alert alert-danger')>{{$message}}</div>
                                @enderror
                            </div>
                            <!-- col.// -->
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Status <span style="color: red;">*</span></label>
                            <select name="status" class="form-control select2" >
                                <option value="1" {{$brand->status ? 'selected' : ''}}>Active</option>
                                <option value="0" {{(!$brand->status) ? 'selected' : ''}}>InActive</option>
                            </select>
                            @error('status')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        
                    </div>
                </div>



        </div>

        <div class="col-lg-3">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Main Image <span style="color: red;">*</span> </h4><small>(144px * 144px)</small>
                </div>
                <div class="card-body">
                    <div class="input-upload">
                        <img src="{{asset('storage/'.$brand->image)}}" alt="">
                        <input class="form-control" type="file" name="image">
                        @error('image')
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

