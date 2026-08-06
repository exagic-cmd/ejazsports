<div class="table-responsive" style="display: flex" >
    <h5 style="margin-top: 10px;">Order Scan here...</h5>  <input style="margin: 0px 30px 0px 30px;width: 30%" type="text" class="form-control" name="order_scan" id="cn_no"> <button class="btn btn-primary" onclick="addScan()">Add</button>
</div>

<br>
<div class="card">
    <header class="card-header">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-6 mb-lg-0 mb-15">
                <span> <i class="material-icons md-calendar_today"></i> <b>Total Amount : {{number_format($totalAmount)}} </b> </span> <br>
                <small class="text-muted">Total Orders : {{count($orderArray)}}</small>
            </div>

        </div>
    </header>
    <!-- card-header end// -->
    <div class="card-body">
        <!-- row // -->
        <div class="row">
            <div class="col-lg-10">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th width="40%">Sr #</th>
                            <th width="40%">Cn No</th>
                            <th width="20%">Order No</th>
                            <th width="20%">Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = 1;?>
                        @foreach($orderArray as $order)
                            <tr>
                                <td>{{$sr++}}</td>
                                <td>{{$order->cn_no}}</td>
                                <td>VEGAS{{$order->order_no}}</td>
                                <td>{{number_format($order->total_amount)}}</td>
                            </tr>
                        @endforeach


                        </tbody>
                    </table>
                </div>
                <!-- table-responsive// -->
            </div>
            <!-- col// -->
            <div class="col-lg-1"></div>

            <!-- col// -->
        </div>


    </div>
    <!-- card-body end// -->
</div>
