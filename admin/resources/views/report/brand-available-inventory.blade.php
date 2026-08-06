@extends('layouts.app')

@section('css')

    <link href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css"  rel="stylesheet">

@endsection

@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Brand Available Inventory </h2>
            <h4>{{$store_id ? $store->name : 'All Store'}}</h4>
            
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
                    <tr style="text-align: center">
                        <th>#Sr</th>
                        <th scope="col">Brand Name</th>
                        <th scope="col">Total Quantity</th>
                        <th scope="col">Total Value</th>
                  
                        <th scope="col">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                        
                       
                        
                        <?php $sr = 1;$tQty = 0; $tValue = 0;?>
                    @foreach($brands as $brand)
                        <tr style="text-align: center">
                            <td>{{$sr++}}</td>
                            <td title="{{$brand->name}}"><b>{{(strlen($brand->title) > 30) ? substr($brand->title,0,30).'...' : $brand->title}}</b></td>
                            
                            <td>{{number_format($totalQty[$brand->id])}}
                            <?php $tQty += $totalQty[$brand->id];?>
                            </td>
                            <td>{{number_format($totalValue[$brand->id])}}
                            <?php $tValue += $totalValue[$brand->id];?>
                            </td>
                            <td><a href="{{route('report.specific-brand-available-inventory',['brand_id' => $brand->id,'store_id'=>$store_id])}}" target="_blank"  class="btn btn-md">Detail</a></td>
                            
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr style="text-align: center;font-size: 14px;font-weight: 600;border:double; ">
                        <th></th>
                        <th></th>
                        
                        <th style="border-bottom: double;">{{number_format($tQty)}}</th>

                        
                        <th style="border-bottom: double;">{{number_format($tValue)}}</th>
                        
                        <th style="color: #00B517; border-bottom: double;"></th>


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

    <script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.print.min.js"></script>

    <script>
        $(document).ready( function () {
            $('#myTable').DataTable({
                'ordering': true,'order':[[2, 'desc']], 'sorting' : true, 'paging' : false, 'info' : false, 'searching':true,
                dom: 'Bfrtip',
                buttons: [
                    'csv', 'excel', 'print'
                ]
            });
        } );

    </script>
@stop
