@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Auto Purchase Orders</h2>
            <p>Purchase order information.</p>
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
                        <th scope="col">Out of Stock %</th>
                        <th scope="col">Sale Average</th>
                        <th scope="col" >Action</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($brands as $b)

                        <tr>

                            <td>{{$sr++}}</td>
                            <td>{{$b->title}}</td>
                            <td>{{$outOfStockPer[$b->id]}} %</td>
                            <td>{{$averageSale[$b->id]}} </td>

                             <td class="text-end">
                                <a href="{{route('purchase-orders.auto-brand-form',['id' => $b->id])}}" class="btn btn-md rounded font-sm">Create</a>

                            </td>

                        </tr>
                    @endforeach

                    </tbody>

                    
                </table>
            </div>
            <!-- table-responsive //end -->
            
            {{$brands->links()}}
        </div>
        <!-- card-body end// -->
    </div>

@stop


@section('js')
    <script>
        $(document).ready( function () {
            $('#myTable').DataTable({
                'order': [2,'DESC'], 'sorting' : false, 'paging' : false, 'info' : false, 'searching':true
            });
        } );

        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 1000);
    </script>
@stop
