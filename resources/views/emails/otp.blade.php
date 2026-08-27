<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 500px;
            margin: 40px auto;
            background: linear-gradient(180deg, #5A8A6E, #9D7E3F);
            border-radius: 16px;
            overflow: hidden;
        }
        .header {
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            color: #1A2820;
            font-size: 28px;
            margin: 0;
            letter-spacing: 2px;
        }
        .header p {
            color: #1A2820;
            font-size: 14px;
            margin: 5px 0 0;
        }
        .body {
            background: rgba(212, 201, 168, 0.15);
            margin: 0 20px 20px;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
        }
        .greeting {
            color: #1A2820;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .message {
            color: #1A2820;
            font-size: 14px;
            margin-bottom: 24px;
            opacity: 0.85;
        }
        .otp-box {
            background: rgba(212, 201, 168, 0.35);
            border: 2px solid rgba(184, 169, 122, 0.7);
            border-radius: 12px;
            padding: 20px;
            margin: 0 auto 24px;
            display: inline-block;
            min-width: 200px;
        }
        .otp-code {
            font-size: 42px;
            font-weight: bold;
            color: #1A2820;
            letter-spacing: 8px;
        }
        .expire {
            color: #1A2820;
            font-size: 13px;
            opacity: 0.75;
        }
        .footer {
            text-align: center;
            padding: 16px;
            color: #1A2820;
            font-size: 12px;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🌾 Rice Mart</h1>
            <p>AI Detection and Suggestion</p>
        </div>
        <div class="body">
            <p class="greeting">Hello, <strong>{{ $userName }}</strong>!</p>
            <p class="message">
                Your One-Time Password (OTP) for account verification is:
            </p>
            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
            </div>
            <p class="expire">⏱ This OTP will expire in <strong>10 minutes</strong>.</p>
            <p class="expire">If you did not request this, please ignore this email.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Rice Mart. All rights reserved.
        </div>
    </div>
</body>
</html>