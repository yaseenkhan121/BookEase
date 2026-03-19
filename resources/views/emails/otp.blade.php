<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; }
        .code { font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #10b981; margin: 20px 0; text-align: center; }
        .footer { font-size: 12px; color: #777; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Account Verification</h2>
        <p>Your verification code is:</p>
        <div class="code">{{ $otpCode }}</div>
        <p>This code will expire in 5 minutes.</p>
        <p>If you did not request this code, please ignore this email.</p>
        <div class="footer">
            &copy; {{ date('Y') }} BookEase. All rights reserved.
        </div>
    </div>
</body>
</html>
