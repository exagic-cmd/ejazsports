@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Booked Orders</h2>
            <p>Order Information {{number_format($orders->sum('total_amount'))}} - ({{count($orders)}})</p>
        </div>


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
                        <th><input type="checkbox" name="pen_order"  ></th>
                        <th>#Sr</th>
                        <th scope="col">Order #</th>
                        <th scope="col">Name</th>
                        <th scope="col">Phone Number</th>
                        <th scope="col">Address</th>
                        <th scope="col">Date</th>
                        <th scope="col">Booking Time</th>
                        <th scope="col">Payment Method</th>

                        <th scope="col">Total Amount</th>
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($orders as $order)

                        <tr style="text-align: center;">
                            <td><input type="checkbox" class="pending" name="pen_order[{{ $order->id }}]" value="{{$order->id}}"></td>
                            <td>{{$sr++}}</td>
                            <td><b>VEGAS{{$order->order_no}}</b>

                            <br>
                                <span style="color: royalblue">{{$order->cn_no}}</span>
                                <br>
                                <b style="color: green">{{$order->courier ? $order->courier->name : ''}}</b>
                            </td>
                            <td>
                                {{$order->name}}
                            </td>
                            <td>
                                {{$order->phone_number}}
                            </td>
                            <td>
                                {{$order->address}}<br>
                                <b>{{$order->area ? $order->area->name : ''}}</b>
                            </td>

                            <td>{{date('d M, Y',strtotime($order->created_at))}}</td>
                            <td>{{$order->booking_time ? date('d M, Y h:i') : ''}}</td>

                            <td>
                                @if($order->payment_method == \App\Models\Order::CASH)
                                    CASH
                                @elseif($order->payment_method == \App\Models\Order::ONLINE)
                                    ONLINE
                                @endif
                            </td>

                            <td><b>{{number_format($order->total_amount)}}</b></td>


                            <td class="text-end">
                                @can('View Orders')
                                    <a href="{{route('orders.show',$order->id)}}" class="btn btn-md rounded font-sm">Detail</a>
                                @endcan

                                <div class="dropdown">
                                    <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                    <div class="dropdown-menu">
                                        @can('Un Booked Order')
                                            <a class="dropdown-item" onclick="unBooked({{$order->id}})" >Un Booked</a>
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

        $(document).ready(function() {
            $('[name="pen_order"]').on('click', function() {

                if($(this).is(':checked')) {
                    $.each($('.pending'), function() {
                        $(this).prop('checked',true);
                    });
                } else {
                    $.each($('.pending'), function() {
                        $(this).prop('checked',false);
                    });
                }

            });
        });

        function unBooked(order_id) {

            $.confirm({
                title: 'Order Un Booked!',
                content: 'Are you sure you want to do this!',
                buttons: {
                    confirm: function () {
                        $.ajax({
                            url: "{{ route('orders.unbooked') }}",
                            type:'GET',
                            data: {order_id:order_id},
                            success: function(data) {
                                if(data.status == true) {
                                toastr.success('Order Unbooked Successfully.');
                                setTimeout(function(){
                                    window.location.reload(1);
                                }, 2000);
                                }
                                else
                                    toastr.error('Something went wrong');

                            }
                        });
                    },
                    cancel: function () {

                    }
                }
            });

        }
    </script>
@stop
