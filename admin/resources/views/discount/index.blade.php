@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Discounts</h2>
            <p>Discount information.</p>
        </div>
        @can('Create Supplier')
            <div>
                <a  href="{{route('discounts.create')}}" class="btn btn-primary"><i class="text-muted material-icons md-post_add"></i>Add New</a>
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
                        <th scope="col">Is Percentage</th>
                        <th scope="col">Amount / Percentage</th>
                        <th scope="col">Max Discount Amount</th>
                        <th scope="col">Start Date</th>
                        <th scope="col">End Date</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($discounts as $discount)

                        <tr>

                            <td>{{$sr++}}</td>
                            <td><b>{{$discount->name}}</b></td>
                            <td>
                                @if($discount->type == \App\Models\Discount::PRODUCT)
                                    Product Discount
                                @elseif($discount->type == \App\Models\Discount::BRAND)
                                    Brand Discount
                                @elseif($discount->type == \App\Models\Discount::CATEGORY)
                                    Category Discount
                                @endif
                            </td>
                            <td><span class="badge rounded-pill {{($discount->is_percent) ? 'alert-success' : 'alert-danger'}}">{{($discount->is_percent) ? 'YES' : 'NO'}}</span></td>
                            <td>{{$discount->amount}} {{$discount->is_percent ? '%' : ''}}</td>
                            <td>{{$discount->max_amount}}</td>

                            <td>{{date('M d, Y',strtotime($discount->start_date))}}</td>

                            <td>{{date('M d, Y',strtotime($discount->end_date))}}</td>

                            <td><span class="badge rounded-pill {{($discount->status) ? 'alert-success' : 'alert-danger'}}">{{($discount->status) ? 'Active' : 'InActive'}}</span></td>



                            <td class="text-end">
{{--                                @can('View Discount')--}}
{{--                                    <a href="{{route('discounts.show',$discount->id)}}" class="btn btn-md rounded font-sm">Detail</a>--}}
{{--                                @endcan--}}
                                <div class="dropdown">
                                    <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                    <div class="dropdown-menu">
                                        @can('Edit Discounts')
                                            <a class="dropdown-item" href="{{route('discounts.edit',$discount->id)}}">Edit info</a>
                                        @endcan

                                        @can('Delete Discounts')
                                            <form onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('discounts.destroy',$discount->id) }}" method="POST">
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
