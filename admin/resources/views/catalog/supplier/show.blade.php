@extends('layouts.app')

@section('css')
<style>
    .img-wrap img:hover{
        -ms-transform: scale(1.2); /* IE 9 */
        -webkit-transform: scale(1.2); /* Safari 3-8 */
        transform: scale(1.2);
    }
    .img-wrap img {
        transition: 1s;
    }
</style>

  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  
  <link href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css" rel="stylesheet">
@stop
@section('content')

<div class="content-header">
    <a href="javascript:history.back()"><i class="material-icons md-arrow_back"></i> Go back </a>
</div>
<div class="card mb-4">
    <div class="card-header bg-brand-2" style="height: 150px"></div>
    <div class="card-body">
        <div class="row">
            <div class="col-xl col-lg flex-grow-0" style="flex-basis: 230px">
                <div class="img-thumbnail shadow w-100 bg-white position-relative text-center" style="height: 190px; width: 200px; margin-top: -120px">
                    <img src="{{asset('imgs/people/avatar-4.png')}}" style="max-width: 80%;!important;" class="center-xy img-fluid" alt="Logo Brand">
                </div>
            </div>
            <!--  col.// -->
            <div class="col-xl col-lg">
                <h3>{{$supplier->name}}</h3>
                <p><span class="badge rounded-pill {{($supplier->status) ? 'alert-success' : 'alert-danger'}}">{{($supplier->status) ? 'Active' : 'InActive'}}</span>
                   
                </p>
            </div>
            <!--  col.// -->
            <div class="col-xl-4 text-md-end">
                @can('Edit Supplier')
                <a class="dropdown-item btn btn-primary d-inline" href="{{route('suppliers.edit',$supplier->id)}}">Edit info</a>
                @endcan

                @can('Delete Supplier')
                <form @class('d-inline') onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('suppliers.destroy',$supplier->id) }}" method="POST">
                @csrf
                @method('DELETE')

                <button style="width: min-content;" class="dropdown-item btn btn-instagram d-inline"  type="submit">Delete</button>
                </form>
                @endcan

            </div>
            <!--  col.// -->
        </div>
        <!-- card-body.// -->
        <hr class="my-4">
        <div class="row g-4">
            <div class="col-md-12 col-lg-4 col-xl-2">
               
            </div>
            <!--  col.// -->
            <div class="col-sm-6 col-lg-4 col-xl-3">
                <h6>Basic</h6>
                <p>
                    <b>NTN Number: </b>{{$supplier->ntn_number}}<br>
                    <b>Email: </b>{{$supplier->email}} <br>
                    <b>Phone Number: </b> {{$supplier->mobile_number}} <br>
                    <b>Office Number: </b> {{$supplier->office_number}} <br>
                    <b>Opening Balance: </b> {{number_format($supplier->opening_balance)}} <br>

                </p>
            </div>
            <!--  col.// -->

           

           

            <div class="card-body" id="update-table">
                <div class="table-responsive" >
                    <h3>Receivings</h3>
            <table id="myTable" class="table table-hover">
                <thead>
                <tr>
                    <th>#Sr</th>
                    <th scope="col">Date</th>
                    <th scope="col">PO #</th>
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
                @foreach($supplier->receivings as $r)

                    <tr>

                        <td>{{$sr++}}</td>
                        <td>{{date('d-m-Y',strtotime($r->date))}}</td>
                        <td><b>{{$r->purchaseOrder ? $r->purchaseOrder->po_no : ''}}</b></td>
                        <td>{{$r->total_products}}</td>
                        <td>{{number_format($r->total_qty)}}</td>
                        <td>{{number_format($r->net_amount)}}</td>
                        <td>{{$r->createdBy ? $r->createdBy->name : ''}}</td>
                        <td>{{$r->approvedBy ? $r->approvedBy->name : ''}}</td>
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
                        </td>

                    </tr>
                @endforeach

                </tbody>
            </table>
                </div>
            </div>
        </div>
        <!--  row.// -->
    </div>
    <!--  card-body.// -->
