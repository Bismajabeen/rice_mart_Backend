<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Password OTP</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 30px;">

    <div style="max-width: 480px; margin: auto; background: #ffffff; border-radius: 10px; padding: 30px; border: 1px solid #e0e0e0;">

        <h2 style="color: #1A2820; text-align: center;">Rice Mart</h2>

        <p style="font-size: 15px; color: #333;">Hi {{ $name }},</p>

        <p style="font-size: 15px; color: #333;">
            We received a request to reset your Rice Mart account password.
            Enter the OTP below to confirm and set your new password.
        </p>

        <div style="text-align: center; margin: 25px 0;">
            <span style="display: inline-block; font-size: 28px; letter-spacing: 6px; font-weight: bold; color: #1A2820; background: #D4C9A8; padding: 12px 24px; border-radius: 8px;">
                {{ $otp }}
            </span>
        </div>

        <p style="font-size: 14px; color: #555;">
            This code will expire in <strong>10 minutes</strong>.
        </p>

        <p style="font-size: 13px; color: #999; margin-top: 20px;">
            If you didn't request a password reset, you can safely ignore this email — your password will not be changed.
        </p>

        <hr style="border: none; border-top: 1px solid #eee; margin: 25px 0;">

        <p style="font-size: 12px; color: #aaa; text-align: center;">
            © {{ date('Y') }} Rice Mart. All rights reserved.
        </p>

    </div>

</body>
</html>
