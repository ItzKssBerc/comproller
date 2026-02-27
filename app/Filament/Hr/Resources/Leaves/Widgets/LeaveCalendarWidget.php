<?php

namespace App\Filament\Hr\Resources\Leaves\Widgets;

use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class LeaveCalendarWidget extends FullCalendarWidget
{
    protected int|string|array $columnSpan = 'full';

    public string|Model|null $model = Leave::class;

    /**
     * Return events that should be rendered statically on calendar.
     */
    public function fetchEvents(array $fetchInfo): array
    {
        return Leave::query()
            ->where('status', 'approved')
            ->where('start_date', '>=', $fetchInfo['start'])
            ->where('end_date', '<=', $fetchInfo['end'])
            ->get()
            ->map(function (Leave $leave) {
                $color = match ($leave->type) {
                    'paid_leave' => 'rgba(16, 185, 129, 0.6)', // green
                    'sick_leave' => 'rgba(239, 68, 68, 0.6)', // red
                    'unpaid_leave' => 'rgba(245, 158, 11, 0.6)', // amber
                    default => 'rgba(107, 114, 128, 0.6)', // gray
                };

                // To make Fullcalendar show days correctly, end_date must be inclusive or +1 day
                // In FullCalendar "exclusive end" is used for all-day events.
                $endDate = Carbon::parse($leave->end_date)->addDay()->format('Y-m-d');

                $typeLabels = [
                    'paid_leave' => __('Paid Leave'),
                    'sick_leave' => __('Sick Leave'),
                    'unpaid_leave' => __('Unpaid Leave'),
                ];
                $typeLabel = $typeLabels[$leave->type] ?? __('Other');

                return [
                    'id' => $leave->id,
                    'title' => $typeLabel,
                    'start' => Carbon::parse($leave->start_date)->format('Y-m-d'),
                    'end' => $endDate,
                    'allDay' => true,
                    'color' => $color,
                ];
            })
            ->toArray();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(?array $event = null): bool
    {
        return false;
    }

    public function onEventClick(array $event): void
    {
        $date = Carbon::parse($event['start'])->format('Y-m-d');
        $this->mountAction('viewDay', [
            'date' => $date,
        ]);
    }

    public function onDateSelect(string $start, ?string $end, bool $allDay, ?array $view, ?array $resource): void
    {
        [$start, $end] = $this->calculateTimezoneOffset($start, $end, $allDay);

        $this->mountAction('viewDay', [
            'date' => $start->format('Y-m-d'),
        ]);
    }

    protected function headerActions(): array
    {
        return [];
    }

    public function viewDayAction(): \Filament\Actions\Action
    {
        return \Filament\Actions\Action::make('viewDay')
            ->modalHeading(fn (array $arguments) => __('Leaves on :date', ['date' => \Carbon\Carbon::parse($arguments['date'] ?? today())->format('Y. m. d.')]))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel(__('Close'))
            ->modalContent(function (array $arguments) {
                $date = $arguments['date'] ?? today();

                return view('filament.hr.leaves-day-table', [
                    'date' => $date,
                ]);
            });
    }

    public function config(): array
    {
        return [
            'headerToolbar' => [
                'left' => 'prev,next',
                'center' => 'title',
                'right' => '',
            ],
        ];
    }
}
