@extends('layouts.app')

@section('css')

<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css" rel="stylesheet" >
<link href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap4.css" rel="stylesheet" >

<link href="{{asset('css/pos-shop.css')}}" rel="stylesheet">

<link href="{{asset('css/pos.css')}}" rel="stylesheet">

<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button {
    font-size: 14px; /* Adjust the font size as needed */
    padding: 4px 8px; /* Adjust the padding as needed */
}
</style>
@stop


@section('content')
<div class="pos-container-wrapper" >
    <div class="pos-content-container" style="padding-left:0px">
        <div class="pos-cashier-main" >
            <div class="pos-nav-container">
                <ul class="pos-nav-lists">
                    <li label="menu_count_0" class="pos-nav active">
                        <a href="#!" onclick="customReport('today')" id="today_css" class="nav-link router-link-exact-active router-link-active">
                             Stats
                        </a>
                    </li>

                    <!--<li label="menu_count_0" class="pos-nav active">-->
                    <!--    <a href="#!" onclick="customReport('weekly')" id="weekly_css" class="nav-link ">-->
                    <!--        Weekly Stats-->
                    <!--    </a>-->
                    <!--</li>-->

                    <!--<li label="menu_count_0" class="pos-nav active">-->
                    <!--    <a href="#!" onclick="customReport('monthly')" id="monthly_css" class="nav-link ">-->
                    <!--        Monthly Stats-->
                    <!--    </a>-->
                    <!--</li>-->

                </ul>
            </div>
            <div class="pos-nav-content" style="overflow-x: unset;
    overflow-y: unset;" id="result">
                <div class="product-lowstock-panel">
                    <div class="pos-product-container" style="overflow-x: unset;
    overflow-y: unset;"><!---->
                        <div class="pos-setting-list row-grid-5">
                            <div class="pos-setting row-layout">
                                <a onclick="scrollToTable('todaySales')">
                                <div class="setting-list-name">
                                    <div class="name">Today Sales</div>

                                </div>
                                <div class="setting-list-rate">
                                    {{$todayBillsCount}}
                                    <span style="padding-left: 100px;">   {{number_format($todayBillsAmount)}} </span>
                                </div>
                                </a>
                            </div>
                            <div class="pos-setting row-layout">
                                <a onclick="scrollToTable('todayRetailSales')">
                                <div class="setting-list-name">
                                    <div class="name">Today Retail Sales</div>

                                </div>
                                <div class="setting-list-rate">
                                    {{$todayCashBillsCount}}
                                    <span style="padding-left: 100px;">   {{number_format($todayCashBillsAmount)}} </span>
                                </div>
                                </a>
                            </div>
                            <div class="pos-setting row-layout">
                                 <a onclick="scrollToTable('todayWholeSales')">
                                <div class="setting-list-name">
                                    <div class="name">Today Wholesale Sales</div>

                                </div>
                                <div class="setting-list-rate">
                                    
                                    {{$todayCardBillsCount}}
                                    <span style="padding-left: 100px;"> {{number_format($todayCardBillsAmount)}} </span>
                                </div>
                                </a>
                            </div>
                            
                            <div class="pos-setting row-layout">
                                
                                 <a onclick="scrollToTable('todayWholeSalesCredit')">
                                <div class="setting-list-name">
                                    <div class="name">Today Wholesale Credit Sales</div>

                                </div>
                                <div class="setting-list-rate">
                                    {{$todayCreditBillsCount}}
                                    <span style="padding-left: 100px;"> {{number_format($todayCreditBillsAmount)}} </span>
                                </div>
                                
                                </a>
                            </div>
                            
                             <div class="pos-setting row-layout">
                                 
                                 <a onclick="scrollToTable('todayWholeSalesPaid')">
                                <div class="setting-list-name">
                                    <div class="name">Today Wholesale Payment</div>

                                </div>
                                <div class="setting-list-rate">
                                    {{$todayPaidBillsCount}}
                                    <span style="padding-left: 100px;"> {{number_format($todayPaidBillsAmount)}} </span>
                                </div>
                                
                                </a>
                            </div>
                            <div class="pos-setting row-layout">
                                
                                <a onclick="scrollToTable('todayLedgerPayment')">
                                <div class="setting-list-name">
                                    <div class="name">Today Ledger Payments</div>
                                </div>
                                <div class="setting-list-rate">
                                    {{$todayCustomerPaymentC}}
                                    <span style="padding-left: 100px;"> {{number_format($todayCustomerPaymentA)}} </span>
                                </div>
                                </a>
                            </div>
                            
                            <div class="pos-setting row-layout">
                                <div class="setting-list-name">
                                    <div class="name">Today Returns (Cash)</div>
                                </div>
                                <div class="setting-list-rate">
                                    {{$todayCReturnBillsCount}}
                                    <span style="padding-left: 100px;"> {{number_format($todayCReturnBillsAmount)}} </span>
                                </div>
                            </div>
                            
                            
                             <div class="pos-setting row-layout">
                                <div class="setting-list-name">
                                    <div class="name">Today Returns (leger adjustment)</div>
                                </div>
                                <div class="setting-list-rate">
                                    {{$todayLReturnBillsCount}}
                                    <span style="padding-left: 100px;"> {{number_format($todayLReturnBillsAmount)}} </span>
                                </div>
                            </div>
                            
                            
                            <div class="pos-setting row-layout">
                                <div class="setting-list-name">
                                    <div class="name">Today Expense</div>
                                </div>
                                <div class="setting-list-rate">
                                   {{$todayExpenseC}}
                                    <span style="padding-left: 100px;"> {{number_format($todayExpenseA)}}</span>
                                </div>
                            </div>
                            
                            
                            <div class="pos-setting row-layout">
                                <div class="setting-list-name">
                                    <div class="name">Today Margin</div>
                                </div>
                                <div class="setting-list-rate">
                                   
                                    <span style="padding-left: 100px;"> {{number_format($todayMargin)}}</span>
                                </div>
                            </div>
                            
                             <div class="pos-setting row-layout">
                                <div class="setting-list-name">
                                    <div class="name">Today Profit</div>
                                </div>
                                <div class="setting-list-rate">
                                   
                                    <span style="padding-left: 100px;"> {{number_format($todayProfit)}}</span>
                                </div>
                            </div>
                        </div>
                        <br>
                        <br>
                        
                        
                        
                        <div class="table-responsive mx-auto" style="width: 90%;" style="margin-top: 20px;">
                            <h2>Today Sales</h2>
                <table class="table table-striped table-bordered" id="todaySales">
                    <thead>
                    <tr>
                        <th class="text-left">Sr #</th>
                        <th class="text-left">Order #</th>
                        
                        <th class="text-left">Customer</th>
                        <th class="text-left">Total Products</th>
                        <th class="text-left">Total Amount</th>
                        
                    </tr>
                    </thead>
                    <tbody>
                        <?php $sr=1;?>
                        @foreach($todaySales as $o)
                        <tr>
                            <td>{{$sr++}}</td>
                            <td>Bill-{{$o->order_no}}</td>
                          
                            <td>{{$o->name}}</td>
                            <td>{{$o->total_products}}</td>
                              <td>{{$o->total_amount}}</td>
                        </tr>
                        @endforeach
                        
                        <tfoot>
                
                <tr>
                    <th colspan="4" class="text-right">Total Products:</th>
                    <th class="text-left" id="totalProducts"></th>
                </tr>
                
                <tr>
                    <th colspan="4" class="text-right">Total Amount:</th>
                    <th class="text-left" id="totalAmount"></th>
                </tr>
            </tfoot>
                    
                    </tbody>
                </table>
            </div>
            
            <br>
            <br>
            
            <div class="table-responsive mx-auto" style="width: 90%;" style="margin-top: 20px;">
                            <h2>Today Retail Sales</h2>
                <table class="table table-striped table-bordered" id="todayRetailSales">
                    <thead>
                    <tr>
                        <th class="text-left">Sr #</th>
                        <th class="text-left">Order #</th>
                        
                        <th class="text-left">Customer</th>
                        <th class="text-left">Total Products</th>
                        <th class="text-left">Total Amount</th>
                        
                    </tr>
                    </thead>
                    <tbody>
                        <?php $sr=1;?>
                        @foreach($todayRetailSales as $o)
                        <tr>
                            <td>{{$sr++}}</td>
                            <td>Bill-{{$o->order_no}}</td>
                          
                            <td>{{$o->name}}</td>
                            <td>{{$o->total_products}}</td>
                              <td>{{$o->total_amount}}</td>
                        </tr>
                        @endforeach
                        
                        <tfoot>
                
                <tr>
                    <th colspan="4" class="text-right">Total Products:</th>
                    <th class="text-left" id="totalProductsR"></th>
                </tr>
                
                <tr>
                    <th colspan="4" class="text-right">Total Amount:</th>
                    <th class="text-left" id="totalAmountR"></th>
                </tr>
            </tfoot>
                    
                    </tbody>
                </table>
            </div>
            
            <br>
            <br>
            
            
            
            
            <div class="table-responsive mx-auto" style="width: 90%;" style="margin-top: 20px;">
                            <h2>Today Whole Sales</h2>
                <table class="table table-striped table-bordered" id="todayWholeSales">
                    <thead>
                    <tr>
                        <th class="text-left">Sr #</th>
                        <th class="text-left">Order #</th>
                        
                        <th class="text-left">Customer</th>
                        <th class="text-left">Total Products</th>
                        <th class="text-left">Total Amount</th>
                        
                    </tr>
                    </thead>
                    <tbody>
                        <?php $sr=1;?>
                        @foreach($todayWholeSales as $o)
                        <tr>
                            <td>{{$sr++}}</td>
                            <td>Bill-{{$o->order_no}}</td>
                          
                            <td>{{$o->name}}</td>
                            <td>{{$o->total_products}}</td>
                              <td>{{$o->total_amount}}</td>
                        </tr>
                        @endforeach
                        
                        <tfoot>
                
                <tr>
                    <th colspan="4" class="text-right">Total Products:</th>
                    <th class="text-left" id="totalProductsW"></th>
                </tr>
                
                <tr>
                    <th colspan="4" class="text-right">Total Amount:</th>
                    <th class="text-left" id="totalAmountW"></th>
                </tr>
            </tfoot>
                    
                    </tbody>
                </table>
            </div>
            
            <br>
            <br>
            
            
            <div class="table-responsive mx-auto" style="width: 90%;" style="margin-top: 20px;">
                            <h2>Today Whole Sales Credit</h2>
                <table class="table table-striped table-bordered" id="todayWholeSalesCredit">
                    <thead>
                    <tr>
                        <th class="text-left">Sr #</th>
                        <th class="text-left">Order #</th>
                        
                        <th class="text-left">Customer</th>
                        <th class="text-left">Total Products</th>
                        <th class="text-left">Total Amount</th>
                        
                    </tr>
                    </thead>
                    <tbody>
                        <?php $sr=1;?>
                        @foreach($todayWholeSalesCredit as $o)
                        <tr>
                            <td>{{$sr++}}</td>
                            <td>Bill-{{$o->order_no}}</td>
                          
                            <td>{{$o->name}}</td>
                            <td>{{$o->total_products}}</td>
                              <td>{{$o->total_amount}}</td>
                        </tr>
                        @endforeach
                        
                        <tfoot>
                
                <tr>
                    <th colspan="4" class="text-right">Total Products:</th>
                    <th class="text-left" id="totalProductsWC"></th>
                </tr>
                
                <tr>
                    <th colspan="4" class="text-right">Total Amount:</th>
                    <th class="text-left" id="totalAmountWC"></th>
                </tr>
            </tfoot>
                    
                    </tbody>
                </table>
            </div>
            
            <br>
            <br>
            
            
            <div class="table-responsive mx-auto" style="width: 90%;" style="margin-top: 20px;">
                            <h2>Today Whole Sales Paid</h2>
                <table class="table table-striped table-bordered" id="todayWholeSalesPaid">
                    <thead>
                    <tr>
                        <th class="text-left">Sr #</th>
                        <th class="text-left">Order #</th>
                        
                        <th class="text-left">Customer</th>
                        <th class="text-left">Total Products</th>
                        <th class="text-left">Total Amount</th>
                        
                    </tr>
                    </thead>
                    <tbody>
                        <?php $sr=1;?>
                        @foreach($todayWholeSalesPaid as $o)
                        <tr>
                            <td>{{$sr++}}</td>
                            <td>Bill-{{$o->order_no}}</td>
                          
                            <td>{{$o->name}}</td>
                            <td>{{$o->total_products}}</td>
                              <td>{{$o->total_amount}}</td>
                        </tr>
                        @endforeach
                        
                        <tfoot>
                
                <tr>
                    <th colspan="4" class="text-right">Total Products:</th>
                    <th class="text-left" id="totalProductsWP"></th>
                </tr>
                
                <tr>
                    <th colspan="4" class="text-right">Total Amount:</th>
                    <th class="text-left" id="totalAmountWP"></th>
                </tr>
            </tfoot>
                    
                    </tbody>
                </table>
            </div>
            
            <br>
            <br>
            
            
            
            <div class="table-responsive mx-auto" style="width: 90%;" style="margin-top: 20px;">
                            <h2>Today Ledger Paymet</h2>
                <table class="table table-striped table-bordered" id="todayLedgerPayment">
                    <thead>
                    <tr>
                        <th class="text-left">Sr #</th>
                       
                        <th class="text-left">Customer</th>
                     
                        <th class="text-left">Paid Amount</th>
                        
                    </tr>
                    </thead>
                    <tbody>
                        <?php $sr=1;?>
                        @foreach($todayLedgerPayment as $o)
                        <tr>
                            <td>{{$sr++}}</td>
                          
                            <td>{{$o->customer->first_name}}</td>
                            
                              <td>{{$o->amount - $o->discount}}</td>
                        </tr>
                        @endforeach
                        
                        <tfoot>
                
                <tr>
                    <th colspan="3" class="text-right">Total Amount:</th>
                    <th class="text-left" id="totalAmountLP"></th>
                </tr>
            </tfoot>
                    
                    </tbody>
                </table>
            </div>
            
            <br>
            <br>
                      
                    </div>
                </div>
            </div>
            
            
            </div>



