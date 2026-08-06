<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>PO | Vegas Cosmetics</title>
    <meta http-equiv="x-ua-compatible" content="ie=edge" />


   <style>
       .purchase-order-main{
           display: flex;
           flex-direction: row;
           align-items: center;
           justify-content: space-between;
       }
       .mycustom-card{
           padding: 20px;
       }
       .purchase-order-address-main{
           display: flex;
           flex-direction: row;
           justify-content:space-between;
       }
       .purchase-total{
           display: flex;
           flex-direction: row;
           justify-content:space-between;
       }
       .purchase-total span{
           margin-left: 40px;
           margin-right: 20px;
           background: gray;
           height: 30px;
           width: 80px;
           color: white;
           padding: 2px 10px;
       }
       .purchase-total p{
           display: flex;
           justify-content: space-between;
           align-items: center;
       }
       .purchase-order-address-main p{
           font-weight: 500;
       }
       .vendor-main{
           display: flex;
           flex-direction: row;
           justify-content: space-between;
           margin: 20px 0;
       }
       .vendor-main h3{
           background-color: #3BB77E;
           padding: 5px 20px;
           font-size: 18px;
           color: white;
       }
       .vendor-main p{
           font-weight: 500;
       }



       /* Table Styles */



       .fl-table {
           border-radius: 5px;
           font-size: 12px;
           font-weight: normal;
           border: none;
           border-collapse: collapse;
           width: 100%;
           max-width: 100%;
           white-space: nowrap;
           background-color: white;
       }

       .fl-table td, .fl-table th {
           text-align: center;
           padding: 8px;
       }

       .fl-table td {
           border: 1px solid black;
           font-size: 12px;
       }

       .fl-table thead th {
           color: #ffffff;
           background: #4FC3A1;
       }


       .fl-table thead th:nth-child(odd) {
           color: #ffffff;
           background: #324960;
       }
       .fl-table tr:nth-child(even) {
           background: #F8F8F8;
       }
   </style>



</head>

<body>

<main class="main-wrap">
    <section class="content-main">

    <div class="card mycustom-card">
        <div class="purchase-order-main" >
            <div><img src="{{public_path('imgs/theme/logo-2.png')}}" alt="logo"></div><br>

            <div style="margin-top: 70px;"><h2 style="margin-bottom: 10px;">Purchase Order [{{$purchaseOrder->po_no}}]</h2>
            <p style="margin: 0px;"><b>Date : </b>{{date('d-m-Y',strtotime($purchaseOrder->date))}}</p>
            </div>
        </div>

        <div class="purchase-order-address-main" style="margin-top: -50px;">
            <div style="width: 100%;">
                <p style="width: 100%;margin: 0px;">Address: Office 164 Street 9<br> industrial Area, I-10 Markaz,Islamabad </p>
                <p style="margin: 0px;">Phone: +923167522 </p>
                <p style="margin: 0px;">Website: www.vegas.pk</p>
            </div>
        </div>
        <div class="vendor-main">
            <div>
                <h3>Vendor</h3>
                <p style="margin: 0px;"><b>{{strtoupper($purchaseOrder->supplier->name)}}</b></p>
                <p style="margin: 0px;">Phone: {{$purchaseOrder->supplier->mobile_number}}</p>
                <p style="margin: 0px;">Email: {{$purchaseOrder->supplier->email}}
                </p>
            </div>
        </div>
        <div class="vendor-main">
            <div>
                <h3>Ship for</h3>
                <p style="margin: 0px;"><b>{{strtoupper($purchaseOrder->shipStore->name)}}</b></p>
                <p style="margin: 0px;">Address: {{$purchaseOrder->shipStore->address}}</p>
                <p style="margin: 0px;">Phone: {{$purchaseOrder->shipStore->phone_number}}</p>
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
                            <td>{{$p->variant->barcode}}</td>
                            <td>{{$p->variant->shade}}</td>
                            <td>{{$p->variant->size}}</td>
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
                    <tr>
                        <td colspan="9"></td>
                    </tr>

                    <tr>
                        <td colspan="7"></td>
                        <td ><b style=" border-bottom: 4px double;">SUB TOTAL</b></td>
                        <td ><b style=" border-bottom: 4px double;">{{number_format($purchaseOrder->total_amount - $purchaseOrder->tax)}}</b></td>
                    </tr>

                    <tr>
                        <td colspan="7"></td>
                        <td ><b style=" border-bottom: 4px double;">TAX</b></td>
                        <td ><b style=" border-bottom: 4px double;">{{number_format($purchaseOrder->tax)}}</b></td>
                    </tr>

                    <tr>
                        <td colspan="7"></td>
                        <td ><b style=" border-bottom: 4px double;">TOTAL</b></td>
                        <td ><b style=" border-bottom: 4px double;">{{number_format($purchaseOrder->total_amount)}}</b></td>
                    </tr>
                    <tbody>
                </table>
            </div>
        </div>
        <br>
        <div class="purchase-total">
            <div style="    min-width: 40%;border: 1px solid;padding: 5px;"><p>{{$purchaseOrder->comment}}</p></div>

        </div>
        <p style="text-align: center; margin-top: 20px; font-size: 16px;">If you have any question about this purchase please contact us</p>
    </div>
    </section>

</main>

<script src="{{asset('js/vendors/jquery-3.6.0.min.js')}}"></script>
<script src="{{asset('js/vendors/bootstrap.bundle.min.js')}}"></script>
<!-- Main Script -->
<script src="{{asset('js/main.js?v=1.0')}}" type="text/javascript"></script>
<script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js" type="text/javascript"></script>


</body>
</html>
