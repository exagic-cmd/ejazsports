@extends('layouts.app')



@section('content')
<div class="pos-content-container" id="pos-content-container">
    <div>
        <div class="pos-home-main">
            <div class="pos-categories">
                <ul class="category-section">
                    <li title="All" class="related_category focus-category" data-category-id="0">
                        All
                    </li>
                    <ul class="parent-categories">

                        <li data-category-id="1" title="nad" class="related_category">

                        </li>


                    </ul>
                    <!-- All Categories -->

                    <!---->
                </ul>
            </div>

            <div class="pos-product-container">
                <div class="product-list product-grid-5" id="product-list">

                    <div>
                        <div class="product-layout" onclick="addToCart(1)">

                            <div class="product-thumb">

                                <div class="ribbon-wrapper">
                                    <div class="ribbon">nad</div>
                                </div>


                                {{--                                                @if($p->thumbnail)--}}
                                {{--                                                    <?php $img = json_decode($p->thumbnail->img_url);?>--}}

                                {{--                                                    @if($img)--}}
                                {{--                                                        <?php $path = env('BACKEND_URL').$img->large->url;?>--}}
                                {{--                                                    @else--}}
                                {{--                                                        <?php $path = env('BACKEND_URL').'/no-image.jpg';?>--}}
                                {{--                                                    @endif--}}
                                {{--                                                @else--}}
                                {{--                                                    <?php $path = env('BACKEND_URL').'/no-image.jpg';?>--}}
                                {{--                                                @endif--}}

                                {{--                                                <img src="{{$path}}">--}}
                            </div>



                            <div class="product-name" title="nad">
                                nad<br>

                            </div>


                            <div class="product-price">
                                <span class="price">

                                    Rs. 150

                                </span>

                            </div>


                        </div>
                    </div>



                    <!---->
                    <!---->
                    <!---->
                    <!---->
                    <!---->
                </div>

            </div>
        </div>
        <div class="pos-cart-container" id="pos-cart-container" style="height: 597px;">

            <div class="pos-content">
                <div class="cart">
                    <div class="cart-header">
                        <div class="cart-hold-section"><span> Cart Details </span>
                            <div class="btn btn-sm btn-pos-default btn-hold"><i class="fa fa-pause"></i> <span
                                    class="hold_cart_count">0</span></div>
                        </div>
                        <div class="cart-count-section">
                            <div class="btn btn-sm btn-pos-primary cart-btn" onclick="clearCart()">Clear Cart</div>
                        </div>
                    </div>
                    <div class="pos-nav-content">
                        <div id="cart_count_0" class="pos-nav-pane active">
                            <ul class="cart_details">

                                <div class="message-alert"><span class="text-danger">Current cart is empty!</span></div>



                            </ul>
                        </div>
                    </div>
                </div>
                <div class="cart-total-container">
                    <div class="cart-total">
                        <div class="pos-table-responsive cart-totals">
                            <table class="pos-table">
                                <tbody>
                                    <tr>
                                        <td class="text-left">Sub Total</td>
                                        <td id="subtotal" class="text-right">
                                            Rs. 0
                                        </td>
                                    </tr>
                                    <tr>
                                        <td id="discname" class="text-left">
                                            Cash Discount
                                        </td>
                                        <td id="discount" class="text-right">
                                            Rs. 0
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left">Grand Total</td>
                                        <td class="text-right">Rs. 0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="cart-button-container pos-action"><button type="button"
                            class="btn btn-lg btn-pos-primary customer-btn" onclick="openCustomerModal()"><i
                                class="fa fa-user-circle"></i> <span><b>Mart Sale</b></span> <i
                                class="fa fa-pencil"></i></button> <button type="button" id="btn-pay"
                            class="btn btn-lg btn-pos-dark pay-btn" onclick="updatePayment()"><b>Pay</b></button>
                        <button type="button" class="btn btn-lg btn-pos-primary hold-btn" onclick="holdCartModal()"><i
                                class="fa fa-pause"></i> <b> Hold</b></button></div>
                </div>
                <!---->
                <!---->
                <!---->
            </div>

        </div>
        <!---->
        <!---->
        <!---->
    </div>
</div>
</div>
</div>
</div>
@stop
