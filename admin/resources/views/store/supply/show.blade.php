@extends('layouts.app')

@section('css')

    <link href="{{asset('css/po.css?v=1.0')}}" rel="stylesheet" type="text/css" />
@stop


@section('content')

    <div >
        <ul class="wrapper d-print-none">
            @if($supply->status == \App\Models\Supply::CREATED)
                <li class="sphere completed ac" >Created</li>
                <li class="sphere ">Issued</li>
                <li class="sphere ">In Transit</li>
                <li class="sphere">Delivered</li>
            @elseif($supply->status == \App\Models\Supply::ISSUED)
                    <li class="sphere completed" >Created</li>
                    <li class="sphere completed ac">Issued</li>
                    <li class="sphere ">In Transit</li>
                    <li class="sphere">Delivered</li>
            @elseif($supply->status == \App\Models\Supply::IN_TRANSIT)
                <li class="sphere completed" >Created</li>
                <li class="sphere completed">Issued</li>
                <li class="sphere completed ac">In Transit</li>
                <li class="sphere">Delivered</li>
            @elseif($supply->status == \App\Models\Supply::DELIVERED)
                <li class="sphere completed" >Created</li>
                <li class="sphere completed">Issued</li>
                <li class="sphere completed">In Transit</li>
                <li class="sphere completed ac">Delivered</li>
            @endif
        </ul>
    </div>

    <div class="card mycustom-card">

        @if($supply->status == \App\Models\Supply::CREATED)
            <div class="corner-ribbon bottom-left sticky orange-stick">
                APPROVAL PENDING
            </div>
        @elseif($supply->status == \App\Models\Supply::ISSUED)
            <div class="corner-ribbon bottom-left sticky green-stick d-print-none">
                ISSUED
            </div>
        @endif
        <div class="purchase-order-main">
            <div><img src="{{asset('imgs/theme/logo-2.png')}}" alt="logo"></div>
            <div><h2>Store Supply</h2></div>

        </div>
        <div class="purchase-order-address-main">

            <div>

            </div>

            <div>
                <p>Date: <span>{{date('d-m-Y',strtotime($supply->send_date))}}</span></p>
                <p>Supply# <span>&nbsp;VEG-00{{$supply->id}}</span></p>
            </div>
        </div>

        <div class="vendor-main">

            <div>
                <h3>Store Out</h3>
                <p><b>{{strtoupper($supply->storeOut->name)}}</b></p>
                <p>Address: {{$supply->storeOut->address}}</p>
                <p>Phone: {{$supply->storeOut->phone_number}}  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                </p>
                <div class="d-print-none">
                    <br>
                    <p><b>Total Products: </b>{{$supply->total_products}}</p>
                    <p><b>Total Quantity: </b> {{$supply->total_product_qty}}</p>
                    <p><b>Received Date: </b> @if($supply->received_date){{date('d-m-Y',strtotime($supply->received_date))}}@endif</p>
                </div>
            </div>
            <div>
                <h3>Store In</h3>
                <p><b>{{strtoupper($supply->storeIn->name)}}</b></p>
                <p>Address: {{$supply->storeIn->address}}</p>
                <p>Phone: {{$supply->storeIn->phone_number}}</p>
                <div class="d-print-none">
                    <br>
                    <p><b>Created By: </b>{{$supply->createdBy->name}}</p>
                    <p><b>Issued By: </b> {{$supply->approvedBy ? $supply->approvedBy->name : ''}}</p>
                    <p><b>Received By: </b> {{$supply->receivedBy ? $supply->receivedBy->name : ''}}</p>
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
                        <th>Send QTY</th>
                        <th>Received QTY</th>
                        <th>Diff</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $sr = 1;?>
                    @foreach($supply->supplyProducts as $p)
                        <tr>
                            <td>{{$sr++}}</td>
                            <td>{{$p->product->code}}</td>
                            <td>{{$p->product->title}}</td>
                            <td>{{$p->variant->barcode}}</td>
                            <td>{{$p->variant->shade}}</td>
                            <td>{{$p->variant->size}}</td>
                            <td>{{$p->qty}}</td>
                            @if($supply->status == \App\Models\Supply::DELIVERED || $supply->status == \App\Models\Supply::ADDED)
                            <td>{{$p->received_qty}}</td>
                            @if($p->qty - $p->received_qty > 0)
                            <td style="background-color: indianred">{{$p->qty - $p->received_qty}}</td>
                            @else
                                <td>{{$p->qty - $p->received_qty}}</td>
                            @endif
                            @else
                                <td></td>
                                <td></td>
                            @endif
                        </tr>
                    @endforeach

                    <tbody>
                </table>
            </div>



        </div>
        <br>
        <div class="purchase-total">
            <div style="    min-width: 40%;border: 1px solid;padding: 5px;"><p>{{$supply->notes}}</p></div>
            <div>

                <p>TOTAL PRODUCTS<span> {{number_format($supply->total_products)}}</span></p>
                <p>TOTAL QUANTITY<span> {{number_format($supply->total_product_qty)}}</span></p>




            </div>
        </div>

    </div>

    <div @class('d-flex')>
        @can('Approve Supplies')
            @if($supply->status == \App\Models\Supply::CREATED)
                <div class="download-print-btn text-left" style="margin-bottom: 40px;">
                    <a href="javascript:void(0)" class="btn btn-facebook download-btn" onclick="changeStatus({{$supply->id}},{{\App\Models\Supply::ISSUED}})">ISSUE</a>
                </div>
            @endif
        @endcan

            @can('Approve Supplies')
                @if($supply->status == \App\Models\Supply::ISSUED)
                    <div class="download-print-btn text-left" style="margin-bottom: 40px;">
                        <a href="javascript:void(0)" class="btn btn-facebook download-btn" onclick="changeStatus({{$supply->id}},{{\App\Models\Supply::IN_TRANSIT}})">In Transit</a>
                    </div>
                @endif
            @endcan

            @can('Approve Supplies')
                @if($supply->status == \App\Models\Supply::DELIVERED)
                    <div class="download-print-btn text-left" style="margin-bottom: 40px;">
                        <a href="javascript:void(0)" class="btn btn-facebook download-btn" onclick="changeStatus({{$supply->id}},{{\App\Models\Supply::ADDED}})">Approve Receiving</a>
                    </div>
                @endif
            @endcan


        @can('Receive Supplies')
                @if($supply->status == \App\Models\Supply::IN_TRANSIT)
                    <div class="download-print-btn text-left" style="margin-bottom: 40px;">
                        <a href="{{route('supply.receiving.form',['id'=>$supply->id])}}" class="btn btn-facebook download-btn" >Add Receiving</a>
                    </div>
                @endif
            @endcan


        @if($supply->status != \App\Models\Supply::CREATED)
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
                pdf.save("{{$supply->po_no}}.pdf");

            });
        }

        function  changeStatus(supply_id,status) {

            $.confirm({
                title: 'Purchase Order Status!',
                content: 'Are you sure you want to do this!',
                buttons: {
                    confirm: function () {
                        $.ajax({
                            url: "{{ route('supply.change.status') }}",
                            type:'GET',
                            data: {supply_id:supply_id,status:status},
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
