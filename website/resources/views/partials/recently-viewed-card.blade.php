<div class="js-slide products-group">
    <div class="product-item">
        <div class="product-item__outer h-100">
            <div class="product-item__inner px-wd-4 p-2 p-md-3">
                <div class="product-item__body pb-xl-2">
                    @php
                        $productSlug = $product->slug ?: \Illuminate\Support\Str::slug($product->title);
                    @endphp
                    @if(!$product->in_stock)
                        <div class="position-absolute" style="top: 5px; left: 15px; z-index: 2;">
                            <span class="badge badge-danger badge-pill font-size-10">Out of Stock</span>
                        </div>
                    @endif
                    <div class="mb-2"><a href="{{ route('brands.products', $product->brand_id) }}" class="font-size-12 text-gray-5">{{ $product->brand_title ?? 'Category' }}</a></div>
                    <h5 class="mb-1 product-item__title"><a href="{{ route('productdetail', $productSlug) }}" class="text-blue font-weight-bold">{{ $product->title }}</a></h5>
                    <div class="mb-2">
                        <a href="{{ route('productdetail', $productSlug) }}" class="d-block text-center"><img class="img-fluid" src="{{ !empty($product->image_url) ? env('BACKEND_IMAGE_URL') . $product->image_url : env('BACKEND_IMAGE_URL') . 'default.jpeg' }}" alt="{{ $product->title }}" onerror="this.onerror=null;this.src='{{ env('BACKEND_IMAGE_URL') . 'default.jpeg' }}';"></a>
                    </div>
                    <div class="flex-center-between mb-1">
                        <div class="prodcut-price">
                            <div class="text-gray-100">Rs.{{ number_format($product->effective_price ?? $product->price, 2) }}</div>
                        </div>
                        {{-- <div class="d-none d-xl-block prodcut-add-cart">
                            <a href="{{ url('/product/' . $product->id) }}" class="btn-add-cart btn-primary transition-3d-hover"><i class="ec ec-eye"></i></a>
                        </div> --}}
                    </div>
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
</div>
