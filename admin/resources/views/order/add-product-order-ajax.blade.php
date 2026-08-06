<br><br><br><div>

    <div class="row mb-4">
        <label class="col-lg-3 col-form-label">Product Name</label>

        <div class="col-lg-9">
            <select name="product_id[]" class="form-control">
                <option selected value="{{$product->id}}">{{$product->product_heading}}</option>
            </select>


        </div>
        <!-- col.// -->
    </div>

    <div class="row mb-4">
        <label class="col-lg-3 col-form-label">Product Variant</label>

        <div class="col-lg-9">
            <select class="form-control select2" name="variant_id[]">
                @foreach($product->variants as $var)

                        @if($var->online_available_stock > 0)

                            <option selected value="{{$var->id}}">{{$var->barcode}} - {{$var->shade}} - {{$var->size}}</option>
                        @else
                            <option style="cursor: not-allowed" disabled value="{{$var->id}}">{{$var->barcode}} - {{$var->shade}} - {{$var->size}}</option>
                        @endif



                @endforeach

            </select>
            @error('variant_id')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <!-- col.// -->
    </div>

    <div class="row mb-4">
        <label class="col-lg-3 col-form-label">Price</label>

        <div class="col-lg-9">
            <input type="numeric" readonly value="{{$product->discount_status ? $product->price - $product->discount_amount : $product->price }}" name="price[]" class="form-control price" placeholder="Type here">
            @error('price')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <!-- col.// -->
    </div>


    <div class="row mb-4">
        <label class="col-lg-3 col-form-label">Quantity</label>

        <div class="col-lg-9">
            <input type="number"  value="1" name="quantity[]" class="form-control quantity" placeholder="Type here">
            @error('quantity')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <!-- col.// -->
    </div>

    <a href="#" class="remove_field">Remove</a>
    <br><br>
</div>
