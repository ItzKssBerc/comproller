<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$attendances = App\Models\Attendance::where('is_processed', false)->get();
echo 'Total Unprocessed Attendances: '.$attendances->count()."\n";

foreach ($attendances as $a) {
    echo "ID: {$a->id}, Clock In: {$a->clock_in_at}, Clock Out: {$a->clock_out_at}, Employee ID: {$a->employee_id}\n";
}

$lastMonth = now()->subMonth();
echo "Last Month: {$lastMonth->month}-{$lastMonth->year}\n";
