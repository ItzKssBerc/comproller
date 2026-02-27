<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('Contract') }} - Bulk Export</title>
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

        /* Tailwind-inspired Utility Classes */
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

        .text-2xl {
            font-size: 20pt;
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

        .text-blue-600 {
            color: #2563eb;
        }

        .bg-slate-50 {
            background-color: #f8fafc;
        }

        .bg-slate-100 {
            background-color: #f1f5f9;
        }

        .p-2 {
            padding: 8px;
        }

        .p-4 {
            padding: 16px;
        }

        .px-4 {
            padding-left: 16px;
            padding-right: 16px;
        }

        .py-2 {
            padding-top: 8px;
            padding-bottom: 8px;
        }

        .m-0 {
            margin: 0;
        }

        .mt-1 {
            margin-top: 4px;
        }

        .mt-2 {
            margin-top: 8px;
        }

        .mt-4 {
            margin-top: 16px;
        }

        .mt-6 {
            margin-top: 24px;
        }

        .mb-2 {
            margin-bottom: 8px;
        }

        .mb-4 {
            margin-bottom: 16px;
        }

        .mb-6 {
            margin-bottom: 24px;
        }

        .border {
            border: 1px solid #e2e8f0;
        }

        .border-t {
            border-top: 1px solid #e2e8f0;
        }

        .rounded-lg {
            border-radius: 8px;
        }

        .rounded-xl {
            border-radius: 12px;
        }

        /* Custom Layout Blocks */
        .header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 24px;
        }

        .section-header {
            background: #f1f5f9;
            padding: 6px 12px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
            color: #475569;
            margin: 16px 0 8px 0;
            border-radius: 6px;
        }

        .grid-table {
            width: 100%;
            border-collapse: collapse;
        }

        .grid-table td {
            padding: 6px 0;
            vertical-align: top;
            font-size: 9.5pt;
        }

        .label-cell {
            font-weight: bold;
            color: #64748b;
            width: 35%;
        }

        .value-cell {
            color: #0f172a;
        }

        .signature-section {
            margin-top: 40px;
        }

        .signature-box {
            width: 45%;
            padding-top: 40px;
            border-top: 1px solid #cbd5e1;
            text-align: center;
        }

        .spacer {
            width: 10%;
        }

        .copy-badge {
            display: inline-block;
            padding: 2px 10px;
            background: #e2e8f0;
            color: #475569;
            font-size: 7pt;
            font-weight: bold;
            border-radius: 9999px;
            text-transform: uppercase;
            margin-top: 4px;
        }
    </style>
</head>

<body>
    @foreach($employees as $employee)
        @php
            $copies = $dualCopy ? ['company', 'employee'] : ['default'];
        @endphp

        @foreach($copies as $copyType)
            <div class="{{ (!$loop->last || !$loop->parent->last) ? 'page-break' : '' }}">
                <!-- Header Section -->
                <div class="header">
                    <table class="w-full border-collapse">
                        <tr>
                            <td class="text-left">
                                <h1 class="text-xl font-bold text-slate-900 m-0">Comproller</h1>
                                <div class="text-sm text-slate-500">{{ $companyName }}</div>
                                @if($copyType === 'company')
                                    <span class="copy-badge">{{ __('Company Copy') }}</span>
                                @elseif($copyType === 'employee')
                                    <span class="copy-badge">{{ __('Employee Copy') }}</span>
                                @endif
                            </td>
                            <td class="text-right text-2xl font-bold text-slate-700 uppercase">
                                {{ __('Contract') }}
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Parties Section -->
                <div class="section-header">{{ __('Parties') }}</div>
                <div class="px-4">
                    <table class="grid-table">
                        <tr>
                            <td class="label-cell">{{ __('Employer') }}:</td>
                            <td class="value-cell font-bold">{{ $companyName }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">{{ __('Employee') }}:</td>
                            <td class="value-cell">
                                <div class="font-bold text-slate-900">{{ $employee->personalData?->last_name }}
                                    {{ $employee->personalData?->first_name }}</div>
                                <div class="text-sm text-slate-500">
                                    {{ __('Birth Date') }}:
                                    {{ $employee->personalData?->date_of_birth ? \Carbon\Carbon::parse($employee->personalData->date_of_birth)->format('Y. m. d.') : '-' }}<br>
                                    {{ __('Address') }}: {{ $employee->contactData?->address ?? '-' }}
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Employment Details Section -->
                <div class="section-header">{{ __('Employment Details') }}</div>
                <div class="px-4">
                    <table class="grid-table">
                        <tr>
                            <td class="label-cell">{{ __('Position') }}:</td>
                            <td class="value-cell font-bold text-slate-900">{{ $employee->contract?->position ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">{{ __('Shift') }}:</td>
                            <td class="value-cell text-slate-700">{{ $employee->contract?->shift ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">{{ __('Employment Type') }}:</td>
                            <td class="value-cell text-slate-700">{{ __($employee->contract?->employment_type ?? '-') }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">{{ __('Employment Term') }}:</td>
                            <td class="value-cell text-slate-700">{{ __($employee->contract?->employment_term ?? '-') }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">{{ __('Activation Date') }}:</td>
                            <td class="value-cell font-bold text-slate-900">
                                {{ $employee->contract?->scheduled_activation_at?->format('Y. m. d.') ?? '-' }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Remuneration Section -->
                <div class="section-header">{{ __('Remuneration') }}</div>
                <div class="px-4">
                    <table class="grid-table">
                        <tr>
                            <td class="label-cell">{{ __('Base Hourly Rate') }}:</td>
                            <td class="value-cell">
                                <span class="text-xl font-bold text-blue-600">
                                    {{ number_format((float) ($employee->salaryDetail?->base_hourly_rate ?: 0), 0, ',', ' ') }}
                                </span>
                                <span class="text-sm font-bold text-slate-500 uppercase ml-1">{{ __('HUF') }}</span>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Terms Summary Section (Compact) -->
                <div class="mt-6 px-4 text-xs text-slate-500">
                    <p class="m-0">{{ __('Contract terms summary') }}</p>
                </div>

                <!-- Signatures Section -->
                <div class="signature-section">
                    <table class="w-full">
                        <tr>
                            <td class="signature-box">
                                <div class="text-sm font-bold text-slate-700 mb-1">{{ __('Employer Signature') }}</div>
                                <div class="text-xs text-slate-500">{{ $companyName }}</div>
                            </td>
                            <td class="spacer"></td>
                            <td class="signature-box">
                                <div class="text-sm font-bold text-slate-700 mb-1">{{ __('Employee Signature') }}</div>
                                <div class="text-xs text-slate-500">{{ $employee->personalData?->last_name }}
                                    {{ $employee->personalData?->first_name }}</div>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Footer Generation Info -->
                <div
                    style="position: absolute; bottom: 0; left: 0; right: 0; text-align: center; font-size: 7pt; color: #94a3b8;">
                    {{ __('Generated on') }}: {{ now()->format('Y. m. d. H:i') }} | Comproller CONTRACT GENERATOR
                </div>
            </div>
        @endforeach
    @endforeach
</body>

</html>