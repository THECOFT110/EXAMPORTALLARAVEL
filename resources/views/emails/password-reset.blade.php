<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset - SALU Exam Portal</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #700c11, #0b133d); padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1 style="color: #fff; margin: 0; font-size: 20px;">Shah Abdul Latif University</h1>
        <p style="color: #e2e8f0; margin: 5px 0 0 0; font-size: 13px;">Online Examination Portal</p>
    </div>
    
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-top: none; padding: 25px; border-radius: 0 0 8px 8px;">
        <h2 style="color: #0b133d; margin-top: 0;">Password Reset Request</h2>
        <p>Dear <strong>{{ $name }}</strong>,</p>
        <p>We received a request to reset your password for your SALU Exam Portal account. Click the button below to set a new password:</p>
        
        <div style="text-align: center; margin: 25px 0;">
            <a href="{{ $reset_link }}" style="display: inline-block; background: #700c11; color: #ffffff; text-decoration: none; padding: 12px 28px; font-weight: bold; border-radius: 6px; font-size: 15px;">Reset My Password</a>
        </div>
        
        <p style="font-size: 13px; color: #64748b;">This password reset link will expire in 60 minutes. If you did not request a password reset, no further action is required.</p>
        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;">
        <p style="font-size: 11px; color: #94a3b8; word-break: break-all;">If the button doesn't work, copy and paste this URL into your browser:<br>{{ $reset_link }}</p>
    </div>
</body>
</html>
