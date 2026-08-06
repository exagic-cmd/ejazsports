@extends('layouts.app')

@section('content')

    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Colors</h2>
            <p>color information.</p>
        </div>
        @can('Create Product')
        <div>
            <a href="{{route('colors.create')}}" class="btn btn-primary"><i class="text-muted material-icons md-post_add"></i>Add New</a>
        </div>
        @endcan
    </div>

    @if(session()->has('message'))
        <div class="alert alert-success text-center">
            {{ session()->get('message') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table id="myTable" class="table table-hover">
                    <thead>
                    <tr>
                        <th>#Sr</th>
                        <th scope="col">Name</th>
                        <th scope="col" class="text-end">Action</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($colors as $color)
                            <tr>
                                <td>{{$sr++}}</td>
                                <td><b>{{$color->name}}</b></td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                        <div class="dropdown-menu">
                                            @can('Edit Product')
                                            <a class="dropdown-item" href="{{route('colors.edit', $color->id)}}">Edit info</a>
                                            @endcan
                                            @can('Delete Product')
                                            <form onsubmit="return confirm('Do you really want to do this?');" action="{{ route('colors.destroy', $color->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="dropdown-item text-danger" type="submit">Delete</button>
                                            </form>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
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
