<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f5f5f5; font-family: 'Segoe UI', Arial, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding: 50px 20px;">
        <tr>
            <td align="center">
                <table role="presentation" width="500" cellspacing="0" cellpadding="0" style="background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 15px rgba(0,0,0,0.06);">
                    
                    <!-- Logo Section -->
                    <tr>
                        <td style="padding: 50px 40px 30px; text-align: center;">
                            <img src="{{ asset('assets/img/logo.png') }}" alt="{{ config('app.name') }}" style="max-height: 60px; max-width: 200px;">
                        </td>
                    </tr>

                    <!-- Title -->
                    <tr>
                        <td style="padding: 0 40px 30px; text-align: center;">
                            <h2 style="color: #333; margin: 0; font-size: 26px; font-weight: 600;">Reset your password</h2>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 0 40px 40px;">
                            <p style="color: #333; font-size: 15px; margin: 0 0 20px; font-weight: 500;">Hey there,</p>
                            
                            <p style="color: #555; font-size: 15px; line-height: 1.7; margin: 0 0 35px;">
                                Need to reset your password? No problem! Just click the button below and you'll be on your way. If you did not make this request, please ignore this email.
                            </p>

                            <!-- Button -->
                            <a href="{{ route('password.reset', ['token' => $token, 'email' => $email]) }}" 
                               style="display: block; background-color: #fed700; color: #333; padding: 16px 30px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 15px; text-align: center;">
                                Reset your password
                            </a>
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td style="padding: 0 40px;">
                            <hr style="border: none; border-top: 1px solid #eee; margin: 0;">
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 35px 40px; text-align: center;">
                            <p style="color: #888; font-size: 13px; margin: 0 0 20px; line-height: 1.6;">
                                Problems or questions? 
                                @if($admin && $admin->phone_number)
                                Call us on <span style="color: #333;">{{ $admin->phone_number }}</span><br>
                                @endif
                                or email <a href="mailto:{{ $admin->email ?? 'support@ejazsports.com' }}" style="color: #333; text-decoration: none;">{{ $admin->email ?? 'support@ejazsports.com' }}</a>
                            </p>
                            
                            <!-- Logo Small -->
                            <img src="{{ asset('assets/img/logo.png') }}" alt="{{ config('app.name') }}" style="max-height: 30px; max-width: 100px; margin-bottom: 15px;">
                            
                            <p style="color: #aaa; font-size: 12px; margin: 0;">
                                {{ date('Y') }} {{ config('app.name') }}<br>
                                all rights reserved
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
