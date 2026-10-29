        <div class="table-responsive">
            <table id="myTable" class="table table-hover table-striped ">
                <thead>
                    <tr style="text-align: center;">
                        {{-- <th><input type="checkbox" name="select_all"  ></th>--}}
                        <th>#Sr</th>
                        <th scope="col">Order #</th>
                        <th scope="col">Name</th>
                        <th scope="col">Phone Number</th>
                        <th scope="col">Date</th>

                        <th scope="col">Payment Method</th>
                         <th scope="col">Website Order</th>
                        <th scope="col">Total Amount</th>
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody><?php $sr = $allOrders->firstItem() ?? 1; ?>
                    @foreach($allOrders as $order)

                    @if($order->additional_notes)
                    <tr style="text-align: center;background-color:#ffcaca;">
                        @else
                    <tr style="text-align: center;">
                        @endif
                        {{-- <td><input type="checkbox" class="pending" name="pen_order" value="{{$order->id}}"></td>--}}
                        <td>{{$sr++}}</td>
                        <td><b>{{$order->order_no}}</b>

                        </td>
                        <td>
                            {{$order->name}}
                        </td>
                        <td>
                            {{$order->phone_number}}
                        </td>


                        <td>{{date('d M, Y',strtotime($order->created_at))}}</td>

                        <td>
                            @if($order->payment_method == \App\Models\Order::CASH)
                            CASH
                            @elseif($order->payment_method == \App\Models\Order::ONLINE)
                            Credit
                            @endif
                        </td>

                     <td>
                        <b>{{ $order->is_website_order ? 'Yes' : 'No' }}</b>
                    </td>

                        <td><b>{{number_format($order->total_amount)}}</b>
                        </td>

                        <td class="text-end">

                            <a target="_blank" href="{{route('orders.show',$order->id)}}" class="btn btn-md rounded font-sm">Detail</a>


                            <div class="dropdown">
                                <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                <div class="dropdown-menu">

                                    <a class="dropdown-item" href="{{route('orders.edit',$order->id)}}">Edit info</a>

                                </div>
                            </div>
                            <!-- dropdown //end -->
                        </td>

                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>

        <br>
        <label>Showing {{$allOrders->firstItem() ?? 0}} to {{$allOrders->lastItem() ?? 0}} of {{$allOrders->total()}} records</label>
        <br>

        <div class="pagination-area mt-30 mb-50">
            <nav aria-label="Page navigation example">
                {{$allOrders->links()}}
            </nav>
        </div>

        <!-- table-responsive //end -->
