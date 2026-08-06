@extends('layouts.app')

@section('css')
    <link href="{{asset('css/po.css?v=1.0')}}" rel="stylesheet" type="text/css" />

@stop

@section('content')
    <div class="card mycustom-card">
        <div class="purchase-order-main">
            <!--<div><img src="{{asset('imgs/theme/logo-2.png')}}" alt="logo"></div>-->
            <div><h2>Goods Received Note</h2></div>

        </div>

        <div class="grn-sign supplier-grn">
            <div>
                <p>Supplier: <span> <b style="padding-left: 10px;">{{strtoupper($receiving->purchaseOrder ? $receiving->purchaseOrder->supplier ? $receiving->purchaseOrder->supplier->name : '' : $receiving->supplier ? $receiving->supplier->name : '')}}</b></span></p>
            </div>
            <div>
                <p>Date:<span> <b style="padding-left: 10px;">{{date('d-m-Y',strtotime($receiving->date))}}</b></span></p>
            </div>
            <div>
                <p>GRN #:<span> <b style="padding-left: 10px;"> GRN-00{{$receiving->id}}</b></span></p>
            </div>
            <div>
                <p>Invoice #:<span> <b style="padding-left: 10px;"> {{$receiving->invoice_no}}</b></span></p>
            </div>
            <div>
                <p>Order number:<span> <b style="padding-left: 10px;">{{$receiving->purchaseOrder ? $receiving->purchaseOrder->po_no : ''}}</b></span></p>
            </div>
            <div>
                <p>Delivery location:<span> <b style="padding-left: 10px;"> {{strtoupper($receiving->receivedStore->name)}}  </b></span></p>
            </div>
            <div>
                <p>Received Products:<span> <b style="padding-left: 10px;">{{$receiving->total_products}}</b> </span></p>
            </div>

            <div >
                <p>Received Quantity:<span> <b style="padding-left: 10px;">{{number_format($receiving->total_qty)}}</b> </span></p>
            </div>

        </div>

        <div class="description-table" style="margin: 20px 0 0 0;">

@if($receiving->purchaseOrder)
            <div class="table-wrapper">
                <table class="fl-table">
                    <thead>
                    <tr>
                        <th>Sr#</th>
                        <th>ITEM#</th>
                        <th>Goods</th>
                        <th>Barcode</th>
                        <th>Shade/Size</th>


                        <th>Order quantity</th>
                        <th>Delivered quantity</th>
                        <th>Difference</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $sr = 1;?>
                    @foreach($receiving->products as $p)
                        <tr>
                            <td>{{$sr++}}</td>
                            <td>{{$p->product->code}}</td>
                            <td>{{$p->product->title}}</td>
                            <td>{{$p->variant ? $p->variant->barcode : ''}}</td>
                            <td>{{$p->variant ? $p->variant->shade : ''}} / {{$p->variant ? $p->variant->size : ''}}</td>
                            <td>{{$orderQuantity[$p->product_variant_id]}}</td>
                            <td>{{$p->qty}}</td>

                            <td>{{$orderQuantity[$p->product_variant_id] - $p->qty }}</td>
                        </tr>
                    @endforeach

                    <tbody>
                </table>
            </div>
            
            @else
            <div class="table-wrapper">
                <table class="fl-table">
                    <thead>
                    <tr>
                        <th>Sr#</th>
                        <th>ITEM#</th>
                        <th>Goods</th>
                        <th>Barcode</th>
                        <th>Shade/Size</th>

                        <th>Delivered quantity</th>
                      
                    </tr>
                    </thead>
                    <tbody>
                    <?php $sr = 1;?>
                    @foreach($receiving->products as $p)
                        <tr>
                            <td>{{$sr++}}</td>
                            <td>{{$p->product->code}}</td>
                            <td>{{$p->product->title}}</td>
                            <td>{{$p->variant ? $p->variant->barcode  : ''}}</td>
                            <td>{{$p->variant ? $p->variant->shade : ''}} / {{$p->variant ? $p->variant->size : ''}}</td>
                           
                            <td>{{$p->qty}}</td>

                           
                        </tr>
                    @endforeach

                    <tbody>
                </table>
            </div>
            
            
            @endif



        </div>


        <div class="grn-sign supplier-grn">
            <div>
                <p>Inspected by:<span> </span></p>
            </div>
            <div>
                <p>Received by:<span> </span></p>
            </div>

            <div>
                <p>Checked by:<span> </span></p>
            </div>

        </div>

    </div>

        <div class="download-print-btn text-center" style="margin-bottom: 40px;">
            <a  class="btn btn-primary print-btn">Print</a>
            <a  class="btn btn-primary download-btn" onclick="CreatePDFfromHTML()">Download</a>
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
                pdf.save("GRN-{{$receiving->invoice_no}}.pdf");

            });
        }

        window.onafterprint = function(){
            window.location.reload();
        }

    </script>

@stop
