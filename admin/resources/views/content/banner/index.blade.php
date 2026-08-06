@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Banners</h2>
            <p>banner information.</p>
        </div>
        @can('Create Banner')
        <div>
            <a  href="{{route('banners.create')}}" class="btn btn-primary"><i class="text-muted material-icons md-post_add"></i>Add New</a>
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
                        <th scope="col">Web Banner</th>
                        <th scope="col">Order #</th>
                        <th scope="col">Web Heading</th>
                        <th scope="col">Web Sub Heading</th>
                        <th scope="col">Status</th>
                        <th scope="col">Created At</th>
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($banners as $banner)

                            <tr>

                                <td>{{$sr++}}</td>
                                <td>@if($banner->web_image)
                                        <img src="{{asset('storage/'.$banner->web_image)}}" class="img-sm img-thumbnail" alt="Item">
                                    @else
                                        <img src ="{{asset('storage/default.jpeg')}}" class="img-sm img-thumbnail" alt="Item">

                                    @endif</td>
                                <td>{{$banner->serial_no}}</td>
                                <td>{{$banner->web_heading}}</td>
                                <td>{{$banner->web_sub_heading}}</td>
                                <td><span class="badge rounded-pill {{($banner->status) ? 'alert-success' : 'alert-danger'}}">{{($banner->status) ? 'Active' : 'InActive'}}</span></td>

                                <td>{{date('M d, Y',strtotime($banner->created_at))}}</td>

                                <td class="text-end">
                                    @can('View Banner')
                                    <a href="{{route('banners.show',$banner->id)}}" class="btn btn-md rounded font-sm">Detail</a>
                                    @endcan
                                        <div class="dropdown">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                        <div class="dropdown-menu">
                                            @can('Edit Banner')
                                            <a class="dropdown-item" href="{{route('banners.edit',$banner->id)}}">Edit info</a>
                                            @endcan

                                            @can('Delete Banner')
                                            <form onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('banners.destroy',$banner->id) }}" method="POST">
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
