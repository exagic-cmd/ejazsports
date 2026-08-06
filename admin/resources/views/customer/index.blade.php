@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Customers</h2>
            <p>customer information.</p>
        </div>
        <div>
            @can('Create Customer')
                <a  href="{{route('customers.create')}}" class="btn btn-primary"><i class="text-muted material-icons md-post_add"></i>Add New</a>
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
        
        <header class="card-header">
                <div class="row align-items-center">
                    
                    <div class="col-lg-6 col-12 me-auto mb-3">
                        <lable>Search Filter</lable>
                        <input type="text" id="searchbox" placeholder="Search By Name,Phone Number ..." class="form-control">

                    </div>

                  
                  
                    
                    <div class="col-md-2 col-12 me-auto mb-md-0 mb-3">
                        <button type="button" class="form-control btn btn-primary search" >Search</button>
                    </div>
                </div>
            </header>

        <!-- card-header end// -->
        <div class="card-body" id="update-table">
            <div class="table-responsive" >
                <table id="myTable" class="table table-hover">
                    <thead>
                    <tr>
                        <th>#Sr</th>
                        <th>Name</th>
                        <!--<th scope="col">Email</th>-->
                        <th scope="col">Phone Number</th>
                        <!--<th scope="col">Address</th>-->
                        <td>Total Orders</td>
                        <td>Order Amount</td>
                        <!--<th scope="col">Store</th>-->
                        <th scope="col"> Balance</th>
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($customers as $customer)

                        <tr>

                            <td>{{$sr++}}</td>
                            <td><b>{{$customer->first_name}} {{$customer->last_name}}</b></td>
                            <!--<td>{{$customer->email}}</td>-->
                            <td>{{$customer->phone_number}}</td>
                            <td>{{$totalOrders[$customer->id]}}</td>
                            <td>{{number_format($orderAmount[$customer->id])}}</td>
                            <!--<td>{{$customer->address}} {{$customer->area ? $customer->area->name : ''}}</td>-->
                            <!--<td>{{$customer->store ? $customer->store->name : ''}}</td>-->
                            <td>{{number_format($balance[$customer->id])}}</td>

                             <td class="text-end">
                                
                                  @can('View Customer')
                                    <a href="{{route('customers.show',$customer->id)}}" class="btn btn-md rounded font-sm">Detail</a>
                                @endcan
                              
                                <div class="dropdown">
                                    <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                    <div class="dropdown-menu">
                                        @can('Edit Customer')
                                            <a class="dropdown-item" href="{{route('customers.edit',$customer->id)}}">Edit info</a>
                                        @endcan

                                        @can('Delete Customer')
                                            <form onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('customers.destroy',$customer->id) }}" method="POST">
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
                'ordering': false, 'sorting' : false, 'paging' : false,'pageLength' : 50, 'info' : false, 'searching':false
            });
        } );

        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 1000);
        
        
        $(document).on('click','.search',function(e) {

 
        searchbox = $('#searchbox').val();

        if(searchbox) {

                        $.ajax({
                            url: "{{route('customer.search')}}",
                            type: 'GET',
                            data: {searchbox:searchbox},
                            success: function (data) {
                                document.getElementById('update-table').innerHTML = data;
                              
                                
                                $('#myTable').DataTable({
                'ordering': false, 'sorting' : false, 'paging' : false,'pageLength' : 50, 'info' : false, 'searching':false
            });
                            }
                        });
                  
             
        }
        else {
            toastr.warning('Please enter any thing!');
        }
    });
    </script>
@stop
