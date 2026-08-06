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
                    <h2 class="content-title">Add New Product</h2>
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
                                    <a class="nav-link a-variant " onclick="updateDiv('variant')" href="#">Variants</a>
                                <a class="nav-link a-image" onclick="updateDiv('image')" href="#image">Images</a>


                                </nav>
                            </aside>


                            <div class="col-lg-9">
                                <form action="{{route('products.store')}}" method="post" id="form" autocomplete="false" enctype="multipart/form-data">

                                    @csrf
                                    <section class="content-body p-xl-4" id="d-general">

                                        <!-- row.// -->
                                        <div class="row mb-4">
                                            <label class="col-lg-3 col-form-label">Brand</label>
                                            <div class="col-lg-9">

                                                <select  size="4" name="brand_id" class="form-control select2 @error('brand_id') is-invalid @enderror">
                                                    @foreach($brands as $b)
                                                        <option value="{{$b->id}}" selected>{{$b->title}}</option>
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
                                            <label class="col-lg-3 col-form-label">Product name <span style="color: red;">*</span></label>
                                            <div class="col-lg-9">
                                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="Type here">
                                                @error('title')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <!-- col.// -->
                                        </div>
                                        <div class="row mb-4">
                                            <label class="col-lg-3 col-form-label">Product Code </label>
                                            <div class="col-lg-9">
                                                <div class="input-group">
                                                    <input type="text" name="code" id="product_code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="Type here">
                                                    <button type="button" class="btn btn-outline-secondary" id="btn-generate-sku">Auto Generate SKU</button>
                                                </div>                                                @error('code')
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
                                                    <span class="form-check-label">Enable this product </span>
                                                </label>
                                                <label class="form-check my-2">
                                                    <input type="checkbox" name="status" value="2" class="form-check-input " >
                                                    <span class="form-check-label">Discontinue this product </span>
                                                </label>
                                                <label class="form-check my-2">
                                                    <input type="checkbox" name="is_featured" value="1" class="form-check-input ">
                                                    <span class="form-check-label">Feature this product </span>
                                                </label>
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


                                    <div class="input_fields_wrap1">
                                    <div @class('clearfix')></div><br>
                                        <div>

                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Color </label>

                                        <div class="col-lg-9">
                                             <select   name="color_id[]" multiple class="form-control select2 @error('category_id') is-invalid @enderror">
                                                    @foreach($colors as $c)
                                                        <option value="{{$c->name}}">{{$c->name}}</option>
                                                    @endforeach
                                                </select>
                                                @error('color_id')
                                                <div @class('alert alert-danger')>{{$message}}</div>
                                                @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>

                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Size </label>

                                        <div class="col-lg-9">
                                             <select   name="size_id[]" multiple class="form-control select2 @error('category_id') is-invalid @enderror">
                                                    @foreach($sizes as $s)
                                                        <option value="{{$s->name}}">{{$s->name}}</option>
                                                    @endforeach
                                                </select>
                                                @error('size_id')
                                                <div @class('alert alert-danger')>{{$message}}</div>
                                                @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>

                                        </div>

                                    </div>

                                    <br>
                                    <button class="btn btn-primary" type="button" onclick="updateDiv('image')">Continue to next</button>

                                </section>

                               <input type="hidden" name="price" class="form-control " value="0" >

                                <section class="content-body p-xl-4 d-none" id="d-image">

                                    <!--<button type="button" style="float: right!important;" class="btn btn-primary add_field_button" >Add New Image</button>-->

                                    <div class="input_fields_wrap">

                                        <div class="clearfix"></div>
                                        <div>

                                    <div class="row mb-4">
                                        <label class="col-lg-3 col-form-label">Images (Multiple)<span style="color: red;">*</span> <small> (497px * 497px)</small></label>
                                        <div class="col-lg-9">
                                            <input type="file" name="images[]" class="form-control " placeholder="Upload here" multiple>
                                            @error('images')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!-- col.// -->
                                    </div>

                                    <!--<div class="row mb-4">-->
                                    <!--    <label class="col-lg-3 col-form-label">Serial #<span style="color: red;">*</span></label>-->
                                    <!--    <div class="col-lg-9">-->
                                    <!--        <input type="numeric" name="image_serial_no[]" class="form-control " placeholder="Type here" value="1">-->
                                    <!--        @error('images')-->
                                    <!--        <div class="alert alert-danger">{{ $message }}</div>-->
                                    <!--        @enderror-->
                                    <!--    </div>-->
                                        <!-- col.// -->
                                    <!--</div>-->

                                    <!--<div class="row mb-4">-->
                                    <!--    <label class="col-lg-3 col-form-label">Status <span style="color: red;">*</span></label>-->
                                    <!--    <div class="col-lg-9">-->
                                    <!--        <input type="checkbox" checked="" name="image_status[]" value="1" class="form-check-input ">-->
                                    <!--        @error('images')-->
                                    <!--        <div class="alert alert-danger">{{ $message }}</div>-->
                                    <!--        @enderror-->
                                    <!--    </div>-->
                                        <!-- col.// -->
                                    <!--</div>-->

                                        </div>

                                    </div>



                                    <br>
                                    <!--<button class="btn btn-primary" type="button" onclick="document.getElementById('form').submit()">Save</button>-->

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
                $(wrapper1).prepend('<div><br><br><div class="row mb-4"> <label class="col-lg-3 col-form-label">Barcode #<span style="color: red;">*</span><small> (497px * 497px)</small></label><div class="col-lg-9"> <small> for multiple barcodes use comma</small> <input type="text" name="barcode[]" class="form-control barcode" placeholder="Type here">@error('barcode')<div class="alert alert-danger">{{ $message }}</div>@enderror</div> </div>  <div class="row mb-4"> <label class="col-lg-3 col-form-label">Shade </label> <div class="col-lg-9"> <input type="text" name="shade[]" class="form-control " placeholder="Type here">@error('shade')<div class="alert alert-danger">{{ $message }}</div>@enderror</div> </div> <div class="row mb-4"> <label class="col-lg-3 col-form-label">Size </label> <div class="col-lg-9"> <input type="text" name="size[]" class="form-control " placeholder="Type here">@error('size')<div class="alert alert-danger">{{ $message }}</div>@enderror</div> </div> <div class="row mb-4"> <label class="col-lg-3 col-form-label">Additional Price</label> <div class="col-lg-9"> <input type="numeric" value="0" name="additional_price[]" class="form-control " placeholder="Type here">@error('additional_price')<div class="alert alert-danger">{{ $message }}</div>@enderror</div> </div><a href="#" class="remove_field">Remove</a>'); //add input box

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


$('select').on('select2:select', function(evt) {
  var element = evt.params.data.element;
  var $element = $(element);

  window.setTimeout(function () {
    var $selected = $(this).find('option:selected');

    if ($selected.length > 1) {
      var $secondLast = $selected.eq($selected.length - 2);
      $element.remove();
      $secondLast.after($element);
    } else {
      $element.remove();
      $(this).prepend($element);
    }

    $(this).val($selected.map(function() {
      return $(this).val();
    }).get()).trigger('change');
  }.bind(this), 1);
});

$('select').on('select2:unselect', function(evt) {
  var $selected = $(this).find('option:selected');

  $(this).val($selected.map(function() {
    return $(this).val();
  }).get()).trigger('change');
});



</script>

    @stop
