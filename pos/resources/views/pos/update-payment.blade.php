    <div><div class="pos-payment-main"><div class="pos-payment-header"><div class="title">
                    Payment
                    <a href="{{route('dashboard')}}"><i class="fa fa-close" ></i></a></div></div>
            <div class="pos-product-container">
                <div class="checkout_details">
                    @if($result->data->customer)
                    @php
                    //dd($result->data);
                    @endphp
                    <div class="customer_detail"><div class="name"><i class="fa fa-user-circle"></i> <input type="hidden" name="customer_id" id="customer_id" value="{{$result->data->customer->id}}">
                            <span>{{$result->data->customer->first_name}} {{$result->data->customer->last_name}}</span></div>
                        <div class="address"><i class="fa fa-phone"></i> <span>{{$result->data->customer->phone_number}}</span></div>
                        <div class="address"><i class="fa fa-mail-forward"></i> <span>{{$result->data->customer->email}} </span></div>
                    </div>
                    @endif
                    <div class="payment_detail"><ul class="vertical-tab">
                        <!--<li class="vertical-nav active"><a href="#" class="">-->
                        <!--            Cash Payment-->
                        <!--        </a></li>-->

                                <!--<li class="vertical-nav"><a onclick="createSaleWithPrint()" class="">-->
                                <!--    Credit-->
                                <!--</a></li>-->

                                <button type="button" onclick="openPasswordDiv()" >Apply Discount</button>

                                <div id="dis-div" style="display:none;">

                                <h3>Cash Discount</h3>

                                <select style="width:200px;" class="control select2" id="tDiscount" >
                                    <?php
                                    for($i = 0;$i<=$result->data->margin;$i++) {?>
                                    <option value="{{$i}}">{{$i}}</option>

                                    <?php }?>

                                </select>

                                </div>

                                </ul>

                                <input type="hidden" id="margin" value="{{$result->data->margin}}">


                                <div class="vertical-tabcontent"><div class="vertical-content"><div class="pull-left total-details">
                                    <div class="payment-total"><span class="amount" >Rs. {{number_format($result->data->balance)}}</span> <span class="text">Previous Balance</span></div>
                                    <div class="payment-total"><span class="amount">Rs. {{number_format($result->data->total)}}</span> <span class="text">Bill Amount</span></div>

                                    <div class="payment-total"><span class="amount" id="tAmountF">Rs. {{number_format($result->data->total + $result->data->balance)}}</span> <span class="text">Total</span></div>

                                    @if($result->data->customer)
                                    <div class="payment-total"><span class="amount" id="tendered">0</span> <span class="text" >Pay Amount</span></div>

                                    @else
                                    <div class="payment-total"><span class="amount" id="tendered">{{$result->data->total}}</span> <span class="text" >Pay Amount</span></div>
                                    @endif
                                    <!--<div class="payment-total"><span class="amount" id="change">0</span> <span class="text">Change</span></div>-->
                                    </div> <div class="pull-right"><div class="cart-calculator" id="calculator-row"><ul class="calculator-row"><li class="cal-li" id="btn1">1</li> <li class="cal-li" id="btn2">2</li> <li class="cal-li" id="btn3">3</li></ul> <ul class="calculator-row"><li class="cal-li" id="btn4">4</li> <li class="cal-li" id="btn5">5</li> <li class="cal-li" id="btn6">6</li></ul> <ul class="calculator-row"><li class="cal-li" id="btn7">7</li> <li class="cal-li" id="btn8">8</li> <li class="cal-li" id="btn9">9</li></ul> <ul class="calculator-row"><li class="cal-li" ></li> <li class="cal-li" id="btn0">0</li> <li class="cal-li" id="btn11">C</li></ul></div></div> <div class="text-right"><div class="control-group" style="margin: 0px;"><textarea name="comment" id="comment" placeholder="Add order note here..." class="control" data-vv-id="4" aria-required="false" aria-invalid="false" style="width: 80%;"></textarea> <!----></div></div> <div class="pos-action text-right" style="padding-right: 0px;"><button type="button" class="btn btn-lg btn-pos-primary"  id="btn-pay" onclick="createSaleWithPrint()"><i class="fa fa-money"></i>   Confirm Payment
                                    </button></div> <!----> <!----></div></div></div></div></div></div> <!----> <!----></div>

    <input type="hidden" id="total" name="total" value="{{$result->data->total}}">

    <input type="hidden" id="aftotal"  value="{{$result->data->total}}">






