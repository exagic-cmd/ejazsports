@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Brands Report</h2>
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
                        <th scope="col">Brand Name</th>
                        <th scope="col">Sold Quantity</th>
                        <th scope="col">Sale Amount</th>
                        <th scope="col">Cost Amount</th>
                        <th scope="col">Gross Profit</th>
                        <th scope="col">Contribution %</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($brands as $brand)
                        <tr style="text-align: center">
                            <td>{{$sr++}}</td>
                            <td title="{{$brand->name}}">{{(strlen($brand->name) > 30) ? substr($brand->name,0,30).'...' : $brand->name}}</td>
                            <td >{{$brand->sold_qty}}</td>
                            <td >{{number_format($brand->sale_price)}}</td>
                            <td >{{number_format($brand->cost_price)}}</td>
                            <td ><b>{{number_format($brand->gross_profit)}}</b></td>
                            <td><b>{{ $brand->contribution}} %</b></td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr style="text-align: center;font-size: 14px;font-weight: 600;border:double; ">
                        <th></th>
                        <th></th>
                        <th style="border-bottom: double;">{{$orderProducts->sum('tqty')}}</th>

                        <th style="border-bottom: double;">{{ number_format($orderProducts->sum('sale_price'))}}</th>
                        <th style="color: #d93030;border-bottom: double;">{{ number_format($orderProducts->sum('cost_price'))}}</th>
                        <th style="color: #00B517; border-bottom: double;">{{ number_format($orderProducts->sum('sale_price') - $orderProducts->sum('cost_price'))}}</th>
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
