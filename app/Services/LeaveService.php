<?php

namespace App\Services;

use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeaveService
{
    public function __construct(private AuditService $auditService) {}

    public function calculateDuration(string $startDate, string $endDate): int
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        return $start->diffInDays($end) + 1;
    }

    public function getBalances(int $userId, int $year): array
    {
        return LeaveBalance::query()
            ->with('leaveType')
            ->where('user_id', $userId)
            ->where('year', $year)
            ->get()
            ->toArray();
    }

    public function validateBalance(int $userId, int $leaveTypeId, int $duration, ?int $year = null): bool
    {
        $year = $year ?? (int) date('Y');

        $balance = LeaveBalance::query()
            ->where('user_id', $userId)
            ->where('leave_type_id', $leaveTypeId)
            ->where('year', $year)
            ->first();

        if (! $balance) {
            return false;
        }

        return ($balance->allocated_days - $balance->used_days) >= $duration;
    }

    public function validateNoOverlap(int $userId, string $startDate, string $endDate, ?int $excludeRequestId = null): bool
    {
        $query = LeaveRequest::query()
            ->where('user_id', $userId)
            ->where('status', '!=', LeaveRequest::STATUS_REJECTED)
            ->where(function ($q) use ($startDate, $endDate): void {
                $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($inner) use ($startDate, $endDate): void {
                        $inner->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            });

        if ($excludeRequestId) {
            $query->where('id', '!=', $excludeRequestId);
        }

        return ! $query->exists();
    }

    public function applyLeave(array $data, int $userId): LeaveRequest
    {
        return DB::transaction(function () use ($data, $userId): LeaveRequest {
            $leaveRequest = LeaveRequest::query()->create([
                'user_id' => $userId,
                'leave_type_id' => $data['leave_type_id'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'duration' => $data['duration'],
                'reason' => $data['reason'] ?? null,
                'status' => LeaveRequest::STATUS_PENDING,
            ]);

            $this->auditService->logLeaveApplication(
                $userId,
                $data,
                ['id' => $leaveRequest->id, 'status' => $leaveRequest->status],
                'Applied for ' . $data['duration'] . ' day(s) leave'
            );

            return $leaveRequest;
        });
    }

    public function approve(LeaveRequest $leaveRequest, int $approvedBy, ?string $remarks = null): LeaveRequest
    {
        return DB::transaction(function () use ($leaveRequest, $approvedBy, $remarks): LeaveRequest {
            $leaveRequest->update([
                'status' => LeaveRequest::STATUS_APPROVED,
                'approved_by' => $approvedBy,
                'approved_at' => now(),
                'manager_remarks' => $remarks,
            ]);

            $this->deductBalance($leaveRequest->user_id, $leaveRequest->leave_type_id, $leaveRequest->duration);

            $this->auditService->logLeaveApproval(
                $approvedBy,
                ['leave_request_id' => $leaveRequest->id, 'user_id' => $leaveRequest->user_id],
                'Approved leave request #' . $leaveRequest->id . ' for ' . $leaveRequest->duration . ' day(s)'
            );

            return $leaveRequest->fresh();
        });
    }

    public function reject(LeaveRequest $leaveRequest, int $approvedBy, ?string $remarks = null): LeaveRequest
    {
        return DB::transaction(function () use ($leaveRequest, $approvedBy, $remarks): LeaveRequest {
            $leaveRequest->update([
                'status' => LeaveRequest::STATUS_REJECTED,
                'approved_by' => $approvedBy,
                'approved_at' => now(),
                'manager_remarks' => $remarks,
            ]);

            $this->auditService->logLeaveRejection(
                $approvedBy,
                ['leave_request_id' => $leaveRequest->id, 'user_id' => $leaveRequest->user_id],
                'Rejected leave request #' . $leaveRequest->id
            );

            return $leaveRequest->fresh();
        });
    }

    private function deductBalance(int $userId, int $leaveTypeId, int $duration): void
    {
        $balance = LeaveBalance::query()
            ->where('user_id', $userId)
            ->where('leave_type_id', $leaveTypeId)
            ->where('year', (int) date('Y'))
            ->first();

        if ($balance) {
            $balance->increment('used_days', $duration);
        }
    }

    public function recalculateBalancesForType(LeaveType $leaveType): void
    {
        $year = (int) date('Y');

        LeaveBalance::query()
            ->where('leave_type_id', $leaveType->id)
            ->where('year', $year)
            ->chunk(100, function ($balances) use ($leaveType): void {
                foreach ($balances as $balance) {
                    $balance->update(['allocated_days' => $leaveType->default_allocation]);
                }
            });
    }
}