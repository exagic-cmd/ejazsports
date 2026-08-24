<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>POS Receipt Template Html Css</title>
    <style type="text/css">
        @page  {
            margin: 0mm 0mm 20mm 0mm;
        }

        .bundle-item {
            padding-left: 20px;
            font-size: 0.9em;
            color: #555;
        }

        .bundle-title {
            font-weight: bold;
            background-color: #f5f5f5;
        }

        .section-title {
            font-weight: bold;
            font-size: 1.1em;
            text-align: center;
            margin: 10px 0;
        }

        .return-row {
            background-color: #ffe6e6;
        }

        body {
            font-family: Sans-Serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .tabletitle td {
            padding: 5px;
            border-bottom: 1px solid #ddd;
        }

        .service td {
            padding: 5px;
            border-bottom: 1px solid #eee;
        }

        .tableitem p {
            margin: 2px 0;
        }
    </style>
</head>

<body>

    <div id="invoice-POS">
        <div id="top" style="text-align:center;">
            <h1><?php echo e(($result->data->order->return_type == 2 || $result->data->order->status == 6) ? 'Return Invoice' : 'Estimate'); ?>

            </h1>
        </div>
        <div id="mid" style="text-align:center;min-height:0px;">
            <div class="info">
                <div>
                    <p>
                        <b>
                            <?php if($result->data->order->status == 6): ?>
                                Return Invoice #
                            <?php else: ?>
                                EST #
                            <?php endif; ?>
                        </b><?php echo e($result->data->order->order_no); ?>

                    </p>
                </div>
                <div>
                    <p><?php echo e(date('d/m/Y h:i:s A', strtotime($result->data->order->created_at))); ?></p>
                </div>
            </div>
        </div>
        <?php $total_order_paid_amount = $result->data->order->paid_amount;
$total_order_amount = $result->data->order->total_amount;
            ?>
        <div id="mid" style="min-height:0px;">
            <div class="info">
                <div>
                    <p><b>Customer Name:
                        </b><?php echo e(isset($result->data->order->name) && trim($result->data->order->name) !== 'Retail' ? $result->data->order->name : ''); ?>

                    </p>
                </div>
                <div>
                    <p><b>Employee Name: </b><?php echo e($result->data->order->employee->name); ?></p>
                </div>
            </div>
        </div>
        <div id="bot">
            <div id="table">
                <table>
                    <tr class="tabletitle">
                        <td class="Rate" style="width:2px;">
                            <h2>#</h2>
                        </td>
                        <td class="item" style="width:40px;">
                            <h2>Description</h2>
                        </td>
                        <td class="Rate" style="width:15px;">
                            <h2>Price</h2>
                        </td>
                        <td class="Hours" style="width:15px;">
                            <h2>Qty</h2>
                        </td>
                        <td class="Rate" style="width:25px;">
                            <h2>Total</h2>
                        </td>
                    </tr>

                    <?php
$total = 0;
$sr = 1;
$total_quantity = 0;
$total_amount = 0;
$bundle_groups = [];
$unique_items = 0;
$return_total_amount = 0;
$return_total_quantity = 0;
$has_sale = false;
$has_return = false;
$is_manual_return = ($result->data->order->return_type == 2);
$is_return_order = ($result->data->order->status == 6);

// Group bundle rows by their exact parent row. Older orders may not have parent_id,
// so keep children with the nearest preceding bundle parent for the same bundle.
$current_bundle_key = null;
foreach ($result->data->order->products as $p) {
    if ($p->bundle_id) {
        if (isset($p->is_bundle) && $p->is_bundle == 1 && (!isset($p->is_bundle_item) || $p->is_bundle_item != 1)) {
            $current_bundle_key = 'bundle_parent_' . $p->id;
            $bundle_groups[$current_bundle_key] = [
                'bundle_id' => $p->bundle_id,
                'products' => [$p],
            ];
        } else {
            $bundle_key = null;
            if (!empty($p->parent_id)) {
                $bundle_key = 'bundle_parent_' . $p->parent_id;
            } elseif ($current_bundle_key && isset($bundle_groups[$current_bundle_key]) && $bundle_groups[$current_bundle_key]['bundle_id'] == $p->bundle_id) {
                $bundle_key = $current_bundle_key;
            } else {
                $bundle_key = 'bundle_' . $p->bundle_id;
            }

            if (!isset($bundle_groups[$bundle_key])) {
                $bundle_groups[$bundle_key] = [
                    'bundle_id' => $p->bundle_id,
                    'products' => [],
                ];
            }
            $bundle_groups[$bundle_key]['products'][] = $p;
        }
    } else {
        $bundle_groups['no_bundle']['products'][] = $p;
        $unique_items++;
    }

    if (isset($p->is_bundle_item) && $p->is_bundle_item == 1) {
        if ($p->returned == 1 && $p->return_qty > 0)
            $has_return = true;
    } else if (isset($p->is_bundle) && $p->is_bundle == 1) {
        if ($p->qty > 0)
            $has_sale = true;
    } else {
        if ($p->qty > 0)
            $has_sale = true;
        if ($p->returned == 1 && $p->return_qty > 0)
            $has_return = true;
    }
}
                    ?>
                    <!-- Sale Products Section (show full original quantities, hidden for manual returns and pure return orders) -->
                    <!-- Debug: is_manual_return=<?php echo e($is_manual_return); ?>, is_return_order=<?php echo e($is_return_order); ?>, has_return=<?php echo e($has_return); ?>, has_sale=<?php echo e($has_sale); ?> -->
                    <?php if($has_sale && !$is_manual_return): ?>
                        <tr>
                            <td colspan="5" class="section-title">Sale Items</td>
                        </tr>
                        <?php $__currentLoopData = $bundle_groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bundle_key => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $products = $group['products']; ?>
                            <?php if($bundle_key !== 'no_bundle'): ?>
                                <?php
                                    // Get bundle parent (is_bundle = 1, is_bundle_item = 0)
                                    $bundle_parent = collect($products)->firstWhere('is_bundle', 1);
                                    if (!$bundle_parent)
                                        continue;

                                    // Bundle name from the eager-loaded relationship (comes from admin API)
                                    $bundle_name = isset($bundle_parent->bundle->name) ? $bundle_parent->bundle->name : 'Bundle #' . $group['bundle_id'];

                                    // Main bundle values (show full original sale quantity)
                                    $bundle_price_per_unit = $bundle_parent->price;
                                    $bundle_qty = $bundle_parent->qty;
                                    // If bundle has no quantity, skip showing it in Sale section
                                    if ($bundle_qty <= 0) {
                                        continue;
                                    }
                                    $bundle_total = $bundle_price_per_unit * $bundle_qty;

                                    // Child items calculation (do not list children separately - use full original quantities)
                                    $child_items = collect($products)->where('is_bundle_item', 1);
                                    // Child qty stores the total component quantity for this bundle parent.
                                    $child_qty_sum = 0;
                                    foreach ($child_items as $ci) {
                                        $child_qty_sum += $ci->qty;
                                    }
                                    $child_qty_sum = (int) round($child_qty_sum);
                                    $child_price = optional($child_items->first())->price ?? 0;
                                    $child_total = $child_qty_sum * $child_price;

                                    // Accumulate totals (bundle treated as single unit)
                                    $total_amount += $bundle_total;
                                    $total_quantity += $bundle_qty;
                                    $total += $bundle_total;
                                    $unique_items++;
                                ?>
                                <!-- Bundle Main Row -->
                                <tr class="service bundle-title">
                                    <td class="tableitem">
                                        <p><?php echo e($sr++); ?></p>
                                    </td>
                                    <td class="tableitem">
                                        <p class="itemtext"><strong><?php echo e($bundle_name); ?></strong>
                                            <?php
                                                $bundle_short_desc = (isset($bundle_parent) && isset($bundle_parent->bundle) && !empty($bundle_parent->bundle->short_desc)) ? $bundle_parent->bundle->short_desc : '';
                                            ?>
                                            <?php if(!empty($bundle_short_desc)): ?>
                                                <br><small><?php echo e($bundle_short_desc); ?></small>
                                            <?php endif; ?>
                                        </p>
                                    </td>
                                    <td class="tableitem">
                                        <p class="itemtext"><?php echo e(number_format($bundle_price_per_unit, 2)); ?></p>
                                    </td>
                                    <td class="tableitem">
                                        <p class="itemtext"><b><?php echo e($bundle_qty); ?></b></p>
                                    </td>
                                    <td class="tableitem">
                                        <p class="itemtext"><?php echo e(number_format($bundle_total, 2)); ?></p>
                                    </td>
                                </tr>

                                <!-- Child Summary Row (compact, no individual child lines) -->
                                <?php if($child_qty_sum > 0): ?>
                                    <tr class="service" style="border-bottom:2px dotted;">
                                        <td class="tableitem"></td>
                                        <td class="tableitem">
                                            <p><small><?php echo e($child_qty_sum); ?> × <?php echo e(number_format($child_price, 2)); ?></small></p>
                                        </td>
                                        <td class="tableitem"></td>
                                        <td class="tableitem"></td>
                                        <td class="tableitem"></td>
                                    </tr>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(isset($p->is_bundle_item) && $p->is_bundle_item == 1): ?> <?php continue; ?> <?php endif; ?>
                                    <?php
                                        $item_qty = $p->qty;
                                    ?>
                                    <?php if($item_qty <= 0): ?> <?php continue; ?> <?php endif; ?>
                                    <?php
                                        $item_total = $p->price * $item_qty;
                                        $total_amount += $item_total;
                                        $total_quantity += $item_qty;
                                        $total += $item_total;
                                    ?>
                                    <tr class="service">
                                        <td class="tableitem">
                                            <p><?php echo e($sr++); ?></p>
                                        </td>
                                        <td class="tableitem">
                                            <p class="itemtext"><?php echo e($p->product->title ?? 'Product'); ?>

                                                <?php if($p->variant): ?>
                                                    <br> (<?php echo e($p->variant->shade ?? ''); ?> - <?php echo e($p->variant->size ?? ''); ?>)
                                                <?php endif; ?>
                                            </p>
                                        </td>
                                        <td class="tableitem">
                                            <p class="itemtext"><?php echo e(number_format($p->price, 2)); ?></p>
                                        </td>
                                        <td class="tableitem">
                                            <p class="itemtext"><b><?php echo e($item_qty); ?></b></p>
                                        </td>
                                        <td class="tableitem">
                                            <p class="itemtext"><?php echo e(number_format($item_total, 2)); ?></p>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <tr style="border-bottom: 2px dotted;">
                            <td></td>
                            <td class="Rate">
                                <h2>Sale Amount</h2>
                            </td>
                            <td class="payment" colspan="3">
                                <h2 style="font-size:12px">Rs.<?php echo e(number_format($total_amount)); ?></h2>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <!-- Return Items Section -->
                    <?php if($has_return && ($result->data->order->return_type == 1 || $result->data->order->return_type == 2)): ?>
                        <tr>
                            <td colspan="5" class="section-title">Return Items</td>
                        </tr>
                        <?php    $sn = 1; ?>
                        <?php
                            // Calculate total return sum (skip bundle parents)
                            foreach ($result->data->order->products as $p) {
                                if (isset($p->is_bundle) && $p->is_bundle == 1)
                                    continue;
                                if ($p->returned == 1 && $p->return_qty > 0) {
                                    $return_total_amount += ($p->price * $p->return_qty);
                                    $return_total_quantity += $p->return_qty;
                                }
                            }
                        ?>

                        <?php $__currentLoopData = $result->data->order->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                // Skip bundle parents in return listing; only show actual products/child items returned
                                if (isset($p->is_bundle) && $p->is_bundle == 1)
                                    continue;
                            ?>
                            <?php if($p->returned == 1 && $p->return_qty > 0): ?>
                                        <?php
                                $item_qty = $p->return_qty;
                                $return_item_total = $p->price * $item_qty;
                                                                                                                            ?>
                                        <tr class="service return-row">
                                            <td class="tableitem">
                                                <p><?php echo e($sn++); ?></p>
                                            </td>
                                            <td class="tableitem">
                                                <p class="itemtext">
                                                    <?php echo e($p->product->title ?? 'Product'); ?>

                                                    <?php if($p->variant): ?>
                                                        <br> (<?php echo e($p->variant->shade ?? ''); ?> - <?php echo e($p->variant->size ?? ''); ?>)
                                                    <?php endif; ?>
                                                </p>
                                            </td>
                                            <td class="tableitem">
                                                <p class="itemtext">
                                                    <?php echo e(number_format($p->price, 2)); ?>

                                                </p>
                                            </td>
                                            <td class="tableitem">
                                                <p class="itemtext"><b><?php echo e($item_qty); ?></b></p>
                                            </td>
                                            <td class="tableitem">
                                                <p class="itemtext">
                                                    <?php echo e(number_format($return_item_total, 2)); ?>

                                                </p>
                                            </td>
                                        </tr>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <tr style="border-bottom:2px dotted;">
                            <td></td>
                            <td class="Rate">
                                <h2>Return Amount</h2>
                            </td>
                            <td class="payment" colspan="3">
                                <h2 style="font-size:12px">- Rs.<?php echo e(number_format($return_total_amount)); ?></h2>
                            </td>
                        </tr>
                    <?php endif; ?>

                    
                    <?php
                        $discount_amount = $result->data->order->discount_amount ?? 0;
                        // Pure return: manual return only (since we now show sales on return orders)
                        $is_pure_return = $is_manual_return;
                        // For pure returns, net amount is just the return amount
                        if ($is_pure_return) {
                            $net_order_amount = -$return_total_amount;
                        } else {
                            $net_order_amount = max(0, $total_amount - $return_total_amount - $discount_amount);
                        }

                        // Fix previousBalance: API subtracts current order's return_amount, add it back
                        $correct_previous_balance = $result->data->previousBalance;
                        if ($result->data->order->return_type == 1) {
                            $correct_previous_balance += ($result->data->order->return_amount ?? 0);
                        }

                        // Fix totalRemaining: API doesn't subtract current order's paid_amount
                        $correct_remaining = $result->data->totalRemaining - ($result->data->order->paid_amount ?? 0);
                    ?>

                    
                    <?php if($discount_amount > 0 && !$is_pure_return): ?>
                        <tr style="border-bottom:2px dotted;">
                            <td></td>
                            <td class="Rate">
                                <h2>Discount</h2>
                            </td>
                            <td class="payment" colspan="3">
                                <h2>Rs.<?php echo e(number_format($discount_amount)); ?></h2>
                            </td>
                        </tr>
                    <?php endif; ?>

                    
                    <tr style="border-bottom:2px dotted;">
                        <td></td>
                        <td class="Rate">
                            <h2><?php echo e($is_pure_return ? 'Total Amount' : 'Total Amount'); ?></h2>
                        </td>
                        <td class="payment" colspan="3">
                            <h2>Rs.<?php echo e(number_format($net_order_amount)); ?></h2>
                        </td>
                    </tr>

                    <?php if($result->data->order->customer_id != 1): ?>
                        
                        <tr style="border-bottom:2px dotted;">
                            <td></td>
                            <td class="Rate">
                                <h2>Previous Balance</h2>
                            </td>
                            <td class="payment" colspan="3">
                                <h2>Rs.<?php echo e(number_format($correct_previous_balance)); ?></h2>
                            </td>
                        </tr>

                        
                        <tr style="border-bottom:2px dotted;">
                            <td></td>
                            <td class="Rate">
                                <h2>Total Payable Amount</h2>
                            </td>
                            <td class="payment" colspan="3">
                                <h2 style="font-size:16px">
                                    Rs.<?php echo e(number_format($net_order_amount + $correct_previous_balance)); ?>

                                </h2>
                            </td>
                        </tr>

                        
                        <tr style="border-bottom:2px dotted;">
                            <td></td>
                            <td class="Rate">
                                <h2>Paid Amount</h2>
                            </td>
                            <td class="payment" colspan="3">
                                <h2>Rs.<?php echo e(number_format($result->data->order->paid_amount)); ?></h2>
                            </td>
                        </tr>

                        
                        <tr style="border-bottom:2px dotted;">
                            <td></td>
                            <td class="Rate">
                                <h2>Balance</h2>
                            </td>
                            <td class="payment" colspan="3">
                                <h2>Rs.<?php echo e(number_format($correct_remaining)); ?></h2>
                            </td>
                        </tr>


                        
                    <?php endif; ?>
                </table>
            </div>
            <p style="text-align:center">Thanks for shopping with us.</p>
            <div style="margin-top:150px;font-size:6px">.</div>
        </div>
    </div>

    <script>
        window.setTimeout('print1()', 1000);
        function print1() {
            window.print();
            window.setTimeout("window.location.href='/'", 1000);
        }
    </script>
</body>

</html>
<?php /**PATH C:\xampp\htdocs\EjazSportsCode\ejazsports\pos\resources\views/pos/print.blade.php ENDPATH**/ ?>