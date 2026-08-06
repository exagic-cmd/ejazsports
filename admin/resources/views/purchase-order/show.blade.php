@extends('layouts.app')

@section('css')

    <link href="{{asset('css/po.css?v=1.0')}}" rel="stylesheet" type="text/css" />
@stop


@section('content')

    <div >
        <ul class="wrapper d-print-none">
            @if($purchaseOrder->status == \App\Models\PurchaseOrder::APPROVAL_PENDING)
            <li class="sphere completed ac" >Created</li>
            <li class="sphere ">Approved</li>
            <li class="sphere ">PO SENT</li>
            <li class="sphere">Received</li>
            <li class="sphere">Paid</li>
                @elseif($purchaseOrder->status == \App\Models\PurchaseOrder::APPROVED)
                <li class="sphere completed">Created</li>
                <li class="sphere completed ac">Approved</li>
                <li class="sphere  ">PO SENT</li>
                <li class="sphere">Received</li>
                <li class="sphere">Paid</li>
            @elseif($purchaseOrder->status == \App\Models\PurchaseOrder::PO_SENT)
                <li class="sphere completed">Created</li>
                <li class="sphere completed ">Approved</li>
                <li class="sphere  completed ac">PO SENT</li>
                <li class="sphere">Received</li>
                <li class="sphere">Paid</li>

            @elseif($purchaseOrder->status == \App\Models\PurchaseOrder::RECEIVED)
                <li class="sphere completed">Created</li>
                <li class="sphere completed ">Approved</li>
                <li class="sphere  completed ">PO SENT</li>
                <li class="sphere completed ac">Received</li>
                <li class="sphere">Paid</li>

            @else

                <li class="sphere completed">Created</li>
                <li class="sphere completed">Approved</li>
                <li class="sphere  completed ">PO SENT</li>
                <li class="sphere completed">Received</li>
                <li class="sphere completed">Paid</li>
                @endif
        </ul>
    </div>

        <div class="card mycustom-card">

            @if($purchaseOrder->status == \App\Models\PurchaseOrder::APPROVAL_PENDING)
            <div class="corner-ribbon bottom-left sticky orange-stick">
                    APPROVAL PENDING
            </div>
                @elseif($purchaseOrder->status == \App\Models\PurchaseOrder::APPROVED)
                <div class="corner-ribbon bottom-left sticky green-stick d-print-none">
                    APPROVED
                </div>
            @endif
            <div class="purchase-order-main">
                <div><img width="200px" src="{{asset('imgs/theme/logo-new.jpg')}}" alt="logo"></div>
                <div><h2>Purchase Order</h2></div>

            </div>
            <div class="purchase-order-address-main">

                <div>
                    <p>Address: Shop C-382/83 Sport Market near Lal Haveli, Raja Bazar, Rawalpindi</p>
                    <p>Phone: 051 5536075</p>
                    <p>Website: www.ejazsports.com</p>
                </div>
                <div>
                    <p>Date:<span>{{date('d-m-Y',strtotime($purchaseOrder->date))}}</span></p>
                    <p>PO#<span>&nbsp;[{{$purchaseOrder->po_no}}]</span></p>
                </div>
            </div>

            <div class="vendor-main">

                <div>
                    <h3>Vendor</h3>
                    <p><b>{{strtoupper($purchaseOrder->supplier->name)}}</b></p>
                    <p>Phone: {{$purchaseOrder->supplier->mobile_number}}</p>
                    <p>Email: {{$purchaseOrder->supplier->email}}  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        @can('Send Purchase Order')
                        @if($purchaseOrder->status == \App\Models\PurchaseOrder::APPROVED)
                            <a  class="btn btn-xs btn-facebook download-btn" onclick="changeStatus({{$purchaseOrder->id}},{{\App\Models\PurchaseOrder::PO_SENT}})" >Send PO</a>
                        @elseif($purchaseOrder->status == \App\Models\PurchaseOrder::APPROVAL_PENDING)
                            <a href="javascript:void(0)" style="cursor: not-allowed;" class="btn btn-xs download-btn" >Send PO</a>
                        @else
                            <a href="javascript:void(0)" style="cursor: not-allowed;" class="btn btn-xs download-btn" >PO Sent</a>
                            @endif
                        @endcan
                    </p>
                    <div class="d-print-none">
                        <br>
                        <p><b>Total Products: </b>{{$purchaseOrder->total_products}}</p>
                        <p><b>Total Quantity: </b> {{$purchaseOrder->total_product_qty}}</p>
                    </div>
                </div>
                <div>
                    <h3>Ship for</h3>
                    <p><b>{{strtoupper($purchaseOrder->shipStore->name)}}</b></p>
                    <p>Address: {{$purchaseOrder->shipStore->address}}</p>
                    <p>Phone: {{$purchaseOrder->shipStore->phone_number}}</p>
                    <div class="d-print-none">
                        <br>
                        <p><b>Created By: </b>{{$purchaseOrder->createdBy->name}}</p>
                        <p><b>Approved By: </b> {{$purchaseOrder->approvedBy ? $purchaseOrder->approvedBy->name : ''}}</p>
                    </div>
                </div>

            </div>



            <div class="description-table" style="margin: 20px 0 0 0;">


                <div class="table-wrapper">
                    <table class="fl-table">
                        <thead>
                        <tr>
                            <th>Sr#</th>
                            <th>Product#</th>
                            <th>DESCRIPTION</th>
                            <th>Barcode#</th>
                            <th>Shade</th>
                            <th>Size</th>
                            <th>QTY</th>
                            <th>UNIT PRICE</th>
                            <th>TOTAL</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = 1;$tp = 0;?>
                        @foreach($purchaseOrder->products as $p)
                        <tr>
                            <td>{{$sr++}}</td>
                            <td>{{$p->product->code}}</td>
                            <td>{{$p->product->title}}</td>
                            <td>{{$p->variant ? $p->variant->barcode : ''}}</td>
                            <td>{{$p->variant ? $p->variant->shade : ''}}</td>
                            <td>{{$p->variant ? $p->variant->size : ''}}</td>
                            <td>{{$p->quantity}}</td>
                            <td>{{number_format($p->trade_price)}}
                            <?php $tp += $p->trade_price;?></td>
                            <td>{{number_format($p->trade_price * $p->quantity)}}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan="6"></td>
                            <td ><b style=" border-bottom: 4px double;">{{number_format($purchaseOrder->total_product_qty)}}</b></td>
                            <td ></td>
                            <td ><b style=" border-bottom: 4px double;">{{number_format($purchaseOrder->total_amount)}}</b></td>
                        </tr>
                        <tbody>
                    </table>
                </div>



            </div>
            <br>
            <div class="purchase-total">
                <div style="    min-width: 40%;border: 1px solid;padding: 5px;"><p>{{$purchaseOrder->comment}}</p></div>
                <div>

                    <p>SUB TOTAL<span> {{number_format($purchaseOrder->total_amount - $purchaseOrder->tax)}}</span></p>
                    <p>TAX <span> {{number_format($purchaseOrder->tax)}}</span></p>
                    <p>TOTAL<span> {{number_format($purchaseOrder->total_amount)}} </span></p>



                </div>
            </div>
            <p style="text-align: center; margin-top: 20px; font-size: 16px;">If you have any question about this purchase please contact us</p>
        </div>

        <div @class('d-flex')>
            @can('Approve Purchase Order')
        @if($purchaseOrder->status == \App\Models\PurchaseOrder::APPROVAL_PENDING)
            <div class="download-print-btn text-left" style="margin-bottom: 40px;">
                <a href="javascript:void(0)" class="btn btn-facebook download-btn" onclick="changeStatus({{$purchaseOrder->id}},{{\App\Models\PurchaseOrder::APPROVED}})">Approve</a>
            </div>
        @endif
            @endcan


        @if($purchaseOrder->status != \App\Models\PurchaseOrder::APPROVAL_PENDING)
        <div class="download-print-btn text-center" style="margin-bottom: 40px;    margin-left: 30%;">
            <a href="#" class="btn btn-primary print-btn">Print</a>
            <a href="#" class="btn btn-primary download-btn" onclick="CreatePDFfromHTML()">Download</a>
        </div>
        @else
            <div class="download-print-btn text-center" style="margin-bottom: 40px;    margin-left: 30%;">
                <a href="javascript:void(0)" style="cursor: not-allowed;" class="btn btn-primary-light ">Print</a>
                <a href="javascript:void(0)" style="cursor: not-allowed;" class="btn btn-primary-light " >Download</a>
            </div>
    @endif
        </div>



