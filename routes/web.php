<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SetupController;




Route::get('/lang/{locale}', [App\Http\Controllers\LanguageController::class, 'switch'])->name('lang.switch');

Route::get('/print/employees/bulk-id-card', [\App\Http\Controllers\EmployeeIdCardController::class, 'bulkShow'])
    ->name('employees.bulk-id-card')
    ->middleware(['auth']);

Route::get('/hr/employees/{employee}/id-card', [\App\Http\Controllers\EmployeeIdCardController::class, 'show'])
    ->name('employees.id-card')
    ->middleware(['auth']);

Route::get('/setup', [SetupController::class, 'create'])->name('setup');
Route::post('/setup', [SetupController::class, 'store'])->name('setup.store');
Route::post('/setup/2fa', [SetupController::class, 'verify2fa'])->name('setup.2fa.verify');
