@extends('layouts.app')


@section('css')
    <style>
        .select2-search__field {
            min-width:400px!important;
        }

        #myUL {
            /* list-style-type: revert; */
            margin: 0;
            background-color: white;
            /* width: 100%; */
            width: 547px;
            overflow-y: scroll;
            max-height: 250px;
            border: 1px solid #bce3c9;
            position: absolute;
            z-index: 999;
            display: none;
        }
        #myUL li a {
            margin-top: -1px;
            padding: 6px;
            text-decoration: none;
            font-size: 16px;
            color: black;
            display: block;
        }
    </style>

@stop

@section('content')

    <div class="row">
        <div class="col-9">
            <div class="content-header">
                <h2 class="content-title">Edit Order VEGAS-{{$order->order_no}}</h2>
                <div>
                    {{--                        <button class="btn btn-light rounded font-sm mr-5 text-body hover-up">Save to draft</button>--}}
                    <button onclick="setFormSubmitting();document.getElementById('form').submit()" class="btn btn-md rounded font-sm hover-up">Update</button>
                </div>
            </div>
        </div>
        <div class="col-9">
            <div class="card">
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row gx-5">
                        <aside class="col-lg-3 border-end">
                            <nav class="nav nav-pills flex-column mb-4">

                                <a class="nav-link a-general active"  onclick="updateDiv('general')" aria-current="page" href="#">Customer Ino</a>
                                <a class="nav-link a-variant " onclick="updateDiv('variant')" href="#">Products</a>
                                <a class="nav-link a-price" onclick="updateDiv('price')" href="#price">Pricing</a>

                            </nav>
                        </aside>


                        <div class="col-lg-9">
                            <form action="{{route('orders.update',$order->id)}}" method="post" id="form" autocomplete="false" enctype="multipart/form-data">

                                @csrf
                                @method('PUT')
                                <section class="content-body p-xl-4" id="d-general">

                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Customer name <span style="color: red;">*</span></label>
                                        <div class="col-lg-9">
                                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ $order->name }}" placeholder="Type here">
                                            @error('name')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>
                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Customer Number <span style="color: red;">*</span></label>
                                        <div class="col-lg-9">
                                            <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ $order->phone_number }}" placeholder="Type here">
                                            @error('phone_number')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>
                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Email <span style="color: red;">*</span></label>
                                        <div class="col-lg-9">
                                            <input type="text" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ $order->email }}" placeholder="Type here">
                                            @error('email')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>
                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Address <span style="color: red;">*</span></label>
                                        <div class="col-lg-9">
                                            <textarea cols="5" class="form-control @error('address') is-invalid @enderror" name="address">{{$order->address}}</textarea>

                                            @error('address')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>

                                    <!-- row.// -->
                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Area / City</label>
                                        <div class="col-lg-9">

                                            <select  size="4" name="city" class="form-control select2 @error('city') is-invalid @enderror">
                                                @foreach($areas as $a)
                                                    @if($a->id == $order->city)
                                                    <option selected value="{{$a->id}}">{{$a->name}}</option>
                                                    @else
                                                        <option value="{{$a->id}}">{{$a->name}}</option>
                                                        @endif
                                                @endforeach
                                            </select>
                                            @error('brand_id')
                                            <div @class('alert alert-danger')>{{$message}}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>


                                    <!-- row.// -->
                                    <br>
                                    <button class="btn btn-primary" type="button" onclick="updateDiv('variant')">Continue to next</button>

                                </section>

                                <section class="content-body p-xl-4 d-none" id="d-variant" >

                                    <input type="text" class="form-control " onclick="document.getElementById('myUL').style.display = 'block';" id="main-search" placeholder="Search for items..." />
                                    <ul id="myUL">

                                    </ul>
                                    <button type="button" style="float: right!important;" class="btn btn-primary add_field_button" >Add Product</button>
                                    <div class="input_fields_wrap">
                                        <div @class('clearfix')></div><br>



                                           @foreach($order->products as $oP)
                                            <div>

                                            <div class="row mb-4">
                                                <label class="col-lg-3 col-form-label">Product Name</label>

                                                <div class="col-lg-9">
                                                    <select name="product_id[]" class="form-control">
                                                        <option selected value="{{$oP->product_id}}">{{$oP->product->product_heading}}</option>
                                                    </select>


                                                </div>
                                                <!-- col.// -->
                                            </div>

                                                <div class="row mb-4">
                                                    <label class="col-lg-3 col-form-label">Product Variant</label>

                                                    <div class="col-lg-9">
                                                        <select class="form-control select2" name="variant_id[]">
                                                            @foreach($variants[$oP->product_id] as $var)
                                                                @if($oP->variant_id == $var->id)
                                                                <option selected value="{{$var->id}}">{{$var->barcode}} - {{$var->shade}} - {{$var->size}}</option>
                                                                @else
                                                                @if($var->online_available_stock > 0)
                                                                        <option value="{{$var->id}}">{{$var->barcode}} - {{$var->shade}} - {{$var->size}}</option>
                                                                    @else
                                                                        <option style="cursor: not-allowed" disabled value="{{$var->id}}">{{$var->barcode}} - {{$var->shade}} - {{$var->size}}</option>
                                                                    @endif

                                                                @endif

                                                                @endforeach

                                                        </select>
                                                        @error('variant_id')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <!-- col.// -->
                                                </div>

                                                <div class="row mb-4">
                                                    <label class="col-lg-3 col-form-label">Price</label>

                                                    <div class="col-lg-9">
                                                        <input type="numeric" readonly value="{{$oP->price}}" name="price[]" class="form-control price" placeholder="Type here">
                                                        @error('price')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <!-- col.// -->
                                                </div>


                                                <div class="row mb-4">
                                                    <label class="col-lg-3 col-form-label">Quantity</label>

                                                    <div class="col-lg-9">
                                                        <input type="number" value="{{$oP->qty}}" name="quantity[]" class="form-control quantity" placeholder="Type here">
                                                        @error('quantity')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <!-- col.// -->
                                                </div>

                                        <a href="#" class="remove_field">Remove</a>
                                            <br><br>
                                            </div>
                                        @endforeach

                                    </div>

                                    <br>
                                    <button class="btn btn-primary" type="button" onclick="updateDiv('price')">Continue to next</button>

                                </section>

                                <section class="content-body p-xl-4 d-none" id="d-price" >

                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Sub Total <span style="color: red;">*</span></label>
                                        <div class="col-lg-9">
                                            <input type="text" id="sub_total" readonly name="sub_total" class="form-control @error('sub_total') is-invalid @enderror" value="{{ $order->total_amount - $order->delivery_charges - $order->discount_amount }}" placeholder="Type here">
                                            @error('sub_total')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>

                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Delivery Charges <span style="color: red;">*</span></label>
                                        <div class="col-lg-9">
                                            <input type="integer" min="0" id="delivery_charges" name="delivery_charges" class="form-control @error('delivery_charges') is-invalid @enderror" value="{{ $order->delivery_charges}}" placeholder="Type here">
                                            @error('sub_total')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>

                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Discount Amount <span style="color: red;">*</span></label>
                                        <div class="col-lg-9">
                                            <input type="integer" min="0" id="discount_amount" name="discount_amount" class="form-control @error('discount_amount') is-invalid @enderror" value="{{$order->discount_amount }}" placeholder="Type here">
                                            @error('sub_total')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>

                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Total Amount <span style="color: red;">*</span></label>
                                        <div class="col-lg-9">
                                            <input type="text" id="total_amount" readonly name="total_amount" class="form-control @error('total_amount') is-invalid @enderror" value="{{ $order->total_amount}}" placeholder="Type here">
                                            @error('total_amount')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>



                                    <br>
                                    <button class="btn btn-primary" type="button" onclick="document.getElementById('form').submit()">Save</button>

                                </section>




                                <!-- content-body .// -->
                        </div>
                        </form>
                        <!-- col.// -->
                    </div>


                    <!-- row.// -->
                </div>
                <!-- card body end// -->
            </div>
        </div>
    </div>

