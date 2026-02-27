<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\Employee;
use App\Models\EmployeePersonalData;

class EmployeeEncryptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_personal_data_is_encrypted_in_database(): void
    {
        $employee = Employee::create([
            'qr_code_hash' => 'test-hash-123',
            'is_active' => true,
        ]);

        $personalData = EmployeePersonalData::create([
            'employee_id' => $employee->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => '1990-01-01',
            'mothers_name' => 'Jane Smith',
        ]);

        // Assert model returns the decrypted value
        $this->assertEquals('John', $personalData->first_name);
        $this->assertEquals('Doe', $personalData->last_name);

        // Fetch direct from DB to check if it's stored encrypted
        $dbRecord = DB::table('employee_personal_data')->where('id', $personalData->id)->first();

        // The raw DB value should NOT equal the plain text
        $this->assertNotEquals('John', $dbRecord->first_name);
        $this->assertNotEquals('Doe', $dbRecord->last_name);

        // It should be a base64 encoded payload (Laravel's default encryption format)
        $this->assertStringContainsString('eyJp', $dbRecord->first_name);
    }
}
