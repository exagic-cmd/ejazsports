@extends('layouts.app')

@section('content')

    <section class="content-main">
        <div class="row">
            <div class="col-9">
                <div class="content-header">
                    <h2 class="content-title">Edit Size</h2>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <form action="{{route('sizes.update', $size->id)}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-4">
                                <label for="size_name" class="form-label">Size Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" placeholder="Type here" class="form-control @error('name') is-invalid @enderror" id="size_name" value="{{ $size->name }}">
                                @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary float-end">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

@stop
