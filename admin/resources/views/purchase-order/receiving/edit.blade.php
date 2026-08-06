@extends('layouts.app')


@section('content')

    <div class="row">
        <div class="col-12">
            <div class="content-header">
                <h2 class="content-title">Update Receiving</h2>
                <div>
                    <button onclick="setFormSubmitting();document.getElementById('form').submit()" class="btn btn-md rounded font-sm hover-up">Update</button>
                </div>
            </div>
        </div>
        <form action="{{route('receiving.update',$receiving->id)}}" method="post" id="form" enctype="multipart/form-data" autocomplete="false" style="display: contents">
            @csrf
            @method('PUT')
            <div class="col-lg-9">
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
                                    <label for="product_name" class="form-label">Supplier id</label>
                                    <select id="supplier_id" class="form-control select2 @error('name') is-invalid @enderror" name="supplier_id">
                                        <option value="">None</option>
                                        @foreach($suppliers as $s)
                                            @if($receiving->supplier_id == $s->id)
                                            <option selected value="{{$s->id}}">{{$s->name}}</option>
                                            @else
                                                <option value="{{$s->id}}">{{$s->name}}
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
                                    <input  type="date" id="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{date('Y-m-d',strtotime($receiving->date))}}" >
                                    @error('date')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>


                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Invoice #</label>
                                    <input  type="text" name="invoice_no" class="form-control @error('invoice_no') is-invalid @enderror" value="{{$receiving->invoice_no}}" >
                                    @error('invoice_no')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!--<div class="col-lg-6">-->
                            <!--    <div class="mb-4">-->
                            <!--        <label for="product_name" class="form-label">Payment Method</label>-->
                            <!--        <select class="form-control select2" name="payment_method">-->
                            <!--            <option {{ ($receiving->payment_method == \App\Models\Receiving::CASH ) ?  'selected' : ''}} value="{{\App\Models\Receiving::CASH}}">Cash</option>-->
                            <!--            <option {{ ($receiving->payment_method == \App\Models\Receiving::CREDIT ) ?  'selected' : ''}} value="{{\App\Models\Receiving::CREDIT}}">Credit</option>-->
                            <!--            <option {{ ($receiving->payment_method == \App\Models\Receiving::SALE_BASIS ) ?  'selected' : ''}} value="{{\App\Models\Receiving::SALE_BASIS}}">Sale Basis</option>-->
                            <!--        </select>-->
                            <!--        @error('payment_method')-->
                            <!--        <div class="alert alert-danger">{{ $message }}</div>-->
                            <!--        @enderror-->
                            <!--    </div>-->
                            <!--</div>-->
                        </div>

                        <input type="hidden" name="payment_method" value="{{\App\Models\Receiving::CREDIT}}">

                        <div @class('row')>

                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Comment</label>
                                    <textarea name="comment" class="form-control" cols="5">{{$receiving->comment}}</textarea>
                                    @error('comment')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>



                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Received At</label>
                                    <select class="form-control select2 @error('store_id') is-invalid @enderror" name="store_id">
                                        @foreach($stores as $s)
                                            @if($receiving->store_id == $s->id)
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

                            <div class="col-lg-3">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Gross Amount</label>
                                    <input style="cursor: not-allowed" type="number" readonly id="gross_amount" name="gross_amount" class="form-control @error('gross_amount') is-invalid @enderror" value="{{$receiving->gross_amount}}" >
                                    @error('gross_amount')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                               <div class="col-lg-3">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Packing Charges</label>
                                    <input  type="number" onkeyup="updateTotal()" id="packing_charges" name="packing_charges" class="form-control @error('packing_charges') is-invalid @enderror" value="{{$receiving->packing_charges}}" >
                                    @error('packing_charges')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-3 d-none">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Tax</label>
                                    <input style="cursor: not-allowed" type="number" readonly  id="g_tax" name="g_tax" class="form-control @error('g_tax') is-invalid @enderror" value="{{$receiving->tax}}" >
                                    @error('tax')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Discount Amount</label>
                                    <input style="cursor: not-allowed" type="number" readonly id="g_discount" name="g_discount" class="form-control @error('g_discount') is-invalid @enderror" value="{{$receiving->discount}}" >
                                    @error('discount')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Net Amount</label>
                                    <input style="cursor: not-allowed" type="number" readonly id="net_amount" name="net_amount" class="form-control @error('net_amount') is-invalid @enderror" value="{{$receiving->net_amount}}" >
                                    @error('net_amount')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Total Products</label>
                                    <input style="cursor: not-allowed" type="number" readonly id="total_products" name="total_products" class="form-control" value="{{$receiving->total_products}}" >
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Total Quantity</label>
                                    <input style="cursor: not-allowed" type="number" readonly  id="total_qty" name="total_qty"  class="form-control" value="{{$receiving->total_qty}}">

                                </div>
                            </div>

                            <div @class('clearFix')></div>
                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Documents</label>
                                    <input  type="file" name="file[]"  class="form-control" multiple >

                                </div>
                            </div>

                        </div>

                    </div>
                </div>

            </div>

            <aside class="col-lg-3 card" id="PODiv" style="padding: 20px;">
                <div class="box bg-light" style="min-height: 80%">
                    <h6 class="mt-15">Purchase Order Details</h6>

                    <hr>
                    <h6 class="mb-0">Supplier :</h6>
                    <p>{{ $receiving->purchaseOrder?->supplier?->name ?? $receiving->supplier?->name ?? '' }}</p>

                    <h6 class="mb-0">Shipment At :</h6>
                    <p>{{$receiving->receivedStore ? $receiving->receivedStore->name : ''}}</p>

                    <h6 class="mb-0">Date :</h6>
                    <p>{{date('d-m-Y',strtotime($receiving->date))}}</p>

                    <h6 class="mb-0">Total Products :</h6>
                    <p>{{$receiving->total_products}} </p>

                    <h6 class="mb-0">Total Quantity :</h6>
                    <p>{{$receiving->total_product_qty}}</p>

                    <h6 class="mb-0">Created By :</h6>
                    <p>{{$receiving->createdBy ? $receiving->createdBy->name : ''}}</p>

                    <h6 class="mb-0">Approved By :</h6>
                    <p>{{$receiving->approvedBy ? $receiving->approvedBy->name : ''}}</p>


                </div>

            </aside>

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

                                    <th scope="col">Product Title</th>

                                    <th scope="col">Shade</th>
                                    <th scope="col">Size</th>
                                    <th scope="col">Order Qty</th>
                                    <th scope="col">Received Qty</th>

                                    <th scope="col">Purchase Price</th>
                                    <th scope="col">Total P.Price</th>
                                    <th scope="col">T. Discount</th>
                                    <th scope="col">T. GST</th>
                                    <th scope="col">Total Net Amount</th>
                                    <th scope="col">Wholesale Price</th>
                                    <th scope="col">Dz Price</th>


                                </tr>
                                </thead>
                                <tbody><?php $sr = 1;?>
                                @foreach($receiving->products as $p)
                                    <tr id="r{{$p->id}}" >
                                        <td>{{$sr++}}</td>

                                        <td><b>{{$p->product->title}}</b></td>

                                        <td>{{$p->variant ? $p->variant->shade : ''}}</td>
                                        <td>{{$p->variant ? $p->variant->size : ''}}</td>
                                        <td >{{$p->quantity}}
                                            <input type="hidden"  id="o_qty{{$p->id}}" value="{{$p->qty}}">
                                        </td>

                                        <td><input type="number" @class('form-control qty') onkeyup="updateDiff({{$p->id}})" name="r_qty[]" id="r_qty{{$p->id}}" value="{{$p->qty}}" >
                                            <br> <span>Difference : <span id="d_qty{{$p->id}}" style="color:red">{{$p->quantity}}</span></span>
                                        </td>
                                        <td><input type="number" @class('form-control') name="t_price[]" onkeyup="updateTradePrice({{$p->id}})" id="t_price{{$p->id}}" value="{{$p->trade_price}}">

                                        @if($lPrice[$p->id]  != 0)
                                            <br> <span>Last : <span id="l_price{{$p->id}}" style="color:green;">{{$lPrice[$p->id]}}</span></span>
                                         @else
                                            @if($p->variant)
                                            <span>Last : <span id="l_price{{$p->id}}" style="color:green;">{{$p->variant->purchase_price}}</span></span>

                                            @else
                                            <span>Last : <span id="l_price{{$p->id}}" style="color:green;">{{$p->product->purchase_price}}</span></span>
                                            @endif

                                            @endif

                                        </td>
                                        <td><input type="number" @class('form-control total-trade-price') name="total_t_price[]" onkeyup="updateNetPrice({{$p->id}})" id="total_t_price{{$p->id}}" value="{{$p->qty * $p->trade_price}}"> </td>
                                        <td><input type="number" @class('form-control total-discount') name="discount[]" onkeyup="updateNetPrice({{$p->id}})" id="discount{{$p->id}}" value="{{$p->qty * $p->discount}}"> <br>
                                            <input type="number" @class('form-control') onkeyup="updateDiscount({{$p->id}})" id="discount_p{{$p->id}}" value="{{round(($p->discount / ($p->trade_price ?  $p->trade_price : 1) ) * 100)}}"> %</td>
                                        <td><input type="number" @class('form-control total-gst') name="gst[]" onkeyup="updateNetPrice({{$p->id}})" id="gst{{$p->id}}" value="{{$p->qty * $p->gst}}"> </td>

                                        <td><input type="number" @class('form-control net-price') name="total_net_price[]"  id="total_net_price{{$p->id}}" value="{{ ($p->trade_price - $p->discount + $p->gst ) * $p->qty }}"> </td>

                                        <td><input type="number" @class('form-control ') name="retail_price[]"   value="{{$p->sale_price}}"> </td>

                                        <td><input type="number" @class('form-control ') name="dz_price[]"   value="{{$p->dz_price}}"> </td>

                                        <input type="hidden" value="{{$p->product_variant_id}}" name="variant_ids[]">
                                        <input type="hidden" value="{{$p->product_id}}" name="product_ids[]">
                                        <input type="hidden" value="0" name="p_tps[]">
                                        <input type="hidden" value="{{$p->po_product}}" name="po_product[]">

                                        <input type="hidden" value="0" name="product[]">

                                    </tr>


                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!--<button style="margin-bottom: 30px;" class="btn btn-primary " type="button" onclick="addNewProduct()">Add Product</button>-->

        </form>
    </div>



@stop

@section('js')
    <script>
        $('.select2').select2();

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

        var config = {
            routes: {
                po: "{{ route('receiving.po.detail') }}",
                product : "{{ route('receiving.po.product.detail') }}",
                addProduct : "{{route('receiving.add.product')}}"
            }
        };


        function updateTotal() {
            packing_charges = parseInt($('#packing_charges').val());
            gross_amount = parseInt($('#gross_amount').val());

            $('#net_amount').val(packing_charges + gross_amount);
        }


    </script>
    <script src="{{asset('js/po.js?v=1.2')}}" type="text/javascript"></script>

@stop

