@extends('layouts.app')

@section('content')
<div class="pos-content-container">
    <div class="pos-cashier-main">
        <div class="pos-nav-container">
            <ul class="pos-nav-lists">
                <li label="menu_count_0" class="pos-nav active">
                    <a href="#!" onclick="openToday()" id="today_css" class="nav-link router-link-exact-active router-link-active">
                        Today Expense
                    </a>
                </li>
                <li label="menu_count_1" class="pos-nav ">
                    <a href="#!" onclick="openPrevious()" id="previous_css" class="nav-link " aria-current="page">
                        Expense History
                    </a>
                </li>

                <div class="add_customer" style="padding-left: 150px;padding-top: 10px;font-size: 18px"><a href="#!" onclick="openExpenseModal()">

                        <div class="customer-add-text" style="color: #511c29;">
                            <i class="fa fa-plus"></i> Add Expense
                        </div></a>
                </div>

            </ul>
        </div>
        <div class="pos-nav-content" id="today">
            <div class="product-lowstock-panel">
                <div class="pos-product-container"><!---->
                    <div class="pos-setting-list row-grid-5">
                        <div class="pos-setting row-layout">
                            <div class="setting-list-name">
                                <div class="name">Today Bill</div>
                            </div>
                            <div class="setting-list-rate">
                                <?php $todayExpense = (array) $result->data->todayExpense;?>
                                {{count($todayExpense)}}
                            </div>
                        </div>
                        <div class="pos-setting row-layout">
                            <div class="setting-list-name">
                                <div class="name">Today Bill Amount</div>
                            </div>
                            <div class="setting-list-rate">
                                {{number_format($result->data->todayExpenseAmount)}}
                            </div>
                        </div>
                    </div>
                    <div class="pos-table-responsive" style="margin-top: 20px;">
                        <table class="pos-table">
                            <thead>
                            <tr>
                                <th class="text-left">Sr #</th>
                                <th class="text-left">Bill #</th>
                                <th class="text-left">Category</th>
                                <th class="text-left">Amount</th>
                                <th class="text-left">Detail</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <?php $c = 1;?>
                            @foreach($result->data->todayExpense as $expense)
                            <tr>
                                <td>{{$c++}}</td>
                                <td>{{$expense->bill_no}}</td>
                                <td>{{$expense->category_id == 1 ? 'Electricity Bill' : 'Refresment'}}</td>
                                <td><b>{{number_format($expense->amount)}}</b></td>
                                <td>{{$expense->detail}}</td>
                            </tr>
                                @endforeach
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="pos-nav-content" id="previous" style="display: none">
            <div class="product-lowstock-panel">
                <div class="pos-product-container"><!---->
                    <div class="pos-setting-list row-grid-5">
                        <div class="pos-setting row-layout">
                            <div class="setting-list-name">
                                <div class="name">Previous Bill</div>
                            </div>
                            <div class="setting-list-rate">
                                <?php $previousExpense = (array) $result->data->previousExpense;?>
                                {{count($previousExpense)}}
                            </div>
                        </div>
                        <div class="pos-setting row-layout">
                            <div class="setting-list-name">
                                <div class="name">Previous Bill Amount</div>
                            </div>
                            <div class="setting-list-rate">
                                {{number_format($result->data->previousExpenseAmount)}}
                            </div>
                        </div>
                    </div>
                    <div class="pos-table-responsive" style="margin-top: 20px;">
                        <table class="pos-table">
                            <thead>
                            <tr>
                                <th class="text-left">Sr #</th>
                                <th class="text-left">Date</th>
                                <th class="text-left">Bill #</th>
                                <th class="text-left">Category</th>
                                <th class="text-left">Amount</th>
                                <th class="text-left">Detail</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <?php $c = 1;?>
                            @foreach($result->data->previousExpense as $expense)
                                <tr>
                                    <td>{{$c++}}</td>
                                    <td>{{date('d-M-Y',strtotime($expense->date))}}</td>
                                    <td>{{$expense->bill_no}}</td>
                                    <td>{{$expense->category_id == 1 ? 'Electricity Bill' : 'Refresment'}}</td>
                                    <td><b>{{number_format($expense->amount)}}</b></td>
                                    <td>{{$expense->detail}}</td>
                                </tr>
                            @endforeach
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="display: none" id="expense-modal"><div id="addCustomer"><div class="pos-modal-overlay"></div> <div class="pos-modal-container"><div class="modal-header"><h4> Add Expense</h4> <i onclick="hideExpenseModal()" class="icon remove-icon"></i></div> <div class="modal-body"><div><div class="pos-discount-form">
                        <form autocomplete="off" action="{{route('expense.create')}}" method="POST">
                            @csrf
                            <div class="page-content">
                                <div class="form-container">
                                    <input type="hidden" name="store_id" value="{{Auth::user()->store_id}}">
                                    <div class="pos-customer-fields">
                                        <div class="control-group"><label for="gender" class="required">Category</label>
                                            <select name="category_id" class="control"  data-vv-id="15" aria-required="true" aria-invalid="false" style="width: 90%;">
                                                <option value="1">Electricity Bill</option> <option value="2">Refresment</option></select>
                                        </div>
                                        <div class="control-group"><label for="date_of_birth" class="required">Date</label>
                                            <input type="date" name="date" class="control" data-vv-id="16" aria-required="true" aria-invalid="false" style="width: 90%;"> <!----></div>
                                        <div class="control-group">
                                            <label for="first_name" >Bill #</label>
                                            <input type="text" name="bill_no" class="control"  style="width: 90%;">
                                            <!----></div>
                                        <div class="control-group">
                                            <label for="last_name" class="required">Amount</label>
                                            <input type="text" name="amount" class="control" data-vv-id="16" aria-required="true" aria-invalid="false" style="width: 90%;">
                                            <!----></div>
                                        <div class="control-group"><label for="email" >Detail</label>
                                            <textarea class="control" name="detail" style="width: 90%;"></textarea>
                                            <!----></div>

                                        <div class="pos-action text-center"><button type="submit" text="Save" class="btn btn-lg btn-pos-primary"> Save </button></div></div></div></div></form></div></div></div>
        </div>
    </div>
</div>
@stop

@section('js')
    <script>
        function openToday() {
            document.getElementById('app').style.opacity = '0.1';

            $('#today_css').addClass('router-link-exact-active router-link-active');
            document.getElementById('previous').style.display = 'none';
            document.getElementById('today').style.display = 'block';
            $('#previous_css').removeClass('router-link-exact-active router-link-active');

            document.getElementById('app').style.opacity = '1';
        }

        function openPrevious() {

            document.getElementById('app').style.opacity = '0.1';

            $('#previous_css').addClass('router-link-exact-active router-link-active');
            document.getElementById('today').style.display = 'none';
            document.getElementById('previous').style.display = 'block';
            $('#today_css').removeClass('router-link-exact-active router-link-active');

            document.getElementById('app').style.opacity = '1';
        }

        function openExpenseModal() {
            document.getElementById('expense-modal').style.display = 'block';
        }
        function hideExpenseModal() {
            document.getElementById('expense-modal').style.display = 'none';
        }
    </script>
    @stop
