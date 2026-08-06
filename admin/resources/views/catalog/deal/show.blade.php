@extends('layouts.app')

@section('css')
<style>
    /* The Modal (background) */
    .modal {
        display: none;
        position: fixed;
        z-index: 100;
        padding-top: 35px;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: black;
    }

    /* Modal Content */
    .modal-content {
        position: relative;
        background-color: #fefefe;
        margin: auto;
        padding: 0;
        width: 90%;
        max-width: 1200px;
    }

    /* The Close Button */
    .close {
        color: white;
        position: absolute;
        top: 10px;
        right: 25px;
        font-size: 35px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: #999;
        text-decoration: none;
        cursor: pointer;
    }

    .mySlides {
        display: none;
    }

    .cursor {
        cursor: pointer;
    }

    /* Next & previous buttons */
    .prev,
    .next {
        cursor: pointer;
        position: absolute;
        top: 50%;
        width: auto;
        padding: 16px;
        margin-top: -50px;
        color: white;
        font-weight: bold;
        font-size: 20px;
        transition: 0.6s ease;
        border-radius: 0 3px 3px 0;
        user-select: none;
        -webkit-user-select: none;
    }

    /* Position the "next button" to the right */
    .next {
        right: 0;
        border-radius: 3px 0 0 3px;
    }

    /* On hover, add a black background color with a little bit see-through */
    .prev:hover,
    .next:hover {
        background-color: rgba(0, 0, 0, 0.8);
    }

    /* Number text (1/3 etc) */
    .numbertext {
        color: #f2f2f2;
        font-size: 12px;
        padding: 8px 12px;
        position: absolute;
        top: 0;
    }


    .caption-container {
        text-align: center;
        background-color: black;
        padding: 2px 16px;
        color: white;
    }
    .demo {
        opacity: 0.6;
    }
    .active,
    .demo:hover {
        opacity: 1;
    }

</style>

    @stop
