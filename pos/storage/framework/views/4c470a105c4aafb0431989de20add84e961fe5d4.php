<html lang="en">

<head>
    <title>POS | Ejaz Sports</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/pos.css?v=1.2')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/pos-shop.css?v=1.8')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/pos-ui.css')); ?>">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" />
    <link rel="icon" sizes="16x16" href="<?php echo e(asset('images/logo.png')); ?>">
    <meta name="description" content="">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style type="text/css">
        #toast-container>div {
            width: 350px;
            top: 45px;

        }

        .highlight {
            background-color: yellow;
            /* Or any other color you prefer */
            transition: background-color 0.3s ease;
        }

        /* Adjust the Select2 container */
        .select2-container .select2-selection--single {
            height: 44px !important;
            padding: 7px !important;
            border-radius: 5px !important;
            border: 1px solid #ccc !important;
        }

        /* Ensure the text inside Select2 aligns properly */
        .select2-container .select2-selection__rendered {
            line-height: 30px !important;
            padding-left: 10px !important;
        }

        /* Style the dropdown arrow */
        .select2-container .select2-selection__arrow {
            height: 100% !important;
        }

        .select2-selection__placeholder {
            color: #999 !important;
            font-style: italic !important;
            display: block !important;
        }

    </style>

    <?php
    function current_page($uri = "/") {
        return strstr(request()->path(), $uri);
    }

    function home_page() {
        if(request()->path()=="/")
            return true;
        else
            return false;
    }
    ?>
    <?php echo $__env->yieldContent('css'); ?>
</head>

