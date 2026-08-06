@extends('layouts.app')

@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
@stop


@section('content')

    <div class="row">
        <div class="col-9">
            <div class="content-header">
                <h2 class="content-title">Add New Direct Receiving</h2>
                <div style="width: 150px;">
                    <button onclick="setFormSubmitting();document.getElementById('form').submit()" class="btn btn-md rounded font-sm hover-up">Start</button>
                </div>
            </div>
        </div>

        <form action="{{route('receiving.direct.submit')}}" method="post" id="form">
            @csrf

            <div class="col-lg-6 d-inline-block">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>General Info</h4>
                    </div>

                    <div class="card-body">
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Store</label>
                            <select class="form-control select2" name="store_id">
                                @foreach($stores as $s)
                                    <option value="{{$s->id}}">{{$s->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Date</label>
                            <input  class="form-control" type="date" name="date" value="">
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Cargo #</label>
                            <input  class="form-control" type="text" name="cargo_no" value="">
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label">Amount</label>
                            <input  class="form-control" type="text" name="net_amount" value="0">
                        </div>
                    </div>
                </div>

            </div>

        </form>
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




