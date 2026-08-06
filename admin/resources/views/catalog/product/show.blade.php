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
        /* .next {
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
            } */

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

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <link href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css" rel="stylesheet">


    <!--<link href="{{ asset('css/po.css?v=1.0') }}" rel="stylesheet" type="text/css" />-->



@stop
@section('content')

    <div class="content-header">
        <div>
            <h2 class="content-title card-title">{{ $product->title }}</h2>
            <p>Available stock : {{ $product->available_stock }}</p>

        </div>
    </div>
    <div class="card">
        <header class="card-header">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-6 mb-lg-0 mb-15">
                    @can('Manage Pricing')
                        <h5> Price : @if ($product->discount_status)
                                <strike style="color: #c4bbbb;">{{ number_format($product->price) }}</strike>
                                {{ number_format($product->price - $product->discount_amount) }}
                            @else
                                {{ number_format($product->price) }}
                            @endif
                        </h5>
                    @endcan
                    <span class="badge rounded-pill {{ $product->status ? 'alert-success' : 'alert-danger' }}">
                        @if ($product->status == 1)
                            Published
                        @elseif($product->status == 2)
                            Dis continue
                        @else
                            Un Published
                        @endif
                    </span>

                    <br>
                    <small class="text-muted">Last Updated: {{ date('M d,Y', strtotime($product->updated_at)) }}</small>
                </div>
                <div class="col-lg-6 col-md-6 ms-auto text-md-end">

                    <a target="_blank" class="btn btn-secondary d-inline"
                        href="{{ route('product.barcode.print', $product->id) }}">Print Barcode</a>

                    @can('Edit Product')
                        <button class="btn btn-warning d-inline" onclick="zeroAllStock('{{ $product->barcode }}')">Zero All Stock</button>
                        <a class="btn btn-primary d-inline" href="{{ route('products.edit', $product->id) }}">Edit
                            info</a>
                    @endcan

                    @can('Delete Product')
                        <form @class('d-inline') onsubmit="return confirm('Do you really want to do this?');"
                            id="delete-form" action="{{ route('products.destroy', $product->id) }}" method="POST">
                            @csrf
                            @method('DELETE')

                            <button style="width: min-content;" class=" btn btn-instagram d-inline"
                                type="submit">Delete</button>
                        </form>
                    @endcan
                    <?php ?>
                </div>
            </div>
            @if ($show_bundle_button)
                <button class="btn btn-success generate-bundle" data-product-id="{{ $product->id }}">
                    Generate Bundle
                </button>
                <small class="text-muted d-block mt-2">
                    Note: Bundles will only be generated for shades that have at least two sizes.
                </small>
            @endif
        </header>
        <!-- card-header end// -->
        <div class="card-body">
            <div class="row mb-50 mt-20 order-info-wrap">
                <div class="col-md-4">
                    <article class="icontext align-items-start">

                        <div class="text">
                            <h6 class="mb-1">Basic</h6>
                            <p class="mb-1">
                                <b>Code : </b> {{ $product->code }} <br>

                                <b>Have Variants : </b> <span style="display: inline-block;font-size: 12px;"
                                    class="badge rounded-pill {{ $product->have_variants ? 'alert-success' : 'alert-danger' }}">{{ $product->have_variants ? 'YES' : 'NO' }}</span>
                                <br>
                                <b>Reordering-level : </b>{{ $product->re_order_level }}<br>

                                <b>Barcode : </b>{{ $product->barcode }}
                            </p>

                        </div>
                    </article>
                </div>
                @can('Manage Pricing')
                    <!-- col// -->
                    <div class="col-md-4">
                        <article class="icontext align-items-start">
                            <span class="icon icon-sm rounded-circle bg-primary-light">
                                <i class="text-primary material-icons md-percentage"></i>
                            </span>
                            <div class="text">
                                <h6 class="mb-1">Price</h6>
                                <p class="mb-1">
                                    <b>Whole Sale : </b> {{ $product->price }} <br>

                                    <b>Purchase Price : </b> {{ $product->purchase_price }} <br>

                                    <b>Dz Price : </b> {{ $product->dz_price }} <br>
                                </p>

                            </div>
                        </article>
                    </div>
                @endcan
                <!-- col// -->
                <div class="col-md-4">
                    <article class="icontext align-items-start">

                        <div class="text">
                            <h6 class="mb-1">Brand / Category</h6>
                            <p class="mb-1">
                                <b>Brand : </b> {{ $product->brand ? $product->brand->title : '' }}<br>
                                <b>Categories : </b>
                                @if ($product->categories)
                                    @foreach ($product->categories as $category)
                                        @if ($loop->last)
                                            @if ($category->category)
                                                {{ $category->category->title }}
                                            @endif
                                        @else
                                            @if ($category->category)
                                                {{ $category->category->title }},
                                            @endif
                                        @endif
                                    @endforeach
                                @endif
                            </p>
                            <b>Related Products </b>
                            <ul style="list-style: circle">
                                @foreach ($product->relatedProducts as $rP)
                                    <li style="margin-left: 20px;">{{ $rP->relatedProduct->title }}</li>
                                @endforeach
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
                    @if ($product->have_variants)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr style="text-align: center">
                                        <th width="5%">Sr #</th>
                                        <th width="25%">Barcode </th>
                                        <th width="10%">Shade</th>
                                        <th width="10%">Size</th>
                                        @can('Manage Pricing')
                                            <th width="10%">Price</th>
                                        @endcan
                                        <th width="10%">Status</th>
                                        <!--<th width="20%">Store</th>-->
                                        <th width="20%">Stock</th>
                                        <th></th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sr = 1;
                                    $stock = 0;
                                    $online = 0; ?>
                                    @foreach ($product->variants as $v)
                                        <tr style="text-align: center;">
                                            <td>
                                                {{ $sr++ }}
                                            </td>
                                            <td>{{ $v->barcode }}</td>
                                            <td>{{ $v->shade }}</td>
                                            <td>{{ $v->size }}</td>
                                            @can('Manage Pricing')
                                                <td>

                                                    <b>Whole Sale : </b> {{ $v->additional_price }} <br>

                                                    <b>Purchase Price : </b> {{ $v->purchase_price }} <br>

                                                    <b>Dz Price : </b> {{ $v->dz_price }} <br>

                                                </td>
                                            @endcan

                                            <td><span style="display: inline-block;font-size: 12px;"
                                                    class="badge rounded-pill {{ $v->status ? 'alert-success' : 'alert-danger' }}">{{ $v->status ? 'Active' : 'InActive' }}</span>
                                            </td>
                                            <!--<td>-->
                                            <!--    @foreach ($stores as $s)
    -->
                                            <!--        {{ $s->name }} : {{ $storeVariants[$s->id][$v->id] }} <br>-->
                                            <!--
    @endforeach-->
                                            <!--</td>-->
                                            <td>{{ $v->available_stock }}</td>

                                            <?php $stock += $v->available_stock; ?>


                                            <td>
                                                <a target="_blank" class="dropdown-item btn btn-secondary d-inline"
                                                    href="{{ route('variant.barcode.print', $v->id) }}">Print Barcode</a>

                                            </td>
                                        </tr>
                                    @endforeach

                                    <tr>
                                        <td colspan="9">
                                            <article class="float-end">
                                                <dl class="dlist" style="border-bottom: 1px double">

                                                    <dt><b>Total Stock : </b></dt>
                                                    <dd>{{ number_format($stock) }}</dd>
                                                </dl>
                                            </article>
                                        </td>


                                    </tr>

                                </tbody>
                            </table>
                        </div>
                        <!-- table-responsive// -->
                    @endif
                </div>


                <!-- col// -->

                <div class="col-lg-4">
                    <div class="box shadow-sm bg-light">
                        <h6 class="mb-15">Short Description</h6>
                        <p>
                            {{ $product->short_description }}
                        </p>
                    </div>
                    <br>

                    <div class="box shadow-sm bg-light">
                        <h6 class="mb-15">Full Description</h6>
                        <p>
                            {{ $product->long_description }}
                        </p>
                    </div>
                    <br>



                </div>
                <!-- col// -->
            </div>
            <hr>

            <div class="row gx-3 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 row-cols-xxl-5">

                @foreach ($product->images as $img)
                    <div class="col">
                        <div class="card card-product-grid">
                            <a href="#" class="img-wrap">
                                <img style="min-height:214px" onclick="openModal();currentSlide(1)"
                                    src="{{ asset('storage/' . $img->url) }}" alt="Product"> </a>
                            <div class="info-wrap">
                                <a href="#" class="title text-truncate text-center"><span
                                        class="badge rounded-pill {{ $img->status ? 'alert-success' : 'alert-danger' }}">{{ $img->status ? 'Active' : 'InActive' }}</span></a>
                                <div class="price mb-2">Serial # {{ $img->serial_no }}</div>

                                <a onclick="openFileModal('{{ $img->id }}')"
                                    style="font-size: 12px;cursor: pointer;"></i><u>Edit</u></a>

                                <a onclick="removeImage('{{ $img->id }}')"
                                    style="float:right; font-size: 12px;cursor: pointer;"></i><u>Remove</u></a>
                                <!-- price.// -->
                            </div>
                        </div>
                        <!-- card-product  end// -->
                    </div>
                @endforeach



            </div>
        </div>
        <!-- card-body end// -->

        @can('Manage Pricing')

            <div class="card">
                <header class="card-header">
                    <div class="row align-items-center">
                        <div class="col-lg-6 col-md-6 mb-lg-0 mb-15">
                            <span> <b>Purchase History : </b> </span> <br>

                        </div>

                    </div>
                </header>
                <!-- card-header end// -->
                <div class="card-body">
                    <!-- row // -->
                    <div class="table-responsive">
                        <table id="myTable1" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Sr#</th>
                                    <th>Invoice #</th>
                                    <th>Date</th>
                                    <th>Supplier</th>

                                    <th>Barcode</th>
                                    <th>Shade</th>
                                    <th>Size</th>
                                    <th>Received Qty</th>
                                    <th>Sold Qty</th>
                                    <th>Purchase Price</th>
                                    <th>Whole Sale Price</th>
                                    <th>Status</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php $sr = 1; ?>
                                @foreach ($product->purchases as $r)
                                    @if ($r->receiving)
                                        <tr>
                                            <td>{{ $sr++ }}</td>
                                            <td>

                                                <a href="{{ route('receiving.show', $r->receiving_id) }}" target="_blank"><i
                                                        class="material-icons md-unarchive"></i>&nbsp;&nbsp;{{ $r->receiving->invoice_no }}</a>
                                            </td>
                                            <td><b>{{ date('d-m-Y', strtotime($r->receiving->date)) }}</b></td>
                                            <td><b>{{ $r->receiving->supplier ? $r->receiving->supplier->name : '' }}</b></td>

                                            <td>{{ $r->variant ? $r->variant->barcode : '' }}</td>
                                            <td>{{ $r->variant ? $r->variant->shade : '' }}</td>
                                            <td>{{ $r->variant ? $r->variant->size : '' }}</td>
                                            <td><b>{{ $r->qty }}</b></td>
                                            <td><b><span
                                                        style="color:green">{{ array_key_exists($r->id, $stockSold) ? $stockSold[$r->id] : '' }}</span></b>
                                            </td>
                                            <td>{{ number_format($r->cost_price) }}</td>
                                            <td>{{ number_format($r->sale_price) }}</td>
                                            <td>{{ array_key_exists($r->id, $stockStatus) ? $stockStatus[$r->id] : '' }}</td>

                                        </tr>
                                    @endif
                                @endforeach
                            <tbody>
                        </table>
                    </div>


                </div>
                <!-- card-body end// -->


                <div class="card">
                    <header class="card-header">
                        <div class="row align-items-center">
                            <div class="col-lg-6 col-md-6 mb-lg-0 mb-15">
                                <span><b>Sale History:</b></span><br>
                            </div>

                            <!-- Date Range Filter -->
                            <div class="col-lg-6 col-md-6">
                                <input type="text" id="daterange-btn" class="form-control"
                                    placeholder="Select Date Range">
                            </div>
                        </div>
                    </header>
                    <!-- card-header end// -->
                    <div class="card-body">
                        <!-- row // -->
                        <div class="table-responsive">

                            @if ($product->have_variants)
                                <!-- Variant Filter -->
                                <div class="mb-3">
                                    <select id="variant-filter" class="form-control">
                                        <option value="">Select Variant</option>
                                        @foreach ($product->variants as $variant)
                                            <option value="{{ $variant->id }}">{{ $variant->shade }} - {{ $variant->size }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <!-- Sales Table -->
                            <table id="myTable2" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Sr#</th>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Shade</th>
                                        <th>Size</th>
                                        <th>Sale Qty</th>
                                        <th>Sale Price</th>
                                        <th>Return Qty</th>
                                        <th>Return Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sr = 1;
                                    $totalSales = 0;
                                    $totalQty = 0;
                                    $totalReturnQty = 0;
                                    $totalReturnSales = 0; ?>
                                    @foreach ($product->sales as $s)
                                        @if ($s->order)
                                            @php
                                                $isManualReturn = isset($s->order->mannual_return) && $s->order->mannual_return == 1;
                                                $saleQty = $isManualReturn ? 0 : max(0, $s->qty);
                                                $returnQty = max(0, $s->return_qty ?? 0);
                                                $salePrice = $isManualReturn ? 0 : $s->price * $saleQty;
                                                $returnPrice = $s->price * $returnQty;
                                            @endphp
                                            @if ($saleQty > 0 || $returnQty > 0)
                                                <tr class="sale-row"
                                                    data-variant-id="{{ $s->variant ? $s->variant->id : '' }}"
                                                    data-date="{{ date('Y-m-d', strtotime($s->created_at)) }}">
                                                    <td>{{ $sr++ }}</td>
                                                    <td>
                                                        <a href="{{ route('orders.show', $s->order_id) }}" target="_blank"><i
                                                                class="material-icons md-unarchive"></i>&nbsp;&nbsp;{{ $s->order->order_no }}</a>
                                                    </td>
                                                    <td><b>{{ date('d-m-Y', strtotime($s->created_at)) }}</b></td>
                                                    <td><b>{{ $s->order->customer ? $s->order->customer->first_name : '' }}</b>
                                                    </td>
                                                    <td>{{ $s->variant ? $s->variant->shade : '' }}</td>
                                                    <td>{{ $s->variant ? $s->variant->size : '' }}</td>

                                                    <td><b>{{ $saleQty }}</b></td>
                                                    <td><b>{{ number_format($salePrice) }}</b></td>
                                                    <td><b>{{ $returnQty }}</b></td>
                                                    <td><b>{{ $returnQty > 0 ? '- ' : '' }}{{ number_format($returnPrice) }}</b></td>
                                                </tr>

                                                <?php
                                                $totalSales += $salePrice;
                                                $totalQty += $saleQty;
                                                $totalReturnQty += $returnQty;
                                                $totalReturnSales += $returnPrice;
                                                ?>
                                            @endif
                                        @endif
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-right"><strong>Total : </strong></td>
                                        <td id="totalQty" class="text-right"><strong>{{ $totalQty }}</strong></td>
                                        <td id="totalSales"><strong>{{ number_format($totalSales) }}</strong></td>
                                        <td id="totalReturnQty" class="text-right"><strong>{{ $totalReturnQty }}</strong></td>
                                        <td id="totalReturnSales"><strong>{{ $totalReturnSales > 0 ? '- ' : '' }}{{ number_format($totalReturnSales) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <!-- card-body end// -->
                </div>


                <div class="card">
                    <header class="card-header">
                        <div class="row align-items-center">
                            <div class="col-lg-6 col-md-6 mb-lg-0 mb-15">
                                <span> <b>Audit List : </b> </span> <br>

                            </div>

                        </div>
                    </header>
                    <!-- card-header end// -->
                   <div class="card">
    <header class="card-header">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-6 mb-lg-0 mb-15">
                <span> <b>Audit List : </b> </span> <br>
            </div>
        </div>
    </header>
    <!-- card-header end// -->
    <div class="card-body">
        <!-- row // -->
        <div class="table-responsive">
            <table id="myTable3" class="table table-hover">
                <thead>
                    <tr>
                        <th>Sr#</th>
                        <th>Date</th>
                        <th>Store</th>
                        <th>Shade</th>
                        <th>Size</th>
                        <th>System QTY</th>
                        <th>Audit QTY</th>
                        <th>Difference QTY</th>
                        <th>Adjust in Stock</th>
                        <th>Adjust in Damage</th>
                        <th>Adjust in Missing</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sr = 1;
                    $totalAdjustStock = 0;
                    $totalAdjustDamage = 0;
                    $totalAdjustMissing = 0;
                    ?>
                    @foreach ($product->audit as $p)
                        @if ($p->audit)
                            <tr>
                                <td>{{ $sr++ }}</td>
                                <td><strong>{{ date('d-m-Y', strtotime($p->created_at)) }}</strong></td>
                                <td>{{ $p->audit->storeId ? $p->audit->storeId->name : '' }}</td>
                                <td>{{ $p->variant ? $p->variant->shade : '' }}</td>
                                <td>{{ $p->variant ? $p->variant->size : '' }}</td>
                                <td>{{ $p->system_qty }}</td>
                                <td>{{ $p->in_hand_qty }}</td>
                                <td>{{ $p->difference_qty }}</td>
                                <td>{{ $p->adjust_in_stock }}</td>
                                <td>{{ $p->adjust_in_damage }}</td>
                                <td>{{ $p->adjust_in_missing }}</td>
                                <td>{{ $p->reason }}</td>
                            </tr>
                            <?php
                            $totalAdjustStock += $p->adjust_in_stock;
                            $totalAdjustDamage += $p->adjust_in_damage;
                            $totalAdjustMissing += $p->adjust_in_missing;
                            ?>
                        @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8" class="text-right"><strong>Total:</strong></td>
                        <td><strong>{{ number_format($totalAdjustStock) }}</strong></td>
                        <td><strong>{{ number_format($totalAdjustDamage) }}</strong></td>
                        <td><strong>{{ number_format($totalAdjustMissing) }}</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <!-- card-body end// -->
</div>
                    <!-- card-body end// -->
                </div>

            @endcan



        </div>
    </div>
    <!-- card end// -->

    <div id="myModal" class="modal">
        <span class="close cursor" onclick="closeModal1()">&times;</span>
        <div class="modal-content">
            @foreach ($product->images as $img)
                <div class="mySlides" style="text-align: center;">
                    <div class="numbertext"></div>
                    <img src="{{ asset('storage/' . $img->url) }}" style="width:50%;min-height: 610px;">
                </div>
            @endforeach





            <!--<a class="prev" style="background: black;" onclick="plusSlides(-1)">&#10094;</a>-->
            <!--<a class="next" style="background: black;" onclick="plusSlides(1)">&#10095;</a>-->


        </div>
    </div>

    <div class="modal fade" id="prModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
        style="z-index:9999" aria-hidden="true">

    </div>




@stop

@section('js')

    <script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.print.min.js"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>




    <script>
        $(document).ready(function() {

            // Initialize date range picker
            $('#daterange-btn').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            // Function to update totals
            function updateTotals() {
                let totalQty = 0;
                let totalSales = 0;
                let totalReturnQty = 0;
                let totalReturnSales = 0;

                $('#myTable2 tbody .sale-row:visible').each(function() {
                    // Access sale qty (7th column, index 6)
                    totalQty += parseInt($(this).find('td').eq(6).text()) || 0;

                    // Access sale price (8th column, index 7)
                    totalSales += parseFloat($(this).find('td').eq(7).text().replace(/[^0-9.-]+/g, "")) ||
                        0;

                    // Access return qty and return price
                    totalReturnQty += parseInt($(this).find('td').eq(8).text()) || 0;
                    totalReturnSales += Math.abs(parseFloat($(this).find('td').eq(9).text().replace(/[^0-9.-]+/g, "")) ||
                        0);
                });

                $('#totalQty').text(totalQty);
                $('#totalSales').text(number_format(totalSales));
                $('#totalReturnQty').text(totalReturnQty);
                $('#totalReturnSales').text((totalReturnSales > 0 ? '- ' : '') + number_format(totalReturnSales));
            }

            // On date range change
            $('#daterange-btn').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format(
                    'DD-MM-YYYY'));
                filterSales();
            });

            // Variant filter change
            $('#variant-filter').on('change', function() {
                filterSales();
            });

            // Function to filter sales based on date range and variant
            function filterSales() {
                var selectedVariant = $('#variant-filter').val();
                var selectedDateRange = $('#daterange-btn').val().split(' - ');

                var startDate = selectedDateRange[0] ? moment(selectedDateRange[0], 'DD-MM-YYYY').format(
                    'YYYY-MM-DD') : '';
                var endDate = selectedDateRange[1] ? moment(selectedDateRange[1], 'DD-MM-YYYY').format(
                    'YYYY-MM-DD') : '';

                $('#myTable2 tbody tr').each(function() {
                    var row = $(this);
                    var rowVariantId = row.data('variant-id');
                    var rowDate = row.data('date');

                    var dateMatch = (!startDate || !endDate || (moment(rowDate).isBetween(startDate,
                        endDate, undefined, '[]')));
                    var variantMatch = !selectedVariant || rowVariantId == selectedVariant;

                    if (dateMatch && variantMatch) {
                        row.show();
                    } else {
                        row.hide();
                    }
                });

                updateTotals();
            }

            // Function to format numbers as currency
            function number_format(number) {
                return new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD'
                }).format(number);
            }

            $('#myTable1').DataTable({
                'ordering': false,
                'sorting': false,
                'paging': false,
                'info': false,
                'searching': true,
                dom: 'Bfrtip',
                buttons: [
                    'csv', 'excel', 'print'
                ]
            });


            $('#myTable2').DataTable({
                'ordering': false,
                'sorting': false,
                'paging': true,
                'pageLength': 100,
                'info': true,
                'searching': true,
                dom: 'Bfrtip',
                buttons: [
                    'csv', 'excel', 'print'
                ]
            });

            $('#myTable3').DataTable({
                'ordering': false,
                'sorting': false,
                'paging': false,
                'info': false,
                'searching': true,
                dom: 'Bfrtip',
                buttons: [
                    'csv', 'excel', 'print'
                ]
            });

            function openModal() {
                document.getElementById("myModal").style.display = "block";
            }

            function closeModal1() {
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
                if (n > slides.length) {
                    slideIndex = 1
                }
                if (n < 1) {
                    slideIndex = slides.length
                }
                for (i = 0; i < slides.length; i++) {
                    slides[i].style.display = "none";
                }
                for (i = 0; i < dots.length; i++) {
                    dots[i].className = dots[i].className.replace(" active", "");
                }
                slides[slideIndex - 1].style.display = "block";
                dots[slideIndex - 1].className += " active";
                captionText.innerHTML = dots[slideIndex - 1].alt;
            }


            function openFileModal(image_id) {



                $.ajax({
                    url: "{{ route('product.image.modal') }}",
                    type: 'GET',
                    data: {
                        image_id: image_id
                    },
                    success: function(data) {
                        document.getElementById('prModalLong').innerHTML = data;
                        $('#prModalLong').modal('show');


                    }
                });
            }

            function removeImage(image_id) {

                $.confirm({
                    title: 'Product Image Remove!',
                    content: 'Are you sure you want to do this!',
                    buttons: {
                        confirm: function() {
                            $.ajax({
                                url: "{{ route('product.image.remove') }}",
                                type: 'GET',
                                data: {
                                    image_id: image_id
                                },
                                success: function(data) {

                                    toastr.success('Image Deleted Successfully!.');
                                    window.location.reload();
                                }
                            });
                        },
                        cancel: function() {}
                    }
                });
            }


            $(document).on('click', '#closePrModal', function(e) {
                $('#prModalLong').modal('hide');
            });


            function closeModal() {
                $('#prModalLong').modal('hide');
            }
        });

        $(document).on('click', '.generate-bundle', function() {
            const $button = $(this);
            const productId = $button.data('product-id');
            const originalText = $button.html();

            if (confirm("This will create bundles for all eligible variants. Continue?")) {
                $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Generating...');

                $.ajax({
                    url: '/admin/products/generate-bundles',
                    method: 'POST',
                    data: {
                        product_id: productId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            if (response.data_modified) {
                                window.location.reload();
                            }
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.message ||
                        'An error occurred while generating bundles');
                    },
                    complete: function() {
                        $button.prop('disabled', false).html(originalText);
                    }
                });
            }
        });
    </script>
    
    <script>
        function zeroAllStock(barcode) {
            if(confirm("Are you sure you want to set the stock to 0 for this product and ALL of its variants?")) {
                $.ajax({
                    url: "{{ route('product.zero-stock') }}",
                    type: 'POST',
                    data: {
                        barcode: barcode,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert(response.message);
                        location.reload();
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON ? xhr.responseJSON.message : 'Error processing barcode');
                    }
                });
            }
        }
    </script>



@stop
