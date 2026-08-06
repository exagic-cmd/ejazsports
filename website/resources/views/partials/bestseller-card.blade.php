<li class="col-wd-3 col-md-4 product-item product-item__card pb-2 mb-2 pb-md-0 mb-md-0 border-bottom border-md-bottom-0">
    <div class="product-item__outer h-100">
        <div class="product-item__inner p-md-3 row no-gutters">
            @php
                $productSlug = $product->slug ?: \Illuminate\Support\Str::slug($product->title);
            @endphp
            @if(!$product->in_stock)
                <div class="position-absolute" style="top: 5px; left: 15px; z-index: 2;">
                    <span class="badge badge-danger badge-pill font-size-10">Out of Stock</span>
                </div>
            @endif
            <div class="col col-lg-auto product-media-left">
                <a href="{{ route('productdetail', $productSlug) }}" class="max-width-150 d-block">
                    <img class="img-fluid" src="{{ !empty($product->image_url) ? env('BACKEND_IMAGE_URL') . $product->image_url : env('BACKEND_IMAGE_URL') . 'default.jpeg' }}" alt="{{ $product->title }}" onerror="this.onerror=null;this.src='{{ env('BACKEND_IMAGE_URL') . 'default.jpeg' }}';">
                </a>
            </div>
            <div class="col product-item__body pl-2 pl-lg-3 mr-xl-2 mr-wd-1">
                <div class="mb-4">
                    <div class="mb-2"><a href="#" class="font-size-12 text-gray-5">{{ $product->category_title ?? $product->brand_title ?? 'Category' }}</a></div>
                    <h5 class="product-item__title"><a href="{{ route('productdetail', $productSlug) }}" class="text-blue font-weight-bold">{{ $product->title }}</a></h5>
                </div>
                <div class="flex-center-between mb-3">
                    <div class="prodcut-price">
                        <div class="text-gray-100">Rs.{{ number_format($product->effective_price ?? $product->price, 2) }}</div>
                    </div>
                    {{-- <div class="d-none d-xl-block prodcut-add-cart">
                        <a href="{{ url('/product/' . $product->id) }}" class="btn-add-cart btn-primary transition-3d-hover"><i class="ec ec-eye"></i></a>
                    </div> --}}
                </div>
                {{-- <div class="product-item__footer">
                    <div class="border-top pt-2 flex-center-between flex-wrap">
                        <a href="#" class="text-gray-6 font-size-13"><i class="ec ec-compare mr-1 font-size-15"></i> Compare</a>
                        <a href="#" class="text-gray-6 font-size-13"><i class="ec ec-favorites mr-1 font-size-15"></i> Wishlist</a>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
</li>
