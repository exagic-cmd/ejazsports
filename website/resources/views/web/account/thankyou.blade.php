@extends('layouts.app')

@section('css')

<style>
    .thankyou-container {
      text-align: center;
      background: #fff;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      max-width: 1200px;
      margin: 20px auto;
      width: 100%;
    }

    .thankyou-container h1 {
      color: #28a745;
      font-size: 2.5rem;
      margin-bottom: 20px;
    }

    .thankyou-container p {
      font-size: 1.2rem;
      color: #333;
      margin-bottom: 20px;
    }

    .thankyou-container .order-details {
      background: #f9f9f9;
      padding: 20px;
      border-radius: 5px;
      margin-top: 20px;
      text-align: left;
    }

    .thankyou-container .order-details h2 {
      font-size: 1.5rem;
      margin-bottom: 15px;
      color: #333;
    }

    .thankyou-container .order-details p {
      margin: 5px 0;
      color: #555;
    }

    .thankyou-container .btn {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      background: #007bff;
      color: #fff;
      text-decoration: none;
      border-radius: 5px;
      transition: background 0.3s ease;
    }

    .thankyou-container .btn:hover {
      background: #0056b3;
    }
  </style>
@section('content')


  <div class="thankyou-container">
    <h1>Thank You for Your Order!</h1>
    <p>Your order has been successfully placed. We appreciate your business!</p>

    <!-- Order Details Section -->
    <div class="order-details">
      <h2>Order Details</h2>
      <p><strong>Order ID:</strong> #12345</p>
      <p><strong>Date:</strong> October 15, 2023</p>
      <p><strong>Total:</strong> Rs. 99999.99</p>
      <p><strong>Status:</strong> Processing</p>
    </div>

    <!-- Call to Action -->
    <a href="/" class="btn">Continue Shopping</a>
  </div>










@stop

@section('js')

<script>
	$(document).ready(function() {
  $('#basicsCollapseOne').removeClass('show');
});
</script>
@stop