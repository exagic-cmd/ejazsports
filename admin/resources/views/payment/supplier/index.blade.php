@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Supplier Payments ({{number_format($allPayments)}})</h2>
            <p>Supplier Payment information.</p>
        </div>
        @can('Create Supplier Payment')
            <div>
                <a  href="{{route('supplier-payments.create')}}" class="btn btn-primary"><i class="text-muted material-icons md-post_add"></i>Add New</a>
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
                        <th scope="col">Date</th>
                        <th scope="col">Supplier </th>
                        <th scope="col">Invoice Amount</th>
                        <th scope="col">Tax</th>
                        <th scope="col">Paid Amount</th>
                        <th scope="col">Payment Method</th>
                        <th scope="col">Created by</th>
                        <th scope="col">Approved By</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($supplierPayments as $sP)

                        <tr>

                            <td>{{$sr++}}</td>
                            <td>{{date('d-m-Y',strtotime($sP->date))}}</td>

                            <td>{{$sP->supplier ? $sP->supplier->name : ''}}</td>
                            <td style="text-align: center;">{{number_format($sP->amount + $sP->tax)}}</td>
                            <td style="text-align: center;">{{number_format($sP->tax)}}</td>
                            <td style="text-align: center;">{{number_format($sP->amount)}}</td>
                            <td>
                                @if($sP->payment_method == \App\Models\SupplierPayment::CASH)
                                    <span class="badge rounded-pill  alert-success">
                                        CASH
                                </span>
                                @elseif($sP->payment_method == \App\Models\SupplierPayment::BANK_TRANSFER)
                                    <span class="badge rounded-pill  alert-success">
                                        BANK TRANSFER
                                </span>
                                    @elseif($sP->payment_method == \App\Models\SupplierPayment::CHEQUE)
                                        <span class="badge rounded-pill  alert-success">
                                        CHEQUE
                                </span>
                                    @endif
                            </td>
                            <td>{{$sP->createdBy ? $sP->createdBy->name : ''}}</td>
                            <td>{{$sP->approvedBy ? $sP->approvedBy->name : ''}}</td>
                            <td>

                                @if($sP->status == \App\Models\SupplierPayment::APPROVAL_PENDING)
                                    <span class="badge rounded-pill  alert-danger">
                                        APPROVAL PENDING
                                </span>
                                @elseif($sP->status == \App\Models\SupplierPayment::APPROVED)
                                    <span class="badge rounded-pill  alert-success">
                                        APPROVED
                                </span>
                                @endif
                            </td>


                            <td class="text-end">

                                @can('View Supplier Payment')
                                    <a href="{{route('supplier-payments.show',$sP->id)}}" class="btn btn-md rounded font-sm">Detail</a>
                                @endcan
                                
                                    <div class="dropdown">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                        <div class="dropdown-menu">
                                            @can('Edit Supplier Payment')
                                                <a class="dropdown-item" href="{{route('supplier-payments.edit',$sP->id)}}">Edit info</a>
                                            @endcan

                                            @can('Delete Supplier Payment')
                                                <form onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('supplier-payments.destroy',$sP->id) }}" method="POST">
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
