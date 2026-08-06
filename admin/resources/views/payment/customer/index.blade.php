@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Customer Payments </h2>
            <p>Customer Payment information.</p>
        </div>
        @can('Create Customer Payment')
            <div>
                <a  href="{{route('customer-payments.create')}}" class="btn btn-primary"><i class="text-muted material-icons md-post_add"></i>Add New</a>
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
                        <th scope="col">Customer </th>
                        <th scope="col">Received Amount</th>
                        <th scope="col">Payment Method</th>
                        <th scope="col">Created by</th>
                        <th scope="col">Approved By</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($customerPayments as $sP)

                        <tr>

                            <td>{{$sr++}}</td>
                            <td>{{date('d-m-Y',strtotime($sP->date))}}</td>

                            <td>{{$sP->customer ? $sP->customer->first_name : ''}}</td>
                           
                            <td style="text-align: center;">{{number_format($sP->amount)}}</td>
                            <td>
                                @if($sP->payment_method == \App\Models\CustomerPayment::CASH)
                                    <span class="badge rounded-pill  alert-success">
                                        CASH
                                </span>
                                @elseif($sP->payment_method == \App\Models\CustomerPayment::BANK_TRANSFER)
                                    <span class="badge rounded-pill  alert-success">
                                        BANK TRANSFER
                                </span>
                                    @elseif($sP->payment_method == \App\Models\CustomerPayment::CHEQUE)
                                        <span class="badge rounded-pill  alert-success">
                                        CHEQUE
                                </span>
                                    @endif
                            </td>
                            <td>{{$sP->createdBy ? $sP->createdBy->name : ''}}</td>
                            <td>{{$sP->approvedBy ? $sP->approvedBy->name : ''}}</td>
                            <td>

                                @if($sP->status == \App\Models\CustomerPayment::APPROVAL_PENDING)
                                    <span class="badge rounded-pill  alert-danger">
                                        APPROVAL PENDING
                                </span>
                                @elseif($sP->status == \App\Models\CustomerPayment::APPROVED)
                                    <span class="badge rounded-pill  alert-success">
                                        APPROVED
                                </span>
                                @endif
                            </td>


                            <td class="text-end">

                                @can('View Customer Payment')
                                    <a href="{{route('customer-payments.show',$sP->id)}}" class="btn btn-md rounded font-sm">Detail</a>
                                @endcan
                                
                                    <div class="dropdown">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                        <div class="dropdown-menu">
                                            @can('Edit Customer Payment')
                                                <a class="dropdown-item" href="{{route('customer-payments.edit',$sP->id)}}">Edit info</a>
                                            @endcan

                                            @can('Delete Customer Payment')
                                                <form onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('customer-payments.destroy',$sP->id) }}" method="POST">
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
                
                {{$customerPayments->links()}}
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
                'ordering': false, 'sorting' : false, 'paging' : false,'pageLength' : 50, 'info' : false, 'searching':true
            });
        } );

        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 1000);
    </script>
@stop
