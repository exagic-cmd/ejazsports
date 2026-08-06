@extends('layouts.app')

@section('css')

    <link href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css"  rel="stylesheet">

@endsection

@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Out of Stock Products Report</h2>
            <p>Brand :  <b> {{$brand->title}} </b></p>
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
                <table id="myTable" class="table table-striped">
                    <thead>
                    <tr style="text-align: center">
                        <th>#Sr</th>
                        <th scope="col">Product Name</th>
                        <th scope="col">Barcode</th>
                        <th scope="col">Shade</th>
                        <th scope="col">Size</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Dis. Amount</th>
                        <th scope="col">Created at</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>

                        @foreach($products as $product)
                                @foreach($product->variants as $v)
                                    @if($loop->first)
                                    <tr style="text-align: center">
                                        <td>{{$sr++}}</td>
                                        <td><b>{{$product->title}}</b></td>
                                        <td>{{$v->barcode}}</td>
                                        <td>{{$v->shade ? $v->shade : '-'}}</td>
                                        <td>{{$v->size ? $v->size : '-'}}</td>
                                        <td><b>Rs.{{number_format($product->price)}}<b></td>
                                        <td><b>Rs.{{number_format($product->price - $product->discount_amount)}}</b></td>
                                        <td>{{date('d-m-Y h:i',strtotime($product->created_at))}}</td>
                                    </tr>
                                    @else
                                        <tr style="text-align: center">
                                            <td></td>
                                            <td> // </td>
                                            <td>{{$v->barcode}}</td>
                                            <td>{{$v->shade ? $v->shade : '-'}}</td>
                                            <td>{{$v->size ? $v->size : '-'}}</td>
                                            <td> //</td>
                                            <td> //</td>
                                        </tr>

                                    @endif
                                @endforeach
                        @endforeach
                    </tbody>

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
                'ordering': false, 'sorting' : false, 'paging' : false, 'info' : false, 'searching':true,
                dom: 'Bfrtip',
                buttons: [
                    'csv', 'excel', 'print'
                ]
            });
        } );

    </script>
@stop
