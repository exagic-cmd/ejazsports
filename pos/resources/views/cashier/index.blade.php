@extends('layouts.app')

@section('content')
    <div class="pos-content-container"><div class="pos-cashier-main"><div class="pos-nav-container">
                <ul class="pos-nav-lists">
                    <li label="menu_count_0" class="pos-nav active">
                        <a href="#!"  onclick="openNewClosingDiv()" id="new_css" class="nav-link router-link-exact-active router-link-active" aria-current="page">
                            Close Counter
                        </a></li><li label="menu_count_1" class="pos-nav">
                        <a href="#!" onclick="openAllClosingDiv()" id="all_css" class="nav-link">
                            All Closings
                        </a></li>
                </ul></div>
            <div class="pos-nav-content" id="new-closing" >
                <div><div class="cashier-close-panel pos-col-12">
                        <!----> <form autocomplete="off" method="POST" action="{{route('closing.create')}}">
                            @csrf
                            <input type="hidden" name="store_id" value="{{Auth::user()->store_id}}">
                            <div class="pos-col-4">
                                <div class="container-panel-header">
                                    <h3> Drawer Amount Details </h3></div>
                                <div class="cashier_content">
                                    <div class="detail_section">
                                        <label>Opening Amount</label> <label>{{number_format($result->data->openingBalance)}}</label></div>
                                    <div class="detail_section"><label>Today's Total Cash Amount</label>
                                        <label class="cash_total">
                                            {{number_format($result->data->cashBills)}}
                                        </label></div>  <div class="detail_section">
                                        <label>Today's Total Return Amount</label>
                                        <label class="cash_total">
                                            {{number_format($result->data->returnBills)}}
                                        </label>
                                    </div>
                                    <div class="detail_section">
                                        <label>Today's Total Expense</label>
                                        <label class="cash_total">
                                            {{number_format($result->data->expenseAmount)}}
                                        </label>
                                    </div>
                                    <div class="detail_section">
                                        <label>Expected Amount In Drawer</label>
                                        <label class="main_total">
                                            {{number_format($result->data->openingBalance + $result->data->cashBills  - $result->data->returnBills - $result->data->expenseAmount)}}
                                        </label></div></div>
                                <input type="hidden" id="exp_amount" value="{{$result->data->openingBalance + $result->data->cashBills  - $result->data->returnBills - $result->data->expenseAmount}}">
                            </div>
                            <div class="pos-col-4">
                                <div class="container-panel-header"><h3>
                                        Counted Drawer Amount </h3></div>
                                <div class="cashier_content">

                                    <div class="detail_section control-group" style="padding: 0px;    margin-bottom: 0px;"><label>
                                            Amount Count
                                        </label> 5 *  <input type="number" onkeyup="updateTotalAmount()" onmouseup="updateTotalAmount()" id="5_amount" value="0" name="5_amount" class="control">
                                    </div>

                                    <div class="detail_section control-group" style="padding: 0px;    margin-bottom: 0px;">10 * <input type="number" onkeyup="updateTotalAmount()" onmouseup="updateTotalAmount()" id="10_amount" value="0" name="10_amount" class="control">
                                    </div>

                                    <div class="detail_section control-group" style="padding: 0px;    margin-bottom: 0px;">20 * <input type="number" onkeyup="updateTotalAmount()" onmouseup="updateTotalAmount()" id="20_amount" value="0" name="20_amount" class="control">
                                    </div>

                                    <div class="detail_section control-group" style="padding: 0px;    margin-bottom: 0px;">50 *  <input type="number" onkeyup="updateTotalAmount()" onmouseup="updateTotalAmount()" id="50_amount" value="0" name="50_amount" class="control">
                                    </div>
                                    <div class="detail_section control-group" style="padding: 0px;    margin-bottom: 0px;">100 *  <input type="number" onkeyup="updateTotalAmount()" onmouseup="updateTotalAmount()" id="100_amount" value="0" name="100_amount" class="control">
                                    </div>
                                    <div class="detail_section control-group" style="padding: 0px;    margin-bottom: 0px;">500 *  <input type="number" onkeyup="updateTotalAmount()" onmouseup="updateTotalAmount()" id="500_amount" value="0" name="500_amount" class="control">
                                    </div>
                                    <div class="detail_section control-group" style="padding: 0px;    margin-bottom: 0px;">1000 *  <input type="number" onkeyup="updateTotalAmount()" onmouseup="updateTotalAmount()" id="1000_amount" value="0" name="1000_amount" class="control">
                                    </div>
                                    <div class="detail_section control-group" style="padding: 0px;    margin-bottom: 0px;">5000 * <input type="number" onkeyup="updateTotalAmount()" onmouseup="updateTotalAmount()" id="5000_amount" value="0" name="5000_amount" class="control">
                                    </div>

                                    </div></div>
                            <div class="pos-col-4"><div class="container-panel-header"><h3> Closing Drawer Details </h3>
                                </div> <div class="cashier_content">
                                    <div class="detail_section"><label>Total Amount In Drawer</label></div>
                                    <div class="detail_section control-group"><label>
                                            Total Amount ($)
                                        </label> <input type="text" readonly id="total_amount" name="total_amount" class="control">
                                    </div>

                                    <div class="detail_section control-group"><label style="color: red;">
                                            Difference Amount ($)
                                        </label> <input type="text" readonly id="diff_amount" name="diff_amount" class="control">
                                    </div>

                                    <div class="detail_section control-group">
                                        <label for="remark" class="required">Remark</label>
                                        <textarea name="remark" id="note" placeholder="Comment regarding cash balance.."
                                                  class="control" data-vv-id="6" aria-required="true" aria-invalid="false"></textarea>
                                        <!----></div></div> <div class="pos-action text-left"><button type="submit" text="Close Drawer" class="btn btn-lg btn-pos-primary"> Close Drawer </button></div></div></form></div>
                </div>
            </div>

            <div class="pos-nav-content" id="all-closing" style="display:none;">
                <div><div class="cashier-close-panel pos-col-12">

                        <div class="pos-table-responsive" style="margin-top: 20px;">
                            <table class="pos-table">
                                <thead>
                                <tr>
                                    <th class="text-left">Sr #</th>
                                    <th class="text-left">Date</th>
                                    <th class="text-left">Opening Balance</th>
                                    <th class="text-left">Cash Bills</th>
                                    <th class="text-left">Card Bills</th>
                                    <th class="text-left">Return Bills</th>
                                    <th class="text-left">Expense</th>
                                    <th class="text-left">Closing Amount</th>
                                    <th class="text-left">Note</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <?php $c = 1;?>
                                @foreach($result->data->allClosings as $cls)
                                    <tr>
                                    <td>{{$c++}}</td>
                                    <td>{{date('d-m-Y',strtotime($cls->date))}}</td>
                                    <td>{{number_format($cls->opening_balance)}}</td>
                                    <td>{{number_format($cls->cash_bills)}}</td>
                                    <td>{{number_format($cls->card_bills)}}</td>
                                    <td>{{number_format($cls->return_bills)}}</td>
                                    <td>{{number_format($cls->expense)}}</td>
                                    <td>{{number_format($cls->closing_amount)}}</td>
                                    <td>{{$cls->note}}</td>
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
    @stop

