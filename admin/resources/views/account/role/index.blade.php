@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Roles</h2>
            <p>Role information.</p>
            <a  href="{{route('roles.create')}}" class="btn btn-primary">Add New</a>
        </div>

        <div>
            <a  href="{{route('permissions.index')}}" class="btn btn-primary"><i class="text-muted material-icons md-post_add"></i>Manage Permissions</a>

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
                        <th scope="col">Permissions</th>
                        <th scope="col" class="text-end">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $sr = 1;?>
                    @foreach($allRoles as $role)
                        <tr>
                            <td>{{$sr++}}</td>
                            <td>{{$role->name}}</td>
                            <td>
                                <div style="max-width: 650px;height: 60px;line-height: 2;overflow-y: scroll;">
                                @foreach($role->permissions as $permission)
                                    <span class="badge bg-primary">{{ $permission->name }}</span>
                                @endforeach
                                </div>
                            </td>

                            <td class="text-end">
                                <div class="dropdown">
                                    <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{route('roles.edit',$role->id)}}">Edit info</a>

                                        <form onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('roles.destroy',$role->id) }}" method="POST">
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
                'paging' : false,
                'info' : false
            });
        } );

        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 1000);
    </script>


@stop

