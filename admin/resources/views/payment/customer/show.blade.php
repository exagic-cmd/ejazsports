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
        }
        
    </style>
@stop

@section('content')

        <div class="card mycustom-card" >

            <div class="payment-vouc-parent" style="border: 1px solid gray; padding: 20px 20px 100px 20px;">
                <div></div>
                <h2 style="font-size: 20px; text-align: center;">Customer Payment Voucher</h2>
                <p style="margin: 40px 0px 30px 30px;text-align: right;" >Ref No: <span style="border-bottom:1px dotted">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CPV-00{{$customerPayment->id}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>

                <div class="pvoucher-date-p grid-1fr">

                    <div>
                        <p class="p16-2p">From: <b style="margin-left: 10%">{{$customerPayment->customer->first_name}}</b></p>
                    </div>
                </div>
                <div class="pvoucher-date-p grid-1fr">
                    <div>
                        <p class="pvoucher-para" >Received Amount: <b style="margin-left: 10%;">{{number_format($customerPayment->amount + $customerPayment->tax,2)}}</b></p>
                    </div>
                    <div>
                        <p class="p16-2p">Date: <b style="margin-left: 10%">{{date('d-m-Y',strtotime($customerPayment->date))}}</b></p>
                    </div>
                </div>

              

                <div class="pvoucher-date-p" style="border: 1px solid lightgray;">

                    <p style="font-weight: 900; font-size: 18px; color: black; text-align: center; padding: 3px">Method of Payment &nbsp;&nbsp;&nbsp;&nbsp;
                        @if($customerPayment->payment_method == \App\Models\CustomerPayment::CASH)
                            ( CASH )
                        @elseif($customerPayment->payment_method == \App\Models\CustomerPayment::BANK_TRANSFER)
                            ( BANK TRANSFER )
                        @elseif($customerPayment->payment_method == \App\Models\CustomerPayment::CHEQUE)
                            ( CHEQUE )
                        @endif
                    </p>
                </div>
                <div class="pvoucher-date-p grid-1fr">
                    <div>
                        <p class="pvoucher-para">Cash: <b style="margin-left: 10%">@if($customerPayment->payment_method == \App\Models\CustomerPayment::CASH)
                                <i class="icon material-icons md-check_box"></i> @endif</b></p>
                    </div>
                    <div>
                        <p class="p16-2p">Cheque #: <b style="margin-left: 10%">@if($customerPayment->payment_method == \App\Models\CustomerPayment::CHEQUE){{$customerPayment->cheque_no}} &nbsp;&nbsp;&nbsp; [{{date('d-m-Y',strtotime($customerPayment->cheque_date))}}]@endif</b></p>
                    </div>
                </div>
                <div class="pvoucher-date-p grid-1fr">

                    <div>
                        <p class="p16-2p">Bank Name: <b style="margin-left: 10%">@if($customerPayment->payment_method == \App\Models\CustomerPayment::BANK_TRANSFER){{$customerPayment->bank_name}}@endif</b></p>
                    </div>
                </div>

                <div class="pvoucher-date-p " style="border: 1px solid lightgray;">

                    <div>
                        <p class="p16-2p">The Sum of: <b style="margin-left: 10%">{{$words}} only</b></p>
                    </div>
                </div>

                <div class="pvoucher-date-p" style="display: grid; grid-template-columns: 2fr 1fr; border: 1px solid lightgray;">
                    <div>
                        <p class="pvoucher-para" style="height: 122px;"><b>Narration: </b><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$customerPayment->notes}}</p>
                    </div>
                    <div>
                        <p class="p16-2p">Customer Ledger: <br>
                            <p style="text-align: right;font-size: 13px;font-weight: 900;">Outstanding Balance: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="border-bottom: 1px double">{{number_format($outstandingBalance)}} </span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>
                            <p style="text-align: right;font-size: 13px;font-weight: 900;">Paid Amount: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="border-bottom: 1px double">{{number_format($customerPayment->amount + $customerPayment->tax)}} </span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>
                            
                            <p style="text-align: right;font-size: 13px;font-weight: 900;">Discount: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="border-bottom: 1px double">{{number_format($customerPayment->discount)}} </span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>
                            <p style="text-align: right;font-size: 13px;font-weight: 900;">Balance: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="border-bottom: 1px double">{{number_format($outstandingBalance - ($customerPayment->amount + $customerPayment->discount))}} </span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>
                        </p>
                    </div>
                </div>

                <div class="pvoucher-date-p" style="display: grid; grid-template-columns: 1fr 1fr 1fr; border: 1px solid lightgray;">
                    <div>
                        <p class="pvoucher-para" style="height: 102px;" >Approved By:</p>
                    </div>
                    <div>
                        <p class="pvoucher-para" style="height: 102px;">Paid By:</p>
                    </div>
                    <div>
                        <p class="p16-2p">Received By: <br><br><br>
                        <span style="margin-left: 20%;">{{$customerPayment->received_by}}</span></p>
                    </div>
                </div>

            </div>

        </div>

        @can('Approve Supplier Payment')
            @if($customerPayment->status == \App\Models\CustomerPayment::APPROVAL_PENDING)
                <div class="download-print-btn text-left" style="margin-bottom: 40px;">
                    <a href="javascript:void(0)" class="btn btn-facebook download-btn" onclick="changeStatus({{$customerPayment->id}},{{\App\Models\SupplierPayment::APPROVED}})">Approve</a>
                </div>
            @endif
        @endcan


        @if($customerPayment->status != \App\Models\CustomerPayment::APPROVAL_PENDING)
            <div class="download-print-btn text-center" style="margin-bottom: 40px;">
                <a href="#" class="btn btn-primary print-btn">Print</a>
                <a href="#" class="btn btn-primary download-btn" onclick="CreatePDFfromHTML()">Download</a>
                
                 @can('Edit Customer Payment')
                    <a class="download-btn btn btn-dark" href="{{route('customer-payments.edit',$customerPayment->id)}}">Edit info</a>
                @endcan

                @can('Delete Customer Payment')
                    <form class="d-inline-block" onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('customer-payments.destroy',$customerPayment->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="download-btn btn btn-danger" onclick="return confirm('Are you sure?')"  type="submit">Delete</button>
                    </form>
                @endcan
                                            
            </div>
        @else
            <div class="download-print-btn text-center" style="margin-bottom: 40px;">
                <a href="javascript:void(0)" style="cursor: not-allowed;" class="btn btn-primary-light ">Print</a>
                <a href="javascript:void(0)" style="cursor: not-allowed;" class="btn btn-primary-light " >Download</a>
                @can('Edit Customer Payment')
                    <a class="download-btn btn btn-dark" href="{{route('customer-payments.edit',$customerPayment->id)}}">Edit info</a>
                @endcan

                @can('Delete Customer Payment')
                    <form class="d-inline-block" onsubmit="return confirm('Do you really want to do this?');" id="delete-form" action="{{ route('customer-payments.destroy',$customerPayment->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="download-btn btn btn-danger" onclick="return confirm('Are you sure?')"  type="submit">Delete</button>
                    </form>
                @endcan
            </div>
        @endif
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
        location.reload();
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
            pdf.save("Payment.pdf");

        });
    }
    function  changeStatus(payment_id,status) {

        $.confirm({
            title: 'Customer Payment Status!',
            content: 'Are you sure you want to do this!',
            buttons: {
                confirm: function () {
                    $.ajax({
                        url: "{{ route('customer-payments.change.status') }}",
                        type:'GET',
                        data: {payment_id:payment_id,status:status},
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

</script>
@stop
