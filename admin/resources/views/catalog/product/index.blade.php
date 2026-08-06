@extends('layouts.app')

@section('content')

        <div class="content-header">
            <div>
                <h2 class="content-title card-title">Products List</h2>
                <p>latest product details.</p>
            </div>
            <div>
{{--                <a href="#" class="btn btn-light rounded font-md">Export</a>--}}
{{--                <a href="#" class="btn btn-light rounded font-md">Import</a>--}}
                <a href="#" class="btn btn-warning btn-sm rounded" data-bs-toggle="modal" data-bs-target="#zeroStockModal">Zero Stock by Barcode</a>
@can('Create Product')
                <a href="{{route('products.create')}}" class="btn btn-primary btn-sm rounded">Create new</a>
                @endcan
            </div>
        </div>
        <div class="card mb-4">
            <header class="card-header">
                <div class="row align-items-center">
                    
                    <div class="col-lg-3 col-12 me-auto mb-3">

                        <input type="text" id="searchbox" placeholder="Search By Name,ID ..." class="form-control">

                    </div>

                    <div class="col-md-3 col-12 me-auto mb-md-0 mb-3">
                        <select class="form-select select2" id="category_id">
                            <option selected="" value="">All category</option>
                            @foreach($categories as $cat)
                            <option value="{{$cat->id}}">{{$cat->title}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-12 me-auto mb-md-0 mb-3">
                        <select class="form-select select2" id="brand_id">
                            <option selected="" value="">All Brand</option>
                            @foreach($brands as $b)
                                <option value="{{$b->id}}">{{$b->title}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-12 me-auto mb-md-0 mb-3">
                        <select class="form-select select2" id="status">
                            <option selected="" value="">Status</option>
                            <option value="1">Active</option>
                            <option value="0">Disabled</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-12 me-auto mb-md-0 mb-3">
                        <button type="button" class="form-control btn btn-primary search" >Search</button>
                    </div>
                </div>
            </header>
            <!-- card-header end// -->
            <div class="card-body" id="result">

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
                                @if($product->discount_status) <strike style="color: #c4bbbb;">{{number_format($product->price)}}</strike><br> {{number_format($product->price - $product->discount_amount)}} @else  {{number_format($product->price)}} @endif
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
                        
                        @can('View Product')
                        <div class="col-lg-2 col-sm-2 col-4 col-action text-end">
{{--                            <a href="#" class="btn btn-sm font-sm rounded btn-brand"> <i class="material-icons md-edit"></i> Edit </a>--}}
                            <a href="{{route('products.show',$product->id)}}" class="btn btn-sm font-sm btn-light rounded"> <i class="material-icons md-view_carousel"></i> Detail </a>
                        </div>
                        @endcan
                    </div>
                    <!-- row .// -->
                </article>

                @endforeach

                <!-- itemlist  .// -->
            </div>
            <!-- card-body end// -->
        </div>
        <!-- card end// -->
        <div class="pagination-area mt-30 mb-50">
            <nav aria-label="Page navigation example" id="link">
                {{$products->links()}}
            </nav>
        </div>

    <!-- Zero Stock Modal -->
    <div class="modal fade" id="zeroStockModal" tabindex="-1" aria-labelledby="zeroStockModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="zeroStockModalLabel">Zero Stock by Barcode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="zeroStockBarcode" class="form-label">Scan / Enter Main Product Barcode or Code</label>
                        <input type="text" class="form-control" id="zeroStockBarcode" autofocus>
                    </div>
                    <div id="zeroStockMessage" class="alert d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btnZeroStock">Zero Out Stock</button>
                </div>
            </div>
        </div>
    </div>

@stop

@section('js')
<script>
    $('.select2').select2();
    $(document).on('click','.search',function(e) {

        category_id = $('#category_id').val();
        brand_id = $('#brand_id').val();
        status = $('#status').val();
        searchbox = $('#searchbox').val();

        if(category_id || brand_id || status || searchbox) {

                        $.ajax({
                            url: "{{route('product.search')}}",
                            type: 'GET',
                            data: {category_id: category_id,brand_id:brand_id, status:status,searchbox:searchbox},
                            success: function (data) {
                                document.getElementById('result').innerHTML = data;
                                document.getElementById('link').innerHTML = '';
                            }
                        });
                  
             
        }
        else {
            toastr.warning('Please select any option');
        }
    });

    // Zero Stock Feature
    $('#zeroStockModal').on('shown.bs.modal', function () {
        $('#zeroStockBarcode').focus();
    });

    $('#btnZeroStock').click(function() {
        var barcode = $('#zeroStockBarcode').val();
        if(!barcode) return;
        
        var btn = $(this);
        btn.text('Processing...').prop('disabled', true);
        var msgDiv = $('#zeroStockMessage');
        
        $.ajax({
            url: "{{ route('product.zero-stock') }}",
            type: 'POST',
            data: {
                barcode: barcode,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                msgDiv.removeClass('d-none alert-danger').addClass('alert-success').html(response.message);
                $('#zeroStockBarcode').val('').focus();
                btn.text('Zero Out Stock').prop('disabled', false);
                setTimeout(function(){ msgDiv.addClass('d-none'); }, 3000);
            },
            error: function(xhr) {
                var errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Error processing barcode';
                msgDiv.removeClass('d-none alert-success').addClass('alert-danger').html(errorMsg);
                $('#zeroStockBarcode').val('').focus();
                btn.text('Zero Out Stock').prop('disabled', false);
            }
        });
    });
    $('#zeroStockBarcode').keypress(function(e){
        if(e.which == 13){
            $('#btnZeroStock').click();
        }
    });
   </script>
 @stop
