<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Card - {{ $enrollment->roll_number }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            color: #111;
        }
        .card-frame {
            border: 3px solid #700c11;
            padding: 16px;
            border-radius: 10px;
            max-width: 500px;
            margin: 40px auto;
            background: #fff;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #700c11;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .uni-name {
            font-size: 13pt;
            font-weight: bold;
            color: #700c11;
            text-transform: uppercase;
        }
        .card-title {
            background: #0b133d;
            color: #fff;
            padding: 3px 12px;
            font-size: 9.5pt;
            font-weight: bold;
            display: inline-block;
            border-radius: 4px;
            margin-top: 4px;
        }
        .card-table {
            width: 100%;
        }
        .data-grid td {
            padding: 4px;
            font-size: 9pt;
        }
        .data-grid td.lbl {
            font-weight: bold;
            color: #475569;
            width: 40%;
        }
        .data-grid td.val {
            font-weight: 600;
            width: 60%;
        }
        .photo-box {
            width: 90px;
            height: 110px;
            border: 1.5px solid #64748b;
            text-align: center;
            line-height: 110px;
            color: #94a3b8;
            font-size: 8pt;
            float: right;
            border-radius: 4px;
            overflow: hidden;
        }
        .footer-sig {
            margin-top: 25px;
            border-top: 1px solid #333;
            text-align: right;
            padding-top: 4px;
            font-size: 8pt;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="card-frame">
        <div class="header">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 55px; vertical-align: middle; text-align: center;">
                        <img src="{{ public_path('images/salu-logo.png') }}" style="width: 48px; height: 48px;" alt="SALU Seal" />
                    </td>
                    <td style="vertical-align: middle; text-align: center; padding-right: 55px;">
                        <div class="uni-name">Shah Abdul Latif University</div>
                        <div style="font-size: 8pt; color: #475569;">Khairpur Mir's, Sindh, Pakistan</div>
                        <div class="card-title">STUDENT REGISTRATION CARD</div>
                    </td>
                </tr>
            </table>
        </div>

        <div style="margin-bottom: 10px;">
            <div class="photo-box">
                @php
                    $photoPath = null;
                    if (!empty($enrollment->photo_url)) {
                        $cleanUrl = ltrim(parse_url($enrollment->photo_url, PHP_URL_PATH) ?? $enrollment->photo_url, '/');
                        if (file_exists(public_path($cleanUrl))) {
                            $photoPath = public_path($cleanUrl);
                        } elseif (file_exists(storage_path('app/public/' . preg_replace('#^storage/#', '', $cleanUrl)))) {
                            $photoPath = storage_path('app/public/' . preg_replace('#^storage/#', '', $cleanUrl));
                        }
                    }
                @endphp
                @if($photoPath)
                    <img src="{{ $photoPath }}" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    Photo
                @endif
            </div>

            <table class="card-table data-grid" style="width: 72%;">
                <tr>
                    <td class="lbl">Enrollment No:</td>
                    <td class="val" style="color: #700c11; font-weight: bold;">{{ $enrollment->roll_number ?? 'Pending' }}</td>
                </tr>
                <tr>
                    <td class="lbl">Student Name:</td>
                    <td class="val">{{ $user->full_name }}</td>
                </tr>
                <tr>
                    <td class="lbl">Father's Name:</td>
                    <td class="val">{{ $enrollment->father_name ?? $user->father_name }}</td>
                </tr>
                <tr>
                    <td class="lbl">CNIC:</td>
                    <td class="val">{{ $user->cnic }}</td>
                </tr>
                <tr>
                    <td class="lbl">Program:</td>
                    <td class="val">{{ $enrollment->program }}</td>
                </tr>
                <tr>
                    <td class="lbl">Session:</td>
                    <td class="val">{{ $enrollment->session }}</td>
                </tr>
                <tr>
                    <td class="lbl">College:</td>
                    <td class="val">{{ $college->name ?? ($enrollment->college->name ?? 'Main Campus') }}</td>
                </tr>
            </table>
            <div style="clear: both;"></div>
        </div>

        <div class="footer-sig">
            Registrar / Authorized Officer<br>
            <span style="font-weight: normal; color: #64748b; font-size: 7pt;">Shah Abdul Latif University</span>
        </div>
    </div>
</body>
</html>
