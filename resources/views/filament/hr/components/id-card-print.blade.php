@php
    $record = $record ?? $getRecord();
    $personal = $record->personalData;
    $contract = $record->contract;
    $identification = $record->identification;

    $lang = $lang ?? request('lang', app()->getLocale());

    $lastName = $personal?->last_name ?: '';
    $firstName = $personal?->first_name ?: '';
    $fullName = trim($lastName . ' ' . $firstName) ?: ($lang === 'hu' ? 'Ismeretlen Dolgozó' : 'Unknown Employee');

    $role = $contract?->position ?: match ($contract?->employment_type) {
        'permanent' => ($lang === 'hu' ? 'Belső Munkatárs' : 'Permanent staff'),
        'contractor' => ($lang === 'hu' ? 'Külső Partner' : 'External Partner'),
        'student' => ($lang === 'hu' ? 'Gyakornok' : 'Academic Intern'),
        default => ($lang === 'hu' ? 'Jogosult Személyzet' : 'Authorized Personnel'),
    };

    $qrCodeHash = $identification?->qr_code_hash;
    if (empty($qrCodeHash)) {
        $qrCodeHash = 'VIRTUAL-ID-' . str_pad($record->id, 6, '0', STR_PAD_LEFT);
    }

    $qrCodeSvg = '';
    if ($qrCodeHash) {
        try {
            $renderer = new \BaconQrCode\Renderer\ImageRenderer(
                new \BaconQrCode\Renderer\RendererStyle\RendererStyle(160, 0),
                new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
            );
            $writer = new \BaconQrCode\Writer($renderer);
            $qrCodeSvg = $writer->writeString($qrCodeHash);
        } catch (\Exception $e) {
            $qrCodeSvg = '<!-- QR Error: ' . $e->getMessage() . ' -->';
        }
    }
@endphp

