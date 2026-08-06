<div>
    <div class="pos-payment-main">
        <div class="pos-payment-header">
            <div class="title">
                Payment
                <a href="{{route('dashboard')}}"><i class="fa fa-close"></i></a></div>
        </div>
        <div class="pos-product-container">
            <div class="checkout_details">
                @if($result->data->customer)
                <div class="customer_detail">
                    <div class="name"><i class="fa fa-user-circle"></i> <input type="hidden" name="customer_id"
                            id="customer_id" value="{{$result->data->customer->id}}">
                        <span>{{$result->data->customer->first_name}} {{$result->data->customer->last_name}}</span>
                    </div>
                    <div class="address"><i class="fa fa-phone"></i>
                        <span>{{$result->data->customer->phone_number}}</span></div>
                    <div class="address"><i class="fa fa-mail-forward"></i> <span>{{$result->data->customer->email}}
                        </span></div>
                </div>
                @else
                    <input type="hidden" name="customer_id" id="customer_id" value="1">
                @endif
                <div class="payment_detail">
                    <ul class="vertical-tab">
                        <!--<li class="vertical-nav active"><a href="#" class="">-->
                        <!--            Cash Payment-->
                        <!--        </a></li>-->
                        <!--<li class="vertical-nav"><a onclick="createSaleWithPrint()" class="">-->
                        <!--    Credit-->
                        <!--</a></li>-->
                        <select class="form-control" name="return_type" id="return_type">
                            <option value="1">Adjust in Legder</option>
                            <option value="2">Cash Return</option>
                        </select>
                        <br>
                        <br>
                        <br>
                        <select class="form-control" name="adjust_type" id="adjust_type">
                            <option value="1">Adjust in Stock</option>
                            <option value="2">Adjust in Damage</option>
                        </select>

                    </ul>
                    <div class="vertical-tabcontent">
                        <div class="vertical-content">
                            <div class="pull-left total-details">
                                <div class="payment-total"><span class="amount">Rs.
                                        {{number_format($result->data->balance)}}</span> <span class="text">Previous
                                        Balance</span></div>
                                <div class="payment-total"><span class="amount">Rs.
                                        {{number_format($result->data->total)}}</span> <span class="text">Bill
                                        Amount</span></div>

                                <div class="payment-total"><span class="amount" id="tAmountF">Rs.
                                        {{number_format($result->data->balance - $result->data->total )}}</span> <span
                                        class="text">Total</span></div>

                                <input type="hidden" name="mannual_return" id="mannual_return" value="1">
                                <!--<div class="payment-total"><span class="amount" id="change">0</span> <span class="text">Change</span></div>-->
                            </div>
                            <div class="text-right">
                                <div class="control-group" style="margin: 0px;"><textarea name="comment" id="comment"
                                        placeholder="Add order note here..." class="control" data-vv-id="4"
                                        aria-required="false" aria-invalid="false" style="width: 80%;"></textarea>
                                    <!---->
                                </div>
                            </div>
                            <div class="pos-action text-right" style="padding-right: 0px;"><button type="button"
                                    class="btn btn-lg btn-pos-primary" id="btn-pay" onclick="createReturn()"><i
                                        class="fa fa-money"></i> Submit
                                </button></div>
                            <!---->
                            <!---->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!---->
    <!---->
</div>
<input type="hidden" id="total" name="total" value="{{$result->data->total}}">

<input type="hidden" id="aftotal" value="{{$result->data->total}}">
