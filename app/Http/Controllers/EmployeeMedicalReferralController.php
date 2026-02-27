<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class EmployeeMedicalReferralController extends Controller
{
    public function show(Request $request, Employee $employee)
    {
        $locale = $request->query('lang', App::getLocale());
        App::setLocale($locale);

        $employee->load([
            'personalData',
            'contactData',
            'financialData',
            'contract',
        ]);

        $companyName = auth()->user()->name ?: config('app.name');

        $pdf = Pdf::loadView('pdf.medical-referral', [
            'employee' => $employee,
            'companyName' => $companyName,
            'locale' => $locale,
            'examinationType' => $request->query('type', 'New Hire'),
        ]);

        return $pdf->download('medical-referral-'.$employee->personalData?->last_name.'.pdf');
    }

    public function bulkDownload(Request $request)
    {
        $employeeIds = $request->input('ids', []);
        $locale = $request->query('lang', App::getLocale());
        App::setLocale($locale);

        $employees = Employee::with([
            'personalData',
            'contactData',
            'financialData',
            'contract',
        ])->whereIn('id', $employeeIds)->get();

        $companyName = auth()->user()->name ?: config('app.name');

        $pdf = Pdf::loadView('pdf.bulk-medical-referral', [
            'employees' => $employees,
            'companyName' => $companyName,
            'locale' => $locale,
            'examinationType' => $request->query('type', 'New Hire'),
        ]);

        return $pdf->download('medical-referrals-'.now()->format('Y-m-d').'.pdf');
    }
}
