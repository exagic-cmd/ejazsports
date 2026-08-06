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
    @stop

@section('content')

    <div class="row">
        <div class="col-9">
            <div class="content-header">
                <h2 class="content-title">Add New Top Bar Content</h2>
                <div>
                    <button onclick="setFormSubmitting();document.getElementById('form').submit()" class="btn btn-md rounded font-sm hover-up">Save</button>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <form action="{{route('top-bar.store')}}" method="post" id="form" autocomplete="false" >
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
                            <label for="product_name" class="form-label">Text <span style="color: red;">*</span></label>
                            <input  type="text" name="text" placeholder="Type here" class="form-control @error('text') is-invalid @enderror" value="{{ old('text') }}">
                            @error('text')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-4 tooltip1">
                            <label for="product_name" class="form-label">Serial No.</label>
                            <span class="tooltiptext">
                                <ul>
                                    @foreach($topBarContents as $tBC)
                                        <li><b>{{$tBC->text}} </b> - {{$tBC->serial_no}}</li>
                                @endforeach
                                </ul>
                            </span>
                            <input  type="number" name="serial_no" placeholder="Type here" class="form-control @error('serial_no') is-invalid @enderror" value="{{ old('serial_no') }}">
                            @error('serial_no')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                    <div class="mb-4">
                        <label for="product_name" class="form-label">Active in Web</label>
                        <input class="form-check-input" value="1" type="checkbox" name="web_active" id="flexSwitchCheckChecked" >
                        @error('mobile_active')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="product_name" class="form-label">Active in Mobile</label>
                        <input class="form-check-input" value="1" type="checkbox" name="mobile_active" id="flexSwitchCheckChecked" >
                        @error('mobile_active')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Status <span style="color: red;">*</span></label>
                            <select name="status" class="form-control select2" >
                                <option value="1">Active</option>
                                <option value="0">InActive</option>
                            </select>
                            @error('status')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>



                </div>
            </div>

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

