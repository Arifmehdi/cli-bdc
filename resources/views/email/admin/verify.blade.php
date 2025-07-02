<!-- resources/views/emails/admin/verify.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        /* Base styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #e5e5e5;
        }
        .logo {
            max-height: 50px;
        }
        .content {
            padding: 30px 20px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #e5e5e5;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3490dc;
            color: white !important;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #2779bd;
        }
        .text-center {
            text-align: center;
        }
        .text-muted {
            color: #6c757d;
        }
        .mt-4 {
            margin-top: 1.5rem;
        }
        .mb-4 {
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header with Logo -->
        <div class="header">
            <a href="{{ config('app.url') }}">
                @if(config('app.logo'))
                    <img src="{{ config('app.logo') }}" alt="{{ config('app.name') }}" class="logo">
                @else
                    <h1>{{ config('app.name') }}</h1>
                @endif
            </a>
        </div>

        <!-- Main Content -->
        <div class="content">
            <h2 class="text-center">Hello {{ $admin->name }}!</h2>

            <p>Thank you for registering with {{ config('app.name') }}. Please verify your email address to complete your account setup.</p>

            <p>This verification link will expire in {{ config('auth.verification.expire', 60) }} minutes.</p>

            <div class="text-center mt-4 mb-4">
                <a href="{{ $url }}" class="button">Verify Email Address</a>
            </div>

            <p class="text-muted">If you're having trouble clicking the button, copy and paste the following URL into your browser:</p>
            <p class="text-muted"><small>{{ $url }}</small></p>

            <p class="mt-4">If you did not create this account, please ignore this email or contact support if you have questions.</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            <br>
            <a href="{{ config('app.url') }}/privacy" style="color: #999; text-decoration: none;">Privacy Policy</a> |
            <a href="{{ config('app.url') }}/terms" style="color: #999; text-decoration: none;">Terms of Service</a>

            @if(config('app.address'))
                <div style="margin-top: 10px;">
                    {{ config('app.address') }}
                </div>
            @endif
        </div>
    </div>
</body>
</html>
