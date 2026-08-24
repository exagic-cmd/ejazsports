<?php $__env->startSection('content'); ?>
<div class="pos-content-container" id="pos-content-container">
    <div class="pos-sales-main">
        <div class="pos-nav-container">
            <ul class="pos-nav-lists">
                <li label="menu_count_0" class="pos-nav"><a href="#!" aria-current="page"
                        class="nav-link router-link-exact-active router-link-active">
                        Return Orders
                    </a>
                </li>
                <div class="add_customer" style="padding-left: 100px;padding-top: 10px;font-size: 18px"><a href="#!"
                        onclick="newOrderDiv()">
                        <div class="customer-add-text" style="color: #511c29;">
                            <i class="fa fa-plus"></i> Add New
                        </div>
                    </a>
                </div>
            </ul>
        </div>
        <div class="pos-nav-content" style="height: 700px;">
            <div class="sale-history-panel">
                <div class="order-search-main" style="height: 680px;">
                    <div class="pos-order-list">
                        <div class="order_search">Total Orders : <?php echo e($result->data->totalCount); ?>

                            (<?php echo e(number_format($result->data->totalAmount)); ?>)</div>
                        <ul class="order_list" style="height: 650px;">
                            <?php $__currentLoopData = $result->data->orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($loop->first): ?>
                            <li class="record active" data-order-id="<?php echo e($o->id); ?>">
                                <div class="order_id">
                                    #<?php echo e($o->order_no); ?>

                                </div>
                                <div class="order_date">
                                    <?php echo e(date('M d, Y',strtotime($o->created_at))); ?>

                                    <br>
                                    <?php echo e(date('h:i A',strtotime($o->created_at))); ?>

                                </div>
                                <div class="order_total">
                                    Rs. <?php echo e(number_format($o->total_amount)); ?>

                                </div>
                            </li>
                            <?php else: ?>
                            <li class="record" data-order-id="<?php echo e($o->id); ?>">
                                <div class="order_id">
                                    #<?php echo e($o->order_no); ?>

                                </div>
                                <div class="order_date">
                                    <?php echo e(date('M d, Y',strtotime($o->created_at))); ?>

                                    <br>
                                    <?php echo e(date('h:i A',strtotime($o->created_at))); ?>

                                </div>
                                <div class="order_total">
                                    Rs. <?php echo e(number_format($o->total_amount)); ?>

                                </div>
                            </li>
                            <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                </div>
                <div class="pos-order-view" id="pos-order-view">
                    <?php if($result->data->latestOrder): ?>
                    <div class="pos-order-info">
                        <div class="order-info">
                            <div class="order_row"><label class="row_title">Order ID</label>
                                <div class="row_value order_id"> #<?php echo e($result->data->latestOrder->order_no); ?>

                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo e($result->data->status); ?>

                                </div>
                                <!---->
                            </div>
                            <div class="order_row"><label class="row_title">Order Date</label>
                                <div class="row_value"><i class="fa fa-calendar"></i>
                                    <?php echo e(date('M d, Y h:i A',strtotime($result->data->latestOrder->created_at))); ?>


                                </div>
                            </div>
                            <div class="order_row"><label class="row_title">Customer Detail</label>
                                <div class="row_value"><span
                                        class="cust_name"><?php echo e($result->data->latestOrder->name); ?></span>
                                    <!----> <span class="cust_email"><i class="fa fa-phone"></i>
                                        <?php echo e($result->data->latestOrder->phone_number); ?>

                                    </span>
                                </div>
                            </div>
                            <!---->
                            <div class="pos-action text-left" style="padding-right: 0px;">
                                <a target="_blank" style="color:white" class="btn btn-lg btn-pos-primary" id="print-btn"
                                    href="<?php echo e(route('pos.order.print',['id'=>$result->data->latestOrder->id])); ?>"><i
                                        class="fa fa-print"></i> Print Invoice
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="pos-order-totals" style="height:750px">
                        <div class="order-info">
                            <div class="order_row"><label class="row_title">Order Summary</label>
                                <div class="row_value">
                                    <?php
                                        $totalReturnQty = 0;
                                        $total_qty = $result->data->latestOrder->total_quantity;
                                        $sale_total = 0;
                                        $return_total = 0;

                                        // Calculate correct totals using actual products (skip bundle parents)
                                        foreach($result->data->latestOrder->products as $p) {
                                            if (isset($p->is_bundle) && $p->is_bundle == 1) {
                                                continue;
                                            }
                                            $totalReturnQty += $p->return_qty;
                                            // Sale total should be full original quantity, not minus returns
                                            $sale_total += $p->price * $p->qty;
                                            $return_total += $p->price * $p->return_qty;
                                        }
                                    ?>

                                    <?php $__currentLoopData = $result->data->latestOrder->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        
                                        <?php if(isset($p->is_bundle) && $p->is_bundle): ?>
                                            <?php continue; ?>
                                        <?php endif; ?>

                                        <?php
                                            $item_sold_qty = ($p->qty - $p->return_qty);
                                            $item_sold_total = $p->price * $item_sold_qty;
                                            $item_return_total = $p->price * $p->return_qty;
                                        ?>

                                        <?php if($p->returned): ?>
                                            
                                            <div style="background-color: #ffbebe;">
                                        <?php else: ?>
                                            <div>
                                        <?php endif; ?>
                                            <div class="product_info"><span class="product_name">
                                                <?php echo e($p->product->title ?? 'Product'); ?>

                                                <?php if(isset($p->variant) && $p->variant): ?>
                                                    - <?php echo e($p->variant->shade ?? ''); ?> - <?php echo e($p->variant->size ?? ''); ?>

                                                <?php endif; ?>
                                                <span class="product-attributes"></span></span>
                                                <span class="product_unit">
                                                    <?php if($item_sold_qty > 0): ?>
                                                        <?php echo e($item_sold_qty); ?> Sold
                                                    <?php endif; ?>
                                                    <?php if($p->return_qty > 0): ?>
                                                        <?php echo e($p->return_qty); ?> Return Unit(s)
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                            <div class="price_info"><span class="product_price">
                                                <?php if($p->returned): ?>
                                                    <div style="color:#a00; font-size:0.95em;">Rs. <?php echo e(number_format($p->price * $p->qty)); ?> (Returned)</div>
                                                <?php else: ?>
                                                    Rs. <?php echo e(number_format($item_sold_total)); ?>

                                                    <?php if($item_return_total > 0): ?>
                                                        <div style="color:#a00; font-size:0.9em;">- Rs. <?php echo e(number_format($item_return_total)); ?></div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                </div>
                                <div>
                                    <div class="order_row">
                                        <div class="total_row_value">
                                            <div class="total_text">Sale Amount</div>
                                            <div class="total_value">
                                                Rs. <?php echo e(number_format($sale_total)); ?>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="order_row">
                                        <div class="total_row_value">
                                            <div class="total_text">Return Amount</div>
                                            <div class="total_value">
                                                - Rs. <?php echo e(number_format($return_total)); ?>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="order_row">
                                        <div class="total_row_value">
                                            <div class="total_text">Discount (-)</div>
                                            <div class="total_value">
                                                Rs. <?php echo e(number_format($result->data->latestOrder->discount_amount ?? 0)); ?>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="order_row" style="border-bottom: none; margin: 0px; padding-bottom: 0px;">
                                        <div class="total_row_value">
                                            <div class="total_text"><b>Total</b></div>
                                            <div class="total_value order_id">
                                                Rs. <?php echo e(number_format(max(0, $sale_total - $return_total - ($result->data->latestOrder->discount_amount ?? 0)))); ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="pos-order-view" id="pos-new-order-view" style="display: none;">
                        <div class="pos-order-info">
                            <div class="order-info">
                                <div class="order_row">
                                    <label class="row_title">Order ID</label>
                                    <div class="row_value order_id">
                                        <input type="text" class="form-field" name="order_no" id="order_no">
                                    </div>
                                </div>
                                <div class="pos-action text-left" style="padding-right: 0px;">
                                    <a target="_blank" class="btn btn-lg btn-pos-primary" style="color:white"
                                        id="print-btn" onclick="searchOrder()">
                                        <i class="fa fa-search"></i> Search
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="pos-order-totals" id="pos-order-totals" style="height: 620px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>

    <script>
        function newOrderDiv() {
            document.getElementById('app').style.opacity = '0.1';
            document.getElementById('pos-order-view').style.display = 'none';
            document.getElementById('pos-new-order-view').style.display = 'inline-flex';
            document.getElementById('app').style.opacity = '1';
        }
        document.getElementById('search1').style.display = 'none';
        document.getElementById('search2').style.display = 'none';
        document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.record').forEach(element => {
            // Remove existing listeners to prevent duplicates
            element.removeEventListener('click', orderInfo);
            // Add new click event listener
            element.addEventListener('click', orderInfo);
        });
    });
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\EjazSportsCode\ejazsports\pos\resources\views/sale/return-orders.blade.php ENDPATH**/ ?>