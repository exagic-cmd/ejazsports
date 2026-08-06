<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f5f5f5; font-family: 'Segoe UI', Arial, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding: 50px 20px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 15px rgba(0,0,0,0.06);">
                    
                    <!-- Logo Section -->
                    <tr>
                        <td style="padding: 50px 40px 30px; text-align: center;">
                            <img src="{{ asset('assets/img/logo.png') }}" alt="{{ config('app.name') }}" style="max-height: 70px; max-width: 200px;">
                        </td>
                    </tr>

                    <!-- Title -->
                    <tr>
                        <td style="padding: 0 40px 20px;">
                            <h2 style="color: #333; margin: 0; font-size: 28px; font-weight: 600;">Your Order Confirmed!</h2>
                        </td>
                    </tr>

                    <!-- Greeting -->
                    <tr>
                        <td style="padding: 0 40px 30px;">
                            <p style="color: #333; font-size: 15px; margin: 0 0 10px; font-weight: 500;">Hi {{ $order->name }},</p>
                            <p style="color: #666; font-size: 15px; line-height: 1.6; margin: 0;">
                                Your order has been confirmed and will be shipping soon.
                            </p>
                        </td>
                    </tr>

                    <!-- Order Info Row -->
                    <tr>
                        <td style="padding: 0 40px 30px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-top: 1px solid #eee; border-bottom: 1px solid #eee;">
                                <tr>
                                    <td width="33%" style="padding: 20px 10px 20px 0;">
                                        <p style="color: #999; font-size: 12px; margin: 0 0 5px; text-transform: uppercase;">Order Date</p>
                                        <p style="color: #333; font-size: 14px; font-weight: 600; margin: 0;">{{ $order->created_at->format('d M, Y') }}</p>
                                    </td>
                                    <td width="33%" style="padding: 20px 10px;">
                                        <p style="color: #999; font-size: 12px; margin: 0 0 5px; text-transform: uppercase;">Order ID</p>
                                        <p style="color: #333; font-size: 14px; font-weight: 600; margin: 0;">#{{ $order->order_no ?? $order->id }}</p>
                                    </td>
                                    <td width="34%" style="padding: 20px 0 20px 10px;">
                                        <p style="color: #999; font-size: 12px; margin: 0 0 5px; text-transform: uppercase;">Address</p>
                                        <p style="color: #333; font-size: 14px; font-weight: 600; margin: 0;">{{ $order->city }}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Order Items -->
                    <tr>
                        <td style="padding: 0 40px 20px;">
                            @foreach($order->products as $item)
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 15px; border: 1px solid #eee; border-radius: 8px;">
                                <tr>
                                    <td style="padding: 15px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td width="70%">
                                                    <p style="color: #333; font-size: 15px; font-weight: 600; margin: 0 0 5px;">
                                                        {{ $item->product->title ?? 'Product' }}
                                                    </p>
                                                    @if($item->variant)
                                                    <p style="color: #888; font-size: 13px; margin: 0;">
                                                        {{ trim(($item->variant->size ?? '') . ' ' . ($item->variant->shade ?? '')) }}
                                                    </p>
                                                    @endif
                                                </td>
                                                <td width="15%" align="center">
                                                    <p style="color: #666; font-size: 14px; margin: 0;">Qty {{ $item->qty }}</p>
                                                </td>
                                                <td width="15%" align="right">
                                                    <p style="color: #333; font-size: 15px; font-weight: 600; margin: 0;">Rs. {{ number_format($item->price * $item->qty, 2) }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            @endforeach
                        </td>
                    </tr>

                    <!-- Totals -->
                    <tr>
                        <td style="padding: 0 40px 30px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="padding: 8px 0; color: #666; font-size: 14px;">Subtotal</td>
                                    <td align="right" style="padding: 8px 0; color: #333; font-size: 14px;">Rs. {{ number_format($order->total_amount - $order->delivery_charges, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; color: #666; font-size: 14px;">Shipping</td>
                                    <td align="right" style="padding: 8px 0; color: #333; font-size: 14px;">Rs. {{ number_format($order->delivery_charges, 2) }}</td>
                                </tr>
                                @if($order->discount_amount > 0)
                                <tr>
                                    <td style="padding: 8px 0; color: #28a745; font-size: 14px;">Discount</td>
                                    <td align="right" style="padding: 8px 0; color: #28a745; font-size: 14px;">- Rs. {{ number_format($order->discount_amount, 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="2" style="padding: 15px 0 10px;"><hr style="border: none; border-top: 1px solid #eee; margin: 0;"></td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; color: #333; font-size: 16px; font-weight: 700;">Total</td>
                                    <td align="right" style="padding: 8px 0; color: #333; font-size: 18px; font-weight: 700;">Rs. {{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Message -->
                    <tr>
                        <td style="padding: 0 40px 20px;">
                            <p style="color: #666; font-size: 14px; line-height: 1.7; margin: 0;">
                                We'll send you shipping confirmation when your item(s) are on the way! We appreciate your business, and hope you enjoy your purchase.
                            </p>
                        </td>
                    </tr>

                    <!-- Shipping Address -->
                    <tr>
                        <td style="padding: 0 40px 30px;">
                            <p style="color: #999; font-size: 12px; margin: 0 0 10px; text-transform: uppercase;">Shipping Address</p>
                            <div style="background: #f9f9f9; border-radius: 8px; padding: 15px; border-left: 3px solid #fed700;">
                                <p style="color: #333; font-size: 14px; font-weight: 600; margin: 0 0 5px;">{{ $order->name }}</p>
                                <p style="color: #666; font-size: 14px; line-height: 1.6; margin: 0;">
                                    {{ $order->address }}<br>
                                    {{ $order->city }}<br>
                                    {{ $order->phone_number }}
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Thank You -->
                    <tr>
                        <td style="padding: 0 40px 30px;">
                            <p style="color: #333; font-size: 14px; margin: 0 0 5px; font-weight: 600;">Thank you!</p>
                            <p style="color: #666; font-size: 14px; margin: 0;">{{ config('app.name') }}</p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 25px 40px; border-top: 1px solid #eee;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td>
                                        <p style="color: #666; font-size: 13px; margin: 0;">
                                            Questions? Contact our <a href="mailto:{{ $admin->email ?? 'support@ejazsports.com' }}" style="color: #28a745; text-decoration: none;">Customer Support</a>
                                            @if($admin && $admin->phone_number)
                                            <br>or call: <span style="color: #333;">{{ $admin->phone_number }}</span>
                                            @endif
                                        </p>
                                    </td>
                                    <td align="right">
                                        <p style="color: #999; font-size: 12px; margin: 0;">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
