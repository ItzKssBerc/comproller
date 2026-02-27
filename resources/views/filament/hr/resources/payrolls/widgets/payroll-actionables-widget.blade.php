<x-filament-widgets::widget>
    <div x-data="{ tab: 'prepare' }">
        <x-filament::tabs class="mb-4">
            <x-filament::tabs.item alpine-active="tab === 'prepare'" x-on:click="tab = 'prepare'">
                {{ __('Payroll Preparation') }}
            </x-filament::tabs.item>

            <x-filament::tabs.item alpine-active="tab === 'completed'" x-on:click="tab = 'completed'">
                {{ __('Recent Completed Payrolls') }}
            </x-filament::tabs.item>
        </x-filament::tabs>

        <div x-show="tab === 'prepare'">
            @livewire(\App\Filament\Hr\Resources\Payrolls\Widgets\GeneratePayrollWidget::class)
        </div>

        <div x-show="tab === 'completed'" x-cloak>
            @livewire(\App\Filament\Hr\Resources\Payrolls\Widgets\RecentPayrollsWidget::class)
        </div>
    </div>
</x-filament-widgets::widget>