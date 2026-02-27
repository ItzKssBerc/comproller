<?php

use App\Http\Controllers\SetupController;
use Illuminate\Support\Facades\Route;

Route::get('/lang/{locale}', [App\Http\Controllers\LanguageController::class, 'switch'])->name('lang.switch');

Route::get('/print/employees/bulk-id-card', [\App\Http\Controllers\EmployeeIdCardController::class, 'bulkShow'])
    ->name('employees.bulk-id-card')
    ->middleware(['auth']);

Route::get('/hr/employees/{employee}/id-card', [\App\Http\Controllers\EmployeeIdCardController::class, 'show'])
    ->name('employees.id-card')
    ->middleware(['auth']);

Route::get('/hr/employees/{employee}/contract', [\App\Http\Controllers\EmployeeContractController::class, 'show'])
    ->name('employees.contract')
    ->middleware(['auth']);

Route::get('/hr/employees/{employee}/medical-referral', [\App\Http\Controllers\EmployeeMedicalReferralController::class, 'show'])
    ->name('employees.medical-referral')
    ->middleware(['auth']);

Route::get('/print/employees/bulk-contract', [\App\Http\Controllers\EmployeeContractController::class, 'bulkDownload'])
    ->name('employees.bulk-contract')
    ->middleware(['auth']);

Route::get('/print/employees/bulk-medical-referral', [\App\Http\Controllers\EmployeeMedicalReferralController::class, 'bulkDownload'])
    ->name('employees.bulk-medical-referral')
    ->middleware(['auth']);

Route::get('/setup', [SetupController::class, 'create'])->name('setup');
Route::post('/setup', [SetupController::class, 'store'])->name('setup.store');
Route::get('/setup/2fa', [SetupController::class, 'verify2fa'])->name('setup.2fa.verify');

Route::get('/payrolls/{payroll}/download', [\App\Http\Controllers\PayrollController::class, 'download'])
    ->name('payrolls.download')
    ->middleware(['auth']);
Route::get('/payrolls/bulk-download', [\App\Http\Controllers\PayrollController::class, 'bulkDownload'])
    ->name('payrolls.bulk-download')
    ->middleware(['auth']);
