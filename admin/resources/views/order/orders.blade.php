@extends('layouts.app')

@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('content')


<div class="content-header">
    <div>
        <h2 class="content-title card-title">All Orders</h2>
        <p>Order Information <b> ({{number_format($orderCount)}}) </b></p>
    </div>

</div>

@if(session()->has('message'))
<div class="alert alert-success text-center">
    {{ session()->get('message') }}
</div>
@endif

<div class="alert alert-success alert-div text-center" style="display: none;">

</div>

<header class="card card-body mb-4">
    <div class="row gx-3">
        <div class="col-lg-3 col-md-6 me-auto">
            <label style="font-weight: 700">Order No</label>
            <input type="text" id="order" placeholder="Search..." class="form-control">

        </div>
       
        <div class="col-lg-3 col-md-6 me-auto">
            <label style="font-weight: 700">Name</label>
            <input type="text" id="cname" placeholder="Search..." class="form-control">

        </div>
        <div class="col-lg-3 col-md-6 me-auto">
            <label style="font-weight: 700">Phone No</label>
            <input type="text" id="phone" placeholder="Search..." class="form-control">

        </div>
        <div class="col-lg-3 col-md-6 me-auto" style="margin-top: 25px;">
            <label style="font-weight: 700">Email</label>
            <input type="text" id="email" placeholder="Search..." class="form-control">

        </div>

        <div class="col-lg-3 col-md-6" style="margin-top: 25px;">
            <label style="font-weight: 700">Date Range</label>
            <input type="text" class="form-control " id="daterange-btn" value='{{old('date_range')}}' name='date_range'>

        </div>

        

        <div class="col-lg-2 col-6 col-md-3" style="margin-top: 25px;">
            <label style="font-weight: 700">Payment</label>

            <select class="form-select" name="payment" id="payment">
                <option value="">All</option>
                <option value="{{\App\Models\Order::CASH}}">CASH</option>
                <option value="{{\App\Models\Order::ONLINE}}">Credit</option>
                
                
                                    
            </select>
        </div>


        <div class="col-lg-12 col-6 col-md-3" style="text-align: center">

            <button class="btn btn-success-light mt-20 search" style="width: 30%">Search</button>
        </div>
    </div>
</header>

<div class="card mb-4">

    <!-- card-header end// -->
    <div class="card-body" id="update-table">
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
                <tbody><?php $sr = 1; ?>
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
                        <td>
                            @if($order->website_order)
                            Yes
                            @else
                            No
                            @endif
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
        <label>Total Records {{$allOrders->count()}} of {{$allOrders->total()}}</label>
        <br>

        <div class="pagination-area mt-30 mb-50">
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-start">
                    {{$allOrders->links()}}
                </ul>
            </nav>
        </div>

        <!-- table-responsive //end -->
    </div>
    <!-- card-body end// -->
</div>

<div class="modal fade" id="prModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" style="z-index:9999" aria-hidden="true">

</div>

@stop


@section('js')
<script>
    $(document).ready(function() {
        $('#myTable').DataTable({
            'ordering': false,
            'sorting': false,
            'paging': false,
            'pageLength': 500,
            'info': false,
            'searching': true
        });
    });

</script>

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $('#daterange-btn').daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'This Year': [moment().startOf('year'), moment()],
                'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
                'All Time': [moment().subtract(5, 'year').startOf('year'), moment()]
            },
            startDate: moment().subtract(3, 'month').startOf('month'),
            endDate: moment()
        },
        function(start, end) {
            $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }
    );

    $(document).on('click', '.search', function(e) {

        document.getElementById('update-table').style.opacity = '.1';

        date_range = $('#daterange-btn').val();
       
       
        payment = $('#payment').val();
        order = $('#order').val();
       
        phone = $('#phone').val();
        email = $('#email').val();
        name = $('#cname').val();

        if (date_range || payment || order) {

            $.ajax({
                url: "{{route('orders.search')}}",
                type: 'GET',
                data: {
                    date_range: date_range,
                    payment: payment,
                    order: order,
                    phone: phone,
                    email: email,
                    name: name
                },
                success: function(data) {
                    document.getElementById('update-table').innerHTML = data;
                    $('#myTable').DataTable({
                        'ordering': false,
                        'sorting': false,
                        'paging': false,
                        'pageLength': 500,
                        'info': false,
                        'searching': true
                    });
                    document.getElementById('update-table').style.opacity = '1';
                }
            });
        } else {
            toastr.warning('Please select any option');
            document.getElementById('update-table').style.opacity = '1';
        }


    });
    
    
</script>
@stop