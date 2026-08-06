@extends('layouts.app')


@section('css')
    <style>
        .select2-search__field {
            min-width:400px!important;
        }</style>
@stop

@section('content')

    <div class="row">
        <div class="col-9">
            <div class="content-header">
                <h2 class="content-title">Edit Product</h2>
                <div>
                    {{--                        <button class="btn btn-light rounded font-sm mr-5 text-body hover-up">Save to draft</button>--}}
                    <button onclick="setFormSubmitting();document.getElementById('form').submit()" class="btn btn-md rounded font-sm hover-up">Update</button>
                </div>
            </div>
        </div>
        <div class="col-9">
            <div class="card">
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row gx-5">
                        <aside class="col-lg-3 border-end">
                            <nav class="nav nav-pills flex-column mb-4">

                                <a class="nav-link a-general active"  onclick="updateDiv('general')" aria-current="page" href="#">General</a>
                                @can('Manage Pricing')
                                <a class="nav-link a-variant " onclick="updateDiv('variant')" href="#">Variants</a>
                                
                                <a class="nav-link a-price" onclick="updateDiv('price')" href="#price">Pricing</a>
                                @endcan
                                <a class="nav-link a-image" onclick="updateDiv('image')" href="#image">Images</a>
                        
                               

                            </nav>
                        </aside>


                        <div class="col-lg-9">
                            <form action="{{route('products.update',$product->id)}}" method="post" id="form" autocomplete="false" enctype="multipart/form-data">

                                @csrf
                                @method('PUT')
                                <section class="content-body p-xl-4" id="d-general">

                                    <!-- row.// -->
                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Brand</label>
                                        <div class="col-lg-9">

                                            <select  size="4" name="brand_id" class="form-control select2 @error('brand_id') is-invalid @enderror">
                                                @foreach($brands as $b)
                                                    @if($product->brand_id == $b->id)
                                                        <option selected value="{{$b->id}}">{{$b->title}}</option>
                                                    @else
                                                        <option value="{{$b->id}}">{{$b->title}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            @error('brand_id')
                                            <div @class('alert alert-danger')>{{$message}}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>

                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Categories </label>

                                        <div class="col-lg-9">

                                            <select   name="category_id[]" multiple class="form-control select2 @error('category_id') is-invalid @enderror">
                                                @foreach($categories as $c)
                                                    @if(in_array($c->id,$selectedCategories))
                                                        <option selected value="{{$c->id}}">{{$c->title}}</option>
                                                    @else
                                                        <option value="{{$c->id}}">{{$c->title}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                            <div @class('alert alert-danger')>{{$message}}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>

                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Product name <span style="color: red;">*</span></label>
                                        <div class="col-lg-9">
                                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ $product->title }}" placeholder="Type here">
                                            @error('title')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>

                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Product code </label>
                                        <div class="col-lg-9">
                                            <div class="input-group">
                                                <input type="text" name="code" id="product_code" class="form-control @error('code') is-invalid @enderror" value="{{ $product->code }}" placeholder="Type here">
                                                <button type="button" class="btn btn-outline-secondary" id="btn-generate-sku">Auto Generate SKU</button>
                                            </div>
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>
                                  
                                    <!-- row.// -->
                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Short Description</label>
                                        <div class="col-lg-9">
                                            <textarea class="form-control @error('short_description') is-invalid @enderror" name="short_description" placeholder="Type here" rows="4">{{$product->short_description}}</textarea>
                                            @error('short_description')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>
                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Long Description</label>
                                        <div class="col-lg-9">
                                            <textarea class="form-control @error('full_description') is-invalid @enderror" name="full_description" placeholder="Type here" rows="4">{{$product->long_description}}</textarea>
                                            @error('full_description')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>
                                    
                                   
                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Weight </label>
                                        <div class="col-lg-9">
                                            <input type="text" name="weight" class="form-control @error('weight') is-invalid @enderror" value="{{ $product->weight }}" placeholder="Type here">
                                            @error('weight')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>


                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Status</label>
                                        <div class="col-lg-9">
                                            <label class="form-check my-2">
                                                @if($product->status == 1)
                                                    <input type="checkbox" name="status" value="1" class="form-check-input " checked="">
                                                @elseif($product->status == 0)
                                                    <input type="checkbox" name="status" value="1" class="form-check-input " >
                                                @endif

                                                <span class="form-check-label">Enable this product </span>
                                            </label>
                                            <label class="form-check my-2">
                                                @if($product->status == 2)
                                                <input type="checkbox" name="status" checked="" value="2" class="form-check-input " >
                                                <span class="form-check-label">Discontinue this product </span>
                                                @else
                                                <input type="checkbox" name="status" value="2" class="form-check-input " >
                                                <span class="form-check-label">Discontinue this product </span>
                                                @endif
                                            </label>
                                            <label class="form-check my-2">
                                                @if($product->is_featured == 1)
                                                <input type="checkbox" name="is_featured" checked="" value="1" class="form-check-input " >
                                                <span class="form-check-label">Feature this product </span>
                                                @else
                                                <input type="checkbox" name="is_featured" value="1" class="form-check-input " >
                                                <span class="form-check-label">Feature this product </span>
                                                @endif
                                            </label>
                                        </div>
                                        <!-- col.// -->
                                    </div>
                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Reorder Level <span style="color: red;">*</span></label>
                                        <div class="col-lg-9">
                                            <input type="text" name="re_order_level" class="form-control @error('product_heading') is-invalid @enderror" value="{{ $product->re_order_level }}" placeholder="Type here">
                                            @error('re_order_level')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>
                                    
                                    
                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Barcode <span style="color: red;">*</span></label>
                                        <div class="col-lg-9">
                                            <input type="text" name="mbarcode" class="form-control @error('product_heading') is-invalid @enderror" value="{{ $product->barcode }}" placeholder="Type here">
                                            @error('mbarcode')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>
                                    <!-- row.// -->
                                    <br>
                                    
                                    @can('Manage Pricing')
                                    <button class="btn btn-primary" type="button" onclick="updateDiv('variant')">Continue to next</button>
                                    
                                    @else
                                    <button class="btn btn-primary" type="button" onclick="updateDiv('image')">Continue to next</button>
                                    
                                    @endcan

                                </section>

                                <section class="content-body p-xl-4 d-none" id="d-variant" >

                                    <button type="button" style="float: right!important;" class="btn btn-primary add_field_button1" >Add New Variant</button>
                                    <div class="input_fields_wrap1">
                                        <div @class('clearfix')></div><br>
                                        @foreach($product->variants as $var)
                                        <div>
                                            <input type="hidden" name="variant_id[]" value="{{$var->id}}">
                                            
                                                <div class="row mb-4">
                                                    <label class="col-lg-3 col-form-label">Barcode #<span style="color: red;">*</span></label>
                                                    <div class="col-lg-9">
                                                        <small> for multiple barcodes use comma</small>
                                                        <input type="text" name="barcode[]" value="{{$var->barcode}}" class="form-control barcode" placeholder="Type here">
                                                        @error('barcode')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <!-- col.// -->
                                                </div>

                                              

                                                <div class="row mb-4">
                                                    <label class="col-lg-3 col-form-label">Color </label>

                                                    <div class="col-lg-9">
                                                        <input type="text" name="shade[]" value="{{$var->shade}}" class="form-control " placeholder="Type here">
                                                        @error('shade')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <!-- col.// -->
                                                </div>

                                                <div class="row mb-4">
                                                    <label class="col-lg-3 col-form-label">Size </label>

                                                    <div class="col-lg-9">
                                                        <input type="text" name="size[]" value="{{$var->size}}" class="form-control " placeholder="Type here">
                                                        @error('size')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <!-- col.// -->
                                                </div>

                                                <div class="row mb-4">
                                                    <label class="col-lg-3 col-form-label">Price</label>

                                                    <div class="col-lg-9">
                                                        <input type="numeric" value="{{$var->additional_price}}"  name="additional_price[]" class="form-control " placeholder="Type here">
                                                        @error('additional_price')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <!-- col.// -->
                                                </div>
                                                
                                                <div class="row mb-4">
                                                    <label class="col-lg-3 col-form-label">Purchase Price</label>

                                                    <div class="col-lg-9">
                                                        <input type="numeric" value="{{$var->purchase_price}}"  name="v_purchase_price[]" class="form-control " placeholder="Type here">
                                                        @error('v_purchase_price')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    
                                                </div>
                                                
                                                <div class="row mb-4">
                                                    <label class="col-lg-3 col-form-label">Dz Price</label>

                                                    <div class="col-lg-9">
                                                        <input type="numeric" value="{{$var->dz_price}}"  name="v_dz_price[]" class="form-control " placeholder="Type here">
                                                        @error('v_dz_price')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <!-- col.// -->
                                                </div>
                                                
                                                <div class="row mb-4">
                                                    <label class="col-lg-3 col-form-label">Re Order Level</label>

                                                    <div class="col-lg-9">
                                                        <input type="numeric" value="{{$var->re_order_level}}"  name="v_re_order_level[]" class="form-control " placeholder="Type here">
                                                        @error('v_re_order_level')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <!-- col.// -->
                                                </div>
                                                <a href="#" class="remove_field">Remove</a>
                                            </div>
                                        @endforeach

                                    </div>

                                    <br>
                                    
                                    @can('Manage Pricing')
                                    <button class="btn btn-primary" type="button" onclick="updateDiv('price')">Continue to next</button>
                                    @else
                                     <button class="btn btn-primary" type="button" onclick="updateDiv('image')">Continue to next</button>
                                    
                                    @endcan

                                </section>

                                <section class="content-body p-xl-4 d-none" id="d-price" >

                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Price <span style="color: red;">*</span></label>
                                        <div class="col-lg-9">
                                            <input type="text" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ $product->price }}" placeholder="Type here">
                                            @error('price')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>
                                    
                                      <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Purchase Price <span style="color: red;">*</span></label>
                                        <div class="col-lg-9">
                                            <input type="text" name="purchase_price" class="form-control @error('purchase_price') is-invalid @enderror" value="{{ $product->purchase_price }}" placeholder="Type here">
                                            @error('purchase_price')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>
                                    
                                      <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Dz Price <span style="color: red;">*</span></label>
                                        <div class="col-lg-9">
                                            <input type="text" name="dz_price" class="form-control @error('dz_price') is-invalid @enderror" value="{{ $product->dz_price }}" placeholder="Type here">
                                            @error('dz_price')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>

                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Discount </label>

                                        <div class="col-lg-9">

                                            <select   name="discount_id" class="form-control select2 @error('discount_id') is-invalid @enderror">
                                                <option value="" selected>None</option>
                                                @foreach($discounts as $d)
                                                    @if($product->implement_discount_id == $d->id)
                                                        <option selected value="{{$d->id}}">{{$d->name}} - {{$d->amount}} {{$d->is_percent ? ' %' : ''}}</option>
                                                    @else
                                                        <option value="{{$d->id}}">{{$d->name}} - {{$d->amount}} {{$d->is_percent ? ' %' : ''}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            @error('discount_id')
                                            <div @class('alert alert-danger')>{{$message}}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>

                                    <br>
                                    <button class="btn btn-primary" type="button" onclick="updateDiv('image')">Continue to next</button>

                                </section>

                                <section class="content-body p-xl-4 d-none" id="d-image">

                                    <button type="button" style="float: right!important;" class="btn btn-primary add_field_button" >Add New Image</button>
                                    <br>

                                    <div class="input_fields_wrap">

                                        <div class="clearfix"></div>

                                        <!--@foreach($product->images as $img)-->
                                        <!--    <div>-->
                                        <!--        <input name="img_id[]" type="hidden" value="{{$img->id}}">-->

                                        <!--        <div class="row mb-4">-->
                                        <!--            <label class="col-lg-3 col-form-label">Image<span style="color: red;">*</span></label>-->
                                        <!--            <div class="col-lg-9">-->
                                        <!--                <img style="width:100px;height: auto"  src="{{asset('storage/'.$img->url)}}" alt="Product">-->
                                        <!--                <input type="file" name="images[]" class="form-control " placeholder="Upload here">-->
                                        <!--                @error('images')-->
                                        <!--                <div class="alert alert-danger">{{ $message }}</div>-->
                                        <!--                @enderror-->
                                        <!--            </div>-->
                                                    <!-- col.// -->
                                        <!--        </div>-->

                                        <!--        <div class="row mb-4">-->
                                        <!--            <label class="col-lg-3 col-form-label">Serial #<span style="color: red;">*</span></label>-->
                                        <!--            <div class="col-lg-9">-->
                                        <!--                <input type="numeric" value="{{$img->serial_no}}" name="image_serial_no[]" class="form-control " placeholder="Type here">-->
                                        <!--                @error('images')-->
                                        <!--                <div class="alert alert-danger">{{ $message }}</div>-->
                                        <!--                @enderror-->
                                        <!--            </div>-->
                                                    <!-- col.// -->
                                        <!--        </div>-->

                                        <!--        <div class="row mb-4">-->
                                        <!--            <label class="col-lg-3 col-form-label">Status <span style="color: red;">*</span></label>-->
                                        <!--            <div class="col-lg-9">-->
                                        <!--                <input type="checkbox" {{$img->status ? 'checked' : ''}} name="image_status[]" value="1" class="form-check-input ">-->
                                        <!--                @error('images')-->
                                        <!--                <div class="alert alert-danger">{{ $message }}</div>-->
                                        <!--                @enderror-->
                                        <!--            </div>-->
                                                    <!-- col.// -->
                                        <!--        </div>-->
                                        <!--        <a href="#" class="remove_field">Remove</a>-->
                                        <!--    </div>-->
                                        <!--@endforeach-->

                                    </div>



                                    <br>
                                    <!--<button class="btn btn-primary" type="button" onclick="document.getElementById('form').submit()">Update</button>-->

                                </section>

                               

                               

                                <!-- content-body .// -->
                        </div>
                        </form>
                        <!-- col.// -->
                    </div>


                    <!-- row.// -->
                </div>
                <!-- card body end// -->
            </div>
        </div>
    </div>

@stop

@section('js')
    <script>

        
        function updateDiv(val) {
            anc = '.a-' + val;
            div = '#d-' + val;
            $('.nav-link').removeClass( "active" );
            $('.content-body').addClass('d-none');

            $(anc).addClass('active');
            $(div).removeClass('d-none');
        }

        $(document).ready(function() {
            var max_fields      = 20; //maximum input boxes allowed
            var wrapper         = $(".input_fields_wrap"); //Fields wrapper
            var add_button      = $(".add_field_button"); //Add button ID

            var max_fields1      = 20; //maximum input boxes allowed
            var wrapper1         = $(".input_fields_wrap1"); //Fields wrapper
            var add_button1      = $(".add_field_button1"); //Add button ID

            var x = 1; //for multiple images
            $(add_button).click(function(e){ //on add input button click
                e.preventDefault();
                if(x < max_fields){ //max input box allowed
                    x++; //text box increment
                    $(wrapper).prepend('<div><br><br><div class="row mb-4"> <label class="col-lg-3 col-form-label">Image<span style="color: red;">*</span><small> (497px * 497px)</small></label> <div class="col-lg-9"> <input type="file" name="images[]" class="form-control " placeholder="Upload here">@error('images')<div class="alert alert-danger">{{ $message }}</div>@enderror</div> </div> <div class="row mb-4"> <label class="col-lg-3 col-form-label">Serial #<span style="color: red;">*</span></label> <div class="col-lg-9"> <input type="numeric" name="image_serial_no[]" class="form-control " placeholder="Type here">@error('images')<div class="alert alert-danger">{{ $message }}</div>@enderror</div> </div> <div class="row mb-4"> <label class="col-lg-3 col-form-label">Status <span style="color: red;">*</span></label> <div class="col-lg-9"> <input type="checkbox" checked="" name="image_status[]" value="1" class="form-check-input ">@error('images')<div class="alert alert-danger">{{ $message }}</div>@enderror</div> </div><a href="#" class="remove_field">Remove</a>'); //add input box

                }
            });

            var y = 1; //for multiple shades
            $(add_button1).click(function(e){ //on add input button click
                e.preventDefault();
                if(y < max_fields1){ //max input box allowed
                    y++; //text box increment
                    $(wrapper1).prepend('<div><input type="hidden" name="variant_id[]" value="0"><br><br><div class="row mb-4"> <label class="col-lg-3 col-form-label">Barcode #<span style="color: red;">*</span><small> (497px * 497px)</small></label><div class="col-lg-9"> <small> for multiple barcodes use comma</small> <input type="text" name="barcode[]" class="form-control barcode" placeholder="Type here">@error('barcode')<div class="alert alert-danger">{{ $message }}</div>@enderror</div> </div> <div class="row mb-4"> <label class="col-lg-3 col-form-label">Shade </label> <div class="col-lg-9"> <input type="text" name="shade[]" class="form-control " placeholder="Type here">@error('shade')<div class="alert alert-danger">{{ $message }}</div>@enderror</div> </div> <div class="row mb-4"> <label class="col-lg-3 col-form-label">Size </label> <div class="col-lg-9"> <input type="text" name="size[]" class="form-control " placeholder="Type here">@error('size')<div class="alert alert-danger">{{ $message }}</div>@enderror</div> </div> <div class="row mb-4"> <label class="col-lg-3 col-form-label">Additional Price</label> <div class="col-lg-9"> <input type="numeric" value="0" name="additional_price[]" class="form-control " placeholder="Type here">@error('additional_price')<div class="alert alert-danger">{{ $message }}</div>@enderror</div> </div>  <div class="row mb-4"><label class="col-lg-3 col-form-label">Purchase Price</label><div class="col-lg-9"><input type="numeric" value="0"  name="v_purchase_price[]" class="form-control " placeholder="Type here"></div></div><div class="row mb-4"><label class="col-lg-3 col-form-label">Dz Price</label><div class="col-lg-9"><input type="numeric" value="0"  name="v_dz_price[]" class="form-control " placeholder="Type here"></div></div><div class="row mb-4"><label class="col-lg-3 col-form-label">Re Order Level</label><div class="col-lg-9"><input type="numeric" value="10"  name="v_re_order_level[]" class="form-control " placeholder="Type here"></div></div><a href="#" class="remove_field">Remove</a>'); //add input box

                }
            });

            $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
                e.preventDefault(); $(this).parent('div').remove(); x--;
            })

            $(wrapper1).on("click",".remove_field", function(e){ //user click on remove text
                e.preventDefault(); $(this).parent('div').remove(); y--;
            })
        });

        //check barcode already exist or not....
        $(document).on('focusout','.barcode',function() {

            var barcode = $(this).val();
            $.ajax({
                url: "{{ route('product.barcode.check') }}",
                type:'GET',
                data: {barcode:barcode},
                success: function(data) {
                    if(data.result == true)
                        alert('Barcode Already Exist! ' + data.message);
                }
            });
        });
    </script>

<script>
    $(document).ready(function() {
        $('#btn-generate-sku').click(function() {
            var btn = $(this);
            btn.text('Generating...');
            $.ajax({
                url: "{{ route('product.generate.sku') }}",
                type: 'GET',
                success: function(response) {
                    $('#product_code').val(response.sku);
                    btn.text('Auto Generate SKU');
                },
                error: function() {
                    alert('Error generating SKU');
                    btn.text('Auto Generate SKU');
                }
            });
        });
    });
</script>

    <script>
        var formSubmitting = false;
        var setFormSubmitting = function() { formSubmitting = true; };

        window.onload = function() {
            window.addEventListener("beforeunload", function (e) {
                if (formSubmitting) {
                    return undefined;
                }

                var confirmationMessage = 'It looks like you have been editing something. '
                    + 'If you leave before saving, your changes will be lost.';

                (e || window.event).returnValue = confirmationMessage; //Gecko + IE
                return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
            });
        };
    </script>
@stop
