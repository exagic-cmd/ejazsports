@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Stores</h2>
            <p>store information.</p>
        </div>
        @can('Create Supplier')
            <div>
                <a  href="{{route('stores.create')}}" class="btn btn-primary"><i class="text-muted material-icons md-post_add"></i>Add New</a>
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
                        <th scope="col">Created At</th>
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($stores as $store)

                        <tr>

                            <td>{{$sr++}}</td>
                            <td><b>{{$store->name}}</b></td>
                            <td>{{$store->phone_number}}</td>
                       

                            <td><span class="badge rounded-pill {{($store->status) ? 'alert-success' : 'alert-danger'}}">{{($store->status) ? 'Active' : 'InActive'}}</span></td>

                            <td>{{date('M d, Y',strtotime($store->created_at))}}</td>

                            <td class="text-end">
                                @can('View Store')
                                    <a href="{{route('stores.show',$store->id)}}" class="btn btn-md rounded font-sm">Detail</a>
                                @endcan
                                <div class="dropdown">
                                    <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                    <div class="dropdown-menu">
                                        @can('Edit Store')
                                            <a class="dropdown-item" href="{{route('stores.edit',$store->id)}}">Edit info</a>
                                        @endcan

                                        @can('Delete Store')
                                            <form onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('stores.destroy',$store->id) }}" method="POST">
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
