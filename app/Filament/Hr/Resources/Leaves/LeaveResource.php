<?php

namespace App\Filament\Hr\Resources\Leaves;

use App\Filament\Hr\Resources\Leaves\Pages\EditLeave;
use App\Filament\Hr\Resources\Leaves\Pages\ListLeaves;
use App\Filament\Hr\Resources\Leaves\Schemas\LeaveForm;
use App\Filament\Hr\Resources\Leaves\Tables\LeavesTable;
use App\Models\Leave;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LeaveResource extends Resource
{
    protected static ?string $model = Leave::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return LeaveForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeavesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            Widgets\LeaveStatsWidget::class,
            Widgets\LeaveCalendarWidget::class,
            Widgets\LeaveTypeChart::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeaves::route('/'),
            'edit' => EditLeave::route('/{record}/edit'),
        ];
    }
}
