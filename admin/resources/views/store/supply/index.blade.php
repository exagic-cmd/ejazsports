@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All  Supplies</h2>
            <p>store supplies information.</p>
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
                    <tr style="text-align: center;">
                        <th>#Sr</th>
                        <th scope="col">Supply#</th>
                        <th scope="col">Send Date</th>
                        <th scope="col">Store Out</th>
                        <th scope="col">Store In</th>
                        <th scope="col">Brand</th>
                        <th scope="col">Total Products</th>
                        <th scope="col">Total Qty</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($storeSupplies as $sS)

                        <tr style="text-align: center;">

                            <td>{{$sr++}}</td>
                            <td>VEG-00{{$sS->id}}</td>
                            <td>{{date('d-m-Y',strtotime($sS->send_date))}}</td>
                            <td><b>{{$sS->storeOut ? $sS->storeOut->name : ''}}</b></td>
                            <td><b>{{$sS->storeIn ? $sS->storeIn->name : ''}}</b></td>
                            <td>{{$sS->brand ? $sS->brand->brand_heading : ''}}</td>
                            <td>{{number_format($sS->total_products)}}</td>
                            <td>{{number_format($sS->total_product_qty)}}</td>

                            <td>
                                @if($sS->status == \App\Models\Supply::CREATED)
                                    <span class="badge rounded-pill alert-danger">CREATED</span>
                                @elseif($sS->status == \App\Models\Supply::ISSUED)
                                <span class="badge rounded-pill alert-info">ISSUED</span>
                                    @elseif($sS->status == \App\Models\Supply::IN_TRANSIT)
                                        <span class="badge rounded-pill alert-primary">IN TRANSIT</span>
                                @elseif($sS->status == \App\Models\Supply::DELIVERED)
                                    <span class="badge rounded-pill alert-success">DELIVERED</span>
                                    @endif
                            </td>

                            <td class="text-end">
                                @can('View Supplies')
                                    <a href="{{route('supplies.show',$sS->id)}}" class="btn btn-md rounded font-sm">Detail</a>
                                @endcan
                                @if($sS->status == \App\Models\Supply::CREATED )
                                <div class="dropdown">
                                    <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                    <div class="dropdown-menu">
                                        @can('Edit Supplies')
                                            <a class="dropdown-item" href="{{route('supplies.edit',$sS->id)}}">Edit info</a>
                                        @endcan

                                        @can('Delete Supplies')
                                            <form onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('supplies.destroy',$sS->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')

                                                <button class="dropdown-item text-danger" onclick="return confirm('Are you sure?')"  type="submit">Delete</button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                                <!-- dropdown //end -->
                                    @endif
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
