@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Order Scanning</h2>
        </div>
        <div>
            <a href="{{route('orders.scan.new')}}" target="_blank" class="btn btn-primary"><i class="text-muted material-icons md-post_add"></i>New Scan</a>
        </div>

    </div>

    @if(session()->has('message'))
        <div class="alert alert-success text-center">
            {{ session()->get('message') }}
        </div>
    @endif

    <div class="alert alert-success alert-div text-center" style="display: none;">

    </div>

    <div class="card mb-4">



        <!-- card-header end// -->
        <div class="card-body" id="order-info" >
            <div class="table-responsive" style="display: flex" >
                <h5 style="margin-top: 10px;">Order Scan here...</h5>  <input style="margin: 0px 30px 0px 30px;width: 30%" type="text" class="form-control" name="order_scan" id="cn_no"> <button class="btn btn-primary" onclick="showOrderInfo()">Add</button>
            </div>

        </div>
        <!-- card-body end// -->
    </div>





@stop


@section('js')
    <script>
        $(document).ready( function () {
            $('#myTable').DataTable({
                'ordering': false, 'sorting' : false, 'paging' : true,'pageLength' : 50, 'info' : false, 'searching':true
            });
        } );

        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 1000);

        function showOrderInfo() {

            var cn_no  = $("#cn_no").val(); //Fields wrapper

            if(cn_no == null || cn_no == undefined || cn_no == '')
            {
                toastr.error('Please Enter CN NO...');
                return false;
            }

            $.ajax({
                url: "{{route('orders.info')}}",
                type: 'GET',
                data: {cn_no: cn_no},
                success: function (data) {
                    if(data.success == false)
                        toastr.error(data.message);
                    else
                        document.getElementById('order-info').innerHTML = data;

                }
            });
        }

        function scanProduct() {
            var barcode = $('#barcode').val();
            var order_id = $('#order_id').val();

            if(barcode == null || barcode == undefined || barcode == '')
            {
                toastr.error('Please Enter Barcode...');
                return false;
            }
            $.ajax({
                url: "{{route('orders.product.scan')}}",
                type: 'GET',
                data: {barcode: barcode, order_id:order_id},
                success: function (data) {
                    if(data.success == false)
                        toastr.error(data.message);
                    else {
                        qid = '#scanned_qty' + data.data.variant_id;
                        sid = '#scanned_status' + data.data.variant_id;

                        alreadtScanned = parseInt($(qid).val());

                        if(alreadtScanned == data.data.qty)
                            toastr.error('Product Already Scanned.');
                        else {
                            $(qid).val(alreadtScanned + 1);

                            if(alreadtScanned + 1 == data.data.qty) {
                                $(sid).val('YES');
                                document.getElementById('scanned_qty'+ data.data.variant_id).style.backgroundColor = 'green';
                                document.getElementById('scanned_status'+ data.data.variant_id).style.backgroundColor = 'green';
                            }
                            toastr.success(data.message);
                            checkStatus();
                        }

                    }


                }
            });
            $('#barcode').val('');
        }
        function checkStatus() {
            var status = document.getElementsByClassName("status");

            for(var i = 0; i < status.length; i++)
            {
                if(status[i].value == 'NO'){
                    return false;
                }
            }

            document.getElementById('save_id').style.cursor = 'pointer';
            $('#save_id').removeAttr("disabled");

            return true;
        }

        function completeScan() {

            result = checkStatus();
            if(result == false)
                return false;
            var order_id  = $("#order_id").val(); //Fields wrapper

            $.ajax({
                url: "{{route('orders.scan.complete')}}",
                type: 'POST',
                data: {order_id: order_id},
                success: function (data) {
                    if(data.success == false)
                        toastr.error(data.message);
                    else
                        toastr.success(data.message);
                    setTimeout(function(){
                        window.location.reload(1);
                    }, 2000);

                }
            });

        }


    </script>
@stop
