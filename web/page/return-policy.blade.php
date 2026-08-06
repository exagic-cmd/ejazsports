@extends('layouts.app')


@section('content')




  <!-- ========== MAIN CONTENT ========== -->
        <main id="content" role="main">
            <!-- breadcrumb -->
            <div class="bg-gray-13 bg-md-transparent">
                <div class="container">
                    <!-- breadcrumb -->
                    <div class="my-md-3">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble">
                                <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1"><a href="index.html">Home</a></li>
                                <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">Return Policy</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- End breadcrumb -->
                </div>
            </div>
            <!-- End breadcrumb -->
            <div class="container">
    <div class="mb-12 text-center">
        <h1>Return Policy</h1>
        <p class="text-gray-44">This Return Policy was last updated on 4th February 2025</p>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">Overview</h3>
        <p class="text-gray-90">
            At Ejaz Sports, we strive to ensure complete customer satisfaction. If you are not entirely satisfied with your purchase, we’re here to help with easy returns and exchanges.
        </p>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">Eligibility for Returns</h3>
        <ol>
            <li>Items must be returned within <strong>7 days</strong> from the date of delivery.</li>
            <li>Products must be unused, in their original condition, and with all tags and packaging intact.</li>
            <li>Items that are damaged, worn, or not in their original condition may not be eligible for a return.</li>
            <li>Custom-made, personalized, or clearance sale items are non-returnable unless defective.</li>
        </ol>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">Return Process</h3>
        <ol>
            <li>Email us at <a href="mailto:info@ejazsports.com" class="text-blue font-weight-bold">info@ejazsports.com</a> with your order details and reason for return.</li>
            <li>Our team will review your request and provide return instructions.</li>
            <li>Ship the item back to us using a trackable shipping method.</li>
            <li>Once received and inspected, we will process your refund or exchange.</li>
        </ol>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">Refunds</h3>
        <p class="text-gray-90">
            Once your return is approved, the refund will be processed to your original payment method within <strong>5-7 business days</strong>. Shipping fees are non-refundable.
        </p>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">Exchanges</h3>
        <p class="text-gray-90">
            If you need to exchange an item for a different size or color, please contact us at <a href="mailto:info@ejazsports.com" class="text-blue font-weight-bold">info@ejazsports.com</a>. Exchanges are subject to stock availability.
        </p>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">Non-Returnable Items</h3>
        <p class="text-gray-90">
            The following items cannot be returned or exchanged:
        </p>
        <ol>
            <li>Customized or personalized items.</li>
            <li>Clearance sale items.</li>
            <li>Products that have been used, washed, or altered.</li>
            <li>Gift cards and digital products.</li>
        </ol>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">Shipping Costs</h3>
        <p class="text-gray-90">
            Customers are responsible for return shipping costs unless the item received is defective or incorrect. We recommend using a trackable shipping service to ensure safe delivery.
        </p>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">Contact Us</h3>
        <p class="text-gray-90">
            If you have any questions regarding our Return Policy, please contact us at <a href="mailto:info@ejazsports.com" class="text-blue font-weight-bold">info@ejazsports.com</a>.
        </p>
    </div>
</div>

            
        </main>
        <!-- ========== END MAIN CONTENT ========== -->




@stop

@section('js')

<script>
	$(document).ready(function() {
  $('#basicsCollapseOne').removeClass('show');
});
</script>

@stop