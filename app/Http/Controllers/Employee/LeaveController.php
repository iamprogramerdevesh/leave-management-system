<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreLeaveRequest;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Services\LeaveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveController extends Controller
{
    public function __construct(private LeaveService $leaveService) {}

    public function create(): View
    {
        $leaveTypes = LeaveType::query()->orderBy('name')->get();

        return view('employee.leave.create', compact('leaveTypes'));
    }

    public function store(StoreLeaveRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        $duration = $this->leaveService->calculateDuration($data['start_date'], $data['end_date']);

        if (! $this->leaveService->validateBalance($user->id, $data['leave_type_id'], $duration)) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient leave balance.',
            ], 422);
        }

        if (! $this->leaveService->validateNoOverlap($user->id, $data['start_date'], $data['end_date'])) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a leave request overlapping with the selected dates.',
            ], 422);
        }

        $data['duration'] = $duration;
        $leaveRequest = $this->leaveService->applyLeave($data, $user->id);

        return response()->json([
            'success' => true,
            'message' => 'Leave application submitted successfully.',
            'data' => $leaveRequest,
        ], 201);
    }

    public function history(): View
    {
        $leaveTypes = LeaveType::query()->orderBy('name')->get();

        return view('employee.leave.history', compact('leaveTypes'));
    }

    public function list(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = LeaveRequest::query()
            ->with(['leaveType', 'approver'])
            ->where('user_id', $user->id);

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
                'leave_type' => $lr->leaveType?->name,
                'start_date' => $lr->start_date->format('d-m-Y'),
                'end_date' => $lr->end_date->format('d-m-Y'),
                'duration' => $lr->duration . ($lr->duration == '1' ? ' Day' : ' Days'),
                'reason' => $lr->reason,
                'status' => $lr->status,
                'manager_remarks' => $lr->manager_remarks,
                'approved_by' => $lr->approver?->name,
                'approved_at' => $lr->approved_at?->format('d-m-Y H:i'),
                'created_at' => $lr->created_at->format('d-m-Y H:i'),
            ]),
        ]);
    }
}
