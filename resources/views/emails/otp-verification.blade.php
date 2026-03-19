<!DOCTYPE html>
<html>
<head>
    <title>Your Verification Code</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e1e1e1; border-radius: 10px;">
    <h2 style="color: #1F7A63;">BookEase Verification</h2>
    <p>Your BookEase verification code is:</p>
    <div style="background-color: #f4f4f4; padding: 20px; text-align: center; border-radius: 5px;">
        <span style="font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #1F7A63;">{{ $otp }}</span>
    </div>
    <p style="margin-top: 20px;">This code expires in <strong>5 minutes</strong>.</p>
    <p>If you did not request this code, please ignore this email.</p>
    <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
    <p style="font-size: 12px; color: #777;">&copy; {{ date('Y') }} BookEase. All rights reserved.</p>
</body>
</html>
