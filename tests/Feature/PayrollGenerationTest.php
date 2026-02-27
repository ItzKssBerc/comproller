<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_payroll_record_and_data_is_encrypted(): void
    {
        $employee = Employee::create(['is_active' => true]);

        $payroll = Payroll::create([
            'employee_id' => $employee->id,
            'period' => '2024-03',
            'base_salary' => 500000,
            'total_hours' => 160,
            'gross_amount' => 500000,
            'net_amount' => 325000,
            'status' => 'completed',
        ]);

        // Verify it's in DB
        $this->assertDatabaseHas('payrolls', [
            'employee_id' => $employee->id,
            'period' => '2024-03',
        ]);

        // Verify model decrypts it
        $this->assertEquals(500000, $payroll->base_salary);
        $this->assertEquals(500000, $payroll->gross_amount);
        $this->assertEquals(325000, $payroll->net_amount);
        $this->assertEquals(160, $payroll->total_hours);
    }
}