@stop

@section('js')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js" ></script>
    <script>

        //update the title name in all other fields e.g meta description, keywords

        function updateDiv(val) {
            anc = '.a-' + val;
            div = '#d-' + val;
            $('.nav-link').removeClass( "active" );
            $('.content-body').addClass('d-none');

            $(anc).addClass('active');
            $(div).removeClass('d-none');
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

        $(document).on('keyup','#delivery_charges',function() {
            subTotal = parseInt($('#sub_total').val());
            deliveryCharges = parseInt($('#delivery_charges').val());
            discountAmount = parseInt($('#discount_amount').val());

            totalAmount = subTotal + deliveryCharges - discountAmount;

            $('#total_amount').val(totalAmount);


        });

        $(document).on('keyup','#discount_amount',function() {
            subTotal = parseInt($('#sub_total').val());
            deliveryCharges = parseInt($('#delivery_charges').val());
            discountAmount = parseInt($('#discount_amount').val());

            totalAmount = subTotal + deliveryCharges - discountAmount;

            $('#total_amount').val(totalAmount);


        });

        $(document).ready(function() {
            var max_fields      = 20; //maximum input boxes allowed
            var wrapper         = $(".input_fields_wrap"); //Fields wrapper
            var add_button      = $(".add_field_button"); //Add button ID

            var x = 1; //for multiple images
            $(add_button).click(function(e){ //on add input button click
                e.preventDefault();
                if(x < max_fields){ //max input box allowed
                    x++; //text box increment
                    $(wrapper).prepend('<br><br><div> <div class="row mb-4"> <label class="col-lg-3 col-form-label">Product Name</label> <div class="col-lg-9"> <select name="product_id[]" class="form-control select2"> @foreach($products as $pro)<option selected value="{{$pro->id}}">{{$pro->title}}</option>@endforeach</select> </div> </div> <div class="row mb-4"> <label class="col-lg-3 col-form-label">Product Variant</label> <div class="col-lg-9"> <select class="form-control select2" name="variant_id[]"></select> </div> </div> <div class="row mb-4"> <label class="col-lg-3 col-form-label">Quantity</label> <div class="col-lg-9"> <input type="numeric" value="{{$oP->qty}}" name="quantity[]" class="form-control " placeholder="Type here"> </div> </div> <a href="#" class="remove_field">Remove</a> <br><br>'); //add input box

                    $('.select2').select2();
                }
            });

            $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
                e.preventDefault(); $(this).parent('div').remove(); x--;
            })


        });

        function myFunctionn() {
            document.getElementById("myUL").style.display = "block";
        }

        $('#main-search').typeahead({
            hint: true,
            highlight: true,
            minLength: 3,
            display: 'title',
            source:  function (value, process) {
                return $.get('{{route('product.ajax.search')}}', { value: value }, function (data) {
                    myFunctionn();
                    $('#myUL').empty();
                    console.log(data);
                    $.each( data.filterResult, function( key, value ) {

                        if(value['online_available_stock'] <= 0)
                        $("#myUL").append("<li><a  class ='nav-search-links' disabled='' style='cursor: not-allowed' href='#'> "+ value['title'] +"  <span style='color: red'>(out-of-stock)</span></a> </li>");
                        else
                            $("#myUL").append("<li><a  class ='nav-search-links' onclick='addProduct(" + value['id'] + ")' href='#'> "+ value['title'] +" </a></li>");

                    });

                    if(data.filterResult.length == 0)
                        $("#myUL").append("<li style='text-align: center;padding: 10px;'>no result found!..</li>");

                });
            }
        });

        function addProduct(id) {
            var wrapper         = $(".input_fields_wrap"); //Fields wrapper

            document.getElementById("myUL").style.display = "none";
            $.ajax({
                url: "{{route('product.add.order')}}",
                type: 'GET',
                data: {product_id: id },
                success: function (data) {
                    $(wrapper).prepend(data);

                    $('.select2').select2();
                    var qty = document.getElementsByClassName("quantity");
                    var price = document.getElementsByClassName("price");
                    subTotal = 0;
                    for(var i = 0; i < qty.length; i++)
                    {
                        subTotal += parseInt(qty[i].value) * parseInt(price[i].value);
                    }

                    $('#sub_total').val(subTotal);
                    deliveryCharges = parseInt($('#delivery_charges').val());
                    discountAmount = parseInt($('#discount_amount').val());

                    totalAmount = subTotal + deliveryCharges - discountAmount;

                    $('#total_amount').val(totalAmount);
                }
            });

        }

        $(document).on('keyup','.quantity',function() {
            var qty = document.getElementsByClassName("quantity");
            var price = document.getElementsByClassName("price");
            subTotal = 0;
            for(var i = 0; i < qty.length; i++)
            {
                subTotal += parseInt(qty[i].value) * parseInt(price[i].value);
            }

            $('#sub_total').val(subTotal);
            deliveryCharges = parseInt($('#delivery_charges').val());
            discountAmount = parseInt($('#discount_amount').val());

            totalAmount = subTotal + deliveryCharges - discountAmount;

            $('#total_amount').val(totalAmount);
        });
    </script>

@stop
