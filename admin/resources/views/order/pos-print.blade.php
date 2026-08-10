<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>POS Receipt Template Html Css</title>
    <style type="text/css">
        @page {
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
            <h1>{{ ($result->data->order->return_type == 2 || $result->data->order->status == 6) ? 'Return Invoice' : 'Estimate' }}
            </h1>
        </div>
        <div id="mid" style="text-align:center;min-height:0px;">
            <div class="info">
                <div>
                    <p>
                        <b>
                            @if ($result->data->order->status == 6)
                                Return Invoice #
                            @else
                                EST #
                            @endif
                        </b>{{ $result->data->order->order_no }}
                    </p>
                </div>
                <div>
                    <p>{{ date('d/m/Y h:i:s A', strtotime($result->data->order->created_at)) }}</p>
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
                        </b>{{ isset($result->data->order->name) && trim($result->data->order->name) !== 'Retail' ? $result->data->order->name : '' }}
                    </p>
                </div>
                <div>
                    <p><b>Employee Name: </b>{{ optional($result->data->order->employee)->name ?? 'N/A' }}</p>
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
                    <!-- Debug: is_manual_return={{$is_manual_return}}, is_return_order={{$is_return_order}}, has_return={{$has_return}}, has_sale={{$has_sale}} -->
                    @if ($has_sale && !$is_manual_return)
                        <tr>
                            <td colspan="5" class="section-title">Sale Items</td>
                        </tr>
                        @foreach ($bundle_groups as $bundle_key => $group)
                            @php $products = $group['products']; @endphp
                            @if ($bundle_key !== 'no_bundle')
                                @php
                                    // Get bundle parent (is_bundle = 1, is_bundle_item = 0)
                                    $bundle_parent = collect($products)->firstWhere('is_bundle', 1);
                                    if (!$bundle_parent)
                                        continue;

                                    // Bundle name from the eager-loaded relationship (comes from admin API)
                                    $bundle_name = optional($bundle_parent->bundle)->name ?? 'Bundle #' . $group['bundle_id'];

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
                                @endphp
                                <!-- Bundle Main Row -->
                                <tr class="service bundle-title">
                                    <td class="tableitem">
                                        <p>{{ $sr++ }}</p>
                                    </td>
                                    <td class="tableitem">
                                        <p class="itemtext"><strong>{{ $bundle_name }}</strong>
                                            @php
                                                $bundle_short_desc = (isset($bundle_parent) && isset($bundle_parent->bundle) && !empty($bundle_parent->bundle->short_desc)) ? $bundle_parent->bundle->short_desc : '';
                                            @endphp
                                            @if (!empty($bundle_short_desc))
                                                <br><small>{{ $bundle_short_desc }}</small>
                                            @endif
                                        </p>
                                    </td>
                                    <td class="tableitem">
                                        <p class="itemtext">{{ number_format($bundle_price_per_unit, 2) }}</p>
                                    </td>
                                    <td class="tableitem">
                                        <p class="itemtext"><b>{{ $bundle_qty }}</b></p>
                                    </td>
                                    <td class="tableitem">
                                        <p class="itemtext">{{ number_format($bundle_total, 2) }}</p>
                                    </td>
                                </tr>

                                <!-- Child Summary Row (compact, no individual child lines) -->
                                @if($child_qty_sum > 0)
                                    <tr class="service" style="border-bottom:2px dotted;">
                                        <td class="tableitem"></td>
                                        <td class="tableitem">
                                            <p><small>{{ $child_qty_sum }} × {{ number_format($child_price, 2) }}</small></p>
                                        </td>
                                        <td class="tableitem"></td>
                                        <td class="tableitem"></td>
                                        <td class="tableitem"></td>
                                    </tr>
                                @endif
                            @else
                                @foreach ($products as $p)
                                    @if (isset($p->is_bundle_item) && $p->is_bundle_item == 1) @continue @endif
                                    @php
                                        $item_qty = $p->qty;
                                    @endphp
                                    @if ($item_qty <= 0) @continue @endif
                                    @php
                                        $item_total = $p->price * $item_qty;
                                        $total_amount += $item_total;
                                        $total_quantity += $item_qty;
                                        $total += $item_total;
                                    @endphp
                                    <tr class="service">
                                        <td class="tableitem">
                                            <p>{{ $sr++ }}</p>
                                        </td>
                                        <td class="tableitem">
                                            <p class="itemtext">{{ $p->product->title ?? 'Product' }}
                                                @if ($p->variant)
                                                    <br> ({{ $p->variant->shade ?? '' }} - {{ $p->variant->size ?? '' }})
                                                @endif
                                            </p>
                                        </td>
                                        <td class="tableitem">
                                            <p class="itemtext">{{ number_format($p->price, 2) }}</p>
                                        </td>
                                        <td class="tableitem">
                                            <p class="itemtext"><b>{{ $item_qty }}</b></p>
                                        </td>
                                        <td class="tableitem">
                                            <p class="itemtext">{{ number_format($item_total, 2) }}</p>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                        <tr style="border-bottom: 2px dotted;">
                            <td></td>
                            <td class="Rate">
                                <h2>Sale Amount</h2>
                            </td>
                            <td class="payment" colspan="3">
                                <h2 style="font-size:12px">Rs.{{ number_format($total_amount) }}</h2>
                            </td>
                        </tr>
                    @endif

                    <!-- Return Items Section -->
                    @if ($has_return && ($result->data->order->return_type == 1 || $result->data->order->return_type == 2))
                        <tr>
                            <td colspan="5" class="section-title">Return Items</td>
                        </tr>
                        <?php    $sn = 1; ?>
                        @php
                            // Calculate total return sum (skip bundle parents)
                            foreach ($result->data->order->products as $p) {
                                if (isset($p->is_bundle) && $p->is_bundle == 1)
                                    continue;
                                if ($p->returned == 1 && $p->return_qty > 0) {
                                    $return_total_amount += ($p->price * $p->return_qty);
                                    $return_total_quantity += $p->return_qty;
                                }
                            }
                        @endphp

                        @foreach ($result->data->order->products as $p)
                            @php
                                // Skip bundle parents in return listing; only show actual products/child items returned
                                if (isset($p->is_bundle) && $p->is_bundle == 1)
                                    continue;
                            @endphp
                            @if ($p->returned == 1 && $p->return_qty > 0)
                                        <?php
                                $item_qty = $p->return_qty;
                                $return_item_total = $p->price * $item_qty;
                                                                                                                            ?>
                                        <tr class="service return-row">
                                            <td class="tableitem">
                                                <p>{{ $sn++ }}</p>
                                            </td>
                                            <td class="tableitem">
                                                <p class="itemtext">
                                                    {{ $p->product->title ?? 'Product' }}
                                                    @if ($p->variant)
                                                        <br> ({{ $p->variant->shade ?? '' }} - {{ $p->variant->size ?? '' }})
                                                    @endif
                                                </p>
                                            </td>
                                            <td class="tableitem">
                                                <p class="itemtext">
                                                    {{ number_format($p->price, 2) }}
                                                </p>
                                            </td>
                                            <td class="tableitem">
                                                <p class="itemtext"><b>{{ $item_qty }}</b></p>
                                            </td>
                                            <td class="tableitem">
                                                <p class="itemtext">
                                                    {{ number_format($return_item_total, 2) }}
                                                </p>
                                            </td>
                                        </tr>
                            @endif
                        @endforeach
                        <tr style="border-bottom:2px dotted;">
                            <td></td>
                            <td class="Rate">
                                <h2>Return Amount</h2>
                            </td>
                            <td class="payment" colspan="3">
                                <h2 style="font-size:12px">- Rs.{{ number_format($return_total_amount) }}</h2>
                            </td>
                        </tr>
                    @endif

                    {{-- ===== TOTALS SECTION ===== --}}
                    @php
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
                    @endphp

                    {{-- Discount row (not shown for manual returns) --}}
                    @if ($discount_amount > 0 && !$is_pure_return)
                        <tr style="border-bottom:2px dotted;">
                            <td></td>
                            <td class="Rate">
                                <h2>Discount</h2>
                            </td>
                            <td class="payment" colspan="3">
                                <h2>Rs.{{ number_format($discount_amount) }}</h2>
                            </td>
                        </tr>
                    @endif

                    {{-- Total Amount --}}
                    <tr style="border-bottom:2px dotted;">
                        <td></td>
                        <td class="Rate">
                            <h2>{{ $is_pure_return ? 'Total Amount' : 'Total Amount' }}</h2>
                        </td>
                        <td class="payment" colspan="3">
                            <h2>Rs.{{ number_format($net_order_amount) }}</h2>
                        </td>
                    </tr>

                    @if ($result->data->order->customer_id != 1)
                        {{-- Previous Balance --}}
                        <tr style="border-bottom:2px dotted;">
                            <td></td>
                            <td class="Rate">
                                <h2>Previous Balance</h2>
                            </td>
                            <td class="payment" colspan="3">
                                <h2>Rs.{{ number_format($correct_previous_balance) }}</h2>
                            </td>
                        </tr>

                        {{-- Total Payable: this order + previous balance --}}
                        <tr style="border-bottom:2px dotted;">
                            <td></td>
                            <td class="Rate">
                                <h2>Total Payable Amount</h2>
                            </td>
                            <td class="payment" colspan="3">
                                <h2 style="font-size:16px">
                                    Rs.{{ number_format($net_order_amount + $correct_previous_balance) }}
                                </h2>
                            </td>
                        </tr>

                        {{-- Paid Amount for this order --}}
                        <tr style="border-bottom:2px dotted;">
                            <td></td>
                            <td class="Rate">
                                <h2>Paid Amount</h2>
                            </td>
                            <td class="payment" colspan="3">
                                <h2>Rs.{{ number_format($result->data->order->paid_amount) }}</h2>
                            </td>
                        </tr>

                        {{-- Balance: remaining after this order --}}
                        <tr style="border-bottom:2px dotted;">
                            <td></td>
                            <td class="Rate">
                                <h2>Balance</h2>
                            </td>
                            <td class="payment" colspan="3">
                                <h2>Rs.{{ number_format($correct_remaining) }}</h2>
                            </td>
                        </tr>


                        {{-- <tr style="border-bottom:2px dotted;">
                            <td></td>
                            <td class="Rate">
                                <h2>Balance</h2>
                            </td>
                            <td class="payment" colspan="3">
                                <h2>Rs.{{ number_format($total_order_amount) }}</h2>
                            </td>
                        </tr> --}}
                    @endif
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
