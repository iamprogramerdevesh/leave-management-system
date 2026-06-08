<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\ApprovalRequest;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Services\LeaveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    public function __construct(private LeaveService $leaveService) {}

    public function index(): View
    {
        $leaveTypes = LeaveType::query()->orderBy('name')->get();

        return view('manager.approvals.index', compact('leaveTypes'));
    }

    public function list(Request $request): JsonResponse
    {
        $query = LeaveRequest::query()
            ->with(['user', 'leaveType']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        } else {
            $query->where('status', LeaveRequest::STATUS_PENDING);
        }

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->input('leave_type_id'));
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
                'manager_remarks' => $lr->manager_remarks,
                'created_at' => $lr->created_at->format('d-m-Y H:i'),
            ]),
        ]);
    }

    public function approve(ApprovalRequest $request, LeaveRequest $leaveRequest): JsonResponse
    {
        if (! $leaveRequest->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'This leave request has already been processed.',
            ], 422);
        }

        $leaveRequest = $this->leaveService->approve(
            $leaveRequest,
            $request->user()->id,
            $request->input('manager_remarks')
        );

        return response()->json([
            'success' => true,
            'message' => 'Leave request approved successfully.',
            'data' => $leaveRequest,
        ]);
    }

    public function reject(ApprovalRequest $request, LeaveRequest $leaveRequest): JsonResponse
    {
        if (! $leaveRequest->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'This leave request has already been processed.',
            ], 422);
        }

        $leaveRequest = $this->leaveService->reject(
            $leaveRequest,
            $request->user()->id,
            $request->input('manager_remarks')
        );

        return response()->json([
            'success' => true,
            'message' => 'Leave request rejected.',
            'data' => $leaveRequest,
        ]);
    }
}