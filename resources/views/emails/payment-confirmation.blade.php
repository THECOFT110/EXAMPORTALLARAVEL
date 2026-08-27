<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Received - SALU Exam Portal</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #15803d, #0b133d); padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1 style="color: #fff; margin: 0; font-size: 20px;">Shah Abdul Latif University</h1>
        <p style="color: #e2e8f0; margin: 5px 0 0 0; font-size: 13px;">Accounts & Examination Department</p>
    </div>
    
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-top: none; padding: 25px; border-radius: 0 0 8px 8px;">
        <h2 style="color: #15803d; margin-top: 0;">Fee Payment Received</h2>
        <p>Dear <strong>{{ $name }}</strong>,</p>
        <p>This is to confirm that your fee payment has been successfully recorded in the examination system.</p>
        
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
            <tr>
                <td style="padding: 8px 12px; font-weight: bold; color: #475569;">Challan Number:</td>
                <td style="padding: 8px 12px; color: #0b133d; font-weight: bold;">{{ $challan_number }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 12px; font-weight: bold; color: #475569;">Amount Paid:</td>
                <td style="padding: 8px 12px; color: #15803d; font-weight: bold; font-size: 16px;">PKR {{ number_format($amount, 2) }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 12px; font-weight: bold; color: #475569;">Payment Status:</td>
                <td style="padding: 8px 12px; color: #15803d; font-weight: bold;">PAID / VERIFIED</td>
            </tr>
        </table>
        
        <p style="font-size: 13px; color: #64748b;">Please retain this email receipt for your records.</p>
        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;">
        <p style="font-size: 12px; color: #94a3b8; text-align: center;">Shah Abdul Latif University, Khairpur, Sindh, Pakistan</p>
    </div>
</body>
</html>
