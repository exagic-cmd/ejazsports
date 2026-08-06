@extends('layouts.app')


@section('content')

    <div class="row">
        <div class="col-9">
            <div class="content-header">
                <h2 class="content-title">Add New Stock Audit</h2>
                <div style="width: 150px;">
                    <button onclick="setFormSubmitting();document.getElementById('form').submit()" class="btn btn-md rounded font-sm hover-up">Start</button>
                </div>
            </div>
        </div>

        <form action="{{route('stock-audits.store')}}" method="post" id="form">
            @csrf

            <div class="col-lg-10 d-inline-block">
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
                            <label for="product_name" class="form-label">Brand#</label>
                            <select class="form-control select2" name="brand_id">
                                @foreach($brands as $b)
                                    <option value="{{$b->id}}">{{$b->title}}</option>
                                @endforeach
                            </select>
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


        $(document).ready( function () {
            $('#myTable').DataTable({
                'ordering': false, 'sorting' : false, 'paging' : false, 'info' : false, 'searching':true
            });
        } );

        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 1000);

        var scan = document.getElementById("order_no");
        scan.addEventListener("keydown", function (e) {
            if (e.code === "Enter") {  //checks whether the pressed key is "Enter"
                searchOrder();
            }
        });

       

    </script>


@endsection



