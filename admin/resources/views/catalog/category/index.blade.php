@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Categories</h2>
            <p>category information.</p>
        </div>
        @can('Create Category')
        <div>
            <a  href="{{route('categories.create')}}" class="btn btn-primary"><i class="text-muted material-icons md-post_add"></i>Add New</a>
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
                        <th scope="col">Title</th>
                        <th scope="col">Order #</th>
                        <th scope="col">Parent Categories</th>
                        <th scope="col">Discount</th>
                        <th scope="col">Status</th>
                        <th scope="col">Created At</th>
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($categories as $category)

                            <tr>

                                <td>{{$sr++}}</td>
                                <td><b>{{$category->title}}</b></td>
                                <td>{{$category->serial_no}}</td>
                                <td>@if($category->parentCategory)  @foreach($category->parentCategory as $parentCategories) @if($loop->last) <b> @if($parentCategories->parentCategory){{$parentCategories->parentCategory->title }} @endif </b> @else <b>  @if($parentCategories->parentCategory) {{$parentCategories->parentCategory->title }} </b> , @endif @endif @endforeach @endif</td>
                                <td>{{$category->discount ? $category->discount->name : ''}}</td>
                                <td><span class="badge rounded-pill {{($category->status) ? 'alert-success' : 'alert-danger'}}">{{($category->status) ? 'Active' : 'InActive'}}</span></td>

                                <td>{{date('M d, Y',strtotime($category->created_at))}}</td>

                                <td class="text-end">
                                    @can('View Category')
                                    <a href="{{route('categories.show',$category->id)}}" class="btn btn-md rounded font-sm">Detail</a>
                                    @endcan
                                        <div class="dropdown">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                        <div class="dropdown-menu">
                                            @can('Edit Category')
                                            <a class="dropdown-item" href="{{route('categories.edit',$category->id)}}">Edit info</a>
                                            @endcan

                                            @can('Delete Category')
                                            <form onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('categories.destroy',$category->id) }}" method="POST">
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
                'ordering': false, 'sorting' : false, 'paging' : false, 'info' : false, 'searching':true
            });
        } );

        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 1000);
    </script>
@stop
