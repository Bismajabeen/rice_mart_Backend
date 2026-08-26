<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Shop Removed - Rice Mart</title>

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
            background: linear-gradient(135deg, #166534, #14532d);
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

        /* Warning Banner */

        .warning-banner {
            background-color: #fef2f2;
            border-left: 4px solid #dc2626;
            padding: 14px 20px;
        }

        .warning-banner p {
            font-size: 13px;
            color: #991b1b;
            font-weight: 600;
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
            margin-bottom: 24px;
        }

        .message strong {
            color: #166534;
        }

        /* Removal Box */

        .removal-box {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border: 2px solid #86efac;
            border-radius: 12px;
            padding: 22px;
            margin-bottom: 24px;
        }

        .removal-label {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .shop-name {
            font-size: 22px;
            font-weight: 800;
            color: #166534;
        }

        /* Reason Box */

        .reason-box {
            background-color: #fff7ed;
            border-left: 4px solid #f59e0b;
            border-radius: 8px;
            padding: 18px;
            margin-bottom: 24px;
        }

        .reason-title {
            font-size: 13px;
            font-weight: 700;
            color: #92400e;
            margin-bottom: 8px;
        }

        .reason {
            font-size: 14px;
            color: #78350f;
            line-height: 1.6;
        }

        /* Important Information */

        .info-box {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 18px;
            margin-bottom: 24px;
        }

        .info-box p {
            font-size: 13px;
            color: #4b5563;
            line-height: 1.7;
            margin-bottom: 8px;
        }

        .info-box ul {
            margin-left: 18px;
        }

        .info-box li {
            font-size: 13px;
            color: #4b5563;
            line-height: 1.8;
        }

        /* Support Box */

        .support-box {
            background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 24px;
        }

        .support-title {
            font-size: 15px;
            font-weight: 700;
            color: #166534;
            margin-bottom: 8px;
        }

        .support-text {
            font-size: 13px;
            color: #4b5563;
            line-height: 1.6;
            margin-bottom: 12px;
        }

        .support-contact {
            font-size: 13px;
            color: #374151;
            line-height: 2;
        }

        .support-contact strong {
            color: #166534;
        }

        /* Contact Note */

        .contact-note {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.6;
            text-align: center;
            margin-bottom: 10px;
        }

        .contact-note strong {
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
            color: #166534;
        }
    </style>
</head>

<body>

    <div class="wrapper">

        <!-- Header -->

        <div class="header">

            <div class="logo">
                🌾 Rice<span>Mart</span>
            </div>

            <div class="tagline">
                Pakistan's AI-Powered Rice Marketplace
            </div>

        </div>

        <!-- Warning Banner -->

        <div class="warning-banner">

            <p>
                ⚠️ Important: Your seller shop has been removed from Rice Mart.
            </p>

        </div>

        <!-- Body -->

        <div class="body">

            <div class="greeting">
                Dear {{ $sellerName }} 👋
            </div>

            <p class="message">

                We are writing to inform you that your shop and associated
                seller account on <strong>Rice Mart</strong> have been removed
                by our administration team.

            </p>

            <!-- Shop Information -->

            <div class="removal-box">

                <div class="removal-label">
                    Removed Shop
                </div>

                <div class="shop-name">
                    {{ $shopName }}
                </div>

            </div>

            <!-- Reason -->

            <div class="reason-box">

                <div class="reason-title">
                    ⚠️ Reason for Removal
                </div>

                <div class="reason">
                    {{ $reason }}
                </div>

            </div>

            <!-- What Happens Now -->

            <div class="info-box">

                <p>
                    <strong>What happens to your account?</strong>
                </p>

                <ul>
                    <li>Your shop is no longer available on Rice Mart.</li>
                    <li>Your products are no longer available for customers.</li>
                    <li>Your seller access has been deactivated.</li>
                    <li>Your previous orders and transaction history are preserved.</li>
                </ul>

            </div>

            <!-- Support -->

            <div class="support-box">

                <div class="support-title">
                    📞 Rice Mart Support
                </div>

                <p class="support-text">
                    If you believe this decision was made in error or you
                    need further information, please contact Rice Mart Support.
                </p>

                <p class="support-contact">
                    📧 Email:
                    <strong>samiairshad090@gmail.com</strong>

                    <br />

                    📱 Phone:
                    <strong>+92 302 4539786</strong>
                </p>

            </div>

            <!-- Contact Note -->

            <p class="contact-note">

                Please keep this email for your records.

                <strong>
                    Your order and transaction history remain preserved
                    according to Rice Mart's records policy.
                </strong>

            </p>

        </div>

        <!-- Footer -->

        <div class="footer">

            <p>

                This email was sent by
                <span class="brand">Rice Mart</span>
                · University of Punjab FYP Project

                <br />

                Please do not reply to this email.

            </p>

        </div>

    </div>

</body>

</html>