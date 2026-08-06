@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Couriers</h2>
            <p>courier fair information.</p>
        </div>
        @can('Create Couriers')
        <div>
            <a  href="{{route('couriers.create')}}" class="btn btn-primary"><i class="text-muted material-icons md-post_add"></i>Add New</a>
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
                        <th scope="col">Representative</th>
                        <th scope="col">Phone Number</th>
                        <th scope="col">Rate</th>
                        <th scope="col">Minimum Weight Allow</th>
                        <th scope="col">Extra Charges Above</th>
                        <th scope="col">Total Areas</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($couriers as $courier)

                            <tr style="text-align: center;">

                                <td>{{$sr++}}</td>
                                <td><b>{{$courier->name}}</b></td>
                                <td>{{$courier->representative_name}}</td>
                                <td>{{$courier->phone_number}}</td>
                                <td>{{$courier->rate}}</td>
                                <td>{{$courier->allow_weight_gm_ml}} (GM/ML)</td>
                                <td>{{$courier->extra_charges_above_weight}} Per ( 1000 GM/ML )</td>
                                <td><b>{{ $courier->areas ? count($courier->areas) : 0}}</b></td>

                                <td><span class="badge rounded-pill {{($courier->status) ? 'alert-success' : 'alert-danger'}}">{{($courier->status) ? 'Active' : 'InActive'}}</span></td>

                                <td class="text-end">
                                    @can('View Couriers')
                                        <a href="{{route('couriers.show',$courier->id)}}" class="btn btn-md rounded font-sm">Detail</a>
                                    @endcan

                                        <div class="dropdown">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                        <div class="dropdown-menu">
                                            @can('Edit Couriers')
                                            <a class="dropdown-item" href="{{route('couriers.edit',$courier->id)}}">Edit info</a>
                                            @endcan

                                            @can('Delete Couriers')
                                            <form onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('couriers.destroy',$courier->id) }}" method="POST">
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