@section('js')
    <script>
        function openNewClosingDiv() {
            document.getElementById('app').style.opacity = '0.1';

            $('#new_css').addClass('router-link-exact-active router-link-active');
            document.getElementById('all-closing').style.display = 'none';
            document.getElementById('new-closing').style.display = 'block';
            $('#all_css').removeClass('router-link-exact-active router-link-active');

            document.getElementById('app').style.opacity = '1';
        }

        function openAllClosingDiv() {

            document.getElementById('app').style.opacity = '0.1';

            $('#all_css').addClass('router-link-exact-active router-link-active');
            document.getElementById('new-closing').style.display = 'none';
            document.getElementById('all-closing').style.display = 'block';
            $('#new_css').removeClass('router-link-exact-active router-link-active');

            document.getElementById('app').style.opacity = '1';
        }

        function updateTotalAmount() {
            fiveAmount = parseInt($('#5_amount').val()) * 5;
            tenAmount = parseInt($('#10_amount').val()) * 10;
            twentyAmount = parseInt($('#20_amount').val()) * 20;
            fiftyAmount = parseInt($('#50_amount').val()) * 50;
            hundredAmount = parseInt($('#100_amount').val()) * 100;
            fiveHundredAmount = parseInt($('#500_amount').val()) * 500;
            oneThousandAmount = parseInt($('#1000_amount').val()) * 1000;
            fiveThousandAmount = parseInt($('#5000_amount').val()) * 5000;

            totalAmount = fiveAmount + tenAmount + twentyAmount + fiftyAmount + hundredAmount + fiveHundredAmount + oneThousandAmount + fiveThousandAmount;



            $('#total_amount').val(totalAmount);

            $('#diff_amount').val(parseInt($('#exp_amount').val() ) - totalAmount);
        }
        
        
        
        document.getElementById('search1').style.display = 'none';
        document.getElementById('search2').style.display = 'none';
    </script>
    @stop