@if($isStandalone ?? false)
    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('ID Card') }} - {{ $fullName }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="bg-gray-200 flex items-center justify-center min-h-screen p-4">
@endif

    @if(!($isStandalone ?? false) && !($hideControls ?? false))
        <div class="space-y-3 py-2">

            {{-- Settings & Actions Panel --}}
            <div
                class="print:hidden flex flex-col sm:flex-row items-center justify-between p-4 bg-white border rounded-xl shadow-sm gap-4">
                <div class="flex items-center gap-4">
                    <x-heroicon-o-printer class="w-6 h-6 text-gray-500" />
                    <span class="text-sm font-bold text-gray-700">{{ __('ID Card Issuance Panel') }}</span>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">
                    {{-- Language Selector --}}
                    <div class="flex items-center gap-2 text-sm bg-gray-50 border border-gray-200 rounded-lg p-1">
                        <a href="?lang=hu"
                            class="px-3 py-1.5 rounded-md font-medium transition-colors text-xs inline-block {{ $lang === 'hu' ? 'bg-blue-600 text-white shadow hover:bg-blue-700' : 'text-gray-600 bg-transparent hover:text-gray-900 hover:bg-gray-200' }}">
                            Magyar
                        </a>
                        <a href="?lang=en"
                            class="px-3 py-1.5 rounded-md font-medium transition-colors text-xs inline-block {{ $lang === 'en' ? 'bg-blue-600 text-white shadow hover:bg-blue-700' : 'text-gray-600 bg-transparent hover:text-gray-900 hover:bg-gray-200' }}">
                            English
                        </a>
                    </div>
                </div>

                <x-filament::button color="gray" icon="heroicon-o-printer" onclick="window.print()" class="scale-90">
                    {{ __('Print Preview') }}
                </x-filament::button>
            </div>
    @endif

        {{-- Card: 70mm × 45mm (Significantly smaller to guarantee it fits printers) --}}
        <div class="flex justify-center print:block print:m-0 min-h-[65mm] sm:min-h-[75mm] print:min-h-0 pt-0 mb-2 print:mb-0">
            <div class="scale-[1.3] sm:scale-[1.5] origin-top print:scale-100 print:transform-none">
                <div class="printable-id-card relative w-[70mm] h-[45mm] bg-white border border-gray-300 rounded-[2.5mm] overflow-hidden flex flex-col shadow-xl print:shadow-none print:border-none">

                    {{-- TOP STRIPE --}}
                    <div class="h-[7mm] bg-slate-900 flex items-center px-2.5 justify-between shrink-0">
                        <div class="flex items-center gap-1.5">
                            <div class="w-4 h-4 bg-white rounded-sm flex items-center justify-center shrink-0">
                                <x-heroicon-s-shield-check class="w-3.5 h-3.5 text-slate-900" />
                            </div>
                            <div class="flex flex-col leading-none gap-[1px]">
                                <span
                                    class="text-[7.5px] font-black text-white uppercase tracking-tight leading-none">COMPROLLER</span>
                                <span
                                    class="text-[3.5px] font-bold text-blue-400 uppercase tracking-widest leading-none">{{ $lang === 'hu' ? 'BIZTONSÁGI RENDSZER' : 'SECURE ACCESS' }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col items-end leading-none gap-[1px]">
                            <span
                                class="text-[3.5px] text-white/70 font-bold uppercase tracking-widest leading-none">{{ $lang === 'hu' ? 'HIVATALOS AZONOSÍTÓ' : 'OFFICIAL IDENTIFICATION' }}</span>
                            <span class="text-[6.5px] text-white font-mono font-black leading-none mt-[1px]">ID No.
                                {{ str_pad($record->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>

                    {{-- MAIN CONTENT --}}
                    <div class="flex-1 flex gap-2.5 px-2.5 py-1.5 overflow-hidden h-[34mm]">

                        {{-- LEFT: PHOTO --}}
                        <div class="flex items-start shrink-0 pt-0.5">
                            <div
                                class="w-[16mm] h-[16mm] bg-gray-50 border border-gray-200 rounded flex flex-col items-center justify-center p-[1mm]">
                                <x-heroicon-o-user class="w-7 h-7 text-gray-300" />
                            </div>
                        </div>

                        {{-- CENTER: DETAILS --}}
                        <div class="flex flex-col flex-1 min-w-0 justify-start gap-1 pt-0.5">
                            <div>
                                <span
                                    class="text-[4px] font-bold text-gray-400 uppercase tracking-widest block mb-[1px]">{{ $lang === 'hu' ? 'NÉV' : 'HOLDER NAME' }}</span>
                                <h2 class="text-[9px] font-black text-black tracking-tight leading-[1.1] uppercase overflow-hidden"
                                    style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                                    {{ $fullName }}
                                </h2>
                            </div>
                            <div>
                                <span
                                    class="text-[4px] font-bold text-gray-400 uppercase tracking-widest block mb-[1px]">{{ $lang === 'hu' ? 'BEOSZTÁS' : 'POSITION' }}</span>
                                <div class="flex items-center gap-1">
                                    <div class="w-[2px] h-[8px] bg-blue-600 shrink-0 rounded-full"></div>
                                    <span
                                        class="text-[6.5px] font-black text-slate-800 uppercase tracking-wide leading-none overflow-hidden text-ellipsis whitespace-nowrap">{{ $role }}</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-1.5 mt-auto pb-0.5">
                                <div>
                                    <span
                                        class="text-[4px] font-bold text-gray-400 uppercase tracking-widest block mb-[1px]">{{ $lang === 'hu' ? 'ÉRVÉNYESSÉG' : 'VALIDITY' }}</span>
                                    <div class="text-[5.5px] font-black text-black uppercase">
                                        {{ $lang === 'hu' ? 'VÉGLEGES' : 'PERMANENT' }}
                                    </div>
                                </div>
                                <div class="border-l border-gray-100 pl-1.5">
                                    <span
                                        class="text-[4px] font-bold text-gray-400 uppercase tracking-widest block mb-[1px]">{{ $lang === 'hu' ? 'AZONOSÍTÓ' : 'AUTH CODE' }}</span>
                                    <div class="text-[5.5px] font-black text-black whitespace-nowrap">
                                        COM-{{ str_pad($record->id, 6, '0', STR_PAD_LEFT) }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT: QR CODE --}}
                        <div class="flex flex-col items-end justify-start shrink-0 pt-0.5">
                            <div
                                class="bg-white border border-gray-200 rounded p-[1mm] w-[16mm] h-[16mm] flex items-center justify-center overflow-hidden">
                                @if($qrCodeSvg)
                                    <div
                                        class="w-full h-full flex items-center justify-center [&>svg]:w-full [&>svg]:h-full [&>svg]:max-w-full [&>svg]:max-h-full">
                                        {!! $qrCodeSvg !!}
                                    </div>
                                @else
                                    <x-heroicon-o-qr-code class="w-8 h-8 text-gray-100" />
                                @endif
                            </div>
                            <span
                                class="text-[4px] font-black text-slate-400 uppercase tracking-widest mt-1 text-center w-full">{{ $lang === 'hu' ? 'BIZTONSÁGI QR' : 'SECURE QR' }}</span>
                        </div>

                    </div>

                    {{-- BOTTOM STRIPE --}}
                    <div
                        class="h-[4mm] bg-gray-50 border-t border-gray-200 flex items-center px-2.5 justify-between shrink-0 overflow-hidden">
                        <div class="flex items-center gap-[1px] opacity-40 shrink-0">
                            @for($i = 0; $i < 5; $i++)
                                <div class="w-[2px] h-[2px] rounded-full bg-slate-900"></div>
                            @endfor
                        </div>
                        <span
                            class="text-[4px] font-black text-slate-400 uppercase tracking-widest">{{ $lang === 'hu' ? 'HITELESÍTETT BIZTONSÁGI KÁRTYA' : 'CERTIFIED SECURITY PROTOCOL' }}</span>
                        <span
                            class="text-[4.5px] font-black text-slate-700 shrink-0">{{ date('Y') }}-{{ str_pad($record->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if(!($isStandalone ?? false) && !($hideControls ?? false))
                <div
                    class="print:hidden max-w-[86mm] mx-auto p-3 bg-blue-50 border border-blue-100 rounded-lg flex items-center gap-3 mt-4">
                    <x-heroicon-s-information-circle class="w-5 h-5 text-blue-500 shrink-0" />
                    <p class="text-[10px] text-blue-700 font-medium leading-tight">
                        {{ __('Small security card format (70mm × 45mm). Adjusted to fit any printer without clipping.') }}
                    </p>
                </div>
            </div>
        @endif

    @if($isStandalone ?? false)
            <div class="print:hidden fixed bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-3">

                {{-- Language Selector for Standalone --}}
                <div class="flex items-center gap-2 bg-white border border-gray-200 shadow-xl rounded-xl p-1.5">
                    <a href="?lang=hu"
                        class="px-5 py-2 rounded-lg font-bold transition-all text-sm inline-block {{ $lang === 'hu' ? 'bg-blue-600 text-white shadow-md hover:bg-blue-700' : 'text-gray-600 bg-transparent hover:text-gray-900 hover:bg-gray-100' }}">
                        Magyar
                    </a>
                    <a href="?lang=en"
                        class="px-5 py-2 rounded-lg font-bold transition-all text-sm inline-block {{ $lang === 'en' ? 'bg-blue-600 text-white shadow-md hover:bg-blue-700' : 'text-gray-600 bg-transparent hover:text-gray-900 hover:bg-gray-100' }}">
                        English
                    </a>
                </div>

                <button onclick="window.print()"
                    class="bg-slate-900 hover:bg-black text-white font-bold px-10 py-3 rounded-xl shadow-xl transition-all active:scale-95 uppercase tracking-widest text-xs">
                    {{ __('Print ID Card') }}
                </button>
            </div>
        </body>

        </html>
    @endif

@if($isStandalone ?? false)
<style>
    @media print {
        body * {
            visibility: hidden !important;
            background: none !important;
        }

        .printable-id-card,
        .printable-id-card * {
            visibility: visible !important;
        }

        .printable-id-card {
            visibility: visible !important;
            position: relative !important;
            margin: 0 auto !important;
            width: 70mm !important;
            height: 45mm !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 2.5mm !important;
            box-shadow: none !important;
            overflow: hidden !important;
            background-color: white !important;
            box-sizing: border-box !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .printable-id-card .bg-slate-900 {
            background-color: #0f172a !important;
        }

        .printable-id-card .bg-gray-50 {
            background-color: #f8fafc !important;
        }

        .printable-id-card .text-white {
            color: #ffffff !important;
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }

        html,
        body {
            width: 100% !important;
            height: 100% !important;
            background: white !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        body {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
        }
    }
</style>
@else
<style>
    @media print {
        .printable-id-card {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .printable-id-card .bg-slate-900 { background-color: #0f172a !important; }
        .printable-id-card .bg-gray-50 { background-color: #f8fafc !important; }
        .printable-id-card .text-white { color: #ffffff !important; }
    }
</style>
@endif