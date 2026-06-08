<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Services\LeaveService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private LeaveService $leaveService) {}

    public function index(): View
    {
        return view('employee.dashboard');
    }

    public function stats(): JsonResponse
    {
        $user = auth()->user();
        $year = (int) date('Y');

        $leaveBalances = $this->leaveService->getBalances($user->id, $year);

        $totalRequests = LeaveRequest::query()
            ->where('user_id', $user->id)
            ->whereYear('created_at', $year)
            ->count();

        $approved = LeaveRequest::query()
            ->where('user_id', $user->id)
            ->where('status', LeaveRequest::STATUS_APPROVED)
            ->whereYear('created_at', $year)
            ->count();

        $pending = LeaveRequest::query()
            ->where('user_id', $user->id)
            ->where('status', LeaveRequest::STATUS_PENDING)
            ->whereYear('created_at', $year)
            ->count();

        $rejected = LeaveRequest::query()
            ->where('user_id', $user->id)
            ->where('status', LeaveRequest::STATUS_REJECTED)
            ->whereYear('created_at', $year)
            ->count();

        $totalBalance = 0;
        $balanceDetails = [];

        foreach ($leaveBalances as $balance) {
            $remaining = $balance['allocated_days'] - $balance['used_days'];
            $totalBalance += $remaining;
            $balanceDetails[] = [
                'leave_type' => $balance['leave_type']['name'],
                'allocated' => $balance['allocated_days'],
                'used' => $balance['used_days'],
                'remaining' => $remaining,
            ];
        }

        return response()->json([
            'data' => [
                'total_requests' => $totalRequests,
                'approved' => $approved,
                'pending' => $pending,
                'rejected' => $rejected,
                'total_balance' => $totalBalance,
                'balance_details' => $balanceDetails,
            ],
        ]);
    }
}