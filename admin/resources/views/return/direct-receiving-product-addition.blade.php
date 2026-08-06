@extends('layouts.app')


@section('content')

    <div class="row">
        <div class="col-9">
            <div class="content-header">
                <h2 class="content-title">Return Stock</h2>
                <div>
                    <h5><strong>Cargo #</strong>: {{$receiving->cargo_no}}<br></h5>
                    <h5><strong>Store </strong>: {{$receiving->receivedStore->name}}</h5>



                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Products Search</h4>
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
                        
                       
                        
                        <i class="fa fa-search"></i>

                                <input autofocus type="text" id="nav-search" placeholder="Search product by Name, SKU" class="form-control search-field">
                             
                    </div>
                    
                    <div id="result"  style="height: 900px; overflow-y: scroll;">
                        
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-3" style="display: none;" id="product-section">
            <form method="post" action="{{route('receiving.direct.product.submit')}}">
                @csrf

                <div id="product-div">

                </div>


                <input type="hidden" name="receiving_id" value="{{$receiving->id}}">

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
                            <th>Sr#</th>
                            <th>DESCRIPTION</th>
                            <th>Shade</th>
                            <th>Size</th>
                            <th>QTY</th>
                            <th>UNIT PRICE</th>
                            <th>Discount</th>
                            <th>Tax</th>
                            <th>TOTAL</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = 1;$tp = 0;?>
                        @foreach($receiving->products->reverse() as $p)
                            <tr>
                                <td>{{$sr++}}</td>
                                
                                <td>{{$p->product ? $p->product->title : ''}}</td>
                                
                                <td>{{$p->variant ? $p->variant->shade : ''}}</td>
                                <td>{{$p->variant ? $p->variant->size : ''}}</td>
                                <td>{{$p->qty}}</td>
                                <td>{{number_format($p->trade_price)}}</td>
                                <td>{{number_format($p->discount)}}</td>
                                <td>{{number_format($p->gst)}}</td>
                                <td>{{number_format(($p->trade_price - $p->discount + $p->gst ) * $p->qty)}}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="4"></td>
                            <td ><b style=" border-bottom: 4px double;">{{number_format($receiving->total_qty)}}</b></td>
                            <td ><b style=" border-bottom: 4px double;">{{number_format($receiving->gross_amount)}}</b></td>
                            <td ><b style=" border-bottom: 4px double;">{{number_format($receiving->discount)}}</b></td>
                            <td ><b style=" border-bottom: 4px double;">{{number_format($receiving->tax)}}</b></td>
                            <td ><b style=" border-bottom: 4px double;">{{number_format($receiving->net_amount)}}</b></td>
                        </tr>
                        <tbody>


                    </table>


                </div>
            </div>

        </div>

    </div>



@stop

@section('js')

    <script>

       //Search Field
$(".search-field").keyup(function(){
    query = $('#nav-search').val();
    
    let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    if(query.length > 2) {
        // document.getElementById('wait').style.display='block';
        document.getElementById('app').style.opacity = '0.1';
        let url = '{{route("receiving.direct.product.search")}}';
        fetch(url, {
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json, text-plain, */*",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": token
            },
            method: 'post',
            credentials: "same-origin",
            body: JSON.stringify({
                val: query

            })
        })
            .then(function (response) {
                return response.text();
            })
            .then(function (html) {
                
                document.getElementById('result').innerHTML = html;
                //  document.getElementById('wait').style.display='none';
                document.getElementById('app').style.opacity = '1';

            })
            .catch(function(error) {
                alert(error);
                //   document.getElementById('wait').style.display='none';
                document.getElementById('app').style.opacity = '1';
            });
    }
    else
    {
        document.getElementById('result').innerHTML = '';
    }
});

        


        $(document).ready( function () {
            $('#myTable').DataTable({
                'ordering': false, 'sorting' : false, 'paging' : false, 'info' : false, 'searching':true
            });
        } );

        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 1000);
        
        
        
        function addToProduct(proId,vId) {
            alert(5);
            
             document.getElementById('app').style.opacity = '0.1';
            
             $.ajax({
                    url: "{{route('receiving.direct.product.scan')}}",
                    type: 'GET',
                    data: {pId: proId,vId:vId,receiving_id:{{$receiving->id}}},
                    success: function (data) {
                        if(data.success == false) {
                            toastr.error(data.message);
                             document.getElementById('app').style.opacity = '1';
                        }
                        else {
                            // console.log(data.data);

                            document.getElementById('product-div').innerHTML = data;

                            document.getElementById('product-section').style.display = 'block';

                            $('#in_hand_qty').text('');

                            $('#in_hand_qty').focus();
                            
                             document.getElementById('app').style.opacity = '1';
                        }

                    }
                });
            
        }

        



    </script>


@endsection



