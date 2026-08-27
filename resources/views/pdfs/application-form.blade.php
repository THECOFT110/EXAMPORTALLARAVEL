<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enrollment Application - {{ $enrollment->id }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9.5pt;
            line-height: 1.35;
            color: #111;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #700c11;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .uni-title {
            font-size: 15pt;
            font-weight: bold;
            color: #700c11;
            text-transform: uppercase;
        }
        .form-title {
            background: #0b133d;
            color: #fff;
            display: inline-block;
            padding: 4px 18px;
            font-size: 11pt;
            font-weight: bold;
            margin-top: 6px;
            border-radius: 4px;
        }
        .section-hdr {
            background: #f1f5f9;
            border-left: 4px solid #700c11;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 10pt;
            color: #0b133d;
            margin: 12px 0 8px 0;
        }
        .grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .grid-table td {
            padding: 4px 6px;
            font-size: 9pt;
            vertical-align: top;
        }
        .grid-table td.lbl {
            width: 25%;
            font-weight: bold;
            color: #475569;
        }
        .grid-table td.val {
            width: 25%;
            font-weight: 600;
            border-bottom: 1px dotted #cbd5e1;
        }
        .photo-box {
            width: 110px;
            height: 130px;
            border: 2px solid #94a3b8;
            text-align: center;
            line-height: 130px;
            color: #94a3b8;
            font-size: 8pt;
            float: right;
        }
        .declaration {
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            background: #f8fafc;
            border-radius: 4px;
            font-size: 8pt;
            line-height: 1.3;
            margin-top: 15px;
        }
        .signatures {
            margin-top: 40px;
            width: 100%;
        }
        .sig {
            display: inline-block;
            width: 45%;
            text-align: center;
            border-top: 1px solid #111;
            padding-top: 4px;
            font-size: 8.5pt;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 70px; vertical-align: middle; text-align: center;">
                    <img src="{{ public_path('images/salu-logo.png') }}" style="width: 60px; height: 60px;" alt="SALU Seal" />
                </td>
                <td style="vertical-align: middle; text-align: center; padding-right: 70px;">
                    <div class="uni-title">Shah Abdul Latif University, Khairpur</div>
                    <div>Directorate of Admissions & Examination Management</div>
                    <div class="form-title">STUDENT ENROLLMENT APPLICATION FORM</div>
                </td>
            </tr>
        </table>
    </div>

    <div style="margin-bottom: 15px;">
        <div class="photo-box">
            @if($enrollment->photo_url)
                <img src="{{ public_path($enrollment->photo_url) }}" style="width: 100%; height: 100%; object-fit: cover;">
            @else
                Passport Size Photo
            @endif
        </div>
        <table class="grid-table" style="width: 75%;">
            <tr>
                <td class="lbl">Application ID:</td>
                <td class="val" style="color: #700c11;">{{ substr($enrollment->id, 0, 13) }}</td>
            </tr>
            <tr>
                <td class="lbl">Enrollment Status:</td>
                <td class="val"><strong style="color: #15803d;">{{ $enrollment->status }}</strong></td>
            </tr>
            <tr>
                <td class="lbl">Program Applied:</td>
                <td class="val">{{ $enrollment->program }}</td>
            </tr>
            <tr>
                <td class="lbl">Academic Session:</td>
                <td class="val">{{ $enrollment->session }} (Sem {{ $enrollment->semester }})</td>
            </tr>
            <tr>
                <td class="lbl">Affiliated College:</td>
                <td class="val">{{ $enrollment->college->name ?? 'Main Campus' }}</td>
            </tr>
        </table>
        <div style="clear: both;"></div>
    </div>

    <div class="section-hdr">1. Personal & Demographic Details</div>
    <table class="grid-table">
        <tr>
            <td class="lbl">Student Name:</td>
            <td class="val">{{ $user->full_name }}</td>
            <td class="lbl">Father's Name:</td>
            <td class="val">{{ $enrollment->father_name ?? $user->father_name }}</td>
        </tr>
        <tr>
            <td class="lbl">Surname:</td>
            <td class="val">{{ $enrollment->surname ?? 'N/A' }}</td>
            <td class="lbl">Gender:</td>
            <td class="val">{{ $enrollment->gender }}</td>
        </tr>
        <tr>
            <td class="lbl">CNIC / B-Form:</td>
            <td class="val">{{ $user->cnic }}</td>
            <td class="lbl">Date of Birth:</td>
            <td class="val">{{ $enrollment->dob ? $enrollment->dob->format('d-M-Y') : 'N/A' }}</td>
        </tr>
        <tr>
            <td class="lbl">Mobile / WhatsApp:</td>
            <td class="val">{{ $enrollment->contact_number ?? $user->phone }}</td>
            <td class="lbl">Email Address:</td>
            <td class="val">{{ $user->email }}</td>
        </tr>
        <tr>
            <td class="lbl">Nationality:</td>
            <td class="val">{{ $enrollment->nationality ?? 'Pakistani' }}</td>
            <td class="lbl">Religion:</td>
            <td class="val">{{ $enrollment->religion ?? 'Islam' }}</td>
        </tr>
        <tr>
            <td class="lbl">Domicile Province:</td>
            <td class="val">{{ $enrollment->domicile_province ?? 'Sindh' }}</td>
            <td class="lbl">Domicile District:</td>
            <td class="val">{{ $enrollment->domicile_district ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="lbl">Permanent Address:</td>
            <td class="val" colspan="3">{{ $enrollment->address }}</td>
        </tr>
    </table>

    <div class="section-hdr">2. Previous Academic Credentials</div>
    <table class="grid-table">
        <tr>
            <td class="lbl">Last Exam Passed:</td>
            <td class="val">{{ $enrollment->last_exam_details ?? 'HSC / Intermediate' }}</td>
            <td class="lbl">Passing Year:</td>
            <td class="val">{{ $enrollment->passing_year ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="lbl">Board / University:</td>
            <td class="val">{{ $enrollment->name_of_board ?? ($enrollment->board ?? 'BISE') }}</td>
            <td class="lbl">Grade / Division:</td>
            <td class="val">{{ $enrollment->division_obtained ?? 'N/A' }}</td>
        </tr>
    </table>

    <div class="declaration">
        <strong>STUDENT UNDERTAKING & DECLARATION:</strong><br>
        I hereby solemnly affirm and declare that the statements made by me in this application are correct and complete to the best of my knowledge and belief. I agree to abide by the rules and regulations of Shah Abdul Latif University, Khairpur.
    </div>

    <div class="signatures">
        <div class="sig" style="float: left;">Applicant's Signature</div>
        <div class="sig" style="float: right;">Principal / Head of Department Stamp & Sign</div>
        <div style="clear: both;"></div>
    </div>
</body>
</html>
