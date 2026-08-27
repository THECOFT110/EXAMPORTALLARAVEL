<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Results Published - SALU Exam Portal</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #0b133d, #700c11); padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1 style="color: #fff; margin: 0; font-size: 20px;">Shah Abdul Latif University</h1>
        <p style="color: #e2e8f0; margin: 5px 0 0 0; font-size: 13px;">Office of the Controller of Examinations</p>
    </div>
    
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-top: none; padding: 25px; border-radius: 0 0 8px 8px;">
        <h2 style="color: #0b133d; margin-top: 0;">Examination Results Announced</h2>
        <p>Dear <strong>{{ $name }}</strong>,</p>
        <p>Your examination results for Roll Number <strong>{{ $roll_number }}</strong> have been published.</p>
        <p>You can check your subject-wise marks and download your official Statement of Marks / Result Card online.</p>
        
        <div style="text-align: center; margin: 25px 0;">
            <a href="{{ $result_link }}" style="display: inline-block; background: #0b133d; color: #ffffff; text-decoration: none; padding: 12px 28px; font-weight: bold; border-radius: 6px; font-size: 15px;">View Results</a>
        </div>
        
        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;">
        <p style="font-size: 12px; color: #94a3b8; text-align: center;">Shah Abdul Latif University, Khairpur, Sindh, Pakistan</p>
    </div>
</body>
</html>
