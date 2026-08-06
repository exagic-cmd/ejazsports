@extends('layouts.app')


@section('content')

    <div class="row">
        <div class="col-9">
            <div class="content-header">
                <h2 class="content-title">Add New Employee</h2>
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
                    <form action="{{route('employees.update',$employee->id)}}" method="post" id="form">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Name</label>
                            <input  type="text" name="name" placeholder="Type here" class="form-control @error('name') is-invalid @enderror" id="product_name" value="{{$employee->name }}">
                            @error('name')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Mobile Number</label>
                            <input  type="text" name="mobile_number" placeholder="Type here" class="form-control @error('mobile_number') is-invalid @enderror"  value="{{ $employee->mobile_number }}">
                            @error('mobile_number')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Commission Per Retail</label>
                            <input  type="text" name="com_per_retail" placeholder="Type here" class="form-control @error('com_per_retail') is-invalid @enderror"  value="{{ $employee->com_per_retail }}">
                            @error('com_per_retail')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Commission Per Whole</label>
                            <input  type="text" name="com_per_whole" placeholder="Type here" class="form-control @error('com_per_whole') is-invalid @enderror"  value="{{ $employee->com_per_whole }}">
                            @error('com_per_whole')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                       

                        <div class="mb-4">
                            <label for="role" class="form-label">Status</label>
                            <select class="form-control select2"
                                    name="status" required>

                                <option value="1"  {{$employee->status ? 'selected' : ''}}>Active</option>
                                <option value="0" {{$employee->status ? '' : 'selected'}}>InActive</option>

                            </select>
                            @error('status')
                            <span class="text-danger text-left">{{ $errors->first('status') }}</span>
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



