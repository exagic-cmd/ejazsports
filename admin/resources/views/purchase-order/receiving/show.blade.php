@extends('layouts.app')

@section('css')

    <link href="{{asset('css/po.css?v=1.1')}}" rel="stylesheet" type="text/css" />
    <style>
    @media only screen and (max-width: 600px) {
        .mycustom-card{
            width: 1200px;
         }
        }

    </style>
@stop


@section('content')

    <div class="card mycustom-card">

        @if($receiving->status == \App\Models\Receiving::APPROVAL_PENDING)
            <div class="corner-ribbon bottom-left sticky orange-stick">
                APPROVAL PENDING
            </div>
        @elseif($receiving->status == \App\Models\Receiving::APPROVED)
            <div class="corner-ribbon bottom-left sticky green-stick d-print-none">
                APPROVED
            </div>
        @endif
        <div class="purchase-order-main">
            <div><img width="200px" src="{{asset('imgs/theme/logo-new.jpg')}}" alt="logo"></div>
            <div><h2>Receiving Notes</h2></div>

        </div>
        <div class="purchase-order-address-main">

           <div>

           </div>

            <div>
                <p><b>Date : </b><span>{{date('d-m-Y',strtotime($receiving->date))}}</span></p>
                <p><b>Invoice # </b><span>&nbsp;{{$receiving->invoice_no}}</span></p>
                <p><b>Cargo # </b><span>&nbsp;{{$receiving->cargo_no}}</span></p>
                <p><b>PO # </b><span>&nbsp;{{$receiving->purchaseOrder ? $receiving->purchaseOrder->po_no : ''}}</span></p>
            </div>
        </div>

        <div class="vendor-main">

            <div>
                <h3>Vendor</h3>
                <p><b>{{ strtoupper($receiving->purchaseOrder?->supplier?->name ?? $receiving->supplier?->name ?? '') }}</b></p>
                <p>Phone: {{ $receiving->purchaseOrder?->supplier?->mobile_number ?? $receiving->supplier?->mobile_number ?? '' }}</p>
                <p>Email: {{ $receiving->purchaseOrder?->supplier?->email ?? $receiving->supplier?->email ?? '' }}  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                </p>
                <br><br>
                <p> <b>Payment Method : </b> @if($receiving->payment_method == \App\Models\SupplierBrand::CASH)
                        CASH
                    @elseif($receiving->payment_method == \App\Models\SupplierBrand::CREDIT)
                        CREDIT
                    @elseif($receiving->payment_method == \App\Models\SupplierBrand::SALE_BASIS)
                        SALE BASIS
                    @endif</p>
                <div class="d-print-none">
                    <br>
                    <p><b>Total Received Products: </b>{{$receiving->total_products}}</p>
                    <p><b>Total Received Quantity: </b> {{$receiving->total_qty}}</p>
                </div>
            </div>
            <div>
                <h3>Received At</h3>
                <p><b>{{strtoupper($receiving->receivedStore->name ?? '')}}</b></p>
                <p>Address: {{$receiving->receivedStore->address ?? ''}}</p>
                <p>Phone: {{$receiving->receivedStore->phone_number ?? ''}}</p>
                <div class="d-print-none">
                    <br>
                    <p><b>Created By: </b>{{$receiving->createdBy->name}}</p>
                    <p><b>Approved By: </b> {{$receiving->approvedBy ? $receiving->approvedBy->name : ''}}</p>

                    <br>
                    <p><b>Documents : </b></p>

                    <ul class="verti-timeline list-unstyled font-sm"><?php $i = 1;?>
                        @foreach($receiving->documents as $d)
                        <li class="event-list">
                            <div class="event-timeline-dot">
                                <i class="material-icons md-play_circle_outline font-xxl"></i>
                            </div>
                            <div class="media">

                                <div class="media-body">
                                    <a href="{{asset('storage/'.$d->file)}}" target="_blank">Document {{$i++}}</a>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>

                </div>

            </div>



        </div>



        <div class="description-table" style="margin: 20px 0 0 0;">


            <div class="table-wrapper ">
                <table class="fl-table">
                    <thead>
                    <tr>
                        <th>Sr#</th>
                        <th>Product #</th>
                        <th style="width: 20%">DESCRIPTION</th>
                        <th>Barcode#</th>
                        <th>Shade</th>
                        <th>Size</th>
                        <th>QTY</th>
                        <th>UNIT PRICE</th>
                        <th>Discount</th>
                        <th>Unit Price After Discount</th>
                        <th>Tax</th>
                        <th>TOTAL</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $sr = 1;$tp = 0;?>
                    @foreach($receiving->products as $p)
                        <tr>
                            <td>{{$sr++}}</td>
                            <td>{{$p->product->code}}</td>
                            <td>{{$p->product->title}}</td>
                            <td>{{$p->variant ? $p->variant->barcode : ''}}</td>
                            <td>{{$p->variant ? $p->variant->shade : ''}}</td>
                            <td>{{$p->variant ? $p->variant->size : ''}}</td>
                            <td>{{$p->qty}}</td>
                            <td>{{number_format($p->trade_price)}}</td>
                            <td>{{number_format($p->discount)}}</td>
                            <td>{{number_format($p->cost_price)}}</td>
                            <td>{{number_format($p->gst)}}</td>
                            <td>{{number_format(($p->trade_price - $p->discount + $p->gst ) * $p->qty)}}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="6"></td>
                        <td ><b style=" border-bottom: 4px double;">{{number_format($receiving->total_qty)}}</b></td>
                        <td ><b style=" border-bottom: 4px double;">{{number_format($receiving->gross_amount)}}</b></td>
                        <td ><b style=" border-bottom: 4px double;">{{number_format($receiving->discount)}}</b></td>
                        <td></td>
                        <td ><b style=" border-bottom: 4px double;">{{number_format($receiving->tax)}}</b></td>
                        <td ><b style=" border-bottom: 4px double;">{{number_format($receiving->net_amount)}}</b></td>
                    </tr>
                    <tbody>
                </table>
            </div>



        </div>
        <br>
        <div class="purchase-total">
            <div style="    min-width: 40%;border: 1px solid;padding: 5px;"><p>{{$receiving->comment}}</p></div>
            <div>

                <p>SUB TOTAL<span> {{number_format($receiving->gross_amount)}}</span></p>
                <p>Discount <span> {{number_format($receiving->discount)}}</span></p>
                <p>TAX <span> {{number_format($receiving->tax)}}</span></p>
                <p>Packing Charges <span> {{number_format($receiving->packing_charges)}}</span></p>
                <p>TOTAL<span> {{number_format($receiving->net_amount)}} </span></p>



            </div>
        </div>

    </div>

    <div @class('d-flex')>
        <!--@can('Approve Purchase Order')-->
        <!--    @if($receiving->status == \App\Models\Receiving::APPROVAL_PENDING)-->
        <!--        <div class="download-print-btn text-left" style="margin-bottom: 40px;">-->
        <!--            <a href="javascript:void(0)" class="btn btn-facebook download-btn" onclick="changeStatus({{$receiving->id}},{{\App\Models\Receiving::APPROVED}})">Approve</a>-->
        <!--        </div>-->
        <!--    @endif-->
        <!--@endcan-->


        @if($receiving->status != \App\Models\Receiving::APPROVAL_PENDING)
            <div class="download-print-btn text-center" style="margin-bottom: 40px;    margin-left: 30%;">
                <a href="#" class="btn btn-primary print-btn">Print</a>
                <a href="#" class="btn btn-primary download-btn" onclick="CreatePDFfromHTML()">Download</a>
                <a href="{{route('receiving.grn',$receiving->id)}}" target="_blank" class="btn btn-primary download-btn">GRN</a>

                  @can('Edit Receiving')
                    <a class="btn btn-dark download-btn" href="{{route('receiving.edit',$receiving->id)}}">Edit info</a>
                    @endcan

                     @can('Delete Receiving')
                        <form class="d-inline-block" onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('receiving.destroy',$receiving->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger download-btn" onclick="return confirm('Are you sure?')"  type="submit">Delete</button>
                        </form>
                    @endcan
            </div>
        @else
            <div class="download-print-btn text-center" style="margin-bottom: 40px;    margin-left: 30%;">
                <a href="javascript:void(0)" style="cursor: not-allowed;" class="btn btn-primary-light ">Print</a>
                <a href="javascript:void(0)" style="cursor: not-allowed;" class="btn btn-primary-light " >Download</a>
                <a href="{{route('receiving.grn',$receiving->id)}}" target="_blank" class="btn btn-primary download-btn">GRN</a>


                  @can('Edit Receiving')
                    <a class="btn btn-dark download-btn" href="{{route('receiving.edit',$receiving->id)}}">Edit info</a>
                    @endcan

                     @can('Delete Receiving')
                        <form class="d-inline-block" onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('receiving.destroy',$receiving->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger download-btn" onclick="return confirm('Are you sure?')"  type="submit">Delete</button>
                        </form>
                    @endcan
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
                pdf.save("{{$receiving->invoice_no}}.pdf");

            });
        }

        function  changeStatus(receiving_id,status) {

            $.confirm({
                title: 'Receiving Status!',
                content: 'Are you sure you want to do this!',
                buttons: {
                    confirm: function () {
                        $.ajax({
                            url: "{{ route('receiving.change.status') }}",
                            type:'GET',
                            data: {receiving_id:receiving_id,status:status},
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
