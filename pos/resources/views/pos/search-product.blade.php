<?php $products = (array) $result->data->products;?>
<?php $bundles = (array) $result->data->bundles;?>
@if(count($products) == 0 && count($bundles) == 0)
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
                                <img src="{{asset('images/download.webp')}}">

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
                                <img src="{{asset('images/download.webp')}}">

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
<!-- Add bundle display section -->
<!-- Bundle display section - matching product layout but keeping original attributes -->
@foreach($result->data->bundles as $bundle)
    <div class="product-layout" onclick="addToCart({{$bundle->id}}, 0, true)">
        <div class="product-thumb">
            <!-- Simple ribbon (like products have) -->
            <div class="ribbon-wrapper">
                <div class="ribbon">Bundle</div>
            </div>

            @php
                // Original image handling - unchanged
                $bundleImage = $bundle->firstImage ?? ($bundle->images[0] ?? null);
            @endphp

            @if($bundleImage && isset($bundleImage->path))
                <img src="{{ env('BACKEND_IMAGE_URL') }}{{ $bundleImage->path }}">
            @else
                <img src="{{ asset('images/download.webp') }}">
            @endif
        </div>

        <div class="product-name" title="{{ $bundle->name ?? 'Bundle' }}">
            {{ strlen($bundle->name) > 40 ? substr($bundle->name, 0, 40) . '...' : $bundle->name }}
            @if(isset($bundle->short_desc))
                <br><small>{{ $bundle->short_desc }}</small>
            @endif
        </div>

        @if($result->data->priceShown && isset($bundle->additional_price))
            <div class="product-price">
                <span class="price">
                    Rs.{{ number_format($bundle->additional_price) }}
                </span>
            </div>
        @endif
    </div>
@endforeach


<input type="hidden" id="route" name="route" value="1">
