@extends('layouts.app')

@section('css')
    <style >
        .pvoucher-para{
            border-right: 1px solid lightgray; font-size: 16px;  padding: 3px;
        }
        .p16-2p{
            font-size: 16px; padding: 3px;
        }
        .grid-1fr{
            display: grid; grid-template-columns: 1fr 1fr; border: 1px solid lightgray;
        }

    </style>
     <style>
    @media only screen and (max-width: 600px) {
        .mycustom-card{
            width: 1200px;
         }
         .card-body{
             width: 450px;
         }
         .no-print{
             display:none!important;
         }
        }
    @media print {
   /* All print related styles to be added here */
   .content-header,
   .download-print-btn, .navbar, .main-footer, .icon, .left, .md-calendar_today{
      display: none !important;
   }
   th{border: 1px solid;}
   .table{font-size:9px;}
   .table > :not(caption) > * > * {
    padding: 2px;}
    .itemside .info {
    padding-left: 0px;
    padding-right: 0px;
}
  small{display: none !important;}
  h6 {font-size: 11px;}
  p {font-size: 11px;}
  b {font-size: 11px;}
  .mb-50, .mt-20{margin-top:0!important;margin-bottom:0!important;}
  .card-header, .card-body{padding: 5px;}

}
 @page {     margin: 0 !important; }
        
    </style>
@stop

@section('content')

        <div class="card mycustom-card" >

        <div class="content-header">
            <div>
                <h2 class="content-title card-title">Return Detail</h2>
                <p> Return Details for Order ID: ES{{$order->order_no}}</p>
            </div>
        </div>
        <div class="card">
            <header class="card-header">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-6 mb-lg-0 mb-15">
                        <span> <i class="material-icons md-calendar_today"></i> <b>{{date('D,  M  d,  Y,  h:i',strtotime($order->return_date))}}</b> </span> <br>
                        <span class="text-muted">Order ID: ES-{{$order->order_no}}</span>
                    </div>
                 
                </div>
            </header>
            <!-- card-header end// -->
            <div class="card-body">
                <div class="row mb-50 mt-20 order-info-wrap">
                    <div class="col-md-4 col-xs-6">
                        <article class="icontext align-items-start">
                                    <span class="icon icon-sm rounded-circle bg-primary-light">
                                        <i class="text-primary material-icons md-person"></i>
                                    </span>
                            <div class="text">
                                <h6 class="mb-1">Customer</h6>
                                <p class="mb-1">
                                    {{$order->name}}<br>
                                    {{$order->email}} <br>
                                    {{$order->phone_number}}
                                </p>
                                <!--<a href="#">View profile</a>-->
                            </div>
                        </article>
                    </div>
                    <!-- col// -->
                    <div class="col-md-4 col-xs-6">
                        <article class="icontext align-items-start">
                                    <span class="icon icon-sm rounded-circle bg-primary-light">
                                        <i class="text-primary material-icons md-local_shipping"></i>
                                    </span>
                            <div class="text">
                                <h6 class="mb-1">Order info</h6>
                                <p class="mb-1">

                                    Inventory Adjust Type: <b>
                                    @if($order->adjust_type == 1)
                                    Stock
                                    @else
                                    Damage
                                    @endif
                                    </b>

                                </p>
                                
                                <p class="mb-1">

                                    Amount Adjust Type: <b>
                                    @if($order->return_type == 1)
                                    Ledger
                                    @else
                                    Cash
                                    @endif
                                    </b>

                                </p>

                            </div>
                        </article>
                    </div>
                
                </div>
                <!-- row // -->
                <div class="row">
                    <div class="col-lg-12">
                        <div >
                            <table class="table">
                                <thead>
                                <tr>
                                    <th >Product</th>
                                    <th >Unit Price</th>
                                    
                                    <th >Quantity</th>
                                    <th  class="text-end">Total</th>
                                   
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($order->products as $pro)
                                @if(isset($pro->is_bundle) && $pro->is_bundle && ( !isset($pro->is_bundle_item) || !$pro->is_bundle_item ))
                                    @continue
                                @endif
                                @if($pro->returned || $order->status == 6)
                                <tr>
                                    <td>
                                        <a class="itemside" href="#">
                                            <div class="left">
                                                <img src="{{asset('storage/default.jpeg')}}" width="40" height="40" class="img-xs no-print" alt="Item">
                                            </div>
                                            <div class="info">{{$pro->name}} <br>
                                            <small>{{$pro->variant ? $pro->variant->shade : ''}} - {{$pro->variant ? $pro->variant->size : ''}}</small></div>
                                        </a>
                                    </td>
                                    <td>{{number_format($pro->price)}}</td>
                                    
                                    @if($order->status == 6)
                                    <td>{{$pro->qty}}</td>
                                    @else
                                      <td>{{$pro->return_qty}}</td>
                                    @endif
                                    <td class="text-end">{{number_format($pro->price * $pro->return_qty)}}</td>
                                  
                                </tr>
                                @endif
                                @endforeach

                                <tr>
                                    <td colspan="4">
                                        <article class="float-end">
                                            <dl class="dlist">
                                                <dt><b>Total:</b></dt>
                                                <dd><b>{{number_format($order->return_amount)}}</b></dd>
                                            </dl>
                                        
                                        </article>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- table-responsive// -->
                    </div>
                    <!-- col// -->
                    <div class="col-lg-1"></div>
                 
                    <!-- col// -->
                </div>
                
                
                
                <!--<button class="btn btn-primary no-print" onclick="window.print()">Print</button>-->
            </div>
            <!-- card-body end// -->
        </div>
        <!-- card end// -->
        </div>
    <div class="download-print-btn text-center" style="margin-bottom: 40px;">
                <a href="{{ route('orders.return.download_pos', $order->id) }}" target="_blank" class="btn btn-primary print-btn">Print</a>
                <a href="{{ route('orders.return.download_pos', $order->id) }}" target="_blank" class="btn btn-primary download-btn">Download</a>
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
    // document.querySelector('.print-btn').addEventListener('click', function() {
    //     printContent('.card-body');
    //     location.reload();
    // });
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
            pdf.save("Return.pdf");

        });
    }

</script>
@stop

