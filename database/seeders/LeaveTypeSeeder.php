<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $leaveTypes = [
            ['name' => 'Casual Leave', 'default_allocation' => 12],
            ['name' => 'Sick Leave', 'default_allocation' => 10],
            ['name' => 'Earned Leave', 'default_allocation' => 15],
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::query()->updateOrCreate(
                ['name' => $leaveType['name']],
                ['default_allocation' => $leaveType['default_allocation']]
            );
        }
    }
}
