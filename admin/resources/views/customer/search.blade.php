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