<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('manager.dashboard');
    }

    public function stats(): JsonResponse
    {
        $year = (int) date('Y');

        $totalRequests = LeaveRequest::query()
            ->whereYear('created_at', $year)
            ->count();

        $approved = LeaveRequest::query()
            ->where('status', LeaveRequest::STATUS_APPROVED)
            ->whereYear('created_at', $year)
            ->count();

        $pending = LeaveRequest::query()
            ->where('status', LeaveRequest::STATUS_PENDING)
            ->whereYear('created_at', $year)
            ->count();

        $rejected = LeaveRequest::query()
            ->where('status', LeaveRequest::STATUS_REJECTED)
            ->whereYear('created_at', $year)
            ->count();

        $recentRequests = LeaveRequest::query()
            ->with(['user', 'leaveType'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn (LeaveRequest $lr): array => [
                'employee' => $lr->user?->name,
                'leave_type' => $lr->leaveType?->name,
                'start_date' => $lr->start_date->format('d-m-Y'),
                'end_date' => $lr->end_date->format('d-m-Y'),
                'duration' => $lr->duration,
                'status' => $lr->status,
            ]);

        return response()->json([
            'data' => [
                'total_requests' => $totalRequests,
                'approved' => $approved,
                'pending' => $pending,
                'rejected' => $rejected,
                'recent_requests' => $recentRequests,
            ],
        ]);
    }
}