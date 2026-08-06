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
                                <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">Terms and Conditions</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- End breadcrumb -->
                </div>
            </div>
            <!-- End breadcrumb -->
            <div class="container">
    <div class="mb-12 text-center">
        <h1>Terms and Conditions</h1>
        <p class="text-gray-44">Last Updated: 4th February 2025</p>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">Introduction</h3>
        <p class="text-gray-90">
            Welcome to Ejaz Sports. These Terms and Conditions govern your use of our website, <strong>www.ejazsports.com</strong>, and the purchase of our products. By accessing our website and making a purchase, you agree to comply with these terms. If you do not agree, please do not use our services.
        </p>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">Intellectual Property</h3>
        <ol>
            <li>All content on this website, including text, images, graphics, logos, and product descriptions, is the intellectual property of Ejaz Sports.</li>
            <li>You may not use, copy, reproduce, or distribute any content without our written permission.</li>
            <li>Unauthorized use of our trademarks or branding is strictly prohibited.</li>
        </ol>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">User Responsibilities</h3>
        <ol>
            <li>You must be at least 18 years old or have parental consent to use this website.</li>
            <li>Providing false or misleading information during checkout or registration is prohibited.</li>
            <li>You are responsible for keeping your account details, including your password, confidential.</li>
        </ol>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">Orders and Payments</h3>
        <ol>
            <li>All orders placed on <strong>www.ejazsports.com</strong> are subject to availability.</li>
            <li>We reserve the right to cancel orders at our discretion if we suspect fraud or incorrect pricing.</li>
            <li>Payments must be made using the approved payment methods available on our website.</li>
            <li>Prices and product details are subject to change without prior notice.</li>
        </ol>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">Shipping and Delivery</h3>
        <ol>
            <li>We aim to process and ship all orders within the estimated delivery time.</li>
            <li>Delays may occur due to unforeseen circumstances such as customs clearance or courier delays.</li>
            <li>Ejaz Sports is not responsible for any additional shipping fees, taxes, or customs duties.</li>
            <li>If an incorrect shipping address is provided, the customer is responsible for additional delivery charges.</li>
        </ol>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">Returns and Refunds</h3>
        <p class="text-gray-90">
            Please refer to our <a href="#" class="text-blue font-weight-bold">Return Policy</a> for detailed information on how to return products and request refunds.
        </p>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">Limitation of Liability</h3>
        <p class="text-gray-90">
            Ejaz Sports is not responsible for any indirect, incidental, or consequential damages arising from the use of our products or website. In no event shall our liability exceed the amount paid for the purchased product.
        </p>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">Prohibited Activities</h3>
        <ol>
            <li>Using our website for fraudulent or illegal activities.</li>
            <li>Disrupting website operations through hacking, spamming, or introducing malicious software.</li>
            <li>Attempting to access unauthorized areas of our website or database.</li>
            <li>Engaging in activities that violate copyright laws or harm our brand reputation.</li>
        </ol>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">Changes to This Agreement</h3>
        <p class="text-gray-90">
            We reserve the right to update these Terms and Conditions at any time. Any changes will be posted on this page, and your continued use of the website constitutes acceptance of the updated terms.
        </p>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">Governing Law</h3>
        <p class="text-gray-90">
            These Terms and Conditions shall be governed and interpreted in accordance with the laws of [Your Country/State]. Any disputes shall be resolved in the appropriate courts of [Your Country/State].
        </p>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">Contact Us</h3>
        <p class="text-gray-90">
            If you have any questions about these Terms and Conditions, please contact us at <a href="mailto:info@ejazsports.com" class="text-blue font-weight-bold">info@ejazsports.com</a>.
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