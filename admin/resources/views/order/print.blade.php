<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ejaz Sports Invoice</title>
    <link rel="stylesheet" href="{{asset('css/receipt.css')}}">
    <style>
        @page {
            size: auto;
            /* auto is the current printer page size */
            margin: 0mm;
            /* this affects the margin in the printer settings */
        }
    </style>
</head>
<body style="font-family: Sans-Serif">
    <?php

    /*echo response()->streamDownload(function ($order) {
        $cnNo = $order->cn_no;
        $apiKey = 'Y1wiro9rzv)r@3tGty5k@1iL4KsHBodTLlHQyg)kyMukgKH1mk6@bF2@S0tW@B5l';
        $loginId = '2553';
        $url = 'http://api.withrider.com/airwaybill?cn=' . $cnNo . '&loginId=' . $loginId . '&apikey=' . $apiKey;
        echo Http::withHeaders(['Content-Type' => 'application/pdf'])
        ->get($url)
        ->body();
    }, 'c4611_sample_explain.pdf');
    exit;*/
    /*$curl = curl_init();
    $cnNo = $order->cn_no;
    $apiKey = 'Y1wiro9rzv)r@3tGty5k@1iL4KsHBodTLlHQyg)kyMukgKH1mk6@bF2@S0tW@B5l';
    $loginId = '2553';
    $url = 'http://api.withrider.com/airwaybill?cn=' . $cnNo . '&loginId=' . $loginId . '&apikey=' . $apiKey;
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($curl, CURLOPT_TIMEOUT, 0);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json'
    ));
    // EXECUTE:
    $result = curl_exec($curl);
    echo $result;
    exit;
    $result = json_decode($result);
*/
    ?>
    <div id="invoice-POS" style="padding:0px 20px;">
        <div id="top" style="text-align:center;">
            <!--<div><img class="logo" src="{{asset('images/logo-2.png')}}"></div>-->
            <div class="info" style="display: inline-block;line-height: 0.7;">
               
                <h1>Customer Invoice</h1>
            </div><!--End Info-->
        </div><!--End InvoiceTop-->
        <br>
        <div id="mid" style="text-align:right;">
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
                    <p><b>Customer Name : </b>{{$order->name}}
                </div>
                <div>
                    <p><b>Phone Number : </b>{{$order->phone_number}}
                </div>
                
                <div>
                    <p><b>Mode of Payment : </b>
                        @if($order->payment_method == 1)
                        CASH
                        @elseif($order->payment_method == 2)
                        CREDIT
                       
                        @endif</p>
                </div>
            </div>
        </div>
        <div id="bot">
            <div id="table">
                <table>
                    <tr class="tabletitle">
                        <td class="Rate">
                            <h2>#</h2>
                        </td>
                        <td class="item">
                            <h2>Description</h2>
                        </td>
                        <td class="Rate">
                            <h2>Price</h2>
                        </td>
                        <!--<td class="Rate"><h2>GST Rate</h2></td>-->
                        <td class="Hours">
                            <h2>Qty</h2>
                        </td>
                        <!--<td class="Rate"><h2>GST Rate</h2></td>-->
                        <td class="Rate">
                            <h2>Total</h2>
                        </td>
                     
                    </tr>
                    <?php $total = 0;
                    $totalQty = 0;
                    $totalItems = 0;
                    $sr = 1; ?>
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
                    <tr class="service">
                        <td class="tableitem">
                            <p>{{$sr++}}</p>
                        </td>
                        <td class="tableitem">
                            <p class="itemtext" style="line-height: 1.5">
                                {{$title}}
                                @if(isset($p->bundle) && !empty($p->bundle->short_desc))
                                    <br><small>{{$p->bundle->short_desc}}</small>
                                @endif
                                @if($variantText)
                                    <br>({{$variantText}})
                                @endif
                            </p>
                        </td>
                        <td class="tableitem">
                            <p class="itemtext">{{number_format($p->price)}}</p>
                        </td>
                        <!--<td class="tableitem"><p class="itemtext">17</p></td>-->
                        <td class="tableitem">
                            <p class="itemtext">{{$qty}}</p>
                        </td>
                        <!--<td class="tableitem"><p class="itemtext">{{number_format(round($p->price * 0.17))}}</p></td>-->
                        <td class="tableitem">
                            <p class="itemtext">{{number_format($lineTotal)}}</p>
                        </td>
                        <?php
                            $total = $total + $lineTotal;
                            $totalQty += $qty;
                            $totalItems++;
                        ?>
                        
                    </tr>
                    @endforeach
                    <tr style="border-top: 2px dotted;">
                        <td class="Rate">
                            <h2>Total Item: ({{$totalItems}})</h2>
                        </td>
                        <td class="Rate">
                            <h2>Total Qty: ({{$totalQty}})</h2>
                        </td>
                        <td class="Rate" colspan="4">
                            <h2>Sub Total: </h2>
                        </td>
                        <td class="Rate">
                            <h2>Rs.{{number_format($order->total_amount - $order->delivery_charges + $order->discount_amount)}}</h2>
                        </td>
                    </tr>
                    <!--<tr >-->
                    <!--    <td></td>-->
                    <!--    <td></td>-->
                    <!--    <td class="Rate" colspan="4"><h2>Delivery Charges : </h2></td>-->
                    <!--    <td class="payment"><h2>Rs.{{number_format($order->delivery_charges)}}</h2></td>-->
                    <!--</tr>-->
                    <tr>
                        <td></td>
                        <td></td>
                        <td class="Rate" colspan="4">
                            <h2>Discount (if any): </h2>
                        </td>
                        <td class="payment">
                            <h2>Rs.{{number_format($order->discount_amount)}}</h2>
                        </td>
                    </tr>
                    <tr style="border-bottom: 2px dotted;">
                        <td></td>
                        <td></td>
                        <td class="Rate" colspan="4">
                            <h2>Payable</h2>
                        </td>
                        <td class="payment">
                            <h2>Rs.{{number_format($order->total_amount)}}</h2>
                        </td>
                    </tr>
                </table>
            </div><!--End Table-->
            <!--<div id="legalcopy">-->
            <!--    <p class="legal" style="    text-align: center;line-height: 1.5;">Verify this invoice through FBR  TaxAsaanMobileApp or SMS at 9966 and win exciting prizes in draw-->
            <!--    </p>-->
            <!--</div>-->
        </div><!--End InvoiceBot-->
    </div><!--End Invoice-->
    <script>
        window.setTimeout('print1()', 1000);
        function print1() {
            window.print();
            window.close();
        }
    </script>
</body>
</html>
