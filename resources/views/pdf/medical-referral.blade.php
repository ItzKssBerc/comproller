<!DOCTYPE html>
<html lang="{{ $locale }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('Medical Referral') }} - {{ $employee->personalData?->last_name }}
        {{ $employee->personalData?->first_name }}</title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10pt;
            color: #334155;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        .page-break {
            page-break-after: always;
        }

        .w-full {
            width: 100%;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-xs {
            font-size: 8pt;
        }

        .text-sm {
            font-size: 9pt;
        }

        .text-base {
            font-size: 10pt;
        }

        .text-lg {
            font-size: 11pt;
        }

        .text-xl {
            font-size: 14pt;
        }

        .font-bold {
            font-weight: bold;
        }

        .text-slate-900 {
            color: #0f172a;
        }

        .text-slate-700 {
            color: #334155;
        }

        .text-slate-500 {
            color: #64748b;
        }

        .text-slate-400 {
            color: #94a3b8;
        }

        .border-b-2 {
            border-bottom: 2px solid #0f172a;
        }

        .mb-2 {
            margin-bottom: 8px;
        }

        .mb-4 {
            margin-bottom: 16px;
        }

        .mb-8 {
            margin-bottom: 32px;
        }

        .mt-4 {
            margin-top: 16px;
        }

        .mt-8 {
            margin-top: 32px;
        }

        .mt-12 {
            margin-top: 48px;
        }

        .section-header {
            background: #f1f5f9;
            padding: 8px 16px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
            color: #475569;
            margin: 24px 0 12px 0;
            border-radius: 6px;
        }

        .grid-table {
            width: 100%;
            border-collapse: collapse;
        }

        .grid-table td {
            padding: 6px 0;
            vertical-align: top;
        }

        .label-cell {
            font-weight: bold;
            color: #64748b;
            width: 35%;
        }

        .value-cell {
            color: #0f172a;
        }

        .signature-box {
            width: 45%;
            padding-top: 40px;
            border-top: 1px solid #cbd5e1;
            text-align: center;
            font-size: 9pt;
        }

        .stamp-box {
            width: 120px;
            height: 80px;
            border: 1px dashed #cbd5e1;
            margin-top: 10px;
            display: inline-block;
        }

        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #0f172a;
            margin-right: 8px;
            vertical-align: middle;
        }

        .opinion-card {
            border: 1px solid #e2e8f0;
            padding: 20px;
            border-radius: 12px;
            background-color: #f8fafc;
            margin-top: 20px;
        }
    </style>
</head>