</div>
<!--  card.// -->
<div class="card mb-4">
    
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-body mb-4">
                
                <div class="row mb-4">
                    <label class="col-lg-3 col-form-label">Date Range<span style="color: red;"> *</span></label>
                    <div class="col-lg-9">
                        <input type="text" class="form-control "id="daterange-btn" value='{{old('date_range')}}' name='date_range'>
                        @error('date_range')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- col.// -->
                </div>

                <div class="form-actions" style="text-align: right">
                    <button onclick="generateReport()" type="submit" class=" btn btn-success-light"> <i class="fa fa-check" ></i> Generate</button>

                </div>
                
            </div>
        </div>
    </div>
    
    <div class="card-body" id="result">

        <div class="table-responsive" >
            <h3>{{$supplier->name}} Ledger</h3>
            <table id="myTable1" class="table table-hover">
                <thead>
                <tr>
                    <th>#Sr</th>
                    <th scope="col">Date</th>
                    <th scope="col">Invoice # / Voucher #</th>
                    <th scope="col">Narration</th>
                    <th scope="col">Debit</th>
                    <th scope="col">Credit</th>
                    <th scope="col">Balance</th>
                </tr>
                </thead>
                <tbody><?php $sr = 1;$balance = $debit = $credit = 0?>
                @foreach($ledger as $r)
                    <tr>

                        <td>{{$sr++}}</td>
                        <td>@if($r['date'])
                        {{date('d-m-Y',strtotime($r['date']))}}
                        @endif</td>
                        
                        @if($r['link'] == 'payment')
                        
                        <td><a href="{{route('supplier-payments.show',$r['id'])}}" target="_blank"><b>{{$r['invoice/voucher']}}</b></a></td>
                        
                        @elseif($r['link'] == 'receiving')
                        
                        <td><a href="{{route('receiving.show',$r['id'])}}" target="_blank"><b>{{$r['invoice/voucher']}}</b></a></td>
                        
                        @elseif($r['link'] == 'return')
                        
                        <td><a href="{{route('supplier-returns.show',$r['id'])}}" target="_blank"><b>{{$r['invoice/voucher']}}</b></a></td>
                        
                        @else
                        
                          <td><b>{{$r['invoice/voucher']}}</b></td>
                        @endif
                        <td>{{$r['narration']}}
                        
                        @if($r['link'] == 'payment')
                            @if($r['method'] == 3)
                                @if($r['clear'])
                                <i class="icon material-icons md-check-box"></i>
                                @else
                                <i class="icon material-icons md-cancel"></i>
                                @endif
                            
                            @endif
                        @endif
                        
                        </td>
                        <td>{{$r['debit'] ? number_format($r['debit']) : ''}}
                            <?php $debit += $r['debit'];
                            $balance += $r['debit']; ?>
                        </td>
                        <td>{{number_format($r['credit'])}}
                        <?php $credit += $r['credit'];   $balance += ($r['credit'] * -1); ?>
                        </td>
                        @if($balance < 0)
                        <td>({{number_format(abs($balance))}})</td>
                        @else
                            <td>{{number_format($balance)}}</td>
                        @endif

                    </tr>
                @endforeach

                <tr style="border-top: 1px solid #0a0a18;">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b style=" border-bottom: double;">{{number_format($debit)}}</b></td>
                    <td><b style=" border-bottom: double;">{{number_format($credit)}}</b></td>
                    @if($balance < 0)
                        <td><b style=" border-bottom: double;">({{number_format(abs($balance))}})</b></td>
                    @else
                        <td><b style=" border-bottom: double;">{{number_format($balance)}}</b></td>
                    @endif

                </tr>


                </tbody>
            </table>
        </div>
    </div>
    <!--  card-body.// -->
</div>
<!--  card.// -->

@stop

@section('js')

<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.print.min.js"></script>

<script>
    $(document).ready( function () {
    $('#myTable').DataTable({
    'ordering': false, 'sorting' : false, 'paging' : true,'pageLength' : 50, 'info' : false, 'searching':true,dom: 'Bfrtip',
            buttons: [
                'csv', 'excel', 'print','pdf'
            ]
    });
    
    $('#myTable1').DataTable({
    'ordering': false, 'sorting' : false, 'paging' : true,'pageLength' : 50, 'info' : false, 'searching':true,dom: 'Bfrtip',
            buttons: [
                 {
                extend: 'excel',
                title: '{{$supplier->name}} Ledger'
            },
            {
                extend: 'pdf',
                title: '{{$supplier->name}} Ledger'
            },
            ]
    });
    } );
    </script>
    
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>



        $('#daterange-btn').daterangepicker(
            {
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                startDate: moment(),
                endDate: moment()
            },
            function (start, end) {
                $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }
        );
    
   function generateReport() {
    const date_range = $('#daterange-btn').val();
    const supplier_id = '{{$supplier->id}}';
    
    $.ajax({
        url: "{{route('supplier.update-report')}}",
        type: 'GET',
        data: {date_range: date_range, supplier_id: supplier_id},
        success: function (data) {
            document.getElementById('result').innerHTML = data;

            // Initialize DataTable with export buttons
            $('#myTable1').DataTable({
                'ordering': false,
                'sorting': false,
                'paging': true,
                'pageLength': 50,
                'info': false,
                'searching': true,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        title: '{{$supplier->name}} Ledger',
                        messageTop: `Date Range: ${date_range}`
                    },
                    {
                        extend: 'pdf',
                        title: '{{$supplier->name}} Ledger',
                        messageTop: `Date Range: ${date_range}`
                    }
                ]
            });
        }
    });
}
</script>
    @stop
