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
                <h3>{{$employee->name}}</h3>
                <p><span class="badge rounded-pill {{($employee->status) ? 'alert-success' : 'alert-danger'}}">{{($employee->status) ? 'Active' : 'InActive'}}</span>
                   
                </p>
            </div>
            <!--  col.// -->
            <div class="col-xl-4 text-md-end">
                @can('Edit Employee')
                <a class="dropdown-item btn btn-primary d-inline" href="{{route('employees.edit',$employee->id)}}">Edit info</a>
                @endcan

                @can('Delete Employee')
                <form @class('d-inline') onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('employees.destroy',$employee->id) }}" method="POST">
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
                    
                    <b>Mobile Number: </b> {{$employee->mobile_number}} <br>
                    <b>Commission Per Retail: </b> {{$employee->com_per_retail}} % <br>
                    <b>Commission Per Whole </b> {{number_format($employee->com_per_whole)}} % <br>

                </p>
            </div>
            <!--  col.// -->
            <br><br>
            
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
    
    <div id="result">

           <div class="card-body" id="update-table">
            <div class="table-responsive" >
                <h3>Retail Orders</h3>
                <table id="myTable" class="table table-hover">
                    <thead>
                    <tr>
                        <th>#Sr</th>
                        <th>Date</th>
                        <th>Order #</th>
                      
                        <th scope="col">Order Amt.</th>
                        <th scope="col">whole - Retail Margin</th>
                        <th scope="col">Com</th>

                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;$t = 0;$orderSum=0;$marginSum = 0;?>
                    @foreach($employee->orders as $o)
                    
                    @if($o->customer_id == 1 && \Carbon\Carbon::parse($o->created_at)->isToday() )
                    @if($o->return_amount == 0 || $o->return_amount != $o->total_amount)
                            <tr>

                                <td>{{$sr++}}</td>
                                <td>{{date('d-m-Y',strtotime($o->created_at))}}</td>
                                
                              <td><a href="{{route('orders.show',$o->id)}}" target="_blank">{{$o->order_no}}</a></td>
                              
                             
                               <td>{{number_format(($o->total_amount - $o->return_amount))}}
                               <?php $orderSum += ($o->total_amount - $o->return_amount);?></td>
                               <td>{{($o->margin - $o->discount_amount)}}
                               <?php $marginSum += ($o->margin - $o->discount_amount);?></td>
                                 <td>{{round(($o->margin - $o->discount_amount) * ($employee->com_per_retail / 100))}}</td>
                                
                               
                               <?php $t += round(($o->margin - $o->discount_amount) * ($employee->com_per_retail/100) , 2);?>
                            </tr>
                            @endif
                            
                            @endif
                            @endforeach
                            
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b>{{number_format($orderSum)}}</b></td>
                                <td><b>{{number_format($marginSum)}}</b></td>
                                <td><b>{{number_format($t)}}</b></td>
                            </tr>
                            
                           

                    </tbody>
                </table>
            </div>
            <!-- table-responsive //end -->
        </div>
        
       <div class="card-body" >
            <div class="table-responsive" >
                <h3>whole Sale Orders</h3>
                <table id="myTable1" class="table table-hover">
                    <thead>
                    <tr>
                        <th>#Sr</th>
                        <th>Date</th>
                        <th>Order #</th>
                        
                        <th scope="col">Order Amt.</th>
                        <th scope="col">Purchase - Whole Margin</th>
                        <th scope="col">Com</th>

                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;$t = 0;$orderSum=0;$marginSum=0;?>
                    
                    @foreach($employee->orders as $o)
                    
                    @if($o->customer_id != 1 && \Carbon\Carbon::parse($o->created_at)->isToday() && ($o->paid_amount == $o->total_amount || (($o->total_amount - $o->paid_amount) < 10 )))
                    
                    
                     @if($o->return_amount == 0 || $o->return_amount != $o->total_amount)
                            <tr>

                                <td>{{$sr++}}</td>
                                <td>{{date('d-m-Y',strtotime($o->created_at))}}</td>
                                
                              <td><a href="{{route('orders.show',$o->id)}}" target="_blank">{{$o->order_no}}</a></td>
                              
                               <td>{{number_format(($o->total_amount - $o->return_amount))}}
                               <?php $orderSum += ($o->total_amount - $o->return_amount);?></td>
                               
                               <td>{{($o->margin  - $o->discount_amount)}}
                               <?php $marginSum += ($o->margin - $o->discount_amount);?></td>
                                <td>{{round(($o->margin - $o->discount_amount) * ($employee->com_per_whole / 100))}}</td>
                                
                               
                               <?php $t += round(($o->margin - $o->discount_amount)  * ($employee->com_per_whole/100) , 2);?>
                            </tr>
                            
                            @endif
                            
                            @endif
                            
                          
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                 <td><b>{{number_format($orderSum)}}</b></td>
                                <td><b>{{number_format($marginSum)}}</b></td>
                                <td><b>{{number_format($t)}}</b></td>
                            </tr>
                    

                    </tbody>
                </table>
            </div>
            <!-- table-responsive //end -->
        </div>
        
        
        
        
        <div class="card-body" >
            <div class="table-responsive" >
                <h3>Credit Orders</h3>
                <table id="myTable2" class="table table-hover">
                    <thead>
                    <tr>
                        <th>#Sr</th>
                        <th>Date</th>
                        <th>Order #</th>
                        
                        <th scope="col">Order Amt.</th>
                        <th scope="col">Purchase - Whole Margin</th>
                        <th scope="col">Com</th>

                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;$t = 0;$orderSum=0;$marginSum=0;?>
                    
                    @foreach($employee->orders as $o)
                    
                    @if($o->customer_id != 1 && \Carbon\Carbon::parse($o->created_at)->isToday() && ($o->pay_amount < $o->total_amount || $o->paid_amount < $o->total_amount))
                     @if($o->return_amount == 0 || $o->return_amount != $o->total_amount)
                            <tr>

                                <td>{{$sr++}}</td>
                                <td>{{date('d-m-Y',strtotime($o->created_at))}}</td>
                                
                              <td><a href="{{route('orders.show',$o->id)}}" target="_blank">{{$o->order_no}}</a></td>
                              
                               <td>{{number_format(($o->total_amount - $o->return_amount))}}
                               <?php $orderSum += ($o->total_amount - $o->return_amount);?></td>
                               
                               <td>{{($o->margin - $o->discount_amount)}}
                               <?php $marginSum += ($o->margin - $o->discount_amount);?></td>
                                 <td>{{round(($o->margin - $o->discount_amount) * ($employee->com_per_whole / 100))}}</td>
                                
                               
                               <?php $t += round(($o->margin -$o->discount_amount) * ($employee->com_per_whole/100) , 2);?>
                            </tr>
                            
                            @endif
                            
                            @endif
                            
                          
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                 <td><b>{{number_format($orderSum)}}</b></td>
                                <td><b>{{number_format($marginSum)}}</b></td>
                                <td><b>{{number_format($t)}}</b></td>
                            </tr>
                    

                    </tbody>
                </table>
            </div>
            <!-- table-responsive //end -->
        </div>
        
        
         <div class="card-body" >
            <div class="table-responsive" >
                <h3>Return Orders</h3>
                <table id="myTable4" class="table table-hover">
                    <thead>
                    <tr>
                        <th>#Sr</th>
                        <th>Date</th>
                        <th>Order #</th>
                        
                        <th scope="col">Order Amt.</th>
                        <th scope="col">Return Amt.</th>
                        <th scope="col">Com</th>

                    </tr>
                    </thead>
                    <tbody><?php $sr = 1;$t = 0;$orderSum=0;$marginSum=0;?>
                    
                    @foreach($returnOrders as $o)
                    @php
                        $rate = ($o->customer_id == 1) ? $employee->com_per_retail : $employee->com_per_whole;
                        $com = -round(($o->margin - $o->discount_amount) * ($rate / 100));
                    @endphp
                            <tr>

                                <td>{{$sr++}}</td>
                                <td>{{date('d-m-Y',strtotime($o->return_date ?? $o->created_at))}}</td>
                                
                              <td><a href="{{route('orders.show',$o->id)}}" target="_blank">{{$o->order_no}}</a></td>
                              
                               <td>{{number_format(($o->total_amount ))}}
                               <?php $orderSum += ($o->total_amount);?></td>
                                
                               <td>{{number_format($o->return_amount)}}
                               <?php $marginSum += ($o->return_amount);?></td>
                                 <td>{{$com}}</td>
                                
                               <?php $t += $com;?>
                            </tr>
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                 <td><b>{{number_format($orderSum)}}</b></td>
                                <td><b>{{number_format($marginSum)}}</b></td>
                                <td><b>{{number_format($t)}}</b></td>
                            </tr>
                    

                    </tbody>
                </table>
            </div>
            <!-- table-responsive //end -->
        </div>
        
        
        <div class="card-body" >
            <div class="table-responsive" >
                <h3>Customer Payments</h3>
                <table id="myTable3" class="table table-hover">
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
                    @foreach($payments as $sP)

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
            </div>
            <!-- table-responsive //end -->
        </div>
        
        </div>

           

            
        </div>
        <!--  row.// -->
    </div>
    <!--  card-body.// -->
</div>
<!--  card.// -->

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
    function initEmployeeTables() {
        ['#myTable', '#myTable1', '#myTable2', '#myTable3', '#myTable4'].forEach(function(tableId) {
            if (!$(tableId).length) {
                return;
            }

            if ($.fn.DataTable.isDataTable(tableId)) {
                $(tableId).DataTable().destroy();
            }

            $(tableId).DataTable({
                ordering: false,
                sorting: false,
                paging: true,
                pageLength: 50,
                info: false,
                searching: true,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        title: '{{$employee->name}} Ledger'
                    },
                    {
                        extend: 'pdf',
                        title: '{{$employee->name}} Ledger'
                    },
                ]
            });
        });
    }

    $(document).ready(function () {
        initEmployeeTables();
    });
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
             
        date_range = $('#daterange-btn').val();
        employee_id = '{{$employee->id}}';
        
        $.ajax({
            url: "{{route('employee.update-report')}}",
            type: 'GET',
            data: {date_range: date_range,employee_id:employee_id},
            success: function (data) {
                document.getElementById('result').innerHTML = data;
                initEmployeeTables();
            }
        });
                  

        }
    </script>

    @stop
