<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->text('attendance_hours')->nullable()->after('base_salary');
            $table->text('paid_leave_hours')->nullable()->after('attendance_hours');
            $table->text('sick_leave_hours')->nullable()->after('paid_leave_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['attendance_hours', 'paid_leave_hours', 'sick_leave_hours']);
        });
    }
};
