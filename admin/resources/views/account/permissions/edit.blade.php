@extends('layouts.app')


@section('content')

    <div class="row">
        <div class="col-9">
            <div class="content-header">
                <h2 class="content-title">Add New Permission</h2>
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
                    <form action="{{route('permissions.update',$permission->id)}}" method="post" id="form">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Name</label>
                            <input  type="text" name="name" placeholder="Type here" class="form-control @error('name') is-invalid @enderror"  value="{{ $permission->name }}">
                            @error('name')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="product_name" class="form-label">System  Name</label>
                            <input  type="text" name="system_name" placeholder="Type here" class="form-control @error('system_name') is-invalid @enderror"  value="{{ $permission->system_name }}">
                            @error('system_name')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Module</label>
                            <input  type="text" name="module" placeholder="Type here" class="form-control @error('module') is-invalid @enderror"  value="{{ $permission->module }}">
                            @error('module')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Sub Module</label>
                            <input  type="text" name="sub_module" placeholder="Type here" class="form-control @error('sub_module') is-invalid @enderror"  value="{{ $permission->sub_module }}">
                            @error('sub_module')
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

