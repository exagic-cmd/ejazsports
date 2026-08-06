@extends('layouts.app')


@section('content')

    <div class="row">
        <div class="col-12">
            <div class="content-header">
                <h2 class="content-title">New Supplier Return</h2>
                <div>
                    <button onclick="setFormSubmitting();document.getElementById('form').submit()" class="btn btn-md rounded font-sm hover-up">Save</button>
                </div>
            </div>
        </div>
        <form action="{{route('supplier-returns.store')}}" method="post" id="form" enctype="multipart/form-data" autocomplete="false" style="display: contents">
            @csrf
        <div class="col-lg-9">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Basic</h4>
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="card-body">


                        <div @class('row')>
                            <!--<div class="col-lg-6">-->
                            <!--    <div class="mb-4">-->
                            <!--        <label for="product_name" class="form-label">Supplier </label>-->
                            <!--        <select id="supplier_id" class="form-control select2 @error('name') is-invalid @enderror" name="supplier_id">-->
                            <!--            <option value="">None</option>-->
                            <!--            @foreach($suppliers as $s)-->
                            <!--                <option value="{{$s->id}}">{{$s->name}}</option>-->
                            <!--            @endforeach-->
                            <!--        </select>-->


                            <!--        @error('supplier_id')-->
                            <!--        <div class="alert alert-danger">{{ $message }}</div>-->
                            <!--        @enderror-->
                            <!--    </div>-->
                            <!--</div>-->

                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Date</label>
                                    <input  type="date" id="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{$today}}" >
                                    @error('date')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>


                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label for="product_name" class="form-label">Cargo / Builty #</label>
                                <input  type="text" name="cargo_no" class="form-control @error('cargo_no') is-invalid @enderror" value="{{old('cargo_no')}}" >
                                @error('cargo_no')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                            <!--<div class="col-lg-6">-->
                            <!--    <div class="mb-4">-->
                            <!--        <label for="product_name" class="form-label">Payment Method</label>-->
                            <!--        <select class="form-control select2" name="payment_method">-->
                            <!--            <option value="{{\App\Models\Receiving::CASH}}">Cash</option>-->
                            <!--            <option value="{{\App\Models\Receiving::CREDIT}}">Credit</option>-->
                            <!--            <option value="{{\App\Models\Receiving::SALE_BASIS}}">Sale Basis</option>-->
                            <!--        </select>-->
                            <!--        @error('payment_method')-->
                            <!--        <div class="alert alert-danger">{{ $message }}</div>-->
                            <!--        @enderror-->
                            <!--    </div>-->
                            <!--</div>-->
                            
                            <input type="hidden" name="payment_method" value="{{\App\Models\Receiving::CASH}}">
                </div>

                        <div @class('row')>

                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Comment</label>
                                    <textarea name="comment" class="form-control" cols="5"></textarea>
                                    @error('comment')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>



                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Return From</label>
                                    <select class="form-control select2 @error('store_id') is-invalid @enderror" name="store_id">
                                        @foreach($stores as $s)
                                            <option value="{{$s->id}}">{{$s->name}}</option>
                                        @endforeach
                                    </select>

                                    @error('store_id')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="product_name" class="form-label">Stock Type</label>
                                    <select class="form-control select2 @error('stock_type') is-invalid @enderror" name="stock_type">
                                        @f
                                            <option value="1">Fresh Stock</option>
                                            <option value="2">Damage Stock</option>
                                       
                                    </select>

                                    @error('stock_type')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>



                            <!--<div class="col-lg-3">-->
                            <!--    <div class="mb-4">-->
                            <!--        <label for="product_name" class="form-label">Total Products</label>-->
                            <!--        <input style="cursor: not-allowed" type="number" readonly id="total_products" name="total_products" class="form-control" value="0" >-->
                            <!--    </div>-->
                            <!--</div>-->

                            <!--<div class="col-lg-3">-->
                            <!--    <div class="mb-4">-->
                            <!--        <label for="product_name" class="form-label">Total Quantity</label>-->
                            <!--        <input style="cursor: not-allowed" type="number" readonly  id="total_qty" name="total_qty"  class="form-control" value="0">-->

                            <!--    </div>-->
                            <!--</div>-->


                        </div>

                </div>
            </div>

        </div>
        
        
        
         <div class="col-lg-9">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Product Search here....</h4>
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="card-body">



                        <div @class('row')>

                           <div class="mb-4">
                        
                       
                        
                        <i class="fa fa-search"></i>

                                <input autofocus type="text" id="nav-search" placeholder="Search product by Name, SKU" class="form-control search-field">
                             
                    </div>
                    
                    <div id="result"  style="height: 900px; overflow-y: scroll;">
                        
                    </div>


                        </div>

                </div>
            </div>

        </div>

        
        
        
          

        <hr>
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Products</h4>
                </div>
                <div class="col-lg-12" id="productDiv">
                    <div class="table-responsive" >
                        <table id="myTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#Sr</th>
                                   
                                    <th scope="col">Product Title</th>
                                   
                                    <th scope="col">Variant</th>
                                   
                                    <th scope="col">Return Qty</th>
                                    <th scope="col"> Add</th>
                                </tr>
                            </thead>
                        <tbody>
                            
                            
                        </tbody>
                        
                        </table>
                        
                        </div>
                        
                        <!-- table-responsive //end -->

                </div>
            </div>
        </div>

           

        </form>
        
         <!--<button class="btn btn-primary "  onclick="addNewProduct()">Add Product</button>-->
    </div>



@stop

@section('js')
    <script>
        $('.select2').select2();
        $('#myTable').DataTable({
                                'ordering': true,
                                'order': [],
                                'sorting': true,
                                'paging': false,
                                'info': true,
                                'searching': true
                            });

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

        var config = {
            routes: {
                po: "{{ route('receiving.po.detail') }}",
                product : "{{ route('receiving.po.product.detail') }}",
                addProduct : "{{route('receiving.add.product')}}"
            }
        };
        
        
        
       //Search Field
$(".search-field").keyup(function(){
    query = $('#nav-search').val();
    
    let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    if(query.length > 2) {
        // document.getElementById('wait').style.display='block';
        document.getElementById('app').style.opacity = '0.1';
        let url = '{{route("receiving.direct.product.search")}}';
        fetch(url, {
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json, text-plain, */*",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": token
            },
            method: 'post',
            credentials: "same-origin",
            body: JSON.stringify({
                val: query

            })
        })
            .then(function (response) {
                return response.text();
            })
            .then(function (html) {
                
                document.getElementById('result').innerHTML = html;
                //  document.getElementById('wait').style.display='none';
                document.getElementById('app').style.opacity = '1';

            })
            .catch(function(error) {
                alert(error);
                //   document.getElementById('wait').style.display='none';
                document.getElementById('app').style.opacity = '1';
            });
    }
    else
    {
        document.getElementById('result').innerHTML = '';
    }
});


    </script>
    <script src="{{asset('js/po.js')}}" type="text/javascript"></script>

@stop

