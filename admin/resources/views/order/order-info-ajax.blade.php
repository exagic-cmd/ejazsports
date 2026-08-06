<div class="table-responsive" style="display: flex" >
    <h5 style="margin-top: 10px;">Product Scan here...</h5>  <input style="margin: 0px 30px 0px 30px;width: 30%" type="text" class="form-control" name="order_scan" id="barcode"> <button class="btn btn-primary" onclick="scanProduct()">Add</button>
</div>
<input type="hidden" name="order_id" id="order_id" value="{{$order->id}}">
<br>
<div class="card">
    <header class="card-header">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-6 mb-lg-0 mb-15">
                <span> <i class="material-icons md-calendar_today"></i> <b>{{date('D,  M  d,  Y,  h:i',strtotime($order->created_at))}}</b> </span> <br>
                <small class="text-muted">Order ID: VEGAS{{$order->order_no}}</small>
            </div>

        </div>
    </header>
    <!-- card-header end// -->
    <div class="card-body">
        <div class="row mb-50 mt-20 order-info-wrap">
            <div class="col-md-4">
                <article class="icontext align-items-start">
                                    <span class="icon icon-sm rounded-circle bg-primary-light">
                                        <i class="text-primary material-icons md-person"></i>
                                    </span>
                    <div class="text">
                        <h6 class="mb-1">Customer</h6>
                        <p class="mb-1">
                            {{$order->name}}<br>
                            {{$order->email}} <br>
                            {{$order->phone_number}}
                        </p>

                    </div>
                </article>
            </div>
            <!-- col// -->
            <div class="col-md-4">
                <article class="icontext align-items-start">
                                    <span class="icon icon-sm rounded-circle bg-primary-light">
                                        <i class="text-primary material-icons md-local_shipping"></i>
                                    </span>
                    <div class="text">
                        <h6 class="mb-1">Order info</h6>
                        <p class="mb-1">
                            <b>Courier : </b>  {{$order->courier ? $order->courier->name : ''}} <br>
                            <b>CN No : </b> {{$order->cn_no}} <br>
                            <b> Booking Time : </b> {{$order->booking_time ? date('d M, Y h:i') : ''}}

                        </p>

                    </div>
                </article>
            </div>
            <!-- col// -->
            <div class="col-md-4">
                <article class="icontext align-items-start">
                                    <span class="icon icon-sm rounded-circle bg-primary-light">
                                        <i class="text-primary material-icons md-place"></i>
                                    </span>
                    <div class="text">
                        <h6 class="mb-1">Deliver to</h6>
                        <p class="mb-1">
                            City: {{$order->area->name}} <br>{{$order->address}} <br>

                        </p>

                    </div>
                </article>
            </div>

            <!-- col// -->
        </div>

        <div>
            <label>Total Products : <b>{{$order->total_products}}</b> </label>
            <label>Total Quantity : <b>{{$order->total_quantity}}</b> </label>
        </div>
        <br>
        <!-- row // -->
        <div class="row">
            <div class="col-lg-10">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th width="40%">Product</th>
                            <th width="40%">Barcode</th>
                            <th width="20%">Unit Price</th>
                            <th width="20%">Quantity</th>
                            <th width="20%" >Total</th>
                            <th width="20%">Scanned Qty</th>
                            <th width="20%">Scanned Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($order->products as $pro)
                            <tr>
                                <td>
                                    <a class="itemside" href="#">
                                        <div class="left">
                                            <img src="{{asset('storage/default.jpeg')}}" width="40" height="40" class="img-xs" alt="Item">
                                        </div>
                                        <div class="info">{{$pro->product->product_heading}} <br>
                                            <small>{{$pro->variant ? $pro->variant->shade : ''}} - {{$pro->variant ? $pro->variant->size : ''}}</small></div>
                                    </a>
                                </td>
                                <td>{{$pro->barcode}}</td>
                                <td>{{number_format($pro->price)}}</td>
                                <td>{{$pro->qty}}</td>
                                <td >{{number_format($pro->price * $pro->qty)}}</td>
                                <td><input type="text" id="scanned_qty{{$pro->variant_id}}" style="width: 75px;background-color: red;color: white" class="form-control" readonly id="scanned_qty" value="0"> </td>
                                <td><input type="text" id="scanned_status{{$pro->variant_id}}" style="width: 75px; background-color: red;color: white" class="form-control status" readonly id="scanned_status" value="NO"> </td>
                            </tr>
                        @endforeach

                        <tr>
                            <td colspan="4">
                                <article class="float-end">
                                    <dl class="dlist">
                                        <dt>Subtotal:</dt>
                                        <dd>{{number_format($order->total_amount - $order->delivery_charges + $order->discount_amount)}}</dd>
                                    </dl>
                                    <dl class="dlist">
                                        <dt>Shipping cost:</dt>
                                        <dd>{{number_format($order->delivery_charges)}}</dd>
                                    </dl>
                                    <dl class="dlist">
                                        <dt>Discount Amount:</dt>
                                        <dd>{{number_format($order->discount_amount)}}</dd>
                                    </dl>
                                    <dl class="dlist">
                                        <dt>Grand total:</dt>
                                        <dd><b class="h5">{{number_format($order->total_amount)}}</b></dd>
                                    </dl>
                                    {{--                                            <dl class="dlist">--}}
                                    {{--                                                <dt class="text-muted">Status:</dt>--}}
                                    {{--                                                <dd>--}}
                                    {{--                                                    <span class="badge rounded-pill alert-success text-success">Payment done</span>--}}
                                    {{--                                                </dd>--}}
                                    {{--                                            </dl>--}}
                                </article>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!-- table-responsive// -->
            </div>
            <!-- col// -->
            <div class="col-lg-1"></div>

            <!-- col// -->
        </div>

        <button disabled id="save_id" onclick="completeScan()" style="cursor: not-allowed" class="btn btn-success-light">Save & Continue</button>
    </div>
    <!-- card-body end// -->
</div>
