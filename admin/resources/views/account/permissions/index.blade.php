

@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Permissions</h2>
            <p>Permissions information.</p>
        </div>

        <div>
            <a href="{{route('permissions.create')}}" class="btn btn-primary"><i class="text-muted material-icons md-post_add"></i>Add New Permission</a>
        </div>

    </div>

    @if(session()->has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
    @endif
    <div class="card mb-4">

        <!-- card-header end// -->
        <div class="card-body">
            <div class="table-responsive">

                <table id="myTable" class="table table-hover">
                    <thead>
                    <tr>
                        <th>#Sr</th>
                        <th scope="col">Name</th>
                        <th scope="col">Guard Name</th>
                        <th scope="col">Module</th>
                        <th scope="col" class="text-end">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $sr = 1;?>
                    @foreach($permissions as $permission)
                        <tr>
                            <td>{{$sr++}}</td>
                            <td>{{$permission->name}}</td>
                            <td><b>{{$permission->system_name}}</b></td>
                            <td><b>{{$permission->module}} </b> >> {{$permission->sub_module}}</td>

                            <td class="text-end">
                                <div class="dropdown">
                                    <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{route('permissions.edit',$permission->id)}}">Edit info</a>

                                        <form onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('permissions.destroy',$permission->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')

                                            <button class="dropdown-item text-danger" onclick="return confirm('Are you sure?')"  type="submit">Delete</button>


                                        </form>
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
                'ordering': false,
                'sorting' : false,
                'paging' : 100,
                'info' : false
            });
        } );

        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 1000);
    </script>


@stop


