<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,sans-serif;">
    <div style="max-width:480px;margin:40px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,0.08);">
        <div style="background:#4f46e5;padding:28px 32px;">
            <h1 style="color:#fff;margin:0;font-size:22px;">OTP Verification</h1>
            <p style="color:#c7d2fe;margin:6px 0 0;font-size:13px;">{{ config('app.name') }}</p>
        </div>
        <div style="padding:32px;">
            <p style="color:#374151;font-size:15px;margin:0 0 20px;">
                Use the OTP below to complete your login. This code is valid for <strong>5 minutes</strong>.
            </p>
            <div style="background:#f8fafc;border:2px dashed #c7d2fe;border-radius:10px;padding:24px;text-align:center;margin-bottom:24px;">
                <span style="font-size:40px;font-weight:bold;letter-spacing:14px;color:#4f46e5;">{{ $otp }}</span>
            </div>
            <p style="color:#6b7280;font-size:13px;margin:0;">
                If you did not request this OTP, please ignore this email. Do not share this code with anyone.
            </p>
        </div>
        <div style="background:#f9fafb;padding:16px 32px;border-top:1px solid #e5e7eb;">
            <p style="color:#9ca3af;font-size:12px;margin:0;text-align:center;">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>