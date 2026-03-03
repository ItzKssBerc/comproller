<div>
    @if (filled($this->userUndertakingMultiFactorAuthentication))
    {{-- ── MFA Challenge ────────────────────────────── --}}
    <div>
        <h2 class="mt-8 text-2xl font-bold leading-9 tracking-tight text-slate-900 dark:text-white transition-colors">
            {{ __('Confirm MFA') }}
        </h2>
        <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400 transition-colors">
            {{ __('Please verify your identity using Google Authenticator.') }}
        </p>
    </div>

    <div class="mt-10">
        <form wire:submit.prevent="authenticate" class="space-y-6">
            @csrf
            {{-- Preserve credentials in Livewire state so authenticate() can re-validate them --}}
            <input type="hidden" wire:model="data.email">
            <input type="hidden" wire:model="data.password">

            <div>
                <label for="code" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-300">
                    {{ __('Authenticator code') }}
                </label>
                <div class="mt-2">
                    <input id="code" wire:model="data.multiFactor.google_2fa.code" name="code" type="text"
                        inputmode="numeric" autocomplete="one-time-code" required maxlength="6"
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-slate-900 dark:text-white bg-white dark:bg-slate-800 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-700 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all duration-200 text-center tracking-[0.5em] font-mono"
                        placeholder="000000" autofocus>
                    @error('data.multiFactor.google_2fa.code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <button type="submit"
                    class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all duration-200 transform hover:scale-[1.01] active:scale-95">
                    {{ __('Confirm & Sign in') }}
                </button>
            </div>
        </form>
    </div>
    @else
    {{-- ── Login Form ───────────────────────────────── --}}
    <div>
        <h2 class="mt-8 text-2xl font-bold leading-9 tracking-tight text-slate-900 dark:text-white transition-colors">
            {{ __('Login') }}
        </h2>
        <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400 transition-colors">
            {{ __('Please fill in your credentials to sign in.') }}
        </p>
    </div>

    <div class="mt-10">
        <form wire:submit.prevent="authenticate" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-300">
                    {{ __('Email address') }}
                </label>
                <div class="mt-2">
                    <input id="email" wire:model="data.email" name="email" type="email" autocomplete="email" required
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-slate-900 dark:text-white bg-white dark:bg-slate-800 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-700 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all duration-200">
                    @error('data.email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-300">
                    {{ __('Password') }}
                </label>
                <div class="mt-2">
                    <input id="password" wire:model="data.password" name="password" type="password"
                        autocomplete="current-password" required
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-slate-900 dark:text-white bg-white dark:bg-slate-800 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-700 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all duration-200">
                    @error('data.password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center">
                <input id="remember" wire:model="data.remember" name="remember" type="checkbox"
                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-600 dark:border-slate-600 dark:bg-slate-800">
                <label for="remember" class="ml-3 block text-sm leading-6 text-slate-900 dark:text-slate-300">
                    {{ __('Remember me') }}
                </label>
            </div>

            <div>
                <button type="submit"
                    class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all duration-200 transform hover:scale-[1.01] active:scale-95">
                    {{ __('Sign in') }}
                </button>
            </div>
        </form>
    </div>
    @endif

    @if (!$this instanceof \Filament\Tables\Contracts\HasTable)
    <x-filament-actions::modals />
    @endif
</div>