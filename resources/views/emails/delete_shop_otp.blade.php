<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shop Deletion OTP</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            padding: 30px 10px;
        }

        .wrapper {
            max-width: 560px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #b91c1c, #7f1d1d);
            padding: 36px 30px;
            text-align: center;
        }

        .header .logo {
            font-size: 26px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 1px;
        }

        .header .logo span {
            color: #fbbf24;
        }

        .header .tagline {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.75);
            margin-top: 4px;
        }

        /* Warning banner */
        .warning-banner {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 12px 20px;
        }

        .warning-banner p {
            font-size: 13px;
            color: #92400e;
            font-weight: 500;
        }

        /* Body */
        .body {
            padding: 36px 30px;
        }

        .greeting {
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 14px;
        }

        .message {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .message strong {
            color: #b91c1c;
        }

        /* Shop name box */
        .shop-box {
            background: linear-gradient(135deg, #fff1f2, #ffe4e6);
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 24px;
        }

        .shop-label {
            font-size: 11px;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .shop-name {
            font-size: 18px;
            font-weight: 800;
            color: #b91c1c;
        }

        /* OTP box */
        .otp-label {
            font-size: 12px;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            text-align: center;
        }

        .otp-box {
            background: linear-gradient(135deg, #fff1f2, #ffe4e6);
            border: 2px dashed #f87171;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            margin-bottom: 28px;
        }

        .otp-code {
            font-size: 42px;
            font-weight: 800;
            letter-spacing: 10px;
            color: #b91c1c;
        }

        .otp-expiry {
            font-size: 12px;
            color: #ef4444;
            margin-top: 10px;
            font-weight: 500;
        }

        /* Warning note */
        .warning-note {
            background-color: #fff7ed;
            border-radius: 8px;
            padding: 16px 18px;
            margin-bottom: 24px;
        }

        .warning-note p {
            font-size: 13px;
            color: #92400e;
            line-height: 1.6;
        }

        .warning-note ul {
            margin-top: 8px;
            margin-left: 18px;
        }

        .warning-note ul li {
            font-size: 13px;
            color: #92400e;
            line-height: 1.8;
        }

        /* Ignore note */
        .ignore-note {
            font-size: 13px;
            color: #9ca3af;
            line-height: 1.6;
            text-align: center;
            margin-bottom: 10px;
        }

        .ignore-note strong {
            color: #374151;
        }

        /* Footer */
        .footer {
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
            padding: 20px 30px;
            text-align: center;
        }

        .footer p {
            font-size: 12px;
            color: #9ca3af;
            line-height: 1.6;
        }

        .footer .brand {
            font-weight: 700;
            color: #b91c1c;
        }
    </style>
</head>
<body>
    <div class="wrapper">

        <!-- Header -->
        <div class="header">
            <div class="logo">🌾 Rice<span>Mart</span></div>
            <div class="tagline">Pakistan's AI-Powered Rice Marketplace</div>
        </div>

        <!-- Warning Banner -->
        <div class="warning-banner">
            <p>⚠️ This is a sensitive shop action. Do not share this email with anyone.</p>
        </div>

        <!-- Body -->
        <div class="body">

            <div class="greeting">Hello, {{ $name }} 👋</div>

            <p class="message">
                We received a request to <strong>permanently delete your shop</strong> from Rice Mart.
                Use the OTP below to confirm this action. This action <strong>cannot be undone</strong>.
            </p>

            <!-- Shop name -->
            <div class="shop-box">
                <div class="shop-label">Shop to be deleted</div>
                <div class="shop-name">{{ $shopName }}</div>
            </div>

            <!-- OTP Box -->
            <div class="otp-label">Your Deletion OTP</div>
            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
                <div class="otp-expiry">⏳ This OTP expires in 10 minutes</div>
            </div>

            <!-- What gets deleted -->
            <div class="warning-note">
                <p><strong>⚠️ Deleting this shop will permanently remove:</strong></p>
                <ul>
                    <li>Your shop profile and CNIC verification details</li>
                    <li>All product listings under this shop</li>
                    <li>Your seller access (your account reverts to a regular customer)</li>
                </ul>
            </div>

            <!-- Ignore note -->
            <p class="ignore-note">
                If you did <strong>not</strong> request shop deletion,
                please ignore this email. Your shop will remain safe.
            </p>

        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                This email was sent by <span class="brand">Rice Mart</span> ·
                University of Punjab FYP Project<br />
                Please do not reply to this email.
            </p>
        </div>

    </div>
</body>
</html>