<div class="box bg-light" style="min-height: 80%">
    <h6 class="mt-15">Purchase Order Details</h6>

        <hr>
        <h6 class="mb-0">Supplier :</h6>
        <p>{{$purchaseOrder->supplier ? $purchaseOrder->supplier->name : ''}}</p>

        <h6 class="mb-0">Shipment At :</h6>
        <p>{{$purchaseOrder->shipStore ? $purchaseOrder->shipStore->name : ''}}</p>

        <h6 class="mb-0">Date :</h6>
        <p>{{date('d-m-Y',strtotime($purchaseOrder->date))}}</p>

        <h6 class="mb-0">Total Products :</h6>
        <p>{{$purchaseOrder->total_products}} </p>

        <h6 class="mb-0">Total Quantity :</h6>
        <p>{{$purchaseOrder->total_product_qty}}</p>

        <h6 class="mb-0">Created By :</h6>
        <p>{{$purchaseOrder->createdBy ? $purchaseOrder->createdBy->name : ''}}</p>

        <h6 class="mb-0">Approved By :</h6>
        <p>{{$purchaseOrder->approvedBy ? $purchaseOrder->approvedBy->name : ''}}</p>


</div>
