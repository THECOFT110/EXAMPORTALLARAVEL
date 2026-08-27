<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admit Card - {{ $enrollment->roll_number ?? 'SALU' }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #111;
            margin: 0;
            padding: 0;
        }
        .admit-wrapper {
            border: 3px double #700c11;
            padding: 18px;
            border-radius: 8px;
            background: #fff;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #700c11;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .uni-name {
            font-size: 16pt;
            font-weight: bold;
            color: #700c11;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .sub-header {
            font-size: 11pt;
            font-weight: 600;
            color: #0b133d;
            margin-top: 2px;
        }
        .doc-title {
            background: #0b133d;
            color: #fff;
            display: inline-block;
            padding: 4px 20px;
            font-size: 12pt;
            font-weight: bold;
            letter-spacing: 1px;
            margin-top: 8px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .content-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .details-col {
            vertical-align: top;
            width: 72%;
        }
        .photo-col {
            vertical-align: top;
            width: 28%;
            text-align: right;
        }
        .photo-box {
            width: 120px;
            height: 145px;
            border: 2px solid #64748b;
            background: #f8fafc;
            display: inline-block;
            text-align: center;
            border-radius: 4px;
            overflow: hidden;
        }
        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .photo-placeholder {
            line-height: 145px;
            font-size: 9pt;
            color: #94a3b8;
        }
        .info-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .info-grid td {
            padding: 5px 4px;
            font-size: 9.5pt;
        }
        .info-grid td.label {
            width: 38%;
            font-weight: bold;
            color: #334155;
        }
        .info-grid td.value {
            width: 62%;
            font-weight: 600;
            color: #0f172a;
            border-bottom: 1px dotted #cbd5e1;
        }
        .seating-box {
            background: #f0fdf4;
            border: 2px solid #16a34a;
            border-radius: 6px;
            padding: 10px;
            margin: 15px 0;
        }
        .seating-box table {
            width: 100%;
        }
        .seating-box td {
            padding: 3px 6px;
        }
        .rules-box {
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            border-radius: 6px;
            padding: 10px 14px;
            margin-top: 15px;
            font-size: 8pt;
            color: #334155;
        }
        .rules-box ol {
            margin: 4px 0 0 15px;
            padding: 0;
        }
        .rules-box li {
            margin-bottom: 3px;
        }
        .footer-sigs {
            margin-top: 40px;
            width: 100%;
        }
        .sig-item {
            display: inline-block;
            width: 45%;
            text-align: center;
            border-top: 1px solid #111;
            padding-top: 5px;
            font-weight: bold;
            font-size: 8.5pt;
        }
    </style>
</head>
<body>
    <div class="admit-wrapper">
        <div class="header">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 75px; vertical-align: middle; text-align: center;">
                        <img src="{{ public_path('images/salu-logo.png') }}" style="width: 65px; height: 65px;" alt="SALU Seal" />
                    </td>
                    <td style="vertical-align: middle; text-align: center; padding-right: 75px;">
                        <div class="uni-name">Shah Abdul Latif University</div>
                        <div class="sub-header">Office of the Controller of Examinations</div>
                        <div style="font-size: 8.5pt; color: #475569;">Khairpur Mir's, Sindh, Pakistan</div>
                        <div class="doc-title">Examination Admit Card</div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="content-table">
            <tr>
                <td class="details-col">
                    <table class="info-grid">
                        <tr>
                            <td class="label">Roll Number:</td>
                            <td class="value" style="font-size: 11pt; color: #700c11;"><strong>{{ $enrollment->roll_number ?? 'Pending Allocation' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="label">Candidate Name:</td>
                            <td class="value">{{ $user->full_name }}</td>
                        </tr>
                        <tr>
                            <td class="label">Father's Name:</td>
                            <td class="value">{{ $enrollment->father_name ?? $user->father_name }}</td>
                        </tr>
                        <tr>
                            <td class="label">CNIC / B-Form:</td>
                            <td class="value">{{ $user->cnic }}</td>
                        </tr>
                        <tr>
                            <td class="label">Program:</td>
                            <td class="value">{{ $enrollment->program }}</td>
                        </tr>
                        <tr>
                            <td class="label">Academic Session:</td>
                            <td class="value">{{ $enrollment->session }} (Semester {{ $enrollment->semester }})</td>
                        </tr>
                        <tr>
                            <td class="label">Institution / College:</td>
                            <td class="value">{{ $enrollment->college->name ?? 'Main Campus' }}</td>
                        </tr>
                    </table>
                </td>
                <td class="photo-col">
                    <div class="photo-box">
                        @if($enrollment->photo_url)
                            <img src="{{ public_path($enrollment->photo_url) }}" alt="Candidate Photo">
                        @else
                            <div class="photo-placeholder">Passport Photo</div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <div class="seating-box">
            <table class="info-grid">
                <tr>
                    <td class="label" style="width: 25%;"><strong>Examination Center:</strong></td>
                    <td class="value" style="width: 75%; color: #15803d; font-size: 10pt;">
                        <strong>{{ $seat->exam_center ?? ($enrollment->college->name ?? 'Shah Abdul Latif University Center') }}</strong>
                    </td>
                </tr>
                <tr>
                    <td class="label"><strong>Room / Hall No:</strong></td>
                    <td class="value">{{ $seat->room_no ?? 'Allocated at Center' }}</td>
                </tr>
                <tr>
                    <td class="label"><strong>Seat / Desk No:</strong></td>
                    <td class="value" style="color: #700c11; font-size: 10.5pt;"><strong>{{ $seat->seat_no ?? 'N/A' }}</strong></td>
                </tr>
                <tr>
                    <td class="label"><strong>Commencement Date:</strong></td>
                    <td class="value">{{ $seat->exam_date ? $seat->exam_date->format('d F, Y') : now()->addWeeks(2)->format('d F, Y') }}</td>
                </tr>
            </table>
        </div>

        <div class="rules-box">
            <strong style="color: #700c11;">IMPORTANT EXAMINATION INSTRUCTIONS:</strong>
            <ol>
                <li>Candidate must bring this original <strong>Admit Card</strong> along with original <strong>CNIC</strong> to each examination session.</li>
                <li>Mobile phones, smart watches, bags, and unauthorized electronic devices are strictly prohibited in the exam hall.</li>
                <li>Candidates must report to the examination center at least 30 minutes prior to the scheduled exam commencement.</li>
                <li>No candidate will be allowed to enter the examination hall after 15 minutes of commencement time.</li>
                <li>Any student found using unfair means (UFM) will be disqualified from all subsequent examinations.</li>
            </ol>
        </div>

        <div class="footer-sigs">
            <div class="sig-item" style="float: left;">
                Candidate's Signature<br>
                <span style="font-size: 7.5pt; font-weight: normal; color: #666;">(To be signed in presence of Invigilator)</span>
            </div>
            <div class="sig-item" style="float: right;">
                Controller of Examinations<br>
                <span style="font-size: 7.5pt; font-weight: normal; color: #666;">Shah Abdul Latif University, Khairpur</span>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>
</body>
</html>
