@extends('layouts.app')
@section('content')
<div class="content-header">
    <div>
        <h2 class="content-title card-title">All Purchase Orders</h2>
        <p>Purchase order information.</p>
    </div>
    @can('Create Brand')
    <div>
      
                <a href="{{route('purchase-orders.auto-product-form')}}" class="btn btn-primary"><i
                class="text-muted material-icons md-post_add"></i>Add New</a>
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
        <div class="table-responsive">
            <table id="myTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>Sr#</th>
                        <th scope="col">Date</th>
                        <th scope="col">PO #</th>
                        <th scope="col">Supplier </th>
                        <th scope="col">Total Product</th>
                        <th scope="col">Total Qty</th>
                        <th scope="col">Projected Amount</th>
                        <th scope="col">Created by</th>
                        <th scope="col">Approved By</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody><?php $sr = 1;?>
                    @foreach($purchaseOrders as $pO)
                    <tr>
                        <td>{{$sr++}}</td>
                        <td>{{date('d-m-Y',strtotime($pO->date))}}</td>
                        <td><b>{{$pO->po_no}}</b></td>
                        <td>{{$pO->supplier ? $pO->supplier->name : ''}}</td>
                        <td>{{$pO->total_products}}</td>
                        <td>{{number_format($pO->total_product_qty)}}</td>
                        <td>{{number_format($pO->total_amount)}}</td>
                        <td>{{$pO->createdBy ? $pO->createdBy->name : ''}}</td>
                        <td>{{$pO->approvedBy ? $pO->approvedBy->name : ''}}</td>
                        <td>
                            @if($pO->status == \App\Models\PurchaseOrder::APPROVAL_PENDING)
                            <span class="badge rounded-pill  alert-danger">
                                APPROVAL PENDING
                            </span>
                            @elseif($pO->status == \App\Models\PurchaseOrder::APPROVED)
                            <span class="badge rounded-pill  alert-success">
                                APPROVED
                            </span>
                            @elseif($pO->status == \App\Models\PurchaseOrder::PO_SENT)
                            <span class="badge rounded-pill  alert-success">
                                PO SENT
                            </span>
                            @elseif($pO->status == \App\Models\PurchaseOrder::RECEIVED)
                            <span class="badge rounded-pill  alert-success">
                                RECEIVED
                            </span>
                            @endif
                        </td>
                        <td class="text-end">
                            @can('View Purchase Order')
                            <a href="{{route('purchase-orders.show',$pO->id)}}"
                                class="btn btn-md rounded font-sm">Detail</a>
                            @endcan
                            @if($pO->status == \App\Models\PurchaseOrder::APPROVAL_PENDING)
                            <div class="dropdown">
                                <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i
                                        class="material-icons md-more_horiz"></i> </a>
                                <div class="dropdown-menu">
                                    @can('Edit Purchase Order')
                                    <a class="dropdown-item" href="{{route('purchase-orders.edit',$pO->id)}}">Edit
                                        info</a>
                                    @endcan

                                    @can('Delete Purchase Order')
                                    <form onsubmit="return confirm('Do you really want to do this?');" id="delete-form"
                                        action="{{ route('purchase-orders.destroy',$pO->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <button class="dropdown-item text-danger"
                                            onclick="return confirm('Are you sure?')" type="submit">Delete</button>
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
    $(document).ready(function () {
        $('#myTable').DataTable({
            'ordering': false,
            'sorting': false,
            'paging': true,
            'pageLength': 50,
            'info': false,
            'searching': true
        });
    });

    setTimeout(function () {
        $('.alert').fadeOut('fast');
    }, 1000);

</script>
@stop
