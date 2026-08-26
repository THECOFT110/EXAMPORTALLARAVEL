<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Email Verification - SALU Exam Portal</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #700c11, #0b133d); padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1 style="color: #fff; margin: 0; font-size: 20px;">Shah Abdul Latif University</h1>
        <p style="color: #e2e8f0; margin: 5px 0 0 0; font-size: 13px;">Online Examination & Admission Portal</p>
    </div>
    
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-top: none; padding: 25px; border-radius: 0 0 8px 8px;">
        <h2 style="color: #0b133d; margin-top: 0;">Email Verification</h2>
        <p>Dear <strong>{{ $name }}</strong>,</p>
        <p>Thank you for registering on the SALU Exam Portal. Please use the verification code below to confirm your account:</p>
        
        <div style="text-align: center; margin: 25px 0;">
            <div style="display: inline-block; background: #f8fafc; border: 2px dashed #700c11; padding: 12px 30px; font-size: 26px; font-weight: bold; letter-spacing: 6px; color: #700c11; border-radius: 6px;">
                {{ $code }}
            </div>
        </div>
        
        <p style="font-size: 13px; color: #64748b;">If you did not register for an account on the SALU Exam Portal, please ignore this email.</p>
        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;">
        <p style="font-size: 12px; color: #94a3b8; text-align: center;">Shah Abdul Latif University, Khairpur, Sindh, Pakistan</p>
    </div>
</body>
</html>
