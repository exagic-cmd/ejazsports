@extends('layouts.app')


@section('content')


        <div class="content-header">
            <div>
                <h2 class="content-title card-title">All Accounts</h2>
                <p>User account information.</p>
                <a  href="{{route('accounts.create')}}" class="btn btn-primary">Add New</a>

            </div>

            <div>
                <a  href="{{route('roles.index')}}" class="btn btn-primary"><i class="text-muted material-icons md-post_add"></i>Manage Roles</a>

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
                            <th scope="col">Email</th>
                            <th scope="col">Roles</th>
                            <th scope="col">Store</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody><?php $sr = 1;?>
                        @foreach($allAccounts as $account)
                        <tr>
                            <td>{{$sr++}}</td>
                            <td><b>{{$account->name}}</b></td>
                            <td>{{$account->email}}</td>

                            <td>
                                @foreach($account->roles as $role)
                                    <span class="badge bg-primary">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td>{{$account->store ? $account->store->name : ''}}</td>

                            <td><span class="badge rounded-pill {{($account->status) ? 'alert-success' : 'alert-danger'}}">{{($account->status) ? 'Active' : 'InActive'}}</span></td>


                            <td class="text-end">
                                <div class="dropdown">
                                    <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{route('accounts.edit',$account->id)}}">Edit info</a>

                                        <form onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('accounts.destroy',$account->id) }}" method="POST">
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
