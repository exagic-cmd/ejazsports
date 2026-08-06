<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ejaz Sports Invoice</title>
    <style>
        /* General styling */
        body {
            font-family: Sans-Serif;
            margin: 0;
            padding: 0;
            color: #000;
        }

        #top {
            text-align: center;
            margin-bottom: 20px;
        }

        h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        #mid, #bot {
            margin: 20px 0;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
            font-size: 12px;
        }

        table th, table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        
        table .lefttt {
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
        }

        .tableitem p, .itemtext {
            margin: 0;
            text-align: center;
        }

        /* Footer totals styling */
        /*tr:last-child td {*/
        /*    font-weight: bold;*/
        /*}*/

        /* A4 page print layout */
        @media print {
            @page {
                size: A4;
                margin: 5mm;
            }

            body {
                margin: 0;
            }

            #top h1 {
                font-size: 28px;
            }

            table th, table td {
                font-size: 10px;
            }

            table th h2, table td h2 {
                margin: 0;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div id="" style="padding: 0px 20px;">
        <div id="top">
            <img src="{{asset('imgs/theme/logo-new.jpg')}}" style="height:130px; width:auto">
            <div class="info">
                <h1>Estimate</h1>
            </div>
        </div>
        <br>
        <div id="mid" style="float:right; text-align: right;">
            <div class="info">
                <div>
                    <p><b>Invoice # </b>{{$order->order_no}}</p>
                </div>
                <div>
                    <p>{{date('d/m/Y h:i:s A', strtotime($order->created_at)) }}</p>
                </div>
            </div>
        </div>
        <div id="mid">
            <div class="info">
                <div>
                    <p><b>Customer Name: </b>{{$order->name}}</p>
                </div>
                <div>
                    <p><b>Phone Number: </b>{{$order->phone_number}}</p>
                </div>
                <div>
                    <p><b>Mode of Payment: </b>
                        @if($order->payment_method == 1) CASH
                        @elseif($order->payment_method == 2) CREDIT
                        @endif
                    </p>
                    <p><b>Website Order: </b>{{$order->is_website_order ? 'Yes' : 'No'}}</p>
                </div>
            </div>
        </div>
        <div id="bot">
            <div id="table">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $total = 0; $totalQty = 0; $totalItems = 0; $sr = 1; ?>
                        @foreach($order->products as $p)
                        @php
                            if (isset($p->is_bundle_item) && $p->is_bundle_item) {
                                continue;
                            }

                            $qty = max(0, $p->qty - ($p->return_qty ?? 0));
                            if ($qty <= 0) {
                                continue;
                            }

                            $title = $p->product->title ?? ($p->bundle->name ?? 'Bundle');
                            $variantText = '';
                            if ($p->variant) {
                                $variantText = trim(($p->variant->shade ?? '') . ($p->variant->size ? ' - ' . $p->variant->size : ''));
                            }
                            $lineTotal = $p->price * $qty;
                        @endphp
                        <tr>
                            <td>{{$sr++}}</td>
                            <td class="lefttt">
                                {{$title}}
                                @if(isset($p->bundle) && !empty($p->bundle->short_desc))
                                    <br><small>{{$p->bundle->short_desc}}</small>
                                @endif
                                @if($variantText)
                                    ({{$variantText}})
                                @endif
                            </td>
                            <td>{{number_format($p->price)}}</td>
                            <td>{{$qty}}</td>
                            <td>{{number_format($lineTotal)}}</td>
                            <?php
                                $total += $lineTotal;
                                $totalQty += $qty;
                                $totalItems++;
                            ?>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">Total Item: ({{$totalItems}})</td>
                            <td colspan="2">Total Qty: ({{$totalQty}})</td>
                            <td>Sub Total: Rs.{{number_format($order->total_amount - $order->delivery_charges + $order->discount_amount)}}</td>
                        </tr>
                        <tr>
                            <td colspan="4">Discount (if any):</td>
                            <td>Rs.{{number_format($order->discount_amount)}}</td>
                        </tr>
                        <tr>
                            <td colspan="4">Payable:</td>
                            <td>Rs.{{number_format($order->total_amount)}}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <script>
       window.setTimeout('print1()', 1000);
        function print1() {
            window.print();
            window.close();
        }
    </script>
</body>
</html>
