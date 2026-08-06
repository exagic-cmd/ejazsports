<div class="box bg-light" style="min-height: 80%">
    <h6 class="mt-15">Supplier Details</h6>
    @foreach($supplier->brands as $b)
    <hr>
    <h6 class="mb-0">Brand:</h6>
    <p>{{$b->brand->brand_heading}}</p>

    <h6 class="mb-0">Lead Time:</h6>
    <p>{{$b->lead_time}}</p>

    <h6 class="mb-0">Margin (%) :</h6>
    <p>{{$b->margin ? $b->margin : 0}} </p>

    <h6 class="mb-0">Additional Discount (%) :</h6>
    <p>{{$b->additional_discount ? $b->additional_discount : 0}}</p>

    <h6 class="mb-0">Marketing Discount (%):</h6>
    <p>{{$b->marketing_discount ? $b->marketing_discount : 0}}</p>

    <h6 class="mb-0">Payment Terms :</h6>
    <p>@if($b->payment_terms == \App\Models\SupplierBrand::CASH)
            CASH
        @elseif($b->payment_terms == \App\Models\SupplierBrand::CREDIT)
            CREDIT
        @elseif($b->payment_terms == \App\Models\SupplierBrand::SALE_BASIS)
            SALE BASIS
        @endif</p>

        @endforeach

</div>
