@extends('layouts.app')


@section('content')

    <div class="row">
        <div class="col-12">
            <div class="content-header">
                <h2 class="content-title">Edit Purchase Order</h2>
                <div>
                    <button onclick="setFormSubmitting();document.getElementById('form').submit()" class="btn btn-md rounded font-sm hover-up">Update</button>
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
                    <form action="{{route('purchase-orders.update',$purchaseOrder->id)}}" method="post" id="form" autocomplete="false">
                        @method('PUT')
                        @csrf

                        <div @class('row')>
                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Supplier</label>
                                    <select id="supplier_id" class="form-control select2 @error('name') is-invalid @enderror" name="supplier_id">
                                        <option value="">None</option>
                                        @foreach($suppliers as $s)
                                            @if($s->id == $purchaseOrder->supplier_id)
                                            <option selected value="{{$s->id}}">{{$s->name}} - {{$s->mobile_number}}</option>
                                            @else
                                                <option value="{{$s->id}}">{{$s->name}} - {{$s->mobile_number}}</option>
                                                @endif
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
                                    <input  type="date" id="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{date('Y-m-d',strtotime($purchaseOrder->date))}}" >
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
                                    <textarea name="comment" class="form-control" cols="5">{{$purchaseOrder->comment}}</textarea>
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
                                            @if($s->id == $purchaseOrder->store_id)
                                            <option selected value="{{$s->id}}">{{$s->name}}</option>
                                            @else
                                                <option value="{{$s->id}}">{{$s->name}}</option>
                                                @endif
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
        $(document).ready( function() {
            updateSelectedProducts();
        });
        $('.select2').select2();

        function updateSelectedProducts() {

            supplier_id = $('#supplier_id').val();
            $.ajax({
                url: "{{ route('supplier.brand.detail') }}",
                type:'GET',
                data: {supplier_id:supplier_id},
                success: function(data) {
                    document.getElementById('brandDiv').innerHTML = data;
                }
            });
            $.ajax({
                url: "{{ route('supplier.product.selected.detail') }}",
                type:'GET',
                data: {supplier_id:supplier_id,po_id :{{$purchaseOrder->id}}},
                success: function(data) {
                    document.getElementById('productDiv').innerHTML = data;
                    $('#myTable').DataTable({
                        'ordering': true,'order' : [], 'sorting' : true, 'paging' : false, 'info' : true, 'searching':true
                    });
                }
            });
        }

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

