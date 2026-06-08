<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard');
    }

    public function stats(): JsonResponse
    {
        $year = (int) date('Y');

        $totalUsers = User::query()->count();
        $activeUsers = User::query()->where('status', User::STATUS_ACTIVE)->count();
        $totalManagers = User::query()->where('role', User::ROLE_MANAGER)->count();
        $totalEmployees = User::query()->where('role', User::ROLE_EMPLOYEE)->count();

        $totalRequests = LeaveRequest::query()->whereYear('created_at', $year)->count();
        $approved = LeaveRequest::query()->where('status', LeaveRequest::STATUS_APPROVED)->whereYear('created_at', $year)->count();
        $pending = LeaveRequest::query()->where('status', LeaveRequest::STATUS_PENDING)->whereYear('created_at', $year)->count();
        $rejected = LeaveRequest::query()->where('status', LeaveRequest::STATUS_REJECTED)->whereYear('created_at', $year)->count();

        $totalBalance = LeaveBalance::query()
            ->where('year', $year)
            ->get()
            ->sum(fn (LeaveBalance $lb): int => $lb->allocated_days - $lb->used_days);

        $recentRequests = LeaveRequest::query()
            ->with(['user', 'leaveType'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn (LeaveRequest $lr): array => [
                'id' => $lr->id,
                'employee' => $lr->user?->name,
                'leave_type' => $lr->leaveType?->name,
                'start_date' => $lr->start_date->format('d-m-Y'),
                'end_date' => $lr->end_date->format('d-m-Y'),
                'duration' => $lr->duration,
                'status' => $lr->status,
            ]);

        return response()->json([
            'data' => [
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'total_managers' => $totalManagers,
                'total_employees' => $totalEmployees,
                'total_requests' => $totalRequests,
                'approved' => $approved,
                'pending' => $pending,
                'rejected' => $rejected,
                'total_balance' => $totalBalance,
                'recent_requests' => $recentRequests,
            ],
        ]);
    }
}