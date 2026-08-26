<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Complete College Enrollment List - {{ $college->name }}</title>
    <style>
        @page {
            margin: 10mm;
            size: A4 landscape;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5pt;
            color: #111;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #700c11;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }
        .uni-title {
            font-size: 14pt;
            font-weight: bold;
            color: #700c11;
        }
        .meta-bar {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 5px 10px;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table th {
            background: #0b133d;
            color: #fff;
            padding: 6px;
            text-align: center;
            font-size: 8pt;
            border: 1px solid #0b133d;
        }
        .data-table td {
            padding: 5px;
            text-align: center;
            border: 1px solid #cbd5e1;
            font-size: 8pt;
        }
        .data-table tr:nth-child(even) {
            background: #f8fafc;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 60px; vertical-align: middle; text-align: left;">
                    <img src="{{ public_path('images/salu-logo.png') }}" style="width: 50px; height: 50px;" alt="Logo" />
                </td>
                <td style="vertical-align: middle; text-align: center; padding-right: 60px;">
                    <div class="uni-title">SHAH ABDUL LATIF UNIVERSITY, KHAIRPUR</div>
                    <div style="font-weight: 600;">OFFICIAL ROSTER OF ENROLLED STUDENTS</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="meta-bar">
        <table style="width: 100%;">
            <tr>
                <td><strong>College:</strong> {{ $college->name }} (Code: {{ $college->code }})</td>
                <td><strong>Academic Year:</strong> {{ is_object($academicYear) ? $academicYear->name : $academicYear }}</td>
                <td><strong>Total Records:</strong> {{ count($students) }}</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">S#</th>
                <th style="width: 15%;">Roll Number</th>
                <th style="text-align: left; width: 22%;">Student Name</th>
                <th style="text-align: left; width: 20%;">Father Name</th>
                <th style="width: 15%;">CNIC</th>
                <th style="width: 8%;">Gender</th>
                <th style="width: 15%;">Program</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $idx => $s)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td style="font-weight: bold;">{{ $s['roll_number'] ?? ($s->roll_number ?? 'Pending') }}</td>
                <td style="text-align: left;">{{ $s['name'] ?? ($s->user->full_name ?? 'Student Name') }}</td>
                <td style="text-align: left;">{{ $s['father_name'] ?? ($s->father_name ?? ($s->user->father_name ?? 'N/A')) }}</td>
                <td>{{ $s['cnic'] ?? ($s->user->cnic ?? 'N/A') }}</td>
                <td>{{ $s['gender'] ?? ($s->gender ?? 'N/A') }}</td>
                <td>{{ $s['program'] ?? ($s->program ?? 'BS') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="padding: 20px; color: #64748b;">No enrollment records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