<body contenteditable="false">

    <div id="wait"
        style="z-index: 122;display:none;width:69px;height:89px;border:1px solid black;position:absolute;top:50%;left:35%;padding:2px;">
        <img src="<?php echo e(URL::asset('images/demo_wait.gif')); ?>" width="64" height="64" /><br>Loading..</div>

    <div id="app">
        <div class="pos-container-wrapper">
            <div class="content-container">
                <div>
                    <div class="pos-navbar-top">
                        <div class="pos-navbar-top-left" id="pos-navbar-top-left">
                            <div class="pos-navbar-top-heading">
                                <a href="" aria-current="page" class="router-link-exact-active router-link-active"
                                    title="Point Of Sale"></a></div>
                            <div class="pos-navbar-top-search" id="search1">
                                <div class="search-content">
                                    <i class="fa fa-search"></i>

                                    <input autofocus type="text" id="nav-search"
                                        placeholder="Search product by Name, SKU" class="search-field">
                                    <i class="fa fa-barcode"></i>

                                </div>


                            </div>

                            <div class="pos-navbar-top-search" id="search2">
                                <div class="search-content">


                                    <select style="width:100%; height: 44px;
    padding: 7px;
    border-radius: 5px;" class="form-control select2  categoryDrop" onchange="getCategoryData(this.value)">

                                        <option value="" disabled selected></option>

                                        <?php $__currentLoopData = $result->data->categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                        <option value="<?php echo e($c->id); ?>"><?php echo e($c->title); ?></option>



                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>

                                </div>


                            </div>


                            <!-- <div class="pos-nav-top-product"><span title="Add Custom Product" class="custom_img"></span> -->
                        </div>

                        <div class="pos-navbar-top-right">
                            <div title="Reset Pos Data" class="nav-reload">
                                <a href="#!" onclick="location.href = '/';"><i class="fa fa-sync"></i></a>

                            </div>
                            <div class="nav-wifi online_label">

                            </div>
                            <div class="nav-top-user">
                                <div class="user-image">
                                    <img src="<?php echo e(asset('images/boy.png')); ?>" alt="demo webkul" title="demo webkul">
                                </div>
                                <div class="user-details">
                                    <span><?php echo e(Auth::user()->name); ?></span>
                                    <p>Cashier</p>
                                </div>
                                <div class="user-logout">
                                    <div title="Logout User">
                                        <a href="<?php echo e(route('logout')); ?>" class="btn btn-default btn-flat" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            <i class="fa fa-sign-out"></i>
                                        </a>
                                        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST"
                                            style="display: none;">
                                            <?php echo e(csrf_field()); ?>

                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <?php echo $__env->make('layouts.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                <?php echo $__env->yieldContent('content'); ?>

                <div>
                    <div id="confirmOrder" style="display: none;">
                        <div class="pos-modal-overlay"></div>
                        <div class="pos-modal-container">
                            <div class="modal-header">
                                <h4>Confirm</h4> <i class="icon remove-icon" onclick="closeConfirmModal()"></i>
                            </div>
                            <div class="modal-body">
                                <div>
                                    <div class="message-alert .text-default"> Confirm: This process will generate an
                                        order. Do you still wanna do it.? </div>
                                    <div class="pos-action text-right" style="padding-right: 0px;"><button type="button"
                                            class="btn btn-lg btn-pos-dark" style="background: #19A638;"
                                            onclick="createSale()"><i class="fa fa-check-circle"></i> Confirm
                                        </button> <button type="button" class="btn btn-lg btn-pos-default"
                                            onclick="closeConfirmModal()" style="background: #2196F3;">
                                            Cancel
                                        </button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div id="printOrderSuccess" style="display: none;">
                        <div class="pos-modal-overlay"></div>
                        <div class="pos-modal-container">
                            <div class="modal-header">
                                <h4>Print Invoice</h4>
                                <!---->
                            </div>
                            <div class="modal-body">
                                <div>
                                    <div class="message-alert .text-success"> Success: Your Order has been placed
                                        successfully! </div>
                                    <div class="pos-action text-right" style="padding-right: 0px;"><button type="button"
                                            onclick="createSaleWithPrint()" class="btn btn-lg btn-pos-dark"
                                            style="background: #19A638;" id="print-btn"><i class="fa fa-print"></i>
                                            Print
                                        </button> <button type="button" class="btn btn-lg btn-pos-default"
                                            style="background: #2196F3;" onclick="closePrintModal()"><i
                                                class="fa fa-arrow-circle-right"></i> Skip
                                        </button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div id="addDiscountToCart" style="display: none;">
                        <div class="pos-modal-overlay"></div>
                        <div class="pos-modal-container">
                            <div class="modal-header">
                                <h4>Apply Discount To Cart</h4> <i class="icon remove-icon"
                                    onclick="closeDiscountModal()"></i>
                            </div>
                            <div class="modal-body">
                                <div>
                                    <div class="pos-discount-list">
                                        <form autocomplete="off" method="POST">
                                            <div class="page-content">
                                                <div class="form-container">
                                                    <div class="pos-customer-fields">
                                                        <div class="control-group"><label for="discount">Choose
                                                                Discount</label> <select name="discount"
                                                                id="discount-val" class="control" style="width: 90%;">
                                                                <option value="">-- Select Discount --</option>


                                                                <option value="5">50</option>


                                                            </select>
                                                            <!---->
                                                        </div>
                                                        <!---->
                                                        <div class="pos-action text-center"><button type="button"
                                                                text="Apply Discount" class="btn btn-lg btn-pos-primary"
                                                                style="background: #19A638;" onclick="applyDiscount()">
                                                                Apply Discount </button></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div id="addCustomerToCart" style="display: none;">
                        <div class="pos-modal-overlay"></div>
                        <div class="pos-modal-container">
                            <div class="modal-header">
                                <h4>Apply Customer To Cart</h4> <i class="icon remove-icon"
                                    onclick="closeCustomerModal()"></i>
                            </div>
                            <div class="modal-body">
                                <div>
                                    <div class="pos-discount-list">
                                        <form autocomplete="off" method="POST">
                                            <div class="page-content">
                                                <div class="form-container">
                                                    <div class="pos-customer-fields">
                                                        <div class="control-group"><label for="discount">Choose
                                                                Discount</label> <select name="customer"
                                                                id="customer-val" class="control" style="width: 90%;">
                                                                <option value="">-- Select Customer --</option>


                                                                <option value="5">naveed</option>


                                                            </select>
                                                            <!---->
                                                        </div>
                                                        <!---->
                                                        <div class="pos-action text-center"><button type="button"
                                                                text="Apply Discount" class="btn btn-lg btn-pos-primary"
                                                                style="background: #19A638;" onclick="applyCustomer()">
                                                                Select Customer </button></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div>
                    <div id="holdCart" style="display: none">
                        <div class="pos-modal-overlay"></div>
                        <div class="pos-modal-container">
                            <div class="modal-header">
                                <h4>Order Note</h4> <i class="icon remove-icon" onclick="closeHoldModal()"></i>
                            </div>
                            <div class="modal-body">
                                <div>
                                    <div class="pos-hold-note">
                                        <form autocomplete="off" method="POST">
                                            <div class="page-content">
                                                <div class="form-container">
                                                    <div class="pos-customer-fields">
                                                        <div class="control-group"><label for="note"
                                                                class="required">Provide Order Note</label> <textarea
                                                                name="note" id="note"
                                                                placeholder="Enter note for order.." class="control"
                                                                data-vv-id="3" aria-required="true" aria-invalid="false"
                                                                style="width: 100%;"></textarea>
                                                            <!---->
                                                        </div>
                                                        <div class="pos-action text-center"><button type="button"
                                                                text="Hold Order" class="btn btn-lg btn-pos-primary"
                                                                style="background: #19A638;" onclick="holdCart()"> Hold
                                                                Order </button> <button type="button" text="Cancel"
                                                                class="btn btn-lg btn-pos-default"
                                                                style="background: #2196F3;" onclick="closeHoldModal()">
                                                                Cancel </button></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>




                <input type="hidden" name="discount-id" id="discount-id">

                <input type="hidden" name="customer-id" id="customer-id">


                <script src="<?php echo e(asset('js/jquery-2.2.3.min.js')); ?>"></script>

                <script type="text/javascript"
                    src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
                <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js">
                </script>

                <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

                <script>
                    toastr.options.progressBar = true;
                    toastr.options.timeOut = '1000';

                </script>

                <script type="text/javascript" src="<?php echo e(asset('js/pos.js?v=7.1')); ?>"></script>

                <script>
                    config = {
                        routes: {
                            getBrandData: "<?php echo e(route('pos.brand.data')); ?>",
                            getCategoryData: "<?php echo e(route('pos.category.data')); ?>",
                            getSearchData: "<?php echo e(route('pos.search.data')); ?>",
                            getCustomerSearchData: "<?php echo e(route('customer.search.data')); ?>",
                            getOrderSearchData: "<?php echo e(route('order.search.data')); ?>",
                            getSpecificCustomerData: "<?php echo e(route('customer.specific.data')); ?>",
                            getCartData: "<?php echo e(route('pos.cart.data')); ?>",
                            getHoldList: "<?php echo e(route('sale.hold.list')); ?>",
                            updatePayment: "<?php echo e(route('pos.update.payment')); ?>",
                            mannualReturnForm: "<?php echo e(route('pos.mannual.return.form')); ?>",
                            createSale: "<?php echo e(route('pos.create.sale')); ?>",
                            createReturn: "<?php echo e(route('pos.create.return')); ?>",
                            orderInfo: "<?php echo e(route('sales.order.info')); ?>",
                            searchOrder: "<?php echo e(route('sales.search.order')); ?>",
                            completeReturnOrder: "<?php echo e(route('sales.complete.return.order')); ?>",
                            partiallyReturnOrder: "<?php echo e(route('sales.partially.return.order')); ?>",
                            customReport: "<?php echo e(route('report.custom')); ?>",
                            productDetail: "<?php echo e(route('product.detail')); ?>",
                            productVariantDetail: "<?php echo e(route('product.variant.detail')); ?>",
                            orderDetail: "<?php echo e(route('order.detail')); ?>"
                        }
                    };

                    //  clearCart(1);

                </script>
                <script>
                    $('.categoryDrop').select2({
                        width: '100%',
                        placeholder: "Select a category"
                    });

                </script>


                <?php echo $__env->yieldContent('js'); ?>

</body>

</html>
<?php /**PATH C:\xampp\htdocs\EjazSportsCode\ejazsports\pos\resources\views/layouts/app.blade.php ENDPATH**/ ?>