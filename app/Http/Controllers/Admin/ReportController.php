<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $leaveTypes = \App\Models\LeaveType::query()->orderBy('name')->get();
        $employees = User::query()->where('role', User::ROLE_EMPLOYEE)->orderBy('name')->get(['id', 'name']);

        return view('admin.reports.index', compact('leaveTypes', 'employees'));
    }

    public function leaveReport(Request $request): JsonResponse
    {
        $query = LeaveRequest::query()
            ->with(['user', 'leaveType', 'approver']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->input('leave_type_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('from_date')) {
            $query->where('start_date', '>=', $request->input('from_date'));
        }

        if ($request->filled('to_date')) {
            $query->where('end_date', '<=', $request->input('to_date'));
        }

        $leaveRequests = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $leaveRequests->map(fn (LeaveRequest $lr): array => [
                'id' => $lr->id,
                'employee' => $lr->user?->name,
                'employee_id' => $lr->user?->employee_id,
                'department' => $lr->user?->department,
                'leave_type' => $lr->leaveType?->name,
                'start_date' => $lr->start_date->format('d-m-Y'),
                'end_date' => $lr->end_date->format('d-m-Y'),
                'duration' => $lr->duration,
                'reason' => $lr->reason,
                'status' => $lr->status,
                'approved_by' => $lr->approver?->name,
                'approved_at' => $lr->approved_at?->format('d-m-Y H:i'),
                'manager_remarks' => $lr->manager_remarks,
                'created_at' => $lr->created_at->format('d-m-Y H:i'),
            ]),
        ]);
    }

    public function summary(): JsonResponse
    {
        $year = (int) date('Y');

        $totalUsers = User::query()->where('status', User::STATUS_ACTIVE)->count();
        $totalEmployees = User::query()->where('role', User::ROLE_EMPLOYEE)->count();

        $totalRequests = LeaveRequest::query()->whereYear('created_at', $year)->count();
        $approved = LeaveRequest::query()->where('status', LeaveRequest::STATUS_APPROVED)->whereYear('created_at', $year)->count();
        $pending = LeaveRequest::query()->where('status', LeaveRequest::STATUS_PENDING)->whereYear('created_at', $year)->count();
        $rejected = LeaveRequest::query()->where('status', LeaveRequest::STATUS_REJECTED)->whereYear('created_at', $year)->count();

        $totalDaysApproved = LeaveRequest::query()
            ->where('status', LeaveRequest::STATUS_APPROVED)
            ->whereYear('created_at', $year)
            ->sum('duration');

        $leaveTypeBreakdown = \App\Models\LeaveType::query()
            ->withCount(['leaveRequests' => fn ($q) => $q->whereYear('created_at', $year)])
            ->orderBy('name')
            ->get()
            ->map(fn ($lt) => [
                'leave_type' => $lt->name,
                'total_requests' => $lt->leave_requests_count,
            ]);

        return response()->json([
            'data' => [
                'total_users' => $totalUsers,
                'total_employees' => $totalEmployees,
                'total_requests' => $totalRequests,
                'approved' => $approved,
                'pending' => $pending,
                'rejected' => $rejected,
                'total_days_approved' => $totalDaysApproved,
                'leave_type_breakdown' => $leaveTypeBreakdown,
            ],
        ]);
    }
}