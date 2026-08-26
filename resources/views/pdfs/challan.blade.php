<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fee Challan - {{ $fee->challan_number }}</title>
    <style>
        @page {
            margin: 12mm 8mm;
            size: A4 landscape;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #111;
            margin: 0;
            padding: 0;
        }
        .challan-container {
            width: 100%;
            display: table;
            table-layout: fixed;
        }
        .challan-copy {
            display: table-cell;
            width: 32%;
            padding: 0 1.5%;
            border-right: 1px dashed #666;
            vertical-align: top;
        }
        .challan-copy:last-child {
            border-right: none;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #700c11;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }
        .uni-name {
            font-size: 10pt;
            font-weight: bold;
            color: #700c11;
            text-transform: uppercase;
        }
        .sub-name {
            font-size: 7.5pt;
            color: #333;
        }
        .copy-title {
            background-color: #0b133d;
            color: #fff;
            padding: 3px 0;
            font-size: 8.5pt;
            font-weight: bold;
            text-align: center;
            margin: 6px 0;
            border-radius: 3px;
        }
        .bank-info {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 4px;
            font-size: 7.5pt;
            margin-bottom: 6px;
            border-radius: 3px;
            text-align: center;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .info-table td {
            padding: 2.5px 0;
            font-size: 8pt;
            vertical-align: top;
        }
        .info-table td.label {
            width: 40%;
            font-weight: bold;
            color: #475569;
        }
        .info-table td.val {
            width: 60%;
            font-weight: 600;
            color: #0f172a;
        }
        .amount-box {
            background-color: #fffbeb;
            border: 1.5px solid #f59e0b;
            padding: 6px;
            text-align: center;
            margin: 8px 0;
            border-radius: 4px;
        }
        .amount-num {
            font-size: 12pt;
            font-weight: bold;
            color: #700c11;
        }
        .signatures {
            margin-top: 25px;
            width: 100%;
        }
        .sig-box {
            display: inline-block;
            width: 48%;
            text-align: center;
            border-top: 1px solid #333;
            font-size: 7.5pt;
            padding-top: 3px;
        }
        .barcode {
            text-align: center;
            font-family: monospace;
            font-size: 8pt;
            letter-spacing: 2px;
            margin: 4px 0;
        }
        .notes {
            font-size: 6.5pt;
            color: #64748b;
            margin-top: 6px;
            line-height: 1.2;
        }
    </style>
</head>
<body>
    <div class="challan-container">
        @php
            $copies = ['Bank Copy', 'University Copy', 'Student Copy'];
        @endphp

        @foreach($copies as $copy)
        <div class="challan-copy">
            <div class="header">
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 2px;">
                    <tr>
                        <td style="width: 32px; vertical-align: middle; text-align: center;">
                            <img src="{{ public_path('images/salu-logo.png') }}" style="width: 28px; height: 28px;" alt="Logo" />
                        </td>
                        <td style="vertical-align: middle; text-align: center;">
                            <div class="uni-name">Shah Abdul Latif University</div>
                            <div class="sub-name">Khairpur, Sindh, Pakistan</div>
                        </td>
                    </tr>
                </table>
                <div class="sub-name" style="margin-top: 2px;"><strong>Examination & Admission Department</strong></div>
            </div>

            <div class="copy-title">{{ $copy }}</div>

            <div class="bank-info">
                <strong>HBL A/C: 00427900123403</strong><br>
                Payable at any HBL Branch across Pakistan
            </div>

            <div class="barcode">*{{ $fee->challan_number }}*</div>

            <table class="info-table">
                <tr>
                    <td class="label">Challan No:</td>
                    <td class="val" style="color: #700c11;">{{ $fee->challan_number }}</td>
                </tr>
                <tr>
                    <td class="label">Issue Date:</td>
                    <td class="val">{{ $fee->created_at ? $fee->created_at->format('d-M-Y') : now()->format('d-M-Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Due Date:</td>
                    <td class="val" style="color: #dc2626;">{{ $fee->due_date ? $fee->due_date->format('d-M-Y') : 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Student Name:</td>
                    <td class="val">{{ $user->full_name }}</td>
                </tr>
                <tr>
                    <td class="label">Father's Name:</td>
                    <td class="val">{{ $enrollment->father_name ?? $user->father_name }}</td>
                </tr>
                <tr>
                    <td class="label">CNIC:</td>
                    <td class="val">{{ $user->cnic }}</td>
                </tr>
                <tr>
                    <td class="label">Program:</td>
                    <td class="val">{{ $enrollment->program }}</td>
                </tr>
                <tr>
                    <td class="label">Session/Semester:</td>
                    <td class="val">{{ $enrollment->session }} / Sem {{ $enrollment->semester }}</td>
                </tr>
                @if($enrollment->roll_number)
                <tr>
                    <td class="label">Roll Number:</td>
                    <td class="val">{{ $enrollment->roll_number }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label">College:</td>
                    <td class="val">{{ $enrollment->college->name ?? 'Main Campus / Affiliated' }}</td>
                </tr>
            </table>

            <div class="amount-box">
                <div style="font-size: 7.5pt; color: #64748b;">TOTAL AMOUNT PAYABLE</div>
                <div class="amount-num">PKR {{ number_format($fee->amount, 2) }}</div>
                <div style="font-size: 7pt; font-style: italic;">Rupees {{ ucwords(Number::spell((int)$fee->amount)) }} Only</div>
            </div>

            <div class="signatures">
                <div class="sig-box" style="float: left;">Depositor Sign</div>
                <div class="sig-box" style="float: right;">Bank Cashier & Stamp</div>
                <div style="clear: both;"></div>
            </div>

            <div class="notes">
                * Note: Fee once deposited is non-refundable and non-transferable.<br>
                * Bank charges may apply as per bank schedule.
            </div>
        </div>
        @endforeach
    </div>
</body>
</html>
