@extends('layouts.app')


@section('css')

    <style>
        a {
            color: #0060B6;
            text-decoration: none;
        }
        a:hover {
            color: #00A0C6;
            text-decoration: none;
            cursor: pointer;
        }
        .tooltip-toggle {
            cursor: pointer;
            position: relative;
        }
        .tooltip-toggle::before {
            position: absolute;
            top: -80px;
            left: -80px;
            background-color: green;
            border-radius: 5px;
            color: #fff;
            content: attr(data-tooltip);
            padding: 1rem;
            text-transform: none;
            -webkit-transition: all 0.5s ease;
            transition: all 0.5s ease;
            width: 300px;
        }
        .tooltip-toggle::after {
            position: absolute;
            top: -12px;
            left: 9px;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 5px solid green;
            content: " ";
            font-size: 0;
            line-height: 0;
            margin-left: -5px;
            width: 0;
        }
        .tooltip-toggle::before,
        .tooltip-toggle::after {
            color: #efefef;
            font-family: monospace;
            font-size: 16px;
            opacity: 0;
            pointer-events: none;
            text-align: left;
        }
        .tooltip-toggle:hover::before,
        .tooltip-toggle:hover::after {
            opacity: 1;
            -webkit-transition: all 0.75s ease;
            transition: all 0.75s ease;
        }
    </style>
    @stop

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
                    <form action="{{route('purchase-orders.store')}}" method="post" id="form" autocomplete="false">
                        @csrf

                        <div @class('row')>

                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Brand</label>
                                    <select  class="form-control select2 @error('name') is-invalid @enderror" name="brand_id">
                                        <option value="{{$brand->id}}">{{$brand->title}}</option>
                                    </select>


                                    @error('supplier_id')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

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

                        <input type="hidden"  name="s_p_qty[]" id="s_p_qty" value="0">
                        <input type="hidden"  name="s_variant_ids[]">
                        <input type="hidden"  name="s_product_ids[]">
                        <input type="hidden"  name="s_p_tps[]">
                    </form>
                    <hr>
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
                                    <th scope="col">Available Qty</th>
                                    <th scope="col">Average</th>
                                    <th scope="col">Total Sold</th>
                                    <th scope="col">Last Purchase Price</th>
                                    <th scope="col">Purchase Qty</th>
                                </tr>
                                </thead>
                                <tbody><?php $sr = 1;?>

                                @foreach($products as $p)

                                    @foreach($p->variants as $v)
                                            <tr>
                                                <td>{{$sr++}}</td>
                                                <td>{{$p->code}}</td>

                                                <td><b>{{$p->title}}</b></td>

                                            <td>{{$v->barcode}}</td>
                                                <td>{{$v->shade}}</td>
                                                <td>{{$v->size}}</td>


                                                <td >
                                                @if($v->available_stock > 0)

                                                    <?php $temp  = '';?>
                                                    @foreach($stores as $s)

                                                       <?php $temp = $temp . ' ' . $s->name . ' : ' . $storeVariants[$s->id][$v->id]  ;?>


                                                        @endforeach

                                                        <span class="tooltip-toggle" data-html="true" data-tooltip="<?php echo $temp; ?>">
											<h5>{{number_format($v->available_stock)}}</h5>
										</span>
                                                    @else
                                                        <h5>{{number_format($v->available_stock)}}</h5>
                                                    @endif
                                                </td>
                                                <td>{{$average[$v->id]}}</td>
                                                <td>{{number_format($totalSold[$v->id])}}</td>
                                                <td>{{number_format($lastPurchasePrice[$p->id])}}</td>
                                                <td><input type="number" @class('form-control') name="p_qty[]" id="p_qty" value="0"> </td>
                                                <input type="hidden" value="{{$v->id}}" name="variant_ids[]">
                                                <input type="hidden" value="{{$p->id}}" name="product_ids[]">
                                                <input type="hidden" value="{{$lastPurchasePrice[$p->id]}}" name="p_tps[]">
                                            </tr>
                                        @endforeach
                                    @endforeach


                                </tbody>
                            </table>
                        </div>
                        <!-- table-responsive //end -->

                    </div>






                </div>
            </div>

        </div>




    </div>



@stop

@section('js')
    <script>
        $('.select2').select2();

        $(document).ready( function () {
            $('#myTable').DataTable({
                'order': [6,'ASC'], 'sorting' : false, 'paging' : false, 'info' : false, 'searching':true
            });
        } );


    </script>

    <script>
        var formSubmitting = false;
        var setFormSubmitting = function() {


            var pQty = $("input[name='p_qty[]']")
                .map(function(){return $(this).val();}).get();

            var variantIds = $("input[name='variant_ids[]']")
                .map(function(){return $(this).val();}).get();

            var productIds = $("input[name='product_ids[]']")
                .map(function(){return $(this).val();}).get();

            var pTps = $("input[name='p_tps[]']")
                .map(function(){return $(this).val();}).get();


            var sQty = [];
            var sVId = [];
            var sProId = [];
            var sPT = [];

            for(i = 0; i < pQty.length; i++) {

                if(parseInt(pQty[i]) > 0) {
                    sQty.push(parseInt(pQty[i]));
                    sVId.push(parseInt(variantIds[i]));
                    sProId.push(parseInt(productIds[i]));
                    sPT.push(parseFloat(pTps[i]));

                }
            }

            $('[name="s_p_qty[]"]').val(  sQty  );

            $('[name="s_variant_ids[]"]').val(  sVId  );

            $('[name="s_product_ids[]"]').val(  sProId  );

            $('[name="s_p_tps[]"]').val(  sPT  );






            formSubmitting = true;
        };

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

