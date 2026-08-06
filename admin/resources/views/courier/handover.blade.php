@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All HandOver List</h2>
            <p>courier orders information.</p>
        </div>
        @can('Create Couriers')
            <div>
                <a  href="{{route('orders.dispatch.new')}}" class="btn btn-primary"><i class="text-muted material-icons md-post_add"></i>Add New</a>
            </div>
        @endcan

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
                        <th scope="col">Courier</th>
                        <th scope="col">Date</th>
                        <th scope="col">Total Orders</th>
                        <th scope="col">Total Amount</th>
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($handovers as $handover)

                        <tr style="text-align: center;">

                            <td>{{$sr++}}</td>
                            <td><b>{{$handover->courier->name}}</b></td>
                            <td>{{date('d M,Y h:i',strtotime($handover->created_at))}}</td>
                            <td>{{$handover->total_orders}}</td>
                            <td>{{number_format($handover->total_amount)}}</td>

                            <td class="text-end">

                                @can('Courier Handover Detail')
                                    <a href="{{route('couriers.handover.detail',$handover->id)}}" class="btn btn-md rounded font-sm">Detail</a>
                                @endcan
                            </td>

                        </tr>
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
    <script>
        $(document).ready( function () {
            $('#myTable').DataTable({
                'ordering': true, 'sorting' : false, 'paging' : true, 'info' : true, 'searching':true
            });
        } );

        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 1000);
    </script>
@stop