@stop

@section('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap4.js"></script>
    <script>
    
   $('#todaySales').DataTable({
            "paging": false,
            
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api(),
                    totalProducts = api.column(3).data().reduce(function(a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0),
                    totalAmount = api.column(4).data().reduce(function(a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0);

                $('#totalProducts').text(totalProducts);
                $('#totalAmount').text(totalAmount.toFixed(2)); // Assuming total amount is in currency format
            }
        
        });
        
        
         $('#todayRetailSales').DataTable({
            "paging": false,
            
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api(),
                    totalProducts = api.column(3).data().reduce(function(a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0),
                    totalAmount = api.column(4).data().reduce(function(a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0);

                $('#totalProductsR').text(totalProducts);
                $('#totalAmountR').text(totalAmount.toFixed(2)); // Assuming total amount is in currency format
            }
        
        });
        
         $('#todayWholeSales').DataTable({
            "paging": false,
            
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api(),
                    totalProducts = api.column(3).data().reduce(function(a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0),
                    totalAmount = api.column(4).data().reduce(function(a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0);

                $('#totalProductsW').text(totalProducts);
                $('#totalAmountW').text(totalAmount.toFixed(2)); // Assuming total amount is in currency format
            }
        
        });
        
        
        $('#todayWholeSalesCredit').DataTable({
            "paging": false,
            
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api(),
                    totalProducts = api.column(3).data().reduce(function(a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0),
                    totalAmount = api.column(4).data().reduce(function(a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0);

                $('#totalProductsWC').text(totalProducts);
                $('#totalAmountWC').text(totalAmount.toFixed(2)); // Assuming total amount is in currency format
            }
        
        });
        
        
           $('#todayWholeSalesPaid').DataTable({
            "paging": false,
            
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api(),
                    totalProducts = api.column(3).data().reduce(function(a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0),
                    totalAmount = api.column(4).data().reduce(function(a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0);

                $('#totalProductsWP').text(totalProducts);
                $('#totalAmountWP').text(totalAmount.toFixed(2)); // Assuming total amount is in currency format
            }
        
        });
        
        
           $('#todayLedgerPayment').DataTable({
            "paging": false,
            
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api(),
                    totalAmount = api.column(2).data().reduce(function(a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0);
                $('#totalAmountLP').text(totalAmount.toFixed(2)); // Assuming total amount is in currency format
            }
        
        });
        
        
    function scrollToTable(id) {
        const table = document.getElementById(id);
        const offset = document.querySelector('.main-header').offsetHeight; // Adjust this selector to your navbar's selector
        const tablePosition = table.getBoundingClientRect().top + window.scrollY - offset;

        window.scrollTo({
            top: tablePosition,
            behavior: 'smooth'
        });
    }
    </script>
@stop
