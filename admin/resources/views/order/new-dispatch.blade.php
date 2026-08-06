@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Order Dispatching</h2>
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

        <div class="col-lg-6">
            <div class="card-body">
        <div class="mb-4">
            <label for="product_name" class="form-label">Courier <span style="color: red;">*</span></label>
            <select class="form-control" id="courier_id" name="courier_id">
                @foreach($couriers as $c)
                    <option value="{{$c->id}}">{{$c->name}}</option>
                @endforeach
            </select>
        </div>
        </div>
        </div>





        <!-- card-header end// -->
        <div class="card-body" id="order-info" >
            <div class="table-responsive" style="display: flex" >
                <h5 style="margin-top: 10px;">Order Scan here...</h5>  <input style="margin: 0px 30px 0px 30px;width: 30%" type="text" class="form-control" name="order_scan" id="cn_no"> <button class="btn btn-primary" onclick="addScan()">Add</button>
            </div>

        </div>
        <!-- card-body end// -->
<br>
        <div class="col-lg-6">
            <div class="card-body">
                <div class="mb-4">
                    <button  id="save_id" onclick="completeScan()" class="btn btn-success-light">Save </button>

                </div>
            </div>
        </div>

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

        var cnNos = new Array;

        function addScan() {

            var cn_no  = $("#cn_no").val(); //Fields wrapper
            var courier_id = $('#courier_id').val();

            if(cn_no == null || cn_no == undefined || cn_no == '')
            {
                toastr.error('Please Enter CN NO...');
                return false;
            }

            //add ids in array
            index = jQuery.inArray(cn_no,cnNos);
            if(index < 0)
                cnNos.push(cn_no);
            else {
                toastr.error('Order Already Scanned..');
                return false;
            }

            $.ajax({
                url: "{{route('orders.dispatch.scan')}}",
                type: 'GET',
                data: {cn_nos: cnNos,courier_id:courier_id},
                success: function (data) {
                    if(data.success == false)
                        toastr.error(data.message);
                    else
                        document.getElementById('order-info').innerHTML = data;

                }
            });
        }

        function completeScan() {

           if(cnNos.length == 0)
           {
               toastr.error('Add any Order....');
               return false;
           }
            var courier_id = $('#courier_id').val();

            $.ajax({
                url: "{{route('orders.dispatch.complete')}}",
                type: 'POST',
                data: {cn_nos:cnNos,courier_id:courier_id},
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