@stop


@section('js')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
<script type="text/javascript" src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>

<script>
    function printContent(el) {
        var restorepage = document.body.innerHTML;
        var printcontent = document.querySelector(el).innerHTML;
        document.body.innerHTML = printcontent;
        window.print();
        document.body.innerHTML = restorepage;
    }
    document.querySelector('.print-btn').addEventListener('click', function() {
        printContent('.mycustom-card');
    });

    // download btn

    var doc = new jsPDF();
    var specialElementHandlers = {
        '#editor': function (element, renderer) {
            return true;
        }
    };

    function CreatePDFfromHTML() {
        var HTML_Width = $(".mycustom-card").width();
        var HTML_Height = $(".mycustom-card").height();
        var top_left_margin = 15;
        var PDF_Width = HTML_Width + (top_left_margin * 2);
        var PDF_Height = (PDF_Width * 1.5) + (top_left_margin * 2);
        var canvas_image_width = HTML_Width;
        var canvas_image_height = HTML_Height;

        var totalPDFPages = Math.ceil(HTML_Height / PDF_Height) - 1;

        html2canvas($(".mycustom-card")[0]).then(function (canvas) {
            var imgData = canvas.toDataURL("image/jpeg", 1.0);
            var pdf = new jsPDF('p', 'pt', [PDF_Width, PDF_Height]);
            pdf.addImage(imgData, 'JPG', top_left_margin, top_left_margin, canvas_image_width, canvas_image_height);
            for (var i = 1; i <= totalPDFPages; i++) {
                pdf.addPage(PDF_Width, PDF_Height);
                pdf.addImage(imgData, 'JPG', top_left_margin, -(PDF_Height*i)+(top_left_margin*4),canvas_image_width,canvas_image_height);
            }
            pdf.save("{{$purchaseOrder->po_no}}.pdf");

        });
    }

    function  changeStatus(po_id,status) {

        $.confirm({
            title: 'Purchase Order Status!',
            content: 'Are you sure you want to do this!',
            buttons: {
                confirm: function () {
                    $.ajax({
                        url: "{{ route('purchase-orders.change.status') }}",
                        type:'GET',
                        data: {po_id:po_id,status:status},
                        success: function(data) {
                            if(data.status == true) {
                                toastr.success('Status updated successfully..');
                                setTimeout(function(){
                                    window.location.reload(1);
                                }, 2000);
                            }
                            else
                                toastr.error('Something went wrong..');
                        }
                    });
                },
                cancel: function () {

                }
            }
        });

    }

    window.onafterprint = function(){
        window.location.reload();
    }

</script>
@stop
