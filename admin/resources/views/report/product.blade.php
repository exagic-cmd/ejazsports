@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Products Report</h2>
            <p>Date Range :  <b> {{date('d M, Y',strtotime($from))}} - {{date('d M, Y',strtotime($to))}} </b></p>
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
        <div class="card-body" id="update-table">
            <div class="table-responsive" >
                <table id="myTable" class="table table-hover">
                    <thead>
                    <tr>
                        <th>#Sr</th>
                        <th scope="col">Product Name</th>
                        <th scope="col">Sold Quantity</th>
                        <th scope="col">Current Remaining</th>
                        <th scope="col">Sale Amount</th>
                        <th scope="col">Cost Amount</th>
                        <th scope="col">Gross Profit</th>
                        <th scope="col">Contribution %</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($products as $product)
                        <tr style="text-align: center">
                            <td>{{$sr++}}</td>
                            <td title="{{$product->product->product_heading}}">{{(strlen($product->product->product_heading) > 30) ? substr($product->product->product_heading,0,30).'...' : $product->product->product_heading}}</td>
                            <td >{{$product->tqty}}</td>
                            <td>{{$product->product->available_stock}}</td>
                            <td >{{number_format($product->sale_price)}}</td>
                            <td >{{number_format($product->cost_price)}}</td>
                            <td ><b>{{number_format($product->sale_price - $product->cost_price)}}</b></td>
                            <td><b>{{ round(( $product->sale_price / $products->sum('sale_price') ) * 100 )}} %</b></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                    <tr style="text-align: center;font-size: 14px;font-weight: 600;border:double; ">
                        <th></th>
                        <th></th>
                        <th style="border-bottom: double;">{{$products->sum('tqty')}}</th>
                        <th></th>
                        <th style="border-bottom: double;">{{ number_format($products->sum('sale_price'))}}</th>
                        <th style="color: #d93030;border-bottom: double;">{{ number_format($products->sum('cost_price'))}}</th>
                        <th style="color: #00B517; border-bottom: double;">{{ number_format($products->sum('sale_price') - $products->sum('cost_price'))}}</th>
                        <th style="color: #00B517; border-bottom: double;">100 %</th>


                    </tr>
                    </tfoot>
                </table>
            </div>
            <!-- table-responsive //end -->
        </div>
        <!-- card-body end// -->
    </div>





@stop


@section('js')
    <script>
        $(document).ready( function () {
            $('#myTable').DataTable({
                'ordering': false, 'sorting' : false, 'paging' : false, 'info' : false, 'searching':true
            });
        } );

    </script>
@stop
