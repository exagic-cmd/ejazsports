@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Employees</h2>
            <p>employees information.</p>
        </div>
        @can('Create Employee')
        <div>
            <a  href="{{route('employees.create')}}" class="btn btn-primary"><i class="text-muted material-icons md-post_add"></i>Add New</a>
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
                        <th>Full Name</th>
                        <th scope="col">Mobile Number</th>
                        <th scope="col">Com Per Retail</th>
                        <th scope="col">Com Per Whole</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($employees as $employee)

                            <tr>

                                <td>{{$sr++}}</td>
                                
                                <td><b>{{$employee->name}}</b></td>
                                <td>{{$employee->mobile_number}}</td>
                                <td>{{$employee->com_per_retail}} %</td>
                                <td>{{$employee->com_per_whole}} %</td>
                                <td><span class="badge rounded-pill {{($employee->status) ? 'alert-success' : 'alert-danger'}}">{{($employee->status) ? 'Active' : 'InActive'}}</span></td>



                                <td class="text-end">
                                    
                                     @can('View Employee')
                                    <a href="{{route('employees.show',$employee->id)}}" class="btn btn-md rounded font-sm">Detail</a>
                                @endcan

                                        <div class="dropdown">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                        <div class="dropdown-menu">
                                            @can('Edit Employee')
                                            <a class="dropdown-item" href="{{route('employees.edit',$employee->id)}}">Edit info</a>
                                            @endcan

                                            @can('Delete Employee')
                                            <form onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('employees.destroy',$employee->id) }}" method="POST">
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