@section('content')

        <div class="content-header">
            <div>
                <h2 class="content-title card-title">{{$deal->title}}</h2>
                <p>Available stock : {{$deal->available_stock}}</p>
                <p>Online Available stock : {{$deal->online_available_stock}}</p>
            </div>
        </div>
        <div class="card">
            <header class="card-header">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-6 mb-lg-0 mb-15">
                        <h5> Price :  <strike style="color: #c4bbbb;">{{number_format($deal->price)}}</strike> {{number_format($deal->price - $deal->discount_amount)}}</h5>
                         <span class="badge rounded-pill {{($deal->status) ? 'alert-success' : 'alert-danger'}}">@if($deal->status == 1)
                                 Published
                             @elseif($deal->status == 2)
                                 Dis continue
                             @else
                                 Un Published
                             @endif</span>
                        @if($deal->is_new)
                            <span @class('badge rounded-pill alert-success') >New</span>
                        @endif
                        @if($deal->is_featured)
                            <span @class('badge rounded-pill alert-success') >Featured</span>
                        @endif

                        <br>
                        <small class="text-muted">Last Updated: {{date('M d,Y',strtotime($deal->updated_at))}}</small>
                    </div>
                    <div class="col-lg-6 col-md-6 ms-auto text-md-end">
                        @can('Edit Product Deal')
                            <a class="dropdown-item btn btn-primary d-inline" href="{{route('deals.edit',$deal->id)}}">Edit info</a>
                        @endcan

                        @can('Delete Product Deal')
                            <form @class('d-inline') onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('deals.destroy',$deal->id) }}" method="POST">
                                @csrf
                                @method('DELETE')

                                <button style="width: min-content;" class=" btn btn-instagram d-inline"  type="submit">Delete</button>
                            </form>
                        @endcan
                    </div>
                </div>
            </header>
            <!-- card-header end// -->
            <div class="card-body">
                <div class="row mb-50 mt-20 order-info-wrap">
                    <div class="col-md-4">
                        <article class="icontext align-items-start">

                            <div class="text">
                                <h6 class="mb-1">Basic</h6>
                                <p class="mb-1">
                                    <b>Code : </b> {{$deal->code}} <br>
                                    <b>Menu Text : </b> {{$deal->menu_text}}<br>
                                    <b>Product Heading : </b> {{$deal->deal_heading}}<br>
                                    <b>Slug : </b> {{$deal->slug}}<br>
                                    <b>Meta Description : </b> {{$deal->description}}<br>
                                    <b>keywords : </b> {{$deal->keywords}}<br>
                                    <b>Have Variants : </b> <span style="display: inline-block;font-size: 12px;" class="badge rounded-pill {{($deal->have_variants) ? 'alert-success' : 'alert-danger'}}">{{($deal->have_variants) ? 'YES' : 'NO'}}</span>
                                </p>

                            </div>
                        </article>
                    </div>
                    <!-- col// -->

                    <!-- col// -->
                    <div class="col-md-4">
                        <article class="icontext align-items-start">

                            <div class="text">
                                <h6 class="mb-1">Brand / Category</h6>
                                <p class="mb-1">
                                    <b>Brand : </b> @if($deal->brands)  @foreach($deal->brands as $brand) @if($loop->last)  @if($brand->brand){{$brand->brand->title }} @endif @else  @if($brand->brand) {{$brand->brand->title }}, @endif @endif @endforeach @endif <br>
                                    <b>Categories : </b> @if($deal->categories)  @foreach($deal->categories as $category) @if($loop->last)  @if($category->category){{$category->category->title }} @endif @else  @if($category->category) {{$category->category->title }}, @endif @endif @endforeach @endif
                                </p>
                                <b>Related Products </b>
                                <ul style="list-style: circle">

                                </ul>

                            </div>
                        </article>
                    </div>
                    <!-- col// -->
                </div>
                <!-- row // -->
                <hr>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr style="text-align: center">
                                    <th width="5%">Sr #</th>
                                    <th width="25%">Product </th>
                                    <th width="10%">Quantity</th>
                                    <th width="10%">Variants</th>
                                    <th width="10%">Price</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $sr = 1;$price = 0;?>
                                @foreach($deal->products as $product)
                                <tr style="text-align: center;">
                                    <td>
                                        {{$sr++}}
                                    </td>
                                    <td>{{$product->product->title}}</td>
                                    <td>{{$product->quantity}}</td>

                                    <td>
                                        @foreach($deal->variants as $v)
                                            {{$v->variant->shade}} : {{$v->variant->size}} <br>
                                        @endforeach
                                    </td>
                                    <td>{{number_format($product->product->price)}}
                                        <?php $price += ($product->product->price * $product->quantity);?> </td>

                                </tr>
                                @endforeach

                                <tr>
                                    <td colspan="9">
                                        <article class="float-end">
                                            <dl class="dlist" style="border-bottom: 1px double">

                                                <dt><b>Total Price : </b></dt>
                                                <dd>{{number_format($price)}}</dd>
                                            </dl>
                                        </article>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- table-responsive// -->
                    </div>
                    <!-- col// -->

                    <div class="col-lg-4">
                        <div class="box shadow-sm bg-light">
                            <h6 class="mb-15">Short Description</h6>
                            <p>
                                {{$deal->short_description}}
                            </p>
                        </div>
                        <br>

                        <div class="box shadow-sm bg-light">
                            <h6 class="mb-15">Full Description</h6>
                            <p>
                                {{$deal->long_description}}
                            </p>
                        </div>
                        <br>
                        <div class="box shadow-sm bg-light">
                            <h6 class="mb-15">Ingredients</h6>
                            <p>
                                {{$deal->ingredients}}
                            </p>
                        </div>
                        <br>
                        <div class="box shadow-sm bg-light ">
                            <h6 class="mb-15">How to use</h6>
                            <p>
                                {{$deal->how_to_use}}
                            </p>
                        </div>
                        <br>

                        <br>

                    </div>
                    <!-- col// -->
                </div>
                <hr>

                <div class="row gx-3 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 row-cols-xxl-5">

                    @foreach($deal->images as $img)
                    <div class="col">
                        <div class="card card-product-grid">
                            <a href="#" class="img-wrap">
                                <img style="min-height:214px" onclick="openModal();currentSlide(1)" src="{{asset('storage/'.$img->url)}}" alt="Product"> </a>
                            <div class="info-wrap">
                                <a href="#"  class="title text-truncate text-center"><span class="badge rounded-pill {{($img->status) ? 'alert-success' : 'alert-danger'}}">{{($img->status) ? 'Active' : 'InActive'}}</span></a>
                                <div class="price mb-2">Serial # {{$img->serial_no}}</div>
                                <!-- price.// -->
                            </div>
                        </div>
                        <!-- card-product  end// -->
                    </div>
                        @endforeach



                </div>
            </div>
            <!-- card-body end// -->
        </div>
        <!-- card end// -->

        <div id="myModal" class="modal">
            <span class="close cursor" onclick="closeModal()">&times;</span>
            <div class="modal-content">
                @foreach($deal->images as $img)

                <div class="mySlides" style="text-align: center;">
                    <div class="numbertext"></div>
                    <img src="{{asset('storage/'.$img->url)}}" style="width:50%;min-height: 610px;">
                </div>
                @endforeach




                <a class="prev" style="background: black;" onclick="plusSlides(-1)">&#10094;</a>
                <a class="next" style="background: black;" onclick="plusSlides(1)">&#10095;</a>


            </div>
        </div>

    @stop

@section('js')
    <script>
        function openModal() {
            document.getElementById("myModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("myModal").style.display = "none";
        }

        var slideIndex = 1;
        showSlides(slideIndex);

        function plusSlides(n) {
            showSlides(slideIndex += n);
        }

        function currentSlide(n) {
            showSlides(slideIndex = n);
        }

        function showSlides(n) {
            var i;
            var slides = document.getElementsByClassName("mySlides");
            var dots = document.getElementsByClassName("demo");
            var captionText = document.getElementById("caption");
            if (n > slides.length) {slideIndex = 1}
            if (n < 1) {slideIndex = slides.length}
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }
            slides[slideIndex-1].style.display = "block";
            dots[slideIndex-1].className += " active";
            captionText.innerHTML = dots[slideIndex-1].alt;
        }
    </script>



    @stop
