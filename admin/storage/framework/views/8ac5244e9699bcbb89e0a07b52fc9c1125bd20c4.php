<?php $__env->startSection('css'); ?>
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


    <!--<link href="<?php echo e(asset('css/po.css?v=1.0')); ?>" rel="stylesheet" type="text/css" />-->



<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

    <div class="content-header">
        <div>
            <h2 class="content-title card-title"><?php echo e($product->title); ?></h2>
            <p>Available stock : <?php echo e($product->available_stock); ?></p>

        </div>
    </div>
    <div class="card">
        <header class="card-header">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-6 mb-lg-0 mb-15">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Pricing')): ?>
                        <h5> Price : <?php if($product->discount_status): ?>
                                <strike style="color: #c4bbbb;"><?php echo e(number_format($product->price)); ?></strike>
                                <?php echo e(number_format($product->price - $product->discount_amount)); ?>

                            <?php else: ?>
                                <?php echo e(number_format($product->price)); ?>

                            <?php endif; ?>
                        </h5>
                    <?php endif; ?>
                    <span class="badge rounded-pill <?php echo e($product->status ? 'alert-success' : 'alert-danger'); ?>">
                        <?php if($product->status == 1): ?>
                            Published
                        <?php elseif($product->status == 2): ?>
                            Dis continue
                        <?php else: ?>
                            Un Published
                        <?php endif; ?>
                    </span>

                    <br>
                    <small class="text-muted">Last Updated: <?php echo e(date('M d,Y', strtotime($product->updated_at))); ?></small>
                </div>
                <div class="col-lg-6 col-md-6 ms-auto text-md-end">

                    <a target="_blank" class="btn btn-secondary d-inline"
                        href="<?php echo e(route('product.barcode.print', $product->id)); ?>">Print Barcode</a>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Product')): ?>
                        <button class="btn btn-warning d-inline" onclick="zeroAllStock('<?php echo e($product->barcode); ?>')">Zero All Stock</button>
                        <a class="btn btn-primary d-inline" href="<?php echo e(route('products.edit', $product->id)); ?>">Edit
                            info</a>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete Product')): ?>
                        <form class="<?php echo \Illuminate\Support\Arr::toCssClasses('d-inline') ?>" onsubmit="return confirm('Do you really want to do this?');"
                            id="delete-form" action="<?php echo e(route('products.destroy', $product->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>

                            <button style="width: min-content;" class=" btn btn-instagram d-inline"
                                type="submit">Delete</button>
                        </form>
                    <?php endif; ?>
                    <?php ?>
                </div>
            </div>
            <?php if($show_bundle_button): ?>
                <button class="btn btn-success generate-bundle" data-product-id="<?php echo e($product->id); ?>">
                    Generate Bundle
                </button>
                <small class="text-muted d-block mt-2">
                    Note: Bundles will only be generated for shades that have at least two sizes.
                </small>
            <?php endif; ?>
        </header>
        <!-- card-header end// -->
        <div class="card-body">
            <div class="row mb-50 mt-20 order-info-wrap">
                <div class="col-md-4">
                    <article class="icontext align-items-start">

                        <div class="text">
                            <h6 class="mb-1">Basic</h6>
                            <p class="mb-1">
                                <b>Code : </b> <?php echo e($product->code); ?> <br>

                                <b>Have Variants : </b> <span style="display: inline-block;font-size: 12px;"
                                    class="badge rounded-pill <?php echo e($product->have_variants ? 'alert-success' : 'alert-danger'); ?>"><?php echo e($product->have_variants ? 'YES' : 'NO'); ?></span>
                                <br>
                                <b>Reordering-level : </b><?php echo e($product->re_order_level); ?><br>

                                <b>Barcode : </b><?php echo e($product->barcode); ?>

                            </p>

                        </div>
                    </article>
                </div>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Pricing')): ?>
                    <!-- col// -->
                    <div class="col-md-4">
                        <article class="icontext align-items-start">
                            <span class="icon icon-sm rounded-circle bg-primary-light">
                                <i class="text-primary material-icons md-percentage"></i>
                            </span>
                            <div class="text">
                                <h6 class="mb-1">Price</h6>
                                <p class="mb-1">
                                    <b>Whole Sale : </b> <?php echo e($product->price); ?> <br>

                                    <b>Purchase Price : </b> <?php echo e($product->purchase_price); ?> <br>

                                    <b>Dz Price : </b> <?php echo e($product->dz_price); ?> <br>
                                </p>

                            </div>
                        </article>
                    </div>
                <?php endif; ?>
                <!-- col// -->
                <div class="col-md-4">
                    <article class="icontext align-items-start">

                        <div class="text">
                            <h6 class="mb-1">Brand / Category</h6>
                            <p class="mb-1">
                                <b>Brand : </b> <?php echo e($product->brand ? $product->brand->title : ''); ?><br>
                                <b>Categories : </b>
                                <?php if($product->categories): ?>
                                    <?php $__currentLoopData = $product->categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($loop->last): ?>
                                            <?php if($category->category): ?>
                                                <?php echo e($category->category->title); ?>

                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if($category->category): ?>
                                                <?php echo e($category->category->title); ?>,
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </p>
                            <b>Related Products </b>
                            <ul style="list-style: circle">
                                <?php $__currentLoopData = $product->relatedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rP): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li style="margin-left: 20px;"><?php echo e($rP->relatedProduct->title); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                    <?php if($product->have_variants): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr style="text-align: center">
                                        <th width="5%">Sr #</th>
                                        <th width="25%">Barcode </th>
                                        <th width="10%">Shade</th>
                                        <th width="10%">Size</th>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Pricing')): ?>
                                            <th width="10%">Price</th>
                                        <?php endif; ?>
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
                                    <?php $__currentLoopData = $product->variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr style="text-align: center;">
                                            <td>
                                                <?php echo e($sr++); ?>

                                            </td>
                                            <td><?php echo e($v->barcode); ?></td>
                                            <td><?php echo e($v->shade); ?></td>
                                            <td><?php echo e($v->size); ?></td>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Pricing')): ?>
                                                <td>

                                                    <b>Whole Sale : </b> <?php echo e($v->additional_price); ?> <br>

                                                    <b>Purchase Price : </b> <?php echo e($v->purchase_price); ?> <br>

                                                    <b>Dz Price : </b> <?php echo e($v->dz_price); ?> <br>

                                                </td>
                                            <?php endif; ?>

                                            <td><span style="display: inline-block;font-size: 12px;"
                                                    class="badge rounded-pill <?php echo e($v->status ? 'alert-success' : 'alert-danger'); ?>"><?php echo e($v->status ? 'Active' : 'InActive'); ?></span>
                                            </td>
                                            <!--<td>-->
                                            <!--    <?php $__currentLoopData = $stores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    -->
                                            <!--        <?php echo e($s->name); ?> : <?php echo e($storeVariants[$s->id][$v->id]); ?> <br>-->
                                            <!--
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>-->
                                            <!--</td>-->
                                            <td><?php echo e($v->available_stock); ?></td>

                                            <?php $stock += $v->available_stock; ?>


                                            <td>
                                                <a target="_blank" class="dropdown-item btn btn-secondary d-inline"
                                                    href="<?php echo e(route('variant.barcode.print', $v->id)); ?>">Print Barcode</a>

                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    <tr>
                                        <td colspan="9">
                                            <article class="float-end">
                                                <dl class="dlist" style="border-bottom: 1px double">

                                                    <dt><b>Total Stock : </b></dt>
                                                    <dd><?php echo e(number_format($stock)); ?></dd>
                                                </dl>
                                            </article>
                                        </td>


                                    </tr>

                                </tbody>
                            </table>
                        </div>
                        <!-- table-responsive// -->
                    <?php endif; ?>
                </div>


                <!-- col// -->

                <div class="col-lg-4">
                    <div class="box shadow-sm bg-light">
                        <h6 class="mb-15">Short Description</h6>
                        <p>
                            <?php echo e($product->short_description); ?>

                        </p>
                    </div>
                    <br>

                    <div class="box shadow-sm bg-light">
                        <h6 class="mb-15">Full Description</h6>
                        <p>
                            <?php echo e($product->long_description); ?>

                        </p>
                    </div>
                    <br>



                </div>
                <!-- col// -->
            </div>
            <hr>

            <div class="row gx-3 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 row-cols-xxl-5">

                <?php $__currentLoopData = $product->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col">
                        <div class="card card-product-grid">
                            <a href="#" class="img-wrap">
                                <img style="min-height:214px" onclick="openModal();currentSlide(1)"
                                    src="<?php echo e(asset('storage/' . $img->url)); ?>" alt="Product"> </a>
                            <div class="info-wrap">
                                <a href="#" class="title text-truncate text-center"><span
                                        class="badge rounded-pill <?php echo e($img->status ? 'alert-success' : 'alert-danger'); ?>"><?php echo e($img->status ? 'Active' : 'InActive'); ?></span></a>
                                <div class="price mb-2">Serial # <?php echo e($img->serial_no); ?></div>

                                <a onclick="openFileModal('<?php echo e($img->id); ?>')"
                                    style="font-size: 12px;cursor: pointer;"></i><u>Edit</u></a>

                                <a onclick="removeImage('<?php echo e($img->id); ?>')"
                                    style="float:right; font-size: 12px;cursor: pointer;"></i><u>Remove</u></a>
                                <!-- price.// -->
                            </div>
                        </div>
                        <!-- card-product  end// -->
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



            </div>
        </div>
        <!-- card-body end// -->

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Pricing')): ?>

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
                                <?php $__currentLoopData = $product->purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($r->receiving): ?>
                                        <tr>
                                            <td><?php echo e($sr++); ?></td>
                                            <td>

                                                <a href="<?php echo e(route('receiving.show', $r->receiving_id)); ?>" target="_blank"><i
                                                        class="material-icons md-unarchive"></i>&nbsp;&nbsp;<?php echo e($r->receiving->invoice_no); ?></a>
                                            </td>
                                            <td><b><?php echo e(date('d-m-Y', strtotime($r->receiving->date))); ?></b></td>
                                            <td><b><?php echo e($r->receiving->supplier ? $r->receiving->supplier->name : ''); ?></b></td>

                                            <td><?php echo e($r->variant ? $r->variant->barcode : ''); ?></td>
                                            <td><?php echo e($r->variant ? $r->variant->shade : ''); ?></td>
                                            <td><?php echo e($r->variant ? $r->variant->size : ''); ?></td>
                                            <td><b><?php echo e($r->qty); ?></b></td>
                                            <td><b><span
                                                        style="color:green"><?php echo e(array_key_exists($r->id, $stockSold) ? $stockSold[$r->id] : ''); ?></span></b>
                                            </td>
                                            <td><?php echo e(number_format($r->cost_price)); ?></td>
                                            <td><?php echo e(number_format($r->sale_price)); ?></td>
                                            <td><?php echo e(array_key_exists($r->id, $stockStatus) ? $stockStatus[$r->id] : ''); ?></td>

                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

                            <?php if($product->have_variants): ?>
                                <!-- Variant Filter -->
                                <div class="mb-3">
                                    <select id="variant-filter" class="form-control">
                                        <option value="">Select Variant</option>
                                        <?php $__currentLoopData = $product->variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($variant->id); ?>"><?php echo e($variant->shade); ?> - <?php echo e($variant->size); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            <?php endif; ?>

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
                                    <?php $__currentLoopData = $product->sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($s->order): ?>
                                            <?php
                                                $isManualReturn = isset($s->order->mannual_return) && $s->order->mannual_return == 1;
                                                $saleQty = $isManualReturn ? 0 : max(0, $s->qty);
                                                $returnQty = max(0, $s->return_qty ?? 0);
                                                $salePrice = $isManualReturn ? 0 : $s->price * $saleQty;
                                                $returnPrice = $s->price * $returnQty;
                                            ?>
                                            <?php if($saleQty > 0 || $returnQty > 0): ?>
                                                <tr class="sale-row"
                                                    data-variant-id="<?php echo e($s->variant ? $s->variant->id : ''); ?>"
                                                    data-date="<?php echo e(date('Y-m-d', strtotime($s->created_at))); ?>">
                                                    <td><?php echo e($sr++); ?></td>
                                                    <td>
                                                        <a href="<?php echo e(route('orders.show', $s->order_id)); ?>" target="_blank"><i
                                                                class="material-icons md-unarchive"></i>&nbsp;&nbsp;<?php echo e($s->order->order_no); ?></a>
                                                    </td>
                                                    <td><b><?php echo e(date('d-m-Y', strtotime($s->created_at))); ?></b></td>
                                                    <td><b><?php echo e($s->order->customer ? $s->order->customer->first_name : ''); ?></b>
                                                    </td>
                                                    <td><?php echo e($s->variant ? $s->variant->shade : ''); ?></td>
                                                    <td><?php echo e($s->variant ? $s->variant->size : ''); ?></td>

                                                    <td><b><?php echo e($saleQty); ?></b></td>
                                                    <td><b><?php echo e(number_format($salePrice)); ?></b></td>
                                                    <td><b><?php echo e($returnQty); ?></b></td>
                                                    <td><b><?php echo e($returnQty > 0 ? '- ' : ''); ?><?php echo e(number_format($returnPrice)); ?></b></td>
                                                </tr>

                                                <?php
                                                $totalSales += $salePrice;
                                                $totalQty += $saleQty;
                                                $totalReturnQty += $returnQty;
                                                $totalReturnSales += $returnPrice;
                                                ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-right"><strong>Total : </strong></td>
                                        <td id="totalQty" class="text-right"><strong><?php echo e($totalQty); ?></strong></td>
                                        <td id="totalSales"><strong><?php echo e(number_format($totalSales)); ?></strong></td>
                                        <td id="totalReturnQty" class="text-right"><strong><?php echo e($totalReturnQty); ?></strong></td>
                                        <td id="totalReturnSales"><strong><?php echo e($totalReturnSales > 0 ? '- ' : ''); ?><?php echo e(number_format($totalReturnSales)); ?></strong></td>
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
                    <?php $__currentLoopData = $product->audit; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($p->audit): ?>
                            <tr>
                                <td><?php echo e($sr++); ?></td>
                                <td><strong><?php echo e(date('d-m-Y', strtotime($p->created_at))); ?></strong></td>
                                <td><?php echo e($p->audit->storeId ? $p->audit->storeId->name : ''); ?></td>
                                <td><?php echo e($p->variant ? $p->variant->shade : ''); ?></td>
                                <td><?php echo e($p->variant ? $p->variant->size : ''); ?></td>
                                <td><?php echo e($p->system_qty); ?></td>
                                <td><?php echo e($p->in_hand_qty); ?></td>
                                <td><?php echo e($p->difference_qty); ?></td>
                                <td><?php echo e($p->adjust_in_stock); ?></td>
                                <td><?php echo e($p->adjust_in_damage); ?></td>
                                <td><?php echo e($p->adjust_in_missing); ?></td>
                                <td><?php echo e($p->reason); ?></td>
                            </tr>
                            <?php
                            $totalAdjustStock += $p->adjust_in_stock;
                            $totalAdjustDamage += $p->adjust_in_damage;
                            $totalAdjustMissing += $p->adjust_in_missing;
                            ?>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8" class="text-right"><strong>Total:</strong></td>
                        <td><strong><?php echo e(number_format($totalAdjustStock)); ?></strong></td>
                        <td><strong><?php echo e(number_format($totalAdjustDamage)); ?></strong></td>
                        <td><strong><?php echo e(number_format($totalAdjustMissing)); ?></strong></td>
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

            <?php endif; ?>



        </div>
    </div>
    <!-- card end// -->

    <div id="myModal" class="modal">
        <span class="close cursor" onclick="closeModal1()">&times;</span>
        <div class="modal-content">
            <?php $__currentLoopData = $product->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="mySlides" style="text-align: center;">
                    <div class="numbertext"></div>
                    <img src="<?php echo e(asset('storage/' . $img->url)); ?>" style="width:50%;min-height: 610px;">
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>





            <!--<a class="prev" style="background: black;" onclick="plusSlides(-1)">&#10094;</a>-->
            <!--<a class="next" style="background: black;" onclick="plusSlides(1)">&#10095;</a>-->


        </div>
    </div>

    <div class="modal fade" id="prModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
        style="z-index:9999" aria-hidden="true">

    </div>




<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>

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
                    url: "<?php echo e(route('product.image.modal')); ?>",
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
                                url: "<?php echo e(route('product.image.remove')); ?>",
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
                        _token: '<?php echo e(csrf_token()); ?>'
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
                    url: "<?php echo e(route('product.zero-stock')); ?>",
                    type: 'POST',
                    data: {
                        barcode: barcode,
                        _token: '<?php echo e(csrf_token()); ?>'
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



<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\EjazSportsCode\ejazsports\admin\resources\views/catalog/product/show.blade.php ENDPATH**/ ?>