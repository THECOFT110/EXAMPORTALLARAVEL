<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Result Card - {{ $enrollment->roll_number }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #0f172a;
        }
        .result-wrapper {
            border: 3px double #0b133d;
            padding: 20px;
            border-radius: 8px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #700c11;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .uni-title {
            font-size: 16pt;
            font-weight: bold;
            color: #700c11;
            text-transform: uppercase;
        }
        .sub-title {
            font-size: 10.5pt;
            color: #334155;
            font-weight: 600;
        }
        .badge-title {
            background: #0b133d;
            color: #fff;
            display: inline-block;
            padding: 4px 18px;
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 1px;
            margin-top: 6px;
            border-radius: 4px;
        }
        .bio-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }
        .bio-table td {
            padding: 6px 10px;
            font-size: 9pt;
        }
        .bio-table td.label {
            font-weight: bold;
            color: #475569;
            width: 20%;
        }
        .bio-table td.val {
            font-weight: 600;
            width: 30%;
        }
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .marks-table th {
            background: #0b133d;
            color: #fff;
            padding: 7px;
            font-size: 9pt;
            text-align: center;
            border: 1px solid #0b133d;
        }
        .marks-table td {
            padding: 7px;
            font-size: 9pt;
            text-align: center;
            border: 1px solid #cbd5e1;
        }
        .marks-table tr:nth-child(even) {
            background: #f8fafc;
        }
        .summary-box {
            background: #fffbeb;
            border: 1.5px solid #f59e0b;
            padding: 10px;
            border-radius: 6px;
            margin-top: 10px;
        }
        .summary-table {
            width: 100%;
            text-align: center;
        }
        .summary-table td {
            padding: 4px;
        }
        .signatures {
            margin-top: 50px;
            width: 100%;
        }
        .sig-col {
            display: inline-block;
            width: 45%;
            text-align: center;
            border-top: 1px solid #111;
            padding-top: 4px;
            font-weight: bold;
            font-size: 8.5pt;
        }
    </style>
</head>
<body>
    <div class="result-wrapper">
        <div class="header">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 75px; vertical-align: middle; text-align: center;">
                        <img src="{{ public_path('images/salu-logo.png') }}" style="width: 65px; height: 65px;" alt="SALU Seal" />
                    </td>
                    <td style="vertical-align: middle; text-align: center; padding-right: 75px;">
                        <div class="uni-title">Shah Abdul Latif University</div>
                        <div class="sub-title">Khairpur, Sindh, Pakistan</div>
                        <div class="sub-title">Office of the Controller of Examinations</div>
                        <div class="badge-title">OFFICIAL RESULT CARD / STATEMENT OF MARKS</div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="bio-table">
            <tr>
                <td class="label">Candidate Name:</td>
                <td class="val">{{ $user->full_name }}</td>
                <td class="label">Roll Number:</td>
                <td class="val" style="color: #700c11; font-weight: bold;">{{ $enrollment->roll_number }}</td>
            </tr>
            <tr>
                <td class="label">Father's Name:</td>
                <td class="val">{{ $enrollment->father_name ?? $user->father_name }}</td>
                <td class="label">CNIC:</td>
                <td class="val">{{ $user->cnic }}</td>
            </tr>
            <tr>
                <td class="label">Degree / Program:</td>
                <td class="val">{{ $enrollment->program }}</td>
                <td class="label">Semester / Session:</td>
                <td class="val">{{ $enrollment->semester }} / {{ $enrollment->session }}</td>
            </tr>
            <tr>
                <td class="label">College / Institute:</td>
                <td class="val" colspan="3">{{ $enrollment->college->name ?? 'University Main Campus' }}</td>
            </tr>
        </table>

        <table class="marks-table">
            <thead>
                <tr>
                    <th style="width: 8%;">S#</th>
                    <th style="width: 20%;">Subject Code</th>
                    <th style="text-align: left; width: 42%;">Course / Subject Title</th>
                    <th style="width: 10%;">Max Marks</th>
                    <th style="width: 10%;">Obtained</th>
                    <th style="width: 10%;">Grade</th>
                </tr>
            </thead>
            <tbody>
                @forelse($results as $idx => $res)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td><strong>{{ $res['subject_code'] ?? ($res->subject_code ?? 'N/A') }}</strong></td>
                    <td style="text-align: left;">{{ $res['subject_name'] ?? ($res->subject_name ?? 'Subject') }}</td>
                    <td>{{ $res['total_marks'] ?? ($res->total_marks ?? 100) }}</td>
                    <td style="font-weight: bold;">{{ $res['marks'] ?? ($res->marks ?? 0) }}</td>
                    <td style="font-weight: bold; color: #700c11;">{{ $res['grade'] ?? ($res->grade ?? 'P') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #64748b; padding: 20px;">No detailed subjects recorded.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="summary-box">
            <table class="summary-table">
                <tr>
                    <td><strong>Total Maximum Marks:</strong> {{ $total_marks }}</td>
                    <td><strong>Marks Obtained:</strong> {{ $obtained_marks }}</td>
                    <td><strong>Percentage:</strong> {{ $percentage }}%</td>
                    <td><strong>Status:</strong> <span style="color: #15803d; font-weight: bold;">PASSED</span></td>
                </tr>
            </table>
        </div>

        <div class="signatures">
            <div class="sig-col" style="float: left;">
                Prepared & Verified by<br>
                <span style="font-size: 7.5pt; color: #64748b; font-weight: normal;">Tabulation Section</span>
            </div>
            <div class="sig-col" style="float: right;">
                Controller of Examinations<br>
                <span style="font-size: 7.5pt; color: #64748b; font-weight: normal;">Shah Abdul Latif University, Khairpur</span>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>
</body>
</html>
