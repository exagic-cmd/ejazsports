@extends('layouts.app')


@section('content')


    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Supplier Cheaques Payments ({{number_format($allPayments)}})</h2>
            <p>Cheaque information.</p>
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
                        <th scope="col">Supplier </th>
                        <th scope="col">Amount</th>
                        <th scope="col">Cheaque Date</th>
                        <th scope="col">Notes</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;?>
                    @foreach($payments as $sP)

                        @if($sP->cheque_date < \Carbon\Carbon::now())
                        <tr style="color:red">
                            @elseif($sP->cheque_date > \Carbon\Carbon::now()->subDays(1) && $sP->cheque_date < \Carbon\Carbon::now()->addDays(1))
                             <tr style="color:green">
                                 
                                  @else
                             <tr style="color:green">
                            @endif

                            <td>{{$sr++}}</td>
                            <td>{{date('d-m-Y',strtotime($sP->date))}}</td>

                            <td>{{$sP->supplier ? $sP->supplier->name : ''}}</td>
                         
                            <td style="text-align: center;">{{number_format($sP->amount - $sP->discount)}}</td>
                            
                             <td>{{date('d-m-Y',strtotime($sP->cheque_date))}}</td>
                             
                             <td>{{$sP->notes}}</td>
                            
                        
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
                                    <a class="btn btn-dark btn-default rounded font-sm" href="{{route('supplier-payments.edit',$sP->id)}}">Edit info</a>
                               
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
