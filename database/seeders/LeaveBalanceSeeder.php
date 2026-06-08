<?php

namespace Database\Seeders;

use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Database\Seeder;

class LeaveBalanceSeeder extends Seeder
{
    public function run(): void
    {
        $year = (int) date('Y');
        $leaveTypes = LeaveType::query()->get();

        User::query()
            ->whereIn('email', [
                'admin@gmail.com',
                'manager@gmail.com',
                'employee@gmail.com',
            ])
            ->each(function (User $user) use ($leaveTypes, $year): void {
                foreach ($leaveTypes as $leaveType) {
                    LeaveBalance::query()->updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'leave_type_id' => $leaveType->id,
                            'year' => $year,
                        ],
                        [
                            'allocated_days' => $leaveType->default_allocation,
                            'used_days' => 0,
                        ]
                    );
                }
            });
    }
}
