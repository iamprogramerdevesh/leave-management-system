<?php

namespace App\Services;

use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(private AuditService $auditService) {}

    public function create(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            $user = User::query()->create([
                'employee_id' => $data['employee_id'],
                'name' => $data['name'],
                'email' => $data['email'],
                'mobile' => $data['mobile'] ?? null,
                'department' => $data['department'],
                'designation' => $data['designation'],
                'role' => $data['role'],
                'status' => $data['status'] ?? User::STATUS_ACTIVE,
                'password' => Hash::make($data['password']),
            ]);

            $this->initializeLeaveBalances($user);

            $this->auditService->logUserCreation(
                auth()->id(),
                $data,
                ['id' => $user->id, 'email' => $user->email],
                'Created user: ' . $user->name . ' (' . $user->email . ')'
            );

            return $user;
        });
    }

    public function update(User $user, array $data): User
    {
        $attributes = [
            'employee_id' => $data['employee_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'] ?? null,
            'department' => $data['department'],
            'designation' => $data['designation'],
            'role' => $data['role'],
            'status' => $data['status'] ?? $user->status,
        ];

        if (! empty($data['password'])) {
            $attributes['password'] = Hash::make($data['password']);
        }

        $user->update($attributes);

        $this->auditService->logUserUpdate(
            auth()->id(),
            $data,
            ['id' => $user->id, 'email' => $user->email],
            'Updated user: ' . $user->name . ' (' . $user->email . ')'
        );

        return $user->fresh();
    }

    public function updateStatus(User $user, string $status): User
    {
        $user->update(['status' => $status]);

        $this->auditService->logUserStatusChange(
            auth()->id(),
            ['user_id' => $user->id, 'new_status' => $status],
            'Changed status of ' . $user->name . ' to ' . $status
        );

        return $user->fresh();
    }

    private function initializeLeaveBalances(User $user): void
    {
        $year = (int) date('Y');

        LeaveType::query()->each(function (LeaveType $leaveType) use ($user, $year): void {
            LeaveBalance::query()->create([
                'user_id' => $user->id,
                'leave_type_id' => $leaveType->id,
                'year' => $year,
                'allocated_days' => $leaveType->default_allocation,
                'used_days' => 0,
            ]);
        });
    }
}
