<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('Bulk Payslips') }}</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
        }

        .page-break {
            page-break-after: always;
        }

        /* 
           We include the styles from payslip.blade.php here, 
           or we can use @include('pdf.payslip-styles')
        if we refactor. For now,
        I'll repeat the styles to ensure it works.
 */
        {!! view('pdf.payslip-styles')->render() !!}
    </style>
</head>

<body>
    @foreach($payrollsData as $data)
        <div class="{{ !$loop->last ? 'page-break' : '' }}">
            @include('pdf.payslip-content', $data)
        </div>
    @endforeach
</body>

</html>