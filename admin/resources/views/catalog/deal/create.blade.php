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
                    <h2 class="content-title">Add New Deal</h2>
                    <div>
{{--                        <button class="btn btn-light rounded font-sm mr-5 text-body hover-up">Save to draft</button>--}}
                        <button onclick="setFormSubmitting();document.getElementById('form').submit()" class="btn btn-md rounded font-sm hover-up">Publish</button>
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
                                    <a class="nav-link a-variant " onclick="updateDiv('variant')" href="#">Products</a>
                                    <a class="nav-link a-price" onclick="updateDiv('price')" href="#price">Pricing</a>
                                    <a class="nav-link a-image" onclick="updateDiv('image')" href="#image">Images</a>
                                    <a class="nav-link a-seo" onclick="updateDiv('seo')" href="#seo">SEO keywords</a>
                                    <a class="nav-link a-related" onclick="updateDiv('related')" href="#related">Related items</a>

                                </nav>
                            </aside>


                            <div class="col-lg-9">
                                <form action="{{route('deals.store')}}" method="post" id="form" autocomplete="false" enctype="multipart/form-data">

                                    @csrf
                                    <section class="content-body p-xl-4" id="d-general">

                                        <!-- row.// -->
                                        <div class="row mb-4">
                                            <label class="col-lg-3 col-form-label">Brand <span style="color: red;">*</span></label>
                                            <div class="col-lg-9">

                                                <select onchange="updateProducts()" id="brand_id"  size="4" name="brand_id[]" multiple class="form-control select2 @error('brand_id') is-invalid @enderror">
                                                    @foreach($brands as $b)
                                                        <option value="{{$b->id}}">{{$b->title}}</option>
                                                    @endforeach
                                                </select>
                                                @error('brand_id')
                                                <div @class('alert alert-danger')>{{$message}}</div>
                                                @enderror
                                            </div>
                                            <!-- col.// -->
                                        </div>

                                        <div class="row mb-4">
                                            <label class="col-lg-3 col-form-label">Categories <span style="color: red;">*</span></label>

                                            <div class="col-lg-9">

                                                <select   name="category_id[]" multiple class="form-control select2 @error('category_id') is-invalid @enderror">
                                                    @foreach($categories as $c)
                                                        <option value="{{$c->id}}">{{$c->title}}</option>
                                                    @endforeach
                                                </select>
                                                @error('category_id')
                                                <div @class('alert alert-danger')>{{$message}}</div>
                                                @enderror
                                            </div>
                                            <!-- col.// -->
                                        </div>

                                        <div class="row mb-4">
                                            <label class="col-lg-3 col-form-label">Deal name <span style="color: red;">*</span></label>
                                            <div class="col-lg-9">
                                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="Type here">
                                                @error('title')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <!-- col.// -->
                                        </div>

                                        <div class="row mb-4">
                                            <label class="col-lg-3 col-form-label">Slug <span style="color: red;">*</span></label>
                                            <div class="col-lg-9">
                                                <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" placeholder="Type here">
                                                @error('slug')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <!-- col.// -->
                                        </div>
                                        <div class="row mb-4">
                                            <label class="col-lg-3 col-form-label">Menu Text <span style="color: red;">*</span></label>
                                            <div class="col-lg-9">
                                                <input type="text" name="menu_text" class="form-control @error('menu_text') is-invalid @enderror" value="{{ old('menu_text') }}" placeholder="Type here">
                                                @error('menu_text')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <!-- col.// -->
                                        </div>
                                        <div class="row mb-4">
                                            <label class="col-lg-3 col-form-label">Deal Heading <span style="color: red;">*</span></label>
                                            <div class="col-lg-9">
                                                <input type="text" name="deal_heading" class="form-control @error('deal_heading') is-invalid @enderror" value="{{ old('deal_heading') }}" placeholder="Type here">
                                                @error('deal_heading')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <!-- col.// -->
                                        </div>
                                        <!-- row.// -->
                                        <div class="row mb-4">
                                            <label class="col-lg-3 col-form-label">Short Description</label>
                                            <div class="col-lg-9">
                                                <textarea class="form-control @error('short_description') is-invalid @enderror" name="short_description" placeholder="Type here" rows="4">{{old('short_description')}}</textarea>
                                                @error('short_description')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <!-- col.// -->
                                        </div>
                                        <div class="row mb-4">
                                            <label class="col-lg-3 col-form-label">Long Description</label>
                                            <div class="col-lg-9">
                                                <textarea class="form-control @error('full_description') is-invalid @enderror" name="full_description" placeholder="Type here" rows="4">{{old('full_description')}}</textarea>
                                                @error('full_description')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <!-- col.// -->
                                        </div>
                                        <div class="row mb-4">
                                            <label class="col-lg-3 col-form-label">Ingredients</label>
                                            <div class="col-lg-9">
                                                <textarea class="form-control @error('ingredients') is-invalid @enderror" name="ingredients" placeholder="Type here" rows="4">{{old('ingredients')}}</textarea>
                                                @error('ingredients')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <!-- col.// -->
                                        </div>
                                        <div class="row mb-4">
                                            <label class="col-lg-3 col-form-label">How to use </label>
                                            <div class="col-lg-9">
                                                <textarea class="form-control @error('how_to_use') is-invalid @enderror" name="how_to_use" placeholder="Type here" rows="4">{{old('how_to_use')}}</textarea>
                                                @error('how_to_use')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <!-- col.// -->
                                        </div>
                                        <div class="row mb-4">
                                            <label class="col-lg-3 col-form-label">Weight </label>
                                            <div class="col-lg-9">
                                                <input type="text" name="weight" class="form-control @error('weight') is-invalid @enderror" value="{{ old('weight') }}" placeholder="Type here">
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
                                                    <input type="checkbox" name="status" value="1" class="form-check-input " checked="">
                                                    <span class="form-check-label">Enable this deal </span>
                                                </label>
                                                <input type="checkbox" name="status" value="2" class="form-check-input " >
                                                <span class="form-check-label">Discontinue this deal </span>
                                            </div>
                                            <!-- col.// -->
                                        </div>
                                        <div class="row mb-4">
                                            <label class="col-lg-3 col-form-label">New Deal</label>
                                            <div class="col-lg-9">
                                                <label class="form-check my-2">
                                                    <input type="checkbox" checked="" name="is_new" value="1" class="form-check-input ">
                                                    <span class="form-check-label">Enable For New Tag</span>
                                                </label>
                                            </div>
                                            <!-- col.// -->
                                        </div>
                                        <div class="row mb-4">
                                            <label class="col-lg-3 col-form-label">Featured Deal</label>
                                            <div class="col-lg-9">
                                                <label class="form-check my-2">
                                                    <input type="checkbox" checked="" name="is_featured" value="1" class="form-check-input ">
                                                    <span class="form-check-label">Enable For Featured Tag</span>
                                                </label>
                                            </div>
                                            <!-- col.// -->
                                        </div>

                                        <div class="row mb-4">
                                            <label class="col-lg-3 col-form-label">Last Pick Product</label>
                                            <div class="col-lg-9">
                                                <label class="form-check my-2">
                                                    <input type="checkbox" name="is_last_pick" value="1" class="form-check-input ">
                                                    <span class="form-check-label">Enable For Last Time Pick Tag</span>
                                                </label>
                                            </div>
                                            <!-- col.// -->
                                        </div>
                                        <div class="row mb-4">
                                            <label class="col-lg-3 col-form-label">Serial No. <span style="color: red;">*</span></label>
                                            <div class="col-lg-9">
                                                <input type="text" name="serial_no" class="form-control @error('serial_no') is-invalid @enderror" value="{{ old('serial_no') }}" placeholder="Type here">
                                                @error('serial_no')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <!-- col.// -->
                                        </div>
                                        <div class="row mb-4">
                                            <label class="col-lg-3 col-form-label">Reorder Level <span style="color: red;">*</span></label>
                                            <div class="col-lg-9">
                                                <input type="text" name="re_order_level" class="form-control @error('product_heading') is-invalid @enderror" value="10" placeholder="Type here">
                                                @error('re_order_level')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <!-- col.// -->
                                        </div>
                                        <!-- row.// -->
                                        <br>
                                        <button class="btn btn-primary" type="button" onclick="updateDiv('variant')">Continue to next</button>

                                </section>

                                <section class="content-body p-xl-4 d-none" id="d-variant" >

                                    <button type="button" style="float: right!important;" class="btn btn-primary" onclick="addProduct()" >Add Product</button>
                                    <div class="input_fields_wrap1">
                                    <div @class('clearfix')></div><br>
                                        <div>
                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Products <span style="color: red;">*</span></label>
                                        <div class="col-lg-9">

                                            <select onchange="addVariants()"   name="product_id" id="product_id"  class="form-control select2 @error('product_id') is-invalid @enderror">
                                                @foreach($products as $p)
                                                    <option value="{{$p->id}}">{{$p->title}}</option>
                                                @endforeach
                                            </select>
                                            @error('product_id')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>

                                            <div class="row mb-4">
                                                <label class="col-lg-3 col-form-label">Quantity <span style="color: red;">*</span></label>
                                                <div class="col-lg-9">

                                                   <input type="number" class="form-control" name="quantity" id="quantity" value="1">
                                                </div>
                                                <!-- col.// -->
                                            </div>

                                            <div class="row mb-4">
                                                <label class="col-lg-3 col-form-label">Variants <span style="color: red;">*</span></label>
                                                <div class="col-lg-9">

                                                    <select   name="variant_id[]" multiple id="variant_id"  class="form-control select2 @error('product_id') is-invalid @enderror">

                                                    </select>

                                                </div>
                                                <!-- col.// -->
                                            </div>

                                            <div class="row mb-4" id="result">

                                            </div>


                                        </div>

                                    </div>

                                    <br>
                                    <button class="btn btn-primary" type="button" onclick="updateDiv('price')">Continue to next</button>

                                </section>

                                <section class="content-body p-xl-4 d-none" id="d-price" >

                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Price <span style="color: red;">*</span></label>
                                        <div class="col-lg-9">
                                            <input type="text" id="price" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" placeholder="Type here">
                                            @error('price')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>

                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Discount Amount <span style="color: red;">*</span></label>
                                        <div class="col-lg-9">
                                            <input type="text" name="discount_amount" class="form-control @error('discount_amount') is-invalid @enderror" value="{{ old('discount_amount') }}" placeholder="Type here">
                                            @error('discount_amount')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>



                                    <br>
                                    <button class="btn btn-primary" type="button" onclick="updateDiv('image')">Continue to next</button>

                                </section>

                                <section class="content-body p-xl-4 d-none" id="d-image">

                                    <button type="button" style="float: right!important;" class="btn btn-primary add_field_button" >Add New Image</button>

                                    <div class="input_fields_wrap">

                                        <div class="clearfix"></div>
                                        <div>

                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Image<span style="color: red;">*</span> <small> (497px * 497px)</small></label>
                                        <div class="col-lg-9">
                                            <input type="file" name="images[]" class="form-control " placeholder="Upload here">
                                            @error('images')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>

                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Serial #<span style="color: red;">*</span></label>
                                        <div class="col-lg-9">
                                            <input type="numeric" name="image_serial_no[]" class="form-control " placeholder="Type here">
                                            @error('images')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>

                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Status <span style="color: red;">*</span></label>
                                        <div class="col-lg-9">
                                            <input type="checkbox" checked="" name="image_status[]" value="1" class="form-check-input ">
                                            @error('images')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>

                                        </div>

                                    </div>



                                    <br>
                                    <button class="btn btn-primary" type="button" onclick="updateDiv('seo')">Continue to next</button>

                                </section>

                                <section class="content-body p-xl-4 d-none" id="d-seo">
                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Meta Description</label>
                                        <div class="col-lg-9">
                                            <textarea class="form-control @error('meta_description') is-invalid @enderror" name="meta_description" placeholder="Type here" rows="4">{{old('meta_description')}}</textarea>
                                            @error('meta_description')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>

                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">keywords</label>
                                        <div class="col-lg-9">
                                            <textarea class="form-control @error('keywords') is-invalid @enderror" name="keywords" placeholder="Type here" rows="4">{{old('keywords')}}</textarea>
                                            @error('keywords')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>

                                    <br>
                                    <button class="btn btn-primary" type="button" onclick="updateDiv('related')">Continue to next</button>
                                </section>

                                <section class="content-body p-xl-4 d-none" id="d-related">

                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Related Deals </label>

                                        <div class="col-lg-9">

                                            <select   name="related_products[]" multiple class="form-control select2 @error('related_products') is-invalid @enderror">
                                                @foreach($relatedDeals as $rP)
                                                    <option value="{{$rP->id}}">{{$rP->product_heading}}</option>
                                                @endforeach
                                            </select>
                                            @error('related_products')
                                            <div @class('alert alert-danger')>{{$message}}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>

                                    <br>
                                    <button class="btn btn-primary" type="button" onclick="document.getElementById('form').submit()">Save</button>
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

    //update the title name in all other fields e.g meta description, keywords
    $('body').on("keyup", "input[name=title]", function() {
        $('textarea[name="meta_description"]').val($('input[name="title"]').val().toLowerCase());
        $('textarea[name="keywords"]').val($('input[name="title"]').val().toLowerCase());
        $('input[name="menu_text"]').val($('input[name="title"]').val());
        $('input[name="deal_heading"]').val($('input[name="title"]').val());
        $('input[name="slug"]').val(($('input[name="title"]').val().toLowerCase().replace(/ /g,'-')));

    });
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

        var x = 1; //for multiple images
        $(add_button).click(function(e){ //on add input button click
            e.preventDefault();
            if(x < max_fields){ //max input box allowed
                x++; //text box increment
                $(wrapper).prepend('<div><br><br><div class="row mb-4"> <label class="col-lg-3 col-form-label">Image<span style="color: red;">*</span><small> (497px * 497px)</small></label> <div class="col-lg-9"> <input type="file" name="images[]" class="form-control " placeholder="Upload here">@error('images')<div class="alert alert-danger">{{ $message }}</div>@enderror</div> </div> <div class="row mb-4"> <label class="col-lg-3 col-form-label">Serial #<span style="color: red;">*</span></label> <div class="col-lg-9"> <input type="numeric" name="image_serial_no[]" class="form-control " placeholder="Type here">@error('images')<div class="alert alert-danger">{{ $message }}</div>@enderror</div> </div> <div class="row mb-4"> <label class="col-lg-3 col-form-label">Status <span style="color: red;">*</span></label> <div class="col-lg-9"> <input type="checkbox" checked="" name="image_status[]" value="1" class="form-check-input ">@error('images')<div class="alert alert-danger">{{ $message }}</div>@enderror</div> </div><a href="#" class="remove_field">Remove</a>'); //add input box

            }
        });

        $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
            e.preventDefault(); $(this).parent('div').remove(); x--;
        })

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

    var products = [];
    var dealProducts = [];
    function addProduct() {
        productId = parseInt($('#product_id').val());
        temp = [];

        if(jQuery.inArray(productId, products) !== -1) {
            toastr.error('Product already added.');
        }
        else {
            products.push(productId);
            product_id = productId;
            quantity = parseInt($('#quantity').val());
            var variantIds = [];
            $('select[name="variant_id[]"] option:selected').each(function() {
                variantIds.push($(this).val());
            });
            dealProducts.push({'product_id' : productId,'quantity' : quantity,'variants' : variantIds});

            $.ajax({
                url: "{{route('product.addDeal')}}",
                type: 'post',
                data: {dealProducts : JSON.stringify(dealProducts)},
                success: function (data) {
                    document.getElementById('result').innerHTML = data;
                    price = $('#total_price').val();
                    $('#price').val(price);
                }
            });

        }



    }

    function addVariants() {

        if($('#product_id').val() ) {
            $.ajax({
                url: "{{route('product.variants')}}",
                type: 'GET',
                data: {product_id: $('#product_id').val()},
                success: function (data) {
                    var model = $('#variant_id');
                    model.empty();

                  //  model.append("<option value='' disabled selected>" + '' + "</option>");


                    $.each(data, function(index, element) {
                        model.append("<option value='"+ element.id +"'>" + element.barcode + ' - ' + element.shade + ' - ' + element.size + ' - ' + element.online_available_stock + "</option>");
                    });
                }
            });
        }
    }

    function updateProducts() {

        var brandIds = [];
        $('select[name="brand_id[]"] option:selected').each(function() {
            brandIds.push($(this).val());
        });

        if(brandIds.length != 0 ) {
            $.ajax({
                url: "{{route('brand.products')}}",
                type: 'GET',
                data: {brand_id: brandIds},
                success: function (data) {
                    var model = $('#product_id');
                    model.empty();

                    model.append("<option value='' disabled selected>" + '' + "</option>");


                    $.each(data, function(index, element) {
                        model.append("<option value='"+ element.id +"'>" + element.title + "</option>");
                    });
                }
            });
        }
    }
</script>

    @stop
