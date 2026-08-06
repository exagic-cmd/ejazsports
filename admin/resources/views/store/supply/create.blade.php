@extends('layouts.app')


@section('content')

    <div class="row">
        <div class="col-12">
            <div class="content-header">
                <h2 class="content-title">New Store Supply</h2>
                <div>
                    <button onclick="setFormSubmitting();document.getElementById('form').submit()" class="btn btn-md rounded font-sm hover-up">Save</button>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
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
                    <form action="{{route('supplies.store')}}" method="post" id="form" autocomplete="false">
                        @csrf

                        <div @class('row')>
                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Store Out</label>
                                    <select id="store_out_id" class="form-control select2 @error('store_out_id') is-invalid @enderror" name="store_out_id">
                                        <option value="">None</option>
                                        @foreach($stores as $s)
                                            <option value="{{$s->id}}">{{$s->name}}</option>
                                        @endforeach
                                    </select>


                                    @error('store_out_id')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Store IN</label>
                                    <select id="store_in_id" class="form-control select2 @error('store_in_id') is-invalid @enderror" name="store_in_id">
                                        <option value="">None</option>
                                        @foreach($stores as $s)
                                            <option value="{{$s->id}}">{{$s->name}}</option>
                                        @endforeach
                                    </select>


                                    @error('store_in_id')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div @class('row')>

                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Issued Date</label>
                                    <input  type="date" id="send_date" name="send_date" class="form-control @error('send_date') is-invalid @enderror" value="{{$today}}" >
                                    @error('send_date')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Supply Type</label>
                                    <select class="form-control select2 @error('type') is-invalid @enderror" name="type">

                                        <option value="">None</option>
                                        <option value="{{\App\Models\Supply::NEW_STOCK}}">New Stock</option>
                                        <option value="{{\App\Models\Supply::RETURN_STOCK}}">Return Stock</option>
                                        <option value="{{\App\Models\Supply::ORDER_STOCK}}">Order Stock</option>
                                    </select>

                                    @error('type')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div @class('row')>


                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Note</label>
                                    <textarea name="notes" class="form-control" cols="5"></textarea>
                                    @error('note')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Add Brand</label>
                                    <select @class('form-control select2') name="brand_id" id="brand_id">
                                        @foreach($brands as $b)
                                        <option value="{{$b->id}}">{{$b->brand_heading}}</option>
                                            @endforeach
                                    </select>

                                    <button style="margin-top: 3%;" type="button" @class('form-control btn btn-primary')  onclick="setFormSubmitting();addBrand();">Add Brand</button>

                                </div>
                            </div>




                        </div>

                        <div @class('row')>

                        <div class="col-lg-3">
                            <div class="mb-4">
                                <label for="product_name" class="form-label">Total Products</label>
                                <input style="cursor: not-allowed" type="number" readonly id="total_products" name="total_products" class="form-control" value="0" >
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="mb-4">
                                <label for="product_name" class="form-label">Total Quantity</label>
                                <input style="cursor: not-allowed" type="number" readonly  id="total_qty" name="total_qty"  class="form-control" value="0">

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



    </div>



@stop

@section('js')

    <script src="{{asset('js/supply.js')}}" type="text/javascript"></script>
    <script>
        $('.select2').select2();

        function addBrand() {

            brand_id = $('#brand_id').val();
            store_out = $('#store_out_id').val();
            store_in = $('#store_in_id').val();

            $.confirm({
                title: 'New Brand Added!',
                content: 'Are you sure you want to do this!',
                buttons: {
                    confirm: function () {

                        $.ajax({
                            url: "{{ route('supply.add.brand') }}",
                            type:'GET',
                            data: {brand_id:brand_id,store_out:store_out,store_in:store_in},
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

        }
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

