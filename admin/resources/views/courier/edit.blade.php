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
                <h2 class="content-title">Edit Courier</h2>
                <div>
                    <button onclick="setFormSubmitting();document.getElementById('form').submit()" class="btn btn-md rounded font-sm hover-up">Save</button>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <form action="{{route('couriers.update',$courier->id)}}" method="post" id="form" autocomplete="false" >
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
                            <label for="product_name" class="form-label">Name <span style="color: red;">*</span></label>
                            <input  type="text" name="name" placeholder="Type here" class="form-control @error('name') is-invalid @enderror" value="{{ $courier->name }}">
                            @error('name')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Representative </label>
                            <input  type="text" name="representative_name" placeholder="Type here" class="form-control @error('representative_name') is-invalid @enderror" value="{{ $courier->representative_name }}">
                            @error('representative_name')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Phone Number </label>
                            <input  type="text" name="phone_number" placeholder="Type here" class="form-control @error('phone_number') is-invalid @enderror" value="{{ $courier->phone_number }}">
                            @error('phone_number')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Rate<span style="color: red;">*</span></label>
                            <input  type="number" name="rate" placeholder="Type here" class="form-control @error('rate') is-invalid @enderror" value="{{ $courier->rate }}">
                            @error('rate')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-4">
                            <label for="product_name" class="form-label">Minimum Weight Allow (GM/ML)</label>
                            <input  type="number" name="allow_weight_gm_ml" placeholder="Type here" class="form-control @error('allow_weight_gm_ml') is-invalid @enderror" value="{{ $courier->allow_weight_gm_ml }}">
                            @error('allow_weight_gm_ml')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Extra Charges Per Gram / ML (1000) </label>
                            <input  type="number" name="extra_charges_above_weight" placeholder="Type here" class="form-control @error('extra_charges_above_weight') is-invalid @enderror" value="{{ $courier->extra_charges_above_weight }}">
                            @error('extra_charges_above_weight')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-4">
                            <label class="col-lg-3 col-form-label">Areas </label>

                            <div class="col-lg-9">

                                <select   name="area_id[]" multiple class="form-control select2 @error('area_id') is-invalid @enderror">
                                    @foreach($areas as $a)
                                        @if(in_array($a->id,$selectedAreas))
                                            <option selected value="{{$a->id}}">{{$a->name}}</option>
                                        @else
                                            <option value="{{$a->id}}">{{$a->name}}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('area_id')
                                <div @class('alert alert-danger')>{{$message}}</div>
                                @enderror
                            </div>
                            <!-- col.// -->
                        </div>


                        <div class="mb-4">
                            <label for="product_name" class="form-label">Status <span style="color: red;">*</span></label>
                            <select name="status" class="form-control select2" >
                                <option value="1" {{$courier->status ? 'selected' : ''}}>Active</option>
                                <option value="0" {{ !($courier->status) ? 'selected' : ''}}>InActive</option>
                            </select>
                            @error('status')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>



                    </div>
                </div>
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

