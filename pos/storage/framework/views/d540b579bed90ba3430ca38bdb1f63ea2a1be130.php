<div class="order-info">
    <?php if($result && $result->data && $result->data->order): ?>

    <div class="pos-order-info " style="width: 100%;height: auto">
        <div class="order-info">
            <div class="order_row"><label class="row_title">Order ID</label>
                <div class="row_value order_id"> #<?php echo e($result->data->order->order_no); ?> </div>
                <!---->
            </div>
            <div class="order_row"><label class="row_title">Order Date</label>
                <div class="row_value"><i class="fa fa-calendar"></i>
                    <?php echo e(date('M d, Y h:i A',strtotime($result->data->order->created_at))); ?>


                </div>
            </div>
            <div class="order_row"><label class="row_title">Customer Detail</label>
                <div class="row_value"><span class="cust_name"><?php echo e($result->data->order->name); ?></span>
                    <!----> <span class="cust_email"><i class="fa fa-phone"></i>
                        <?php echo e($result->data->order->phone_number); ?>

                    </span>
                </div>
            </div>
            <!---->
        </div>

        <div class="order-info">
            <div class="order_row">
                <label class="row_title">Order Summary</label>
                <div class="row_value">
                    <?php $__currentLoopData = $result->data->order->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        // Always skip the bundle parent container! We only want to show actual products / bundle items
                        if (isset($p->is_bundle) && $p->is_bundle == 1) continue;
                    ?>
                    <?php if($p->returned): ?>
                    <div style="background-color: #ffbebe;">
                        <?php else: ?>
                        <div>
                            <input type="checkbox" name="product_ids" value="<?php echo e($p->id); ?>">
                            <input type="text" class="form-control configuration-icon" id="product_qty<?php echo e($p->id); ?>"
                                value="0">
                            <?php endif; ?>
                            <div class="product_info"><span class="product_name">
                                    <?php echo e($p->product->title ?? 'Product'); ?> - <?php echo e($p->variant? $p->variant->shade : ''); ?> - <?php echo e($p->variant ? $p->variant->size : ''); ?>

                                    <span class="product-attributes">
                                    </span></span> <span class="product_unit">
                                    <?php echo e($p->qty); ?>

                                    Unit(s)

                                </span>
                            </div>
                            <div class="price_info"><span class="product_price">
                                    Rs. <?php echo e(number_format($p->price * $p->qty)); ?>

                                </span>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <div>
                    <div class="order_row">
                        <div class="total_row_value">
                            <div class="total_text">Sub Total</div>
                            <div class="total_value">
                                Rs. <?php echo e(number_format($result->data->order->total_amount)); ?>

                            </div>
                        </div>
                    </div>
                    <div class="order_row">
                        <div class="total_row_value">
                            <div class="total_text">
                                Discount (-)
                            </div>
                            <div class="total_value">
                                Rs. <?php echo e($result->data->order->discount_amount); ?>

                            </div>
                        </div>
                    </div>
                    <div class="order_row" style="border-bottom: none; margin: 0px; padding-bottom: 0px;">
                        <div class="total_row_value">
                            <div class="total_text"><b>Total</b></div>
                            <div class="total_value order_id">
                                Rs. <?php echo e(number_format($result->data->order->total_amount)); ?>

                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <select class="form-control" name="return_type" id="return_type">
                        <option value="1">Adjust in Legder</option>
                        <option value="2">Cash Return</option>
                    </select>
                </div>
                <div class="pos-action text-left" style="padding-right: 0px;">
                    <a target="_blank" class="btn btn-lg btn-pos-primary" style="color:white" id="print-btn"
                        onclick="completeReturnOrder(<?php echo e($result->data->order->id); ?>)"> Fully Return
                    </a>
                    <a target="_blank" style="color:white" class="btn btn-lg btn-pos-primary"
                        onclick="partiallyReturnOrder(<?php echo e($result->data->order->id); ?>,<?php echo e(count($result->data->order->products)); ?>)">
                        Partially Return
                    </a>
                </div>
                <?php else: ?>
                <div class="order_row"><label class="row_title">No Order found...</label></div>
                <?php endif; ?>
            </div>
        </div>
<?php /**PATH C:\xampp\htdocs\EjazSportsCode\ejazsports\pos\resources\views/sale/update-search-order.blade.php ENDPATH**/ ?>