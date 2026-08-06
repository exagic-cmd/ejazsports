@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Areas / Locations</h2>
            <p>area fair information.</p>
        </div>
        @can('Create Areas')
        <div>
            <a  href="{{route('areas.create')}}" class="btn btn-primary"><i class="text-muted material-icons md-post_add"></i>Add New</a>
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
                        <th scope="col">Name</th>
                        <th scope="col">Serial #</th>
                        <th scope="col">Delivery Charges</th>
                        <th scope="col">Minimum Order Amount</th>
                        <th scope="col">Charges Above Min. Amount</th>
                        <th scope="col">Minimum Weight Allow (G/ML)</th>
                        <th scope="col">Extra Charges Per (1000 G/1000 ML) </th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($areas as $area)

                            <tr>

                                <td>{{$sr++}}</td>
                                <td><b>{{$area->name}}</b></td>
                                <td>{{$area->serial_no}}</td>
                                <td>{{$area->delivery_charges}}</td>
                                <td>{{$area->min_order_amount}}</td>
                                <td>{{$area->delivery_charges_above}}</td>
                                <td>{{$area->min_weight_allow}} g/ml</td>
                                <td>{{$area->extra_charges_per_g_ml}} (1000)</td>

                                <td><span class="badge rounded-pill {{($area->status) ? 'alert-success' : 'alert-danger'}}">{{($area->status) ? 'Active' : 'InActive'}}</span></td>

                                <td class="text-end">

                                        <div class="dropdown">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                        <div class="dropdown-menu">
                                            @can('Edit Areas')
                                            <a class="dropdown-item" href="{{route('areas.edit',$area->id)}}">Edit info</a>
                                            @endcan

                                            @can('Delete Areas')
                                            <form onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('areas.destroy',$area->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')

                                                <button class="dropdown-item text-danger" onclick="return confirm('Are you sure?')"  type="submit">Delete</button>
                                            </form>
                                                @endcan
                                        </div>
                                    </div>
                                    <!-- dropdown //end -->
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
