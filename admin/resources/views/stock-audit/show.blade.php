@extends('layouts.app')

@section('css')

    <link href="{{asset('css/po.css?v=1.0')}}" rel="stylesheet" type="text/css" />
@stop



@section('content')


    <div class="card mycustom-card">

        @if($stockAudit->status == \App\Models\StockAudit::PENDING)
            <div style="width:215px;    padding-left: 79px;" class="corner-ribbon bottom-left sticky orange-stick">
                PENDING
            </div>
        @elseif($stockAudit->status == \App\Models\StockAudit::APPROVED)
            <div style="width:215px;    padding-left: 79px;" class="corner-ribbon bottom-left sticky green-stick d-print-none">
                APPROVED
            </div>
        @endif

        <div class="purchase-order-main">
            <div><img src="{{asset('imgs/theme/logo-new.jpg')}}" style="height:133px" alt="logo"></div>
            <div><h2>Audit Detail</h2></div>

        </div>

        <div class="purchase-order-address-main">

            <div>

            </div>

            <div>
                <p>Date:<span>{{date('d-m-Y',strtotime($stockAudit->date))}}</span></p>

            </div>
        </div>


        <div class="vendor-main">

            <div>

                <div class="">
                    <br>
                    <h4><b>Brand: </b>{{$stockAudit->brand ? $stockAudit->brand->title : ''}}</h4>
                    <h4><b>Store #</b>{{$stockAudit->storeId ? $stockAudit->storeId->name : ''}}</h4>
                    <p><b>Remarks: </b> {{$stockAudit->remarks}}</p>
                </div>
            </div>
            <div>
                <div class="">
                    <br>
                    <h3><b>Audit By: </b>{{$stockAudit->auditBy ? $stockAudit->auditBy->name : ''}}</h3>
                    <br>
                    <h3><b>Approve By: </b>{{$stockAudit->approveBy ? $stockAudit->approveBy->name : ''}}</h3>

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
                        <th>Shade</th>
                        <th>Size</th>
                        <th>System QTY</th>
                        <th>Audit QTY</th>
                        <th>Difference QTY</th>
                        <th>Adjust in Stock</th>
                        <th>Adjust in Damage</th>
                        <th>Adjust in Expiry</th>
                        <th>Adjust in Missing</th>
                         <th>Adjust in Tester</th>
                         <th>Reason</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $sr = 1;$tp = 0;?>
                    @foreach($stockAudit->products as $p)
                        <tr>
                            <td>{{$sr++}}</td>
                            <td>{{$p->product->title}}</td>
                            <td>{{ $p->variant->shade ?? '-' }}</td>
                            <td>{{$p->variant->size ?? '-'}}</td>
                            <td>{{$p->system_qty}}</td>
                            <td>{{$p->in_hand_qty}}</td>
                            <td>{{$p->difference_qty}}</td>
                            @if($p->adjust_in_stock > 0)<td style="background-color:green;color:white">{{$p->adjust_in_stock}} @else
                           <td> {{$p->adjust_in_stock}}
                            @endif</td>
                            @if($p->adjust_in_damage > 0)<td style="background-color:red;color:white">{{$p->adjust_in_damage}} @else
                           <td> {{$p->adjust_in_damage}}
                            @endif</td>
                            @if($p->adjust_in_expiry > 0)<td style="background-color:red;color:white">{{$p->adjust_in_expiry}} @else
                           <td> {{$p->adjust_in_expiry}}
                            @endif</td>
                            @if($p->adjust_in_missing > 0)<td style="background-color:red;color:white">{{$p->adjust_in_missing}} @else
                           <td> {{$p->adjust_in_missing}}
                            @endif
                            </td>
                            
                            @if($p->adjust_in_tester > 0)<td style="background-color:red;color:white">{{$p->adjust_in_tester}} @else
                           <td> {{$p->adjust_in_tester}}
                            @endif
                            </td>
                            <td>{{$p->reason}}</td>
                        </tr>
                    @endforeach
                    <tbody>
                </table>
            </div>

<br><br>

<h3>Non Audit Items</h3>
            <div class="table-wrapper">
                <table class="fl-table">
                    <thead>
                    <tr>
                        <th>Sr#</th>
                        <th>Product#</th>
                        <th>Shade</th>
                        <th>Size</th>
                        <th>Barcode</th>
                        <th>Overall Qty</th>
                        <th>System QTY</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $sr = 1;$tp = 0;?>
                    @foreach($variants as $p)
                    @if($stock[$p->id] > 0)
                        <tr>
                            <td>{{$sr++}}</td>
                            <td>{{$p->product->title}}</td>
                            <td>{{$p->shade}}</td>
                            <td>{{$p->size}}</td>
                            <td>{{$p->barcode}}</td>
                            <td>{{$p->available_stock}}</td>
                            <td>{{$stock[$p->id]}}</td>

                        </tr>
                        @endif
                    @endforeach
                    <tbody>
                </table>
            </div>



        </div>
        <br>


    </div>

    <div @class('d-flex')>

        @can('Edit Stock Audit')
            @if($stockAudit->status == \App\Models\StockAudit::PENDING)
                <div class="download-print-btn text-left" style="margin-bottom: 40px;">
                    <a href="javascript:void(0)" class="btn btn-success-light download-btn" onclick="changeStatus({{$stockAudit->id}},{{\App\Models\StockAudit::APPROVED}})">Mark as Approved</a>
                </div>
            @endif
        @endcan

        <div class="download-print-btn text-center" style="margin-bottom: 40px;    margin-left: 30%;">
            <a href="#" class="btn btn-primary print-btn">Print</a>
            <a href="#" class="btn btn-primary download-btn" onclick="CreatePDFfromHTML()">Download</a>
        </div>

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
                pdf.save("{{$stockAudit->id}}.pdf");

            });
        }

        function  changeStatus(id,status) {

            $.confirm({
                title: 'Approve Status!',
                content: 'Are you sure you want to do this!',
                buttons: {
                    confirm: function () {
                        $.ajax({
                            url: "{{ route('stock-audit.status.update') }}",
                            type:'GET',
                            data: {id:id,status:status},
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