<body class="text-slate-700">
    <!-- PAGE 1: REFERRAL -->
    <div class="page-break">
        <!-- Header -->
        <div class="border-b-2 mb-8" style="padding-bottom: 12px;">
            <table class="w-full">
                <tr>
                    <td class="text-left">
                        <div class="text-xl font-bold text-slate-900 m-0">Comproller</div>
                        <div class="text-sm text-slate-500">{{ $companyName }}</div>
                    </td>
                    <td class="text-right text-lg font-bold text-slate-700 uppercase" style="width: 70%;">
                        {{ __('Medical Referral') }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- Employee Info -->
        <div class="section-header">{{ __('Employee Data') }}</div>
        <div style="padding-left: 16px;">
            <table class="grid-table">
                <tr>
                    <td class="label-cell">{{ __('Name') }}:</td>
                    <td class="value-cell font-bold">{{ $employee->personalData?->last_name }}
                        {{ $employee->personalData?->first_name }}</td>
                </tr>
                <tr>
                    <td class="label-cell">{{ __('Birth Date') }}:</td>
                    <td class="value-cell">
                        {{ $employee->personalData?->date_of_birth ? \Carbon\Carbon::parse($employee->personalData->date_of_birth)->format('Y. m. d.') : '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="label-cell">{{ __('Mother\'s Name') }}:</td>
                    <td class="value-cell">{{ $employee->personalData?->mothers_name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">{{ __('Social Security Number (TAJ)') }}:</td>
                    <td class="value-cell font-bold">{{ $employee->financialData?->social_security_number ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">{{ __('Address') }}:</td>
                    <td class="value-cell">{{ $employee->contactData?->address ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <!-- Employment Info -->
        <div class="section-header">{{ __('Employment Details') }}</div>
        <div style="padding-left: 16px;">
            <table class="grid-table">
                <tr>
                    <td class="label-cell">{{ __('Position') }}:</td>
                    <td class="value-cell font-bold">{{ $employee->contract?->position ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">{{ __('Job Hazard Class') }}:</td>
                    <td class="value-cell">{{ $employee->contract?->hazard_class ?? 'A' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">{{ __('Examination Type') }}:</td>
                    <td class="value-cell font-bold">{{ __($examinationType) }}</td>
                </tr>
            </table>
        </div>

        <div class="mt-8 px-4 text-sm text-slate-600 italic" style="text-align: justify; line-height: 1.6;">
            <p>A fenti munkavállalót a munkaköri, szakmai, illetve személyi higiénés alkalmasság orvosi vizsgálatáról és
                véleményezéséről szóló 33/1998. (VI. 24.) NM rendelet alapján beutalom alkalmassági vizsgálatra.</p>
        </div>

        <!-- Referral Signatures -->
        <div class="mt-12">
            <table class="w-full">
                <tr>
                    <td style="width: 50%; vertical-align: bottom;" class="text-sm text-slate-500">
                        {{ __('Date') }}: {{ now()->format('Y. m. d.') }}
                    </td>
                    <td class="signature-box">
                        <div class="font-bold mb-2 text-slate-900">{{ __('Employer Signature') }}</div>
                        <div class="text-xs text-slate-400">{{ $companyName }}</div>
                        <div class="stamp-box"></div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div style="position: absolute; bottom: 0; left: 0; right: 0; text-align: center;"
            class="text-xs text-slate-400">
            Comproller MEDICAL REFERRAL | {{ now()->format('Y. m. d. H:i') }}
        </div>
    </div>

    <!-- PAGE 2: OPINION -->
    <div>
        <!-- Header -->
        <div class="border-b-2 mb-8" style="padding-bottom: 12px;">
            <table class="w-full">
                <tr>
                    <td class="text-left">
                        <div class="text-xl font-bold text-slate-900 m-0">Comproller</div>
                    </td>
                    <td class="text-right text-lg font-bold text-slate-700 uppercase" style="width: 70%;">
                        {{ __('Medical Opinion') }}
                    </td>
                </tr>
            </table>
        </div>

        <div style="padding-left: 16px;">
            <table class="grid-table">
                <tr>
                    <td class="label-cell" style="width: 25%;">{{ __('Employee') }}:</td>
                    <td class="value-cell font-bold" style="width: 45%;">{{ $employee->personalData?->last_name }}
                        {{ $employee->personalData?->first_name }}</td>
                    <td class="label-cell" style="width: 15%;">{{ __('TAJ') }}:</td>
                    <td class="value-cell">{{ $employee->financialData?->social_security_number ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <div class="opinion-card">
            <div class="font-bold text-slate-900 mb-6 uppercase tracking-wider text-sm">{{ __('Medical Opinion') }}:
            </div>
            <table class="w-full mb-8">
                <tr>
                    <td style="width: 33%;">
                        <div class="mb-4"><span class="checkbox"></span> <span class="font-bold">{{ __('Fit') }}</span>
                        </div>
                    </td>
                    <td style="width: 33%;">
                        <div class="mb-4"><span class="checkbox"></span> <span
                                class="font-bold">{{ __('Unfit') }}</span></div>
                    </td>
                    <td>
                        <div class="mb-4"><span class="checkbox"></span> <span
                                class="font-bold">{{ __('Fit with restrictions') }}</span></div>
                    </td>
                </tr>
            </table>
            <div class="mb-4">
                <div style="margin-bottom: 15px;">
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-tighter mb-1">{{ __('Restrictions / Conditions') }}</div>
                    <div style="border-bottom: 1px dotted #cbd5e1; height: 0.5em;"></div>
                </div>
                <div style="margin-bottom: 15px;">
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-tighter mb-1">{{ __('Required Follow-up Tests') }}</div>
                    <div style="border-bottom: 1px dotted #cbd5e1; height: 0.5em;"></div>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-tighter mb-1">{{ __('Remarks') }}</div>
                    <div style="border-bottom: 1px dotted #cbd5e1; height: 0.5em;"></div>
                </div>
            </div>
            <div class="text-base">
                <span class="text-slate-500 font-bold">{{ __('Next examination due date') }}:</span> <span
                    class="text-slate-300">..................................................</span>
            </div>
        </div>

        <!-- Opinion Signatures -->
        <div class="mt-12">
            <table class="w-full">
                <tr>
                    <td style="width: 50%; vertical-align: bottom;" class="text-sm text-slate-500">
                        {{ __('Date') }}: <span class="text-slate-300">....................................</span>
                    </td>
                    <td class="signature-box">
                        <div class="font-bold mb-2 text-slate-900">{{ __('Doctor Signature') }}</div>
                        <div class="stamp-box"></div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div style="position: absolute; bottom: 0; left: 0; right: 0; text-align: center;"
            class="text-xs text-slate-400">
            Comproller MEDICAL OPINION | {{ now()->format('Y. m. d. H:i') }}
        </div>
    </div>
</body>

</html>