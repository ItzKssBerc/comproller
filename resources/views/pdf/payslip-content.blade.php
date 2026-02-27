@php
    $copies = ($dualCopy ?? false) ? ['company', 'employee'] : ['default'];
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
                <td><strong>{{ __('Name') }}:</strong><br>{{ $employee->personalData?->last_name }} {{ $employee->personalData?->first_name }}</td>
                <td><strong>{{ __('Position') }}:</strong><br>{{ $employee->contract?->position ?? '-' }}</td>
                <td><strong>{{ __('Base Hourly Rate') }}:</strong><br>{{ number_format((float) $employee->salaryDetail?->base_hourly_rate ?: 0, 0, ',', ' ') }} {{ __('HUF') }}</td>
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
                        <td class="text-right">{{ number_format((float) $detail['daily_wage'], 0, ',', ' ') }} {{ __('HUF') }}</td>
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
                        <p>{{ __('Attendance') }}: {{ number_format((float) $payroll->attendance_hours ?: 0, 1, ',', ' ') }} {{ __('h') }}</p>
                        <p>{{ __('Paid leave') }}: {{ number_format((float) $payroll->paid_leave_hours ?: 0, 1, ',', ' ') }} {{ __('h') }}</p>
                        @if($payroll->sick_leave_hours > 0)
                            <p>{{ __('Sick leave') }}: {{ number_format((float) $payroll->sick_leave_hours ?: 0, 1, ',', ' ') }} {{ __('h') }}</p>
                        @endif
                        <p class="font-semibold mt-2">{{ __('Total Hours') }}: {{ number_format((float) $payroll->total_hours, 1, ',', ' ') }} {{ __('h') }}</p>
                    </td>
                    <td class="text-right">
                        <p><strong>{{ __('Gross Amount') }}:</strong> {{ number_format((float) $payroll->gross_amount, 0, ',', ' ') }} {{ __('HUF') }}</p>
                        @if($payroll->total_deductions > 0)
                            <p><strong>{{ __('Total Deductions') }}:</strong> - {{ number_format((float) $payroll->total_deductions, 0, ',', ' ') }} {{ __('HUF') }}</p>
                        @endif
                        <p class="text-lg font-bold mt-2">{{ __('Net Amount') }}: {{ number_format((float) $payroll->net_amount, 0, ',', ' ') }} {{ __('HUF') }}</p>
                    </td>
                </tr>
            </table>
        </div>

    </div>
@endforeach
