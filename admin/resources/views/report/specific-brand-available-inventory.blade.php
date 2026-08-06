@extends('layouts.app')

@section('css')

    <link href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css"  rel="stylesheet">

<style>
     .table-responsive {
        overflow-x: auto !important; 
    }
</style>
@endsection



@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">{{$brand->title}} Available Inventory </h2>
            
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
                       <!--<th>Brand</th>-->
                         <th scope="col">Barcode</th>
                        
                        <th scope="col">Product Name</th>
                        <th  scope="col">Variant</th>
                        <th scope="col">MRP</th>
                        <th scope="col">Total Quantity</th>
                        <th scope="col">Total Value</th>
                        
                        @unlessrole('Supplier Portal')
                        @foreach($stores as $store)
                        <th scope="col">{{$store->name}} </th>
                        
                        @endforeach
                  @endunlessrole
                        
                    </tr>
                    </thead>
                    <tbody>
                        
                       
                        
                        <?php $sr = 1;$tQty = 0; $tValue = 0;$tS = 0;?>
                    @foreach($products as $product)
                    @foreach($product->variants as $v)
                    
                        <tr style="text-align: center">
                            <td>{{$sr++}}</td>
                            
                            <!--<td>{{$v->product ? $v->product->brand ? $v->product->brand->title : '' : ''}}</td>-->
                        
                        
                        <?php $bar = explode(",",$v ? $v->barcode: '');?>
                        <td>
                            
                            
                          @if(count($bar) >= 1)
                            @if(!strpos($bar[0],'NULL'))
                                {{$bar[0]}}
                            @endif
                        @endif
                          
                            
                            
                            </td>
                            
                            <!--<td title="{{$v->barcode}}">{{Str::limit($v->barcode, $limit = 20, $end = '...')}}</td>-->
                            
                            <td title="{{$product->title}}"><b>{{$product->title}}</b></td>
                            
                            <td ><b>{{$v->shade}} - {{$v->size}}</b></td>
                            <td>{{number_format($product->price)}}</td>
                            <td>{{number_format($totalQty[$v->id])}}
                            <?php $tQty += $totalQty[$v->id];?>
                            </td>
                            <td>{{number_format($totalValue[$v->id])}}
                            <?php $tValue += $totalValue[$v->id];?>
                            </td>
                            @unlessrole('Supplier Portal')
                            @foreach($storeQty[$v->id] as $s)
                            <td >{{$s['total_qty']}} ({{$s['total_value']}})</td>
                           
                            <?php $tS += $s['total_value'];?>
                            @endforeach
                            
                            @endunlessrole
                           
                            
                        </tr>
                        @endforeach
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr style="text-align: center;font-size: 14px;font-weight: 600;border:double; ">
                        <th></th>
                      
                        
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        
                        
                        
                        <th style="border-bottom: double;">{{number_format($tQty)}}</th>

                        
                        <th style="border-bottom: double;">{{number_format($tValue)}}</th>
                        @unlessrole('Supplier Portal')
                        @foreach($stores as $store)
                       <th style="border-bottom: double;">{{number_format($tS)}}</th>
                        @endforeach
                        @endunlessrole


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
                'ordering': true,'order':[[7, 'desc']], 'sorting' : true, 'paging' : false, 'info' : false, 'searching':true,
                dom: 'Bfrtip',
                buttons: [
                    'csv', 'excel', 'print'
                ]
            });
        } );

    </script>
@stop
