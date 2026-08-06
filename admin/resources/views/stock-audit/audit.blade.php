@extends('layouts.app')


@section('content')

    <div class="row">
        <div class="col-9">
            <div class="content-header">
                <h2 class="content-title">Stock Audit</h2>
                <div>
                    <h5><strong>Brand </strong>: {{$stockAudit->brand->title}}<br></h5>
                    <h5><strong>Store </strong>: {{$stockAudit->storeId->name}}</h5>


                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Products Scan</h4>
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
                    <div class="mb-4">
                        <label for="product_name" class="form-label">Product Title </label>
                        <input  type="text" name="scan" id="scan" placeholder="Scan here" class="form-control" >
                    </div>
                    
                     <div class="card-body" style="padding: 0.5rem;">
                    <div class="mb-1">
                        <button type="button" onclick="searchItem()" class="btn btn-facebook" >Product Search</button>
                    </div>
                </div>
                    
                    <div class="mb-4">
                        <label for="product_name" class="form-label">Variant Barcode </label>
                        <input  type="text" name="scan1" id="scan1" placeholder="Scan here" class="form-control" >
                    </div>
                    
                    <div class="card-body" style="padding: 0.5rem;">
                    <div class="mb-1">
                        <button type="button" onclick="searchItem1()" class="btn btn-facebook" >Variant Search</button>
                    </div>
                </div>
                </div>
            </div>

        </div>

        <div class="col-lg-8" style="display: none;" id="product-section">
            <form method="post" action="{{route('stock-audits.update',$stockAudit->id)}}">
                @csrf
                @method('PUT')
                <div id="product-div">

                </div>


                <input type="hidden" name="audit_id" value="{{$stockAudit->id}}">

                <div class="card-body" style="padding: 0.5rem;">
                    <div class="mb-1">
                        <button type="submit" class="btn btn-facebook" >Submit</button>
                    </div>
                </div>

            </form>
        </div>


        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Products List</h4>
                </div>

                <div class="card-body">

                        <table id="myTable" class="table table-striped">
                            <thead>
                            <tr>
                                <th scope="col">Sr #</th>
                                <th scope="col">Product Name</th>
                                <th scope="col">Variant</th>
                                <th scope="col">System Qty</th>
                                <th scope="col">In Hand Qty</th>
                                <th scope="col">Difference Qty</th>
                                <th scope="col">Adjust in Stock</th>
                                <th scope="col">Adjust in Damage</th>
                                <th scope="col">Adjust in Expiry</th>
                                <th scope="col">Adjust in Missing</th>
                                <th scope="col">Adjust in Tester</th>
                                <th scope="col">Reason</th>
                            </tr>
                            </thead>
                            <tbody>
                                
                                <?php $sr = 1;?>

                            @foreach($stockAudit->products->reverse() as $p)
                                <tr>
                                    <td>{{$sr++}}</td>
                                    <td>{{$p->product->title}}</td>
                                    <td>{{$p->variant ? $p->variant->barcode : ''}}</td>
                                    <td>{{$p->system_qty}}</td>
                                    <td>{{$p->in_hand_qty}}</td>
                                    <td>{{$p->difference_qty}}</td>
                                    <td>{{$p->adjust_in_stock}}</td>
                                    <td>{{$p->adjust_in_damage}}</td>
                                    <td>{{$p->adjust_in_expiry}}</td>
                                    <td>{{$p->adjust_in_missing}}</td>
                                     <td>{{$p->adjust_in_tester}}</td>
                                     <td>{{$p->reason}}</td>

                                </tr>
                                @endforeach


                            </tbody>


                        </table>


                </div>
            </div>

        </div>

    </div>



@stop

@section('js')

    <script>
    
    $('#scan').focus();
    
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

        var scan = document.getElementById("scan");
        scan.addEventListener("keydown", function (e) {
            
            setFormSubmitting();
            if (e.code === "Enter") {  //checks whether the pressed key is "Enter"
                barcode = $('#scan').val();
                //$('#scan').val('');

                $.ajax({
                    url: "{{route('stock-audit.product.scan')}}",
                    type: 'GET',
                    data: {barcode: barcode,brand_id:{{$stockAudit->brand_id}},store_id:{{$stockAudit->store_id}}},
                    success: function (data) {
                        if(data.success == false)
                            toastr.error(data.message);
                        else {
                            // console.log(data.data);

                            document.getElementById('product-div').innerHTML = data;

                            document.getElementById('product-section').style.display = 'block';
                            
                            $('#in_hand_qty').text('');
                            
                            $('#in_hand_qty').focus();
                        }

                    }
                });

            }
        });

        $(document).on('keyup', '#in_hand_qty', function(ev){

            availableQty = parseInt($('#available_qty').val());
            inHandQty = parseInt($('#in_hand_qty').val());

            diff = inHandQty - availableQty;

            $('#difference_qty').val(diff);
        });

        function updateDiff() {


            adjustInStock = parseInt($('#adjust_in_stock').val());
            adjustInDamage = parseInt($('#adjust_in_damage').val());
            adjustInExpiry = parseInt($('#adjust_in_expiry').val());
            adjustInMissing = parseInt($('#adjust_in_missing').val());
         

            availableQty = parseInt($('#available_qty').val());
            inHandQty = parseInt($('#in_hand_qty').val());

            diff = inHandQty - availableQty;

            remaining = diff - adjustInStock - adjustInDamage - adjustInExpiry + adjustInMissing;

            $('#difference_qty').val(remaining);
        }
        
        function searchItem() {
               barcode = $('#scan').val();
              

                $.ajax({
                    url: "{{route('stock-audit.product.scan')}}",
                    type: 'GET',
                    data: {barcode: barcode,brand_id:{{$stockAudit->brand_id}},store_id:{{$stockAudit->store_id}}},
                    success: function (data) {
                        if(data.success == false)
                            toastr.error(data.message);
                        else {
                            // console.log(data.data);

                            document.getElementById('product-div').innerHTML = data;

                            document.getElementById('product-section').style.display = 'block';
                            
                            $('#in_hand_qty').text('');
                            
                            $('#in_hand_qty').focus();
                        }

                    }
                });
        }
        
        
        function searchItem1() {
              
               barcode1 = $('#scan1').val();
                //$('#scan').val('');

                $.ajax({
                    url: "{{route('stock-audit.variant.scan')}}",
                    type: 'GET',
                    data: {barcode1: barcode1,brand_id:{{$stockAudit->brand_id}},store_id:{{$stockAudit->store_id}}},
                    success: function (data) {
                        if(data.success == false)
                            toastr.error(data.message);
                        else {
                            // console.log(data.data);

                            document.getElementById('product-div').innerHTML = data;

                            document.getElementById('product-section').style.display = 'block';
                            
                            $('#in_hand_qty').text('');
                            
                            $('#in_hand_qty').focus();
                        }

                    }
                });
        }

    </script>


@endsection



