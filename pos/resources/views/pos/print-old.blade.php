<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>POS Receipt Template Html Css</title>



    <link rel="stylesheet" href="{{asset('css/receipt.css')}}">
    <style type="text/css">
        @page {

            /* auto is the current printer page size */

            /* this affects the margin in the printer settings */
            margin: 0mm 0mm 20mm 0mm;
        }

    </style>


</head>

<body style="font-family: Sans-Serif;">
    <div id="invoice-POS">

        <div id="top" style="text-align:center;">

            <!--<div class="info" style="display: inline-block;line-height: 0.7;">-->
            <h1>Estimate</h1>

            <!--            <p style="line-height:1.5"><b>Address: </b>Shop C-382/83 Sport Market near Lal Haveli, Raja Bazar, Rawalpindi</p>-->
            <!--            <p><b>Phone:</b> 051 5536075</p>-->

            <!--<p><b>PoS no : </b>POS-{{$result->data->store->id}}</p>-->

            <!--</div>-->
            <!--End Info-->
        </div>
        <!--End InvoiceTop-->
        <div id="mid" style="text-align:center;min-height:0px;">
            <div class="info">
                <div>
                    <p><b>@if($result->data->order->status == 6 )
                            Return Invoice #
                            @else
                            EST #
                            @endif
                        </b>{{$result->data->order->order_no}}</p>
                </div>
                <div>
                    <p>{{date('d/m/Y h:i:s A', strtotime($result->data->order->created_at)) }}</p>
                </div>
            </div>
        </div>
        <div id="mid" style="min-height:0px;">
            <div class="info">
                <div>
                    <p><b>Customer Name :
                        </b>{{ isset($result->data->order->name) && trim($result->data->order->name) !== 'Retail' ? $result->data->order->name : '' }}
                </div>
                <div>
                    <p><b>Employee Name : </b>{{$result->data->order->employee->name}}
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

                    <?php $total = 0;$sr=1;?>
                    @foreach($result->data->order->products as $p)

                    <tr class="service">
                        <td class="tableitem">
                            <p>{{$sr++}}</p>
                        </td>

                        <td class="tableitem">
                            <p class="itemtext" style="line-height: 1.5">{{$p->product->title}} @if($p->variant) <br>
                                ({{$p->variant ? $p->variant->shade : ''}} - {{$p->variant ? $p->variant->size : ''}})
                                @endif</p>
                        </td>
                        <td class="tableitem">
                            <p class="itemtext">{{number_format($p->price,2)}}</p>
                        </td>

                        <td class="tableitem">
                            <p class="itemtext"><b>{{$p->qty}}</b></p>
                        </td>


                        <td class="tableitem">
                            <p class="itemtext">{{number_format($p->price * $p->qty)}}</p>
                        </td>

                        <?php $total = $total + ($p->price * $p->qty);?>
                    </tr>
                    @endforeach
                    <tr>
                        <td></td>
                        <p class="clearfix"> Total Items: ({{count($result->data->order->products)}})</p>
                        <td class="Rate" colspan="1">
                            <h2>Discount (if any): </h2>
                        </td>
                        <td class="payment" colspan="3">
                            <h2 style="font-size:12px">Rs.{{number_format($result->data->order->discount_amount)}}</h2>
                        </td>
                    </tr>
                    <!--<tr >-->
                    <!--    <td></td>-->
                    <!--    <td></td>-->

                    <!--    <td class="Rate" colspan="4"><h2>PoS Service Fee: </h2></td>-->
                    <!--    <td class="payment"><h2>Rs.1.00</h2></td>-->
                    <!--</tr>-->
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr style="border-bottom: 2px dotted;">
                        <td></td>
                        <td class="Rate" colspan="1">
                            <h2>Bill Amount </h2>
                        </td>
                        <td class="payment" colspan="3">
                            <h2 style="font-size:12px">Rs.{{number_format($result->data->order->total_amount)}}</h2>
                        </td>
                    </tr>
                    @if($result->data->order->customer_id != 1)
                    <tr style="border-bottom: 2px dotted;">
                        <td></td>
                        <td class="Rate" colspan="1">
                            <h2>Previous Balance </h2>
                        </td>
                        <td class="payment" colspan="3">
                            <h2>Rs.{{number_format($result->data->previousBalance )}}</h2>
                        </td>
                    </tr>
                    @endif
                    <tr style="border-bottom: 2px dotted;">
                        <td></td>
                        <td class="Rate" colspan="1">
                            <h2>Total Payable Amount </h2>
                        </td>
                        <td class="payment" colspan="3">
                            <h2 style="font-size:16px">
                                Rs.{{number_format($result->data->order->total_amount + $result->data->previousBalance)}}
                            </h2>
                        </td>
                    </tr>
                    @if($result->data->order->customer_id != 1)
                    <tr style="border-bottom: 2px dotted;">
                        <td></td>
                        <td class="Rate" colspan="1">
                            <h2>Paid Amount </h2>
                        </td>
                        <td class="payment" colspan="3">
                            <h2>Rs.{{number_format($result->data->order->paid_amount)}}</h2>
                        </td>
                    </tr>
                    <tr style="border-bottom: 2px dotted;">
                        <td></td>
                        <td class="Rate" colspan="1">
                            <h2>Balance </h2>
                        </td>
                        <td class="payment" colspan="3">
                            <h2>Rs.{{number_format($result->data->totalRemaining)}}</h2>
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
            <!--End Table-->
            <p style="text-align: center">Thanks for shopping with us.</p>
            <!--<div id="legalcopy">-->
            <!--    <p class="legal" style="    text-align: center;line-height: 1.5;"><strong>FBR Invoice # </strong><br>  1252585418887/04/2022-->
            <!--    </p>-->
            <!--</div>-->
            <div style="margin-top:150px; font-size:6px">.</div>
        </div>
        <!--End InvoiceBot-->
    </div>
    <!--End Invoice-->
    <script>
        window.setTimeout('print1()', 1000);
        function print1() {
            window.print();
            window.setTimeout("window.location.href= '/'", 1000);
        }
    </script>

</body>



</html>
