@extends('layouts.app')


@section('css')

@stop

@section('content')

    <div class="row">
        <div class="col-9">
            <div class="content-header">
                <h2 class="content-title">Edit Banner</h2>
                <div>
                    <button onclick="setFormSubmitting();document.getElementById('form').submit()" class="btn btn-md rounded font-sm hover-up">Save</button>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <form action="{{route('banners.update',$banner->id)}}" method="post" id="form" autocomplete="false" enctype="multipart/form-data">
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
                            <label for="product_name" class="form-label">Web Heading </label>
                            <input  type="text" name="web_heading" placeholder="Type here" class="form-control @error('web_heading') is-invalid @enderror" value="{{ $banner->web_heading }}">
                            @error('web_heading')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Web Sub Heading </label>
                            <input  type="text" name="web_sub_heading" placeholder="Type here" class="form-control @error('web_sub_heading') is-invalid @enderror" value="{{ $banner->web_sub_heading }}">
                            @error('web_sub_heading')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Mobile Heading </label>
                            <input  type="text" name="mobile_heading" placeholder="Type here" class="form-control @error('mobile_heading') is-invalid @enderror" value="{{ $banner->mobile_heading }}">
                            @error('mobile_heading')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Mobile Sub Heading </label>
                            <input  type="text" name="mobile_sub_heading" placeholder="Type here" class="form-control @error('mobile_sub_heading') is-invalid @enderror" value="{{ $banner->mobile_sub_heading }}">
                            @error('mobile_sub_heading')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Link </label>
                            <input  type="text" name="url" placeholder="Type here" class="form-control @error('url') is-invalid @enderror" value="{{ $banner->url }}">
                            @error('url')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4 tooltip1">
                            <label for="product_name" class="form-label">Serial No.</label>

                            <input  type="number" name="serial_no" placeholder="Type here" class="form-control @error('serial_no') is-invalid @enderror" value="{{ $banner->serial_no }}">
                            @error('serial_no')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Status <span style="color: red;">*</span></label>
                            <select name="status" class="form-control select2" >
                                <option value="1" {{$banner->status ? 'selected' : ''}}>Active</option>
                                <option value="0" {{(!$banner->status) ? 'selected' : ''}}>InActive</option>
                            </select>
                            @error('status')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>


                    </div>
                </div>



        </div>

        <div class="col-lg-3">

            <!-- card end// -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Web Banner</h4>
                </div>
                <div class="card-body">
                    <div class="input-upload">
                        <img src="{{asset('storage/'.$banner->web_image)}}" alt="">
                        <input class="form-control" type="file" name="web_image">
                        @error('web_image')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <!-- card end// -->
            <!-- card end// -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Mobile Banner</h4>
                </div>
                <div class="card-body">
                    <div class="input-upload">
                        <img src="{{asset('storage/'.$banner->mobile_image)}}" alt="">
                        <input class="form-control" type="file" name="mobile_image">
                        @error('mobile_image')
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

