<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('Payslip') }} - {{ $payroll->period }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #1a1a1a;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            background: white;
        }

        .page-break {
            page-break-after: always;
        }

        .container {
            width: 100%;
            height: 100%;
        }

        /* Header Styles */
        .header {
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }

        .header h1 {
            font-size: 18pt;
            font-weight: bold;
            margin: 0;
            color: #000;
        }

        .header p {
            margin: 4px 0 0 0;
            color: #4b5563;
            font-size: 10pt;
        }

        /* Info Grid (Simulating 3 columns with table) */
        .info-table {
            width: 100%;
            margin-bottom: 24px;
        }

        .info-table td {
            width: 33.33%;
            vertical-align: top;
        }

        /* Table Styles */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table th {
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 9pt;
            text-transform: uppercase;
        }

        .data-table td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            font-size: 9pt;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .font-semibold {
            font-weight: bold;
        }

        /* Summary Section (ALWAYS BOTTOM) */
        /* Note: In DomPDF, we use a wrapper and vertical alignment if needed, 
           or just normal flow if it follows a large table. 
           To force it to the bottom, we can use a footer or absolute positioning, 
           but here we'll use a table at the end of the content. */
        .summary-wrapper {
            margin-top: 40px;
            border-top: 1px solid #e5e7eb;
            padding-top: 16px;
            width: 100%;
        }

        .summary-table {
            width: 100%;
        }

        .summary-table td {
            width: 50%;
            vertical-align: top;
        }

        .text-lg {
            font-size: 12pt;
        }

        .mt-2 {
            margin-top: 8px;
        }

        .copy-badge {
            display: inline-block;
            padding: 2px 8px;
            background: #f3f4f6;
            color: #4b5563;
            font-size: 8pt;
            font-weight: bold;
            border-radius: 4px;
            margin-top: 4px;
        }
    </style>
</head>

<body>
    @php
        $copies = $dualCopy ? ['company', 'employee'] : ['default'];
    @endphp

    @foreach($copies as $copyType)
        <div class="container {{ !$loop->last ? 'page-break' : '' }}">

            <!-- HEADER -->
            <div class="header">
                <table style="width: 100%;">
                    <tr>
                        <td>
                            <h1>{{ __('Comproller Payslip') }}</h1>
                            <p>{{ __('Payroll statement for the period of :period', ['period' => $payroll->period]) }}</p>
                        </td>
                        <td class="text-right" style="vertical-align: top;">
                            @if($copyType === 'company')
                                <div class="copy-badge">{{ __('Company Copy') }}</div>
                            @elseif($copyType === 'employee')
                                <div class="copy-badge">{{ __('Employee Copy') }}</div>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <!-- EMPLOYEE INFORMATION -->
            <table class="info-table">
                <tr>
                    <td><strong>{{ __('Name') }}:</strong><br>{{ $employee->personalData?->last_name }}
                        {{ $employee->personalData?->first_name }}</td>
                    <td><strong>{{ __('Position') }}:</strong><br>{{ $employee->contract?->position ?? '-' }}</td>
                    <td><strong>{{ __('Base Hourly Rate') }}:</strong><br>{{ number_format((float) $employee->salaryDetail?->base_hourly_rate ?: 0, 0, ',', ' ') }}
                        {{ __('HUF') }}</td>
                </tr>
                <tr>
                    <td style="padding-top: 10px;"><strong>{{ __('Employee ID') }}:</strong><br>#{{ $employee->id }}</td>
                    <td style="padding-top: 10px;"><strong>{{ __('Mother\'s Name') }}:</strong><br>{{ $employee->personalData?->mothers_name ?? '-' }}</td>
                    <td></td>
                </tr>
            </table>

            <!-- MAIN DATA TABLE -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th class="text-right">{{ __('Hours') }}</th>
                        <th class="text-right">{{ __('Daily Wage') }}</th>
                        @if($copyType !== 'employee')
                            <th class="text-left" style="width: 120px;">{{ __('Signature') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($details as $detail)
                        <tr>
                            <td>{{ $detail['date']->format('Y. m. d.') }}</td>
                            <td>{{ $detail['label'] }}</td>
                            <td class="text-right">{{ number_format((float) $detail['duration'], 1, ',', ' ') }}</td>
                            <td class="text-right">{{ number_format((float) $detail['daily_wage'], 0, ',', ' ') }}
                                {{ __('HUF') }}</td>
                            @if($copyType !== 'employee')
                                <td></td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- SUMMARY SECTION -->
            <div class="summary-wrapper">
                <table class="summary-table">
                    <tr>
                        <td>
                            <p>{{ __('Attendance') }}:
                                {{ number_format((float) $payroll->attendance_hours ?: 0, 1, ',', ' ') }} {{ __('h') }}</p>
                            <p>{{ __('Paid leave') }}:
                                {{ number_format((float) $payroll->paid_leave_hours ?: 0, 1, ',', ' ') }} {{ __('h') }}</p>
                            @if($payroll->sick_leave_hours > 0)
                                <p>{{ __('Sick leave') }}:
                                    {{ number_format((float) $payroll->sick_leave_hours ?: 0, 1, ',', ' ') }} {{ __('h') }}</p>
                            @endif
                            <p class="font-semibold mt-2">{{ __('Total Hours') }}:
                                {{ number_format((float) $payroll->total_hours, 1, ',', ' ') }} {{ __('h') }}</p>
                        </td>
                        <td class="text-right">
                            <p><strong>{{ __('Gross Amount') }}:</strong>
                                {{ number_format((float) $payroll->gross_amount, 0, ',', ' ') }} {{ __('HUF') }}</p>
                            @if($payroll->total_deductions > 0)
                                <p><strong>{{ __('Total Deductions') }}:</strong> -
                                    {{ number_format((float) $payroll->total_deductions, 0, ',', ' ') }} {{ __('HUF') }}</p>
                            @endif
                            <p class="text-lg font-bold mt-2">{{ __('Net Amount') }}:
                                {{ number_format((float) $payroll->net_amount, 0, ',', ' ') }} {{ __('HUF') }}</p>
                        </td>
                    </tr>
                </table>
            </div>

        </div>
    @endforeach
</body>

</html>