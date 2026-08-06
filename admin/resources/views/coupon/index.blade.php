@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Coupons</h2>
            <p>Coupon information.</p>
        </div>
        @can('Create Supplier')
            <div>
                <a  href="{{route('coupons.create')}}" class="btn btn-primary"><i class="text-muted material-icons md-post_add"></i>Add New</a>
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
                        <th scope="col">Name</th>
                        <th scope="col">Type</th>
                        <th scope="col">Usage</th>
                        <th scope="col">Is Percentage</th>
                        <th scope="col">Amount / Percentage</th>
                        <th scope="col">Min Order Amount</th>
                        <th scope="col">Start Date</th>
                        <th scope="col">End Date</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($coupons as $coupon)

                        <tr style="text-align: center;">

                            <td>{{$sr++}}</td>
                            <td><b>{{$coupon->name}}</b></td>
                            <td>
                                @if($coupon->type == \App\Models\Coupon::PRODUCT)
                                    Product Coupon
                                @elseif($coupon->type == \App\Models\Coupon::BRAND)
                                    Brand Coupon
                                @elseif($coupon->type == \App\Models\Coupon::CATEGORY)
                                    Category Coupon
                                @elseif($coupon->type == \App\Models\Coupon::ORDER)
                                    Order Coupon
                                @elseif($coupon->type == \App\Models\Coupon::DELIVERY)
                                    Delivery Coupon
                                @endif
                            </td>
                            <td>
                                @if($coupon->usage == \App\Models\Coupon::ONCE)
                                    Only One Time
                                @elseif($coupon->usage == \App\Models\Coupon::EACH_CUSTOMER_ONCE)
                                    Each Customer One Time
                                @elseif($coupon->usage == \App\Models\Coupon::LIMITED)
                                    Limited ({{$coupon->limit_count}})
                                @elseif($coupon->type == \App\Models\Coupon::UNLIMITED)
                                    Un Limited
                                @endif
                            </td>
                            <td><span class="badge rounded-pill {{($coupon->is_percent) ? 'alert-success' : 'alert-danger'}}">{{($coupon->is_percent) ? 'YES' : 'NO'}}</span></td>
                            <td>{{number_format($coupon->discount_amount)}} {{$coupon->is_percent ? '%' : ''}} @if($coupon->is_percent) ({{number_format($coupon->max_discount_amount)}})@endif</td>
                            <td>{{number_format($coupon->min_order_amount)}}</td>

                            <td>{{date('M d, Y',strtotime($coupon->start_date))}}</td>

                            <td>{{date('M d, Y',strtotime($coupon->end_date))}}</td>

                            <td><span class="badge rounded-pill {{($coupon->status) ? 'alert-success' : 'alert-danger'}}">{{($coupon->status) ? 'Active' : 'InActive'}}</span></td>



                            <td class="text-end">
{{--                                @can('View Discount')--}}
{{--                                    <a href="{{route('discounts.show',$discount->id)}}" class="btn btn-md rounded font-sm">Detail</a>--}}
{{--                                @endcan--}}
                                <div class="dropdown">
                                    <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                    <div class="dropdown-menu">
                                        @can('Edit Coupons')
                                            <a class="dropdown-item" href="{{route('coupons.edit',$coupon->id)}}">Edit info</a>
                                        @endcan

                                        @can('Delete Coupons')
                                            <form onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('coupons.destroy',$coupon->id) }}" method="POST">
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
