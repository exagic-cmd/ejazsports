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
                                <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">Privacy Policy</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- End breadcrumb -->
                </div>
            </div>
            <!-- End breadcrumb -->

            <div class="container">
    <div class="mb-12 text-center">
        <h1>Privacy Policy</h1>
        <p class="text-gray-44">This Privacy Policy was last updated on 4th February 2025</p>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">Introduction</h3>
        <p class="text-gray-90">
            Welcome to Ejaz Sports. Your privacy is important to us, and this Privacy Policy explains how we collect, use, and safeguard your information when you visit our website, <strong>www.ejazsports.com</strong>. By using our services, you agree to the terms outlined in this policy.
        </p>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">Information We Collect</h3>
        <ol>
            <li><strong>Personal Information:</strong> We may collect your name, email address, phone number, shipping and billing address, and payment details when you make a purchase.</li>
            <li><strong>Non-Personal Information:</strong> We collect data such as browser type, IP address, and browsing behavior to improve our website’s functionality.</li>
            <li><strong>Cookies:</strong> Our website uses cookies to enhance user experience and track preferences.</li>
        </ol>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">How We Use Your Information</h3>
        <ol>
            <li>To process and fulfill your orders, including payments and shipments.</li>
            <li>To improve our website, services, and customer support.</li>
            <li>To send promotional emails, newsletters, or updates about new products (you can opt-out at any time).</li>
            <li>To protect against fraudulent activities and unauthorized transactions.</li>
        </ol>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">How We Protect Your Data</h3>
        <p class="text-gray-90">
            We implement strict security measures to protect your personal information. Our website uses SSL encryption, secure payment gateways, and regular security audits to safeguard your data.
        </p>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">Third-Party Sharing</h3>
        <p class="text-gray-90">
            We do not sell or share your personal information with third parties, except when required for payment processing, order fulfillment, or legal compliance.
        </p>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">Your Rights</h3>
        <ol>
            <li>You can request access to the personal data we hold about you.</li>
            <li>You can request corrections or deletions of your data.</li>
            <li>You can opt-out of marketing emails at any time.</li>
        </ol>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">Changes to This Privacy Policy</h3>
        <p class="text-gray-90">
            We may update this Privacy Policy from time to time. Any changes will be posted on this page, and your continued use of our website signifies your acceptance of the revised policy.
        </p>
    </div>
    <div class="mb-10">
        <h3 class="mb-6 pb-2 font-size-25">Contact Us</h3>
        <p class="text-gray-90">
            If you have any questions about this Privacy Policy, please contact us at <a href="mailto:info@ejazsports.com" class="text-blue font-weight-bold">info@ejazsports.com</a>.
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