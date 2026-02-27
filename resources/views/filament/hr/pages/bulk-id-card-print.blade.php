<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('ID Cards') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }

            html, body {
                margin: 0 !important;
                padding: 0 !important;
                background-color: white !important;
            }

            .page-break {
                page-break-after: always;
                break-after: page;
            }

            .no-print {
                display: none !important;
            }

            .print-page {
                margin: 0;
                padding: 15mm 0;
            }

            .bulk-container {
                gap: 0 !important;
                display: block !important;
            }
        }

        body {
            background-color: #f3f4f6;
        }
    </style>
</head>

<body class="p-8">
    <div class="no-print fixed top-4 right-4 z-50">
        <button onclick="window.print()"
            class="bg-slate-900 text-white px-6 py-2 rounded-lg shadow-xl font-bold hover:bg-black transition-all">
            {{ __('Print All Cards') }}
        </button>
    </div>

    <div class="bulk-container flex flex-col items-center gap-16 print:gap-0">
        @foreach($employees as $employee)
            <div class="{{ ($loop->iteration % 3 === 0 && !$loop->last) ? 'page-break' : '' }} w-full flex flex-col items-center print-page">
                @include('filament.hr.components.id-card-print', [
                    'record' => $employee, 
                    'isStandalone' => false, 
                    'hideControls' => true
                ])
            </div>
        @endforeach
    </div>
</body>

</html>