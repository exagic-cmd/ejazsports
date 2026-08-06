<li class="col-xl-6 product-item max-width-xl-100 remove-divider">
    <div class="product-item__outer w-100">
        <div class="product-item__inner bg-white p-3 remove-prodcut-hover">
            <div class="product-item__body pb-xl-2">
                @php
                    $productSlug = $product->slug ?: \Illuminate\Support\Str::slug($product->title);
                @endphp
                @if(!$product->in_stock)
                    <div class="position-absolute" style="top: 5px; left: 15px; z-index: 2;">
                        <span class="badge badge-danger badge-pill font-size-10">Out of Stock</span>
                    </div>
                @endif
                <div class="mb-2"><a href="{{ route('brands.products', $brand->id ?? $product->brand_id) }}" class="font-size-12 text-gray-5">{{ $brand->title ?? $product->brand_title ?? '' }}</a></div>
                <h5 class="mb-1 product-item__title"><a href="{{ route('productdetail', $productSlug) }}" class="text-blue font-weight-bold">{{ Str::limit($product->title, 40) }}</a></h5>
                <div class="mb-2">
                    <a href="{{ route('productdetail', $productSlug) }}" class="d-block text-center"><img class="img-fluid" src="{{ !empty($product->image) ? env('BACKEND_IMAGE_URL') . $product->image : (!empty($product->image_url) ? env('BACKEND_IMAGE_URL') . $product->image_url : env('BACKEND_IMAGE_URL') . 'default.jpeg') }}" alt="{{ $product->title }}" onerror="this.onerror=null;this.src='{{ env('BACKEND_IMAGE_URL') . 'default.jpeg' }}';"></a>
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
        </div>
    </div>
</li>
