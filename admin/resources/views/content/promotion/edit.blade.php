@extends('layouts.app')


@section('css')

@stop

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="content-header">
                <h2 class="content-title">Edit Promotion</h2>
                <div>
                    <button onclick="setFormSubmitting();document.getElementById('form').submit()" class="btn btn-md rounded font-sm hover-up">Update</button>
                </div>
            </div>
        </div>
        <form style="display: contents" action="{{route('promotion.update',$promotion->id)}}" method="post" id="form" autocomplete="false" enctype="multipart/form-data">
            @csrf
            @method('PUT')
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

                        @csrf
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Promotion Name </label>
                            <input  type="text" name="name" placeholder="Type here" class="form-control @error('name') is-invalid @enderror" value="{{ $promotion->name }}">
                            @error('name')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Web Count</label>
                            <input  type="numeric" name="web_count" placeholder="Type here" class="form-control @error('web_count') is-invalid @enderror" value="{{ $promotion->web_count }}">
                            @error('web_sub_heading')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Mobile Count</label>
                            <input  type="text" name="mobile_count" placeholder="Type here" class="form-control @error('mobile_count') is-invalid @enderror" value="{{ $promotion->mobile_count }}">
                            @error('mobile_count')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-4 tooltip1">
                            <label for="product_name" class="form-label">Serial No.</label>
                            <input  type="number" name="serial_no" placeholder="Type here" class="form-control @error('serial_no') is-invalid @enderror" value="{{ $promotion->serial_no }}">
                            @error('serial_no')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Status <span style="color: red;">*</span></label>
                            <select name="status" class="form-control select2" >
                                <option value="1" {{$promotion->status ? 'selected' : ''}}>Active</option>
                                <option value="0" {{ !($promotion->status) ? 'selected' : ''}}>InActive</option>
                            </select>
                            @error('status')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

            </div>

            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Promotion Banners <span style="color: red;">*</span></h4>
                    </div>
                    <div class="card-body">
                        <button type="button" style="float: right!important;" class="btn btn-primary add_field_button" >Add New Banner</button>
                        <div class="input_fields_wrap">
                            <div @class('clearfix')></div><br>
                            @foreach($promotion->banners as $banner)
                            <div>
                                <input type="hidden" name="banner_id[]" value="{{$banner->id}}">

                                <div class="row mb-4">
                                    <label class="col-lg-3 col-form-label">Image<span style="color: red;">*</span></label>

                                    <div class="col-lg-9">
                                        <img height="200px" src="{{asset('storage/'.$banner->image)}}">
                                        <input type="file" name="images[]" class="form-control " placeholder="Upload here">
                                        @error('images')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>

                                <div class="row mb-4">
                                    <label class="col-lg-3 col-form-label">URL </label>

                                    <div class="col-lg-9">
                                        <input type="text" name="url[]" value="{{$banner->url}}" class="form-control " placeholder="Type here">
                                        @error('url')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>

                                <div class="row mb-4">
                                    <label class="col-lg-3 col-form-label">Serial #</label>

                                    <div class="col-lg-9">
                                        <input type="numeric"  value="{{$banner->serial_no}}" name="serial_no_i[]" class="form-control " placeholder="Type here">

                                    </div>

                                </div>
                                <a href="#" class="remove_field">Remove</a>
                                </div>
                                @endforeach

                        </div>
                    </div>
                </div>




            </div>

        </form>



    </div>



@stop

@section('js')
    <script>
        $('.select2').select2();

        $(document).ready(function() {
            var max_fields      = 4; //maximum input boxes allowed
            var wrapper         = $(".input_fields_wrap"); //Fields wrapper
            var add_button      = $(".add_field_button"); //Add button ID


            var x = 1; //for multiple images
            $(add_button).click(function(e){ //on add input button click
                e.preventDefault();
                if(x < max_fields){ //max input box allowed
                    x++; //text box increment
                    $(wrapper).prepend('<div><input type="hidden" name="banner_id[]" value="0"><br><br><div class="row mb-4"><label class="col-lg-3 col-form-label">Image<span style="color: red;">*</span></label><div class="col-lg-9"><input type="file" name="images[]" class="form-control " placeholder="Upload here"></div></div><div class="row mb-4"><label class="col-lg-3 col-form-label">URL</label><div class="col-lg-9"><input type="text" name="url[]" class="form-control " placeholder="Type here"></div></div><div class="row mb-4"><label class="col-lg-3 col-form-label">Serial #</label><div class="col-lg-9"><input type="numeric" value="0" name="serial_no_i[]" class="form-control" placeholder="Type here"></div></div><a href="#" class="remove_field">Remove</a>'); //add input box

                }
            });

            $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
                e.preventDefault(); $(this).parent('div').remove(); x--;
            })
        });

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

