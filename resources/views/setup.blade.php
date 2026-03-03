<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full transition-colors duration-500">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Comproller Admin') }} | Comproller</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
        }
    </style>
</head>

<body class="h-full dark:bg-slate-900 transition-colors duration-500">
    <div class="flex min-h-full">
        <!-- Left Side: Branding & Info -->
        <div class="relative hidden w-0 flex-1 lg:block bg-aurora">
            <div class="absolute inset-0 flex flex-col justify-between p-12 text-white">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-white/20 p-2 backdrop-blur-sm">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
                            </svg>
                        </div>
                        <span class="text-2xl font-bold tracking-tight">Comproller</span>
                    </div>
                </div>
                <div>
                    <h2 id="typewriter" data-text-hu="{{ __('Welcome to the system') }}"
                        data-text-en="Welcome to the system"
                        class="text-4xl font-bold tracking-tight mb-4 min-h-[1.2em]"></h2>
                    <p class="text-lg text-white/90 max-w-md">
                        {{ __('Setup the first Application profile to start the Comproller financial controller
                        system.') }}
                    </p>
                </div>
                <div class="text-sm text-slate-400">
                    &copy; <x-year /> Comproller System v1.0
                </div>
            </div>
        </div>

        <!-- Right Side: Form -->
        <div
            class="flex flex-1 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-24 bg-white dark:bg-slate-900 relative transition-colors duration-500">
            <!-- Header Actions -->
            <div class="absolute top-6 right-6 flex items-center gap-6">
                <!-- Language Switcher -->
                <div class="flex gap-2">
                    <a href="{{ route('lang.switch', 'hu') }}"
                        class="px-3 py-1.5 text-sm font-medium rounded-md transition-all {{ app()->getLocale() === 'hu' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-500' : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">HU</a>
                    <a href="{{ route('lang.switch', 'en') }}"
                        class="px-3 py-1.5 text-sm font-medium rounded-md transition-all {{ app()->getLocale() === 'en' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-500' : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">EN</a>
                </div>

                <!-- Theme Switcher -->
                <button onclick="toggleTheme()"
                    class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    <svg id="sun-icon" class="h-5 w-5 hidden dark:block" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M3 12h2.25m.386-6.364l-1.591 1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                    </svg>
                    <svg id="moon-icon" class="h-5 w-5 block dark:hidden" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                    </svg>
                </button>
            </div>

            <div class="mx-auto w-full max-w-sm lg:w-96 animate-fade-in-up">
                @if(isset($show2Fa) && $show2Fa)
                <div>
                    <h2
                        class="mt-4 text-2xl font-bold leading-9 tracking-tight text-slate-900 dark:text-white transition-colors">
                        {{ __('Two-Factor Authentication') }}
                    </h2>
                    <p class="mt-1 text-sm leading-6 text-slate-500 dark:text-slate-400 transition-colors">
                        {{ __('Scan the QR code below with your authenticator app (e.g. Google Authenticator) and enter
                        the 6-digit code to finish setup.') }}
                    </p>
                </div>

                <div
                    class="mt-4 flex justify-center bg-white p-3 rounded-xl shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-700">
                    <div
                        class="w-48 h-48 sm:w-56 sm:h-56 overflow-hidden flex items-center justify-center [&>svg]:w-full [&>svg]:h-full [&>svg]:max-w-full">
                        {!! $qrCodeSvg !!}
                    </div>
                </div>

                <div class="mt-3 text-center">
                    <p
                        class="text-sm font-mono text-slate-500 dark:text-slate-400 break-all bg-slate-100 dark:bg-slate-800 p-2 rounded">
                        {{ $secret }}</p>
                </div>

                <div class="mt-6">
                    <form action="{{ route('setup.2fa.verify') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="one_time_password"
                                class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-300">{{
                                __('Authentication Code') }}</label>
                            <div class="mt-1">
                                <input id="one_time_password" name="one_time_password" type="text" inputmode="numeric"
                                    required
                                    class="block w-full rounded-md border-0 py-2.5 px-3 text-slate-900 dark:text-white bg-white dark:bg-slate-800 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-700 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-amber-600 sm:text-sm sm:leading-6 transition-all duration-200 text-center tracking-widest font-mono"
                                    placeholder="000000" maxlength="6" autofocus>
                                @error('one_time_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <button type="submit"
                                class="flex w-full justify-center rounded-md bg-amber-600 px-3 py-2.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-amber-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-600 transition-all duration-200 transform hover:scale-[1.01] active:scale-95">
                                {{ __('Verify & Login') }}
                            </button>
                        </div>
                    </form>
                </div>
                @else
                <div>
                    <h2
                        class="mt-8 text-2xl font-bold leading-9 tracking-tight text-slate-900 dark:text-white transition-colors">
                        {{ __('Create Application Profile') }}
                    </h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400 transition-colors">
                        {{ __('Please fill in the details to proceed.') }}
                    </p>
                </div>

                <div class="mt-10">
                    <form action="{{ route('setup.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div>
                            <label for="name"
                                class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-300">{{
                                __('Company Name') }}</label>
                            <div class="mt-2">
                                <input id="name" name="name" type="text" autocomplete="organization" required
                                    class="block w-full rounded-md border-0 py-2.5 px-3 text-slate-900 dark:text-white bg-white dark:bg-slate-800 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-700 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-amber-600 sm:text-sm sm:leading-6 transition-all duration-200"
                                    placeholder="{{ __('E.g. Company Ltd') }}" value="{{ old('name') }}">
                                @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="email"
                                class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-300">{{
                                __('Email Address') }}</label>
                            <div class="mt-2">
                                <input id="email" name="email" type="email" autocomplete="email" required
                                    class="block w-full rounded-md border-0 py-2.5 px-3 text-slate-900 dark:text-white bg-white dark:bg-slate-800 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-700 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-amber-600 sm:text-sm sm:leading-6 transition-all duration-200"
                                    placeholder="{{ __('admin@company.com') }}" value="{{ old('email') }}">
                                @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="password"
                                class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-300">{{
                                __('Password') }}</label>
                            <div class="mt-2">
                                <input id="password" name="password" type="password" required
                                    class="block w-full rounded-md border-0 py-2.5 px-3 text-slate-900 dark:text-white bg-white dark:bg-slate-800 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-700 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-amber-600 sm:text-sm sm:leading-6 transition-all duration-200">
                                @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="password_confirmation"
                                class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-300">{{
                                __('Confirm Password') }}</label>
                            <div class="mt-2">
                                <input id="password_confirmation" name="password_confirmation" type="password" required
                                    class="block w-full rounded-md border-0 py-2.5 px-3 text-slate-900 dark:text-white bg-white dark:bg-slate-800 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-700 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all duration-200">
                            </div>
                        </div>

                        <div>
                            <button type="submit"
                                class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all duration-200 transform hover:scale-[1.01] active:scale-95">
                                {{ __('Create Account & Login') }}
                            </button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
    <script>
        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }
    </script>
    <style>
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.5s ease-out forwards;
        }
    </style>
</body>

</html>