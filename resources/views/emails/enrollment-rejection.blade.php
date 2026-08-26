<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Enrollment Status Update - SALU Exam Portal</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #dc2626, #0b133d); padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1 style="color: #fff; margin: 0; font-size: 20px;">Shah Abdul Latif University</h1>
        <p style="color: #e2e8f0; margin: 5px 0 0 0; font-size: 13px;">Enrollment & Examination Department</p>
    </div>
    
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-top: none; padding: 25px; border-radius: 0 0 8px 8px;">
        <h2 style="color: #dc2626; margin-top: 0;">Enrollment Application Update</h2>
        <p>Dear <strong>{{ $name }}</strong>,</p>
        <p>Your recent exam enrollment application could not be approved due to the following reason:</p>
        
        <div style="background: #fef2f2; border-left: 4px solid #dc2626; padding: 12px 16px; margin: 15px 0; border-radius: 4px;">
            <strong style="color: #991b1b;">Reason for Rejection:</strong><br>
            <span style="color: #7f1d1d;">{{ $reason }}</span>
        </div>
        
        <p>If you believe this is an error or would like to submit corrected documents, please reach out to the examination department support desk.</p>
        
        <div style="text-align: center; margin: 25px 0;">
            <a href="{{ $contact_link }}" style="display: inline-block; background: #0b133d; color: #ffffff; text-decoration: none; padding: 10px 24px; font-weight: bold; border-radius: 6px; font-size: 14px;">Contact Support</a>
        </div>
        
        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;">
        <p style="font-size: 12px; color: #94a3b8; text-align: center;">Shah Abdul Latif University, Khairpur, Sindh, Pakistan</p>
    </div>
</body>
</html>
