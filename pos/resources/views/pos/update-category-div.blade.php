<?php $products = (array) $result->data->products;?>
@if(count($products) == 0)
    <span style="text-align: center;color: red"> No result found...</span>
@endif
@foreach($result->data->products as $pro)

@if($pro->variants)
    @foreach($pro->variants as $v)
        <div>
            
         
                <div class="product-layout" onclick="addToCart({{$pro->id}},{{$v->id}})">
                  
                   
                            <div class="product-thumb">

                                <div class="ribbon-wrapper">

                                    <div class="ribbon">{{$v->available_stock}}</div>
                                </div>

                                
                                @if($pro->thumbnail)

                                    <img src="{{env('BACKEND_IMAGE_URL').$pro->thumbnail->url}}">

                                @else
                                <img src="{{asset('images/1.jpg')}}">
                                
                                @endif
                            </div>



                            <div class="product-name"  title="{{$pro->title}}">
                                {{strlen($pro->title) > 40 ? substr($pro->title, 0, 40) . '...' : $pro->title}}<br>
                                <span style=" color: #db324d;"> {{$v->shade ? $v->shade : ''}}  {{$v->size ? ' - '.$v->size : ''}} </span>
                            </div>
                            
                            
                            @if($result->data->priceShown)
                            
                           
                                <div class="product-price">
                                                <span class="price">
                                                       Rs.{{number_format($v->additional_price)}}
                                                </span>

                                </div>
                        
                            
                            @endif

                            <!--@if($pro->discount_status)-->
                            <!--    <div class="product-price"><span class="price price-cross">-->
                            <!--                Rs.{{number_format($pro->price)}}-->
                            <!--            </span> <span class="special-price">-->
                            <!--                Rs.{{number_format($pro->price - $pro->discount_amount)}}-->
                            <!--            </span></div>-->
                            <!--@else-->
                            <!--    <div class="product-price">-->
                            <!--                    <span class="price">-->
                            <!--                           Rs.{{number_format($pro->price)}}-->
                            <!--                    </span>-->

                            <!--    </div>-->
                            <!--@endif-->


                        </div>
                </div>
    @endforeach
    
    @else 
    
      <div>
            
            
              
                <div class="product-layout" onclick="addToCart({{$pro->id}},0)">
                   
                            <div class="product-thumb">

                                <div class="ribbon-wrapper">

                                    <div class="ribbon">{{$pro->available_stock}}</div>
                                </div>

                                
                                @if($pro->thumbnail)

                                    <img src="{{env('BACKEND_IMAGE_URL').$pro->thumbnail->url}}">

                                @else
                                <img src="{{asset('images/1.jpg')}}">
                                
                                @endif
                            </div>



                            <div class="product-name"  title="{{$pro->title}}">
                                {{strlen($pro->title) > 40 ? substr($pro->title, 0, 40) . '...' : $pro->title}}<br>
                              
                            </div>
                            
                            @if($result->data->priceShown)
                            @if($pro->discount_status)
                                <div class="product-price"><span class="price price-cross">
                                            Rs.{{number_format($pro->price)}}
                                        </span> <span class="special-price">
                                            Rs.{{number_format($pro->price - $pro->discount_amount)}}
                                        </span></div>
                            @else
                                <div class="product-price">
                                                <span class="price">
                                                       Rs.{{number_format($pro->price)}}
                                                </span>

                                </div>
                            @endif
                            
                            @endif
                            
                            
                            

                            <!--@if($pro->discount_status)-->
                            <!--    <div class="product-price"><span class="price price-cross">-->
                            <!--                Rs.{{number_format($pro->price)}}-->
                            <!--            </span> <span class="special-price">-->
                            <!--                Rs.{{number_format($pro->price - $pro->discount_amount)}}-->
                            <!--            </span></div>-->
                            <!--@else-->
                            <!--    <div class="product-price">-->
                            <!--                    <span class="price">-->
                            <!--                           Rs.{{number_format($pro->price)}}-->
                            <!--                    </span>-->

                            <!--    </div>-->
                            <!--@endif-->


                        </div>
                </div>
    
    @endif

@endforeach

<input type="hidden" id="route" name="route" value="1">
