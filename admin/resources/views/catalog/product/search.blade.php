@foreach($products as $product)
    <article class="itemlist">
        <div class="row align-items-center">
            <div class="col col-check flex-grow-0">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox">
                </div>
            </div>
            <div class="col-lg-3 col-sm-4 col-8 flex-grow-1 col-name">
                <a class="itemside" href="{{route('products.show',$product->id)}}">
                    <div class="left">
                        @if($product->thumbnail)
                            <img src="{{asset('storage/'.$product->thumbnail->url)}}" class="img-sm img-thumbnail" alt="Item">
                        @else
                            <img src ="{{asset('storage/default.jpeg')}}" class="img-sm img-thumbnail" alt="Item">

                        @endif
                    </div>
                    <div class="info">
                        <h6 class="mb-0">{{$product->title}}</h6><br>
                        <small >{{$product->available_stock}} (stock)</small>
                        @if($product->have_variants)
                            <small class="right-0"> <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{count($product->variants)}}</b> (variants)</small>
                        @endif
                    </div>
                </a>
            </div>


            <div class="col-lg-2 col-sm-2 col-4 col-status">
                <span>{{$product->brand ? $product->brand->title : ''}}</span>
            </div>
            <div class="col-lg-2 col-sm-2 col-4 col-date">
                <span>@if($product->categories)  @foreach($product->categories as $category) @if($loop->last) <b> @if($category->category){{$category->category->title }} @endif </b> @else <b>  @if($category->category) {{$category->category->title }} </b> , @endif @endif @endforeach @endif</span>
            </div>
            
            @can('Manage Pricing')

            <div class="col-lg-1 col-sm-2 col-4 col-price"><span>
                                @if($product->discount_status) <strike style="color: #c4bbbb;">{{$product->price}}</strike><br> {{$product->price - $product->discount_amount}} @else  {{$product->price}} @endif
                            </span></div>
                            
                            @endcan

            <div class="col-lg-1 col-sm-2 col-4 col-status">
                <span class="badge rounded-pill {{$product->status ?'alert-success' : 'alert-danger'}}">@if($product->status == 1)
                        Published
                    @elseif($product->status == 2)
                        Dis continue
                    @else
                        Un Published
                    @endif</span>
            </div>
            <div class="col-lg-2 col-sm-2 col-4 col-action text-end">
                {{--                            <a href="#" class="btn btn-sm font-sm rounded btn-brand"> <i class="material-icons md-edit"></i> Edit </a>--}}
                <a href="{{route('products.show',$product->id)}}" class="btn btn-sm font-sm btn-light rounded"> <i class="material-icons md-view_carousel"></i> Detail </a>
            </div>
        </div>
        <!-- row .// -->
    </article>

@endforeach

<!-- itemlist  .// -->
</div>
<!-- card-body end// -->
