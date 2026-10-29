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
    <div class="row gx-3 gy-3">
        <div class="col-lg-3 col-md-6 col-6">
            <label style="font-weight: 700">Order No</label>
            <input type="text" id="order" placeholder="Search..." class="form-control">
        </div>

        <div class="col-lg-3 col-md-6 col-6">
            <label style="font-weight: 700">Name</label>
            <input type="text" id="cname" placeholder="Search..." class="form-control">
        </div>

        <div class="col-lg-3 col-md-6 col-6">
            <label style="font-weight: 700">Phone No</label>
            <input type="text" id="phone" placeholder="Search..." class="form-control">
        </div>

        <div class="col-lg-3 col-md-6 col-6">
            <label style="font-weight: 700">Email</label>
            <input type="text" id="email" placeholder="Search..." class="form-control">
        </div>

        <div class="col-lg-3 col-md-6 col-6">
            <label style="font-weight: 700">Date Range</label>
            <input type="text" class="form-control" id="daterange-btn" value='{{old('date_range')}}' name='date_range'>
        </div>

        <div class="col-lg-3 col-md-6 col-6">
            <label style="font-weight: 700">Payment</label>
            <select class="form-select" name="payment" id="payment">
                <option value="">All</option>
                <option value="{{\App\Models\Order::CASH}}">CASH</option>
                <option value="{{\App\Models\Order::ONLINE}}">Credit</option>
            </select>
        </div>

        <div class="col-12" style="text-align: center">
            <button class="btn btn-success-light search" style="width: 30%">Search</button>
        </div>
    </div>
</header>

<div class="card mb-4">

    <!-- card-header end// -->
    <div class="card-body" id="update-table">
        @include('order.orders-table')
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
            'pageLength': 50,
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

    function loadOrders(url, data) {

        document.getElementById('update-table').style.opacity = '.1';

        $.ajax({
            url: url,
            type: 'GET',
            data: data,
            success: function(data) {
                document.getElementById('update-table').innerHTML = data;
                $('#myTable').DataTable({
                    'ordering': false,
                    'sorting': false,
                    'paging': false,
                    'pageLength': 50,
                    'info': false,
                    'searching': true
                });
                document.getElementById('update-table').style.opacity = '1';
            },
            error: function() {
                toastr.error('Something went wrong while loading orders');
                document.getElementById('update-table').style.opacity = '1';
            }
        });
    }

    $(document).on('click', '.search', function(e) {

        var date_range = $('#daterange-btn').val();
        var payment = $('#payment').val();
        var order = $('#order').val();
        var phone = $('#phone').val();
        var email = $('#email').val();
        var name = $('#cname').val();

        if (date_range || payment || order || phone || email || name) {
            loadOrders("{{route('orders.search')}}", {
                date_range: date_range,
                payment: payment,
                order: order,
                phone: phone,
                email: email,
                name: name
            });
        } else {
            toastr.warning('Please select any option');
        }
    });

    // Pagination links load via ajax so filters are kept between pages
    $(document).on('click', '#update-table .pagination a', function(e) {
        e.preventDefault();
        loadOrders($(this).attr('href'), {});
        window.scrollTo({ top: $('#update-table').offset().top - 100, behavior: 'smooth' });
    });
    
    
</script>
@stop