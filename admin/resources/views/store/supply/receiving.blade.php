@extends('layouts.app')


@section('content')

    <div class="row">
        <div class="col-12">
            <div class="content-header">
                <h2 class="content-title">New Supply Receiving</h2>
                <div>
                    <button onclick="setFormSubmitting();document.getElementById('form').submit()" class="btn btn-md rounded font-sm hover-up">Save</button>
                </div>
            </div>
        </div>
        <form action="{{route('supply.receiving.store')}}" method="post" id="form" enctype="multipart/form-data" autocomplete="false" style="display: contents">
            @csrf
            <input type="hidden" name="supply_id" value="{{$supply->id}}">
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


                        <div @class('row')>
                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Supply #</label>
                                    <input readonly type="text" class="form-control" value="VEG-00{{$supply->id}}">


                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Send Date</label>
                                    <input readonly  type="date" id="date" name="date" class="form-control" value="{{date('Y-m-d',strtotime($supply->send_date))}}" >

                                </div>
                            </div>


                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Store Out</label>
                                    <input readonly type="text" class="form-control" value="{{$supply->storeOut->name}}">

                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Store In</label>
                                    <input readonly type="text" class="form-control" value="{{$supply->storeIn->name}}">

                                </div>
                            </div>


                        </div>

                        <div @class('row')>

                            <div class="col-lg-3">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Total Send Products</label>
                                    <input style="cursor: not-allowed" type="number" readonly  name="total_products" class="form-control" value="{{$supply->total_products}}" >
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Total Send Quantity</label>
                                    <input style="cursor: not-allowed" type="number" readonly  name="total_qty"  class="form-control" value="{{$supply->total_product_qty}}">

                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Total Received Products</label>
                                    <input style="cursor: not-allowed" type="number" readonly id="total_products" name="total_products" class="form-control" value="0" >
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Total Received Quantity</label>
                                    <input style="cursor: not-allowed" type="number" readonly  id="total_qty" name="total_qty"  class="form-control" value="0">

                                </div>
                            </div>


                        </div>

                    </div>
                </div>

            </div>

            <hr>
            <div class="col-lg-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Products</h4>
                    </div>
                    <div class="col-lg-12" id="productDiv">
                        <div class="table-responsive" >
                            <table id="myTable" class="table table-hover">
                                <thead>
                                <tr>
                                    <th>#Sr</th>
                                    <th scope="col">Product Code</th>
                                    <th scope="col">Product Title</th>
                                    <th scope="col">Barcode</th>
                                    <th scope="col">Shade</th>
                                    <th scope="col">Size</th>
                                    <th scope="col">Send Qty</th>
                                    <th scope="col">Received Qty</th>
                                    <th scope="col"> Add</th>
                                </tr>
                                </thead>
                                <tbody><?php $sr = 1;?>
                                @foreach($supply->supplyProducts as $p)
                                    <tr id="r{{$p->variant_id}}" style="background-color: #ffdada;">
                                        <td>{{$sr++}}</td>
                                        <td>{{$p->code}}</td>
                                        <td><b>{{$p->product->product_heading}}</b></td>
                                        <td>{{$p->variant->barcode}}</td>
                                        <td>{{$p->variant->shade}}</td>
                                        <td>{{$p->variant->size}}</td>
                                        <td >{{$p->qty}}
                                            <input type="hidden"  id="o_qty{{$p->variant_id}}" value="{{$p->qty}}">
                                        </td>

                                        <td><input type="number" @class('form-control')  name="r_qty[]" id="r_qty{{$p->variant_id}}" value="0">
                                            <br> <span>Difference : <span id="d_qty{{$p->variant_id}}" style="color:red">{{$p->qty}}</span></span>
                                        </td>

                                        <td><button class="form-control btn btn-primary" type="button" onclick="addQuantity({{$p->variant_id}})" >Add</button>
                                            <br> <span>Received : <span id="add_qty{{$p->variant_id}}" >0</span></span><br>
                                            <span id="text_qty{{$p->variant_id}}" ></span>
                                        </td>

                                        <input type="hidden" value="{{$p->variant_id}}" name="variant_ids[]">
                                        <input type="hidden" value="{{$p->product_id}}" name="product_ids[]">

                                        <input type="hidden" value="0" @class('form-control qty') name="received_qty[]" id="t_r_qty{{$p->variant_id}}">

                                    </tr>

                                @endforeach

                                </tbody>
                            </table>
                        </div>
                        <!-- table-responsive //end -->

                    </div>
                </div>
            </div>

        </form>
    </div>

@stop

@section('js')
    <script>
        $('.select2').select2();

        $(document).ready( function () {
            $('#myTable').DataTable({
                'ordering': true, 'sorting' : true, 'paging' : true,'pageLength' : 500, 'info' : false, 'searching':true
            });
        } );

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
    <script src="{{asset('js/supply.js')}}" type="text/javascript"></script>

@stop

