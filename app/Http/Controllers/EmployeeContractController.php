<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class EmployeeContractController extends Controller
{
    public function show(Request $request, Employee $employee)
    {
        $locale = $request->query('lang', App::getLocale());
        $dualCopy = $request->boolean('dual_copy', false);
        App::setLocale($locale);

        $employee->load([
            'personalData',
            'contactData',
            'financialData',
            'contract',
            'salaryDetail',
        ]);

        $companyName = auth()->user()->name ?: config('app.name');

        $pdf = Pdf::loadView('pdf.contract', [
            'employee' => $employee,
            'companyName' => $companyName,
            'locale' => $locale,
            'dualCopy' => $dualCopy,
        ]);

        return $pdf->download('contract-'.$employee->personalData?->last_name.'.pdf');
    }

    public function bulkDownload(Request $request)
    {
        $employeeIds = $request->input('ids', []);
        $locale = $request->query('lang', App::getLocale());
        $dualCopy = $request->boolean('dual_copy', false);
        App::setLocale($locale);

        $employees = Employee::with([
            'personalData',
            'contactData',
            'financialData',
            'contract',
            'salaryDetail',
        ])->whereIn('id', $employeeIds)->get();

        $companyName = auth()->user()->name ?: config('app.name');

        $pdf = Pdf::loadView('pdf.bulk-contract', [
            'employees' => $employees,
            'companyName' => $companyName,
            'locale' => $locale,
            'dualCopy' => $dualCopy,
        ]);

        return $pdf->download('contracts-'.now()->format('Y-m-d').'.pdf');
    }
}
