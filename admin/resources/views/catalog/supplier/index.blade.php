@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Suppliers</h2>
            <p>supplier information.</p>
        </div>
        @can('Create Supplier')
            <div>
                <a  href="{{route('suppliers.create')}}" class="btn btn-primary"><i class="text-muted material-icons md-post_add"></i>Add New</a>
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
                        <th scope="col">Phone Number</th>
                        <th scope="col">Status</th>
                        <th scope="col">Balance</th>
                        <th scope="col">Created At</th>
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($suppliers as $supplier)

                        <tr>

                            <td>{{$sr++}}</td>
                            <td><b>{{$supplier->name}}</b></td>
                            <td>{{$supplier->mobile_number}}</td>
                      
                            <td><span class="badge rounded-pill {{($supplier->status) ? 'alert-success' : 'alert-danger'}}">{{($supplier->status) ? 'Active' : 'InActive'}}</span></td>
                            <td>{{number_format($balance[$supplier->id])}}</td>

                            <td>{{date('M d, Y',strtotime($supplier->created_at))}}</td>

                            <td class="text-end">
                                @can('View Brand')
                                    <a href="{{route('suppliers.show',$supplier->id)}}" class="btn btn-md rounded font-sm">Detail</a>
                                @endcan
                                <div class="dropdown">
                                    <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                    <div class="dropdown-menu">
                                        @can('Edit Supplier')
                                            <a class="dropdown-item" href="{{route('suppliers.edit',$supplier->id)}}">Edit info</a>
                                        @endcan

                                        @can('Delete Supplier')
                                            <form onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('suppliers.destroy',$supplier->id) }}" method="POST">
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
                'ordering': false, 'sorting' : false, 'paging' : true,'pageLength' : 50, 'info' : false, 'searching':true
            });
        } );

        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 1000);
    </script>
@stop
