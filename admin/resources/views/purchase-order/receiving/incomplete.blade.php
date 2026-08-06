@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Incomplete Receiving</h2>
            <p>Receiving information.</p>
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
                        <th>#Sr</th>
                        <th scope="col">Date</th>
                        <th scope="col">PO #</th>
                        <th scope="col">Supplier</th>
                        <th scope="col">Invoice #</th>
                        <th scope="col">Total Product</th>
                        <th scope="col">Total Qty</th>
                        <th scope="col">Net Amount</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($receiving as $r)

                        <tr >

                            <td @class('text-center')>{{$sr++}}</td>
                            <td @class('text-center')>{{date('d-m-Y',strtotime($r->date))}}</td>
                             <td @class('text-center')><a href="{{route('purchase-orders.show',$r->purchaseOrder ? $r->purchaseOrder->id : '')}}" target="_blank"><b>{{$r->purchaseOrder ? $r->purchaseOrder->po_no : ''}}</b></a></td>
                            <td @class('text-center')>{{ $r->purchaseOrder?->supplier?->name ?? $r->supplier?->name ?? '' }}</td>
                            <td @class('text-center')>{{$r->invoice_no}}</td>
                            <td @class('text-center')>{{$r->total_products}}</td>
                            <td @class('text-center')>{{number_format($r->total_qty)}}</td>
                            <td @class('text-center')>{{number_format($r->net_amount)}}</td>

                            <td>

                                @if($r->status == \App\Models\Receiving::APPROVAL_PENDING)
                                    <span class="badge rounded-pill  alert-danger">
                                        APPROVAL PENDING
                                </span>
                                @elseif($r->status == \App\Models\Receiving::APPROVED)
                                    <span class="badge rounded-pill  alert-success">
                                        APPROVED
                                </span>
                                @endif
                            </td>



                            <td class="text-end">

                                @can('View Receiving')
                                    <a href="{{route('receiving.show',$r->id)}}" class="btn btn-md rounded font-sm">Detail</a>
                                @endcan
                                @if($r->status == \App\Models\Receiving::APPROVAL_PENDING)
                                @can('Edit Receiving')
                                <a href="{{route('receiving.edit',$r->id)}}" class="btn btn-md rounded font-sm">Update</a>
                                @endcan


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
