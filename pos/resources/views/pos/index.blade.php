@extends('layouts.app')


@section('css')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@stop

@section('content')
    <div class="pos-content-container" id="pos-content-container">
        <div><div class="pos-home-main">
            <div class="pos-categories">

                </div>

                <div class="pos-product-container">
                    <div class="product-list product-grid-5" id="product-list">



                        <!----> <!----> <!----> <!----> <!---->
                    </div>


                </div></div> <div class="pos-cart-container" id="pos-cart-container" >

                    <div class="pos-content"><div class="cart"><div class="cart-header"><div class="cart-hold-section"><span> Cart Details </span><div class="btn btn-sm btn-pos-default btn-hold"><a href="{{route('sale.hold')}}"><i class="fa fa-pause"></i> <span class="hold_cart_count" id="hold-count">0</span></a></div></div>
                            <div class="cart-count-section">
                                <div class="btn btn-sm btn-pos-primary cart-btn" onclick="clearCart()">Clear Cart</div></div>
                        </div> <div class="pos-nav-content"><div id="cart_count_0" class="pos-nav-pane active"><ul class="cart_details" style="padding-bottom: 120px;">

                                    <div class="message-alert"><span class="text-danger">Current cart is empty!</span></div>



                                </ul></div></div></div> <div class="cart-total-container"><div class="cart-total"><div class="pos-table-responsive cart-totals"><table class="pos-table"><tbody> <tr><td class="text-left">Grand Total</td> <td class="text-right">Rs. 0</td></tr></tbody></table></div></div> <div class="cart-button-container pos-action"><button type="button"  class="btn btn-lg btn-pos-primary customer-btn"><a href="{{route('customer.data')}}"><i class="fa fa-user-circle"></i> <span><b id="customer_name" style="color: white;"> Customer</b></span> <i class="fa fa-pencil"></i></a></button> <button type="button" id="btn-pay" class="btn btn-lg btn-pos-dark pay-btn" onclick="holdCartModal()"><b>Hold</b></button> <button type="button" class="btn btn-lg btn-pos-primary hold-btn" onclick="updatePayment"> <b> Pay</b></button></div></div> <!----> <!----> <!----></div>
            </div> <!----> <!----> <!----></div>
        </div>
    </div>
    </div>
    </div>
    <input type="hidden" name="customer_id" id="customer_id" value="1">
    <input type="hidden" id="route" name="route" value="1">
@stop
@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script type="text/javascript">
        if(sessionStorage.getItem('cartProducts') === null) {
            document.getElementById('customer_name').innerHTML = 'Customer';
        } else{
            cartProducts = JSON.parse(sessionStorage.getItem('cartProducts'));
            discount_id = sessionStorage.getItem('discount');
            let url = '{{route('pos.cart.data')}}';
            fetch(url, {
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json, text-plain, */*",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": token
                },
                method: 'post',
                credentials: "same-origin",
                body: JSON.stringify({
                    cart: cartProducts,
                    discount_id: discount_id,
                    customer_id: sessionStorage.getItem('customer_id'),
                    manual_return_only: sessionStorage.getItem('manual_return_only') || '0'
                })
            })
            .then(async (response) => {
                if(!response.ok)
                throw await response.json();
                return response.text();
            })
                .then(function (html) {
                    document.getElementById('pos-cart-container').innerHTML = html;

                    // Sync manual return checkbox and buttons state
                    const isManualReturn = sessionStorage.getItem('manual_return_only') === '1';
                    const checkbox = document.getElementById('manual_return_checkbox');
                    if (checkbox) {
                        checkbox.checked = isManualReturn;
                    }
                    updateButtonStates(isManualReturn);

                    if(sessionStorage.getItem('holdCarts') !== null) {
                        holdCarts = JSON.parse(sessionStorage.getItem('holdCarts'));
                        document.getElementsByClassName('hold_cart_count')[0].innerHTML = holdCarts.length;
                    }
                    if(sessionStorage.getItem('customer_id') === null) {
                    }else {
                        $('#customer_id').val(sessionStorage.getItem('customer_id'));
                        document.getElementById('customer_name').innerHTML = sessionStorage.getItem('customer_name');

                    }
                })
                .catch(function(error) {
                    toastr.error(error);

                });
        }
        if(sessionStorage.getItem('holdCarts') === null) {
        }
        else {
            holdCarts = JSON.parse(sessionStorage.getItem('holdCarts'));
            document.getElementById('hold-count').innerHTML = holdCarts.length;
        }
        if(sessionStorage.getItem('customer_id') === null) {
            document.getElementById('customer_name').innerHTML = 'Customer';
            }else {
                $('#customer_id').val(sessionStorage.getItem('customer_id'));
                document.getElementById('customer_name').innerHTML = sessionStorage.getItem('customer_name');
            }
    </script>
    <script>
    function openPasswordDiv() {
        let person = prompt("Please enter password");
if (person == 12345) {
  document.getElementById('dis-div').style.display = 'block';
}
    }
    $(document).ready(function() {
    // $('.select2').select2();
});
</script>
    @stop


