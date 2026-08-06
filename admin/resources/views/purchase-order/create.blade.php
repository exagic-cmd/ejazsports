@extends('layouts.app')


@section('content')

    <div class="row">
        <div class="col-12">
            <div class="content-header">
                <h2 class="content-title">New Purchase Order</h2>
                <div>
                    <button onclick="setFormSubmitting();document.getElementById('form').submit()" class="btn btn-md rounded font-sm hover-up">Save</button>
                </div>
            </div>
        </div>
        <div class="col-lg-10">
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
                    <form action="{{route('purchase-orders.store')}}" method="post" id="form" autocomplete="false">
                        @csrf

                        <div @class('row')>
                        <div class="col-lg-6">
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Supplier</label>
                            <select id="supplier_id" class="form-control select2 @error('name') is-invalid @enderror" name="supplier_id">
                                <option value="">None</option>
                                @foreach($suppliers as $s)
                                    <option value="{{$s->id}}">{{$s->name}} - {{$s->mobile_number}}</option>
                                    @endforeach
                            </select>


                            @error('supplier_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        </div>

                        <div class="col-lg-6">
                        <div class="mb-4">
                            <label for="product_name" class="form-label">Date</label>
                            <input  type="date" id="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{$today}}" >
                            @error('date')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        </div>
                        </div>

                            <div @class('row')>

                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label for="product_name" class="form-label">Comment</label>
                                <textarea name="comment" class="form-control" cols="5"></textarea>
                                @error('comment')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>



                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label for="product_name" class="form-label">Shippment At</label>
                                <select class="form-control select2 @error('store_id') is-invalid @enderror" name="store_id">
                                    @foreach($stores as $s)
                                        <option value="{{$s->id}}">{{$s->name}}</option>
                                    @endforeach
                                </select>

                                @error('store_id')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        </div>

                        <hr>
                        <div class="col-lg-12" id="productDiv">

                        </div>



                    </form>
                </div>
            </div>

        </div>

        <aside class="col-lg-2 card" id="brandDiv">

        </aside>


    </div>



@stop

@section('js')
    <script>
        $('.select2').select2();

        $(document).on('change','#supplier_id',function(e) {

            supplier_id = $(this).val();

            $.confirm({
                title: 'Supplier info update!',
                content: 'Are you sure you want to do this!',
                buttons: {
                    confirm: function () {
                        $.ajax({
                            url: "{{ route('supplier.brand.detail') }}",
                            type:'GET',
                            data: {supplier_id:supplier_id},
                            success: function(data) {
                                document.getElementById('brandDiv').innerHTML = data;
                            }
                        });
                        $.ajax({
                            url: "{{ route('supplier.product.detail') }}",
                            type:'GET',
                            data: {supplier_id:supplier_id},
                            success: function(data) {
                                document.getElementById('productDiv').innerHTML = data;
                                $('#myTable').DataTable({
                                    'ordering': true,'order' : [], 'sorting' : true, 'paging' : false, 'info' : true, 'searching':true
                                });
                            }
                        });
                    },
                    cancel: function () {

                    }
                }
            });





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

