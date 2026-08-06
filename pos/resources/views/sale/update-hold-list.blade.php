   <div class="pos-sales-main"><div class="pos-nav-container"><ul class="pos-nav-lists">
            
            <!--<li label="menu_count_0" class="pos-nav"><a href="{{route('sales.data')}}"  class="nav-link" >-->
            <!--        Sale History-->
            <!--    </a></li>-->
                <li label="menu_count_1" class="pos-nav"><a href="#!" class="nav-link router-link-exact-active router-link-active" aria-current="page" ><span class="hold_cart_count" id="hold-count">0</span>
                    Hold Sale
                </a></li></ul></div> <div class="pos-nav-content" style="height: 553px;"><div class="sale-hold-panel">
            <div class="sale-hold-list">

                @if($result->data->cart)

                    <div class="pos-product-container"><div class="product-list row-grid-3">

                            @foreach($result->data->cart as $c)
                                <div class="product-layout"><div class="order-detail"><div class="order_note"><i class="fa fa-info-circle"></i>
                                            Note
                                        </div> <div class="order_date_time">
                                            {{date('h:m A d M, Y',strtotime($c->time))}}
                                        </div></div> <div class="order_hold_note">
                                        {{$c->note}}
                                    </div> <div class="order-product-container"><div class="item-heading">
                                            Hold Products Details:
                                        </div> <div class="item-list"><div class="product_info">
                                            <?php $products  = (array) $result->data->products;?>
                                            <?php $variants  = (array) $result->data->variants;?>
                                                @foreach(json_decode($c->products) as $p)
                                                    <div style="font-size: 14px;"><div class="product_name">
                                                            
                                                        {{$products[$p->id]->title}} 
                                                        @if($variants[$p->variant_id])
                                                        <small>({{$variants[$p->variant_id]->size}} - {{$variants[$p->variant_id]->shade}})</small>
                                                        
                                                        @endif
                                                        <!----></div> <div class="product_qty">
                                                            x {{$p->qty}}
                                                        </div> <div class="product_price">
                                                            @if($products[$p->id]->discount_status == true)
                                                                Rs. {{$products[$p->id]->price - $products[$p->id]->discount_amount}}
                                                            @else
                                                                Rs. {{$products[$p->id]->price}}
                                                            @endif
                                                        </div></div>
                                                @endforeach

                                            </div><div class="product_info"><!----></div><div class="product_info"><!----></div></div></div> <div class="hold_footer"><div class="pull-left"><button type="button" text=" Add To Cart" class="btn btn-md btn-pos-primary" onclick="addHoldCart({{$c->id}})"><i class="fa fa-cart-plus"></i>
                                                Add To Cart
                                            </button></div> <div class="pull-right"><button type="button" text=" Remove" class="btn btn-md btn-pos-default" onclick="removeHoldCart({{$c->id}})"><i class="fa fa-trash"></i>
                                                Remove
                                            </button></div></div></div>
                            @endforeach






                        </div></div>
                @else
                    <div class="pos-product-container"><div class="message-alert danger">
                            Warning: There is no hold order available!
                        </div></div>
                @endif


            </div> </div></div> <!----></div>