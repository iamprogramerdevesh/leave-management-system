<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLeaveTypeRequest;
use App\Http\Requests\Admin\UpdateLeaveTypeRequest;
use App\Models\LeaveType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveTypeController extends Controller
{
    public function index(): View
    {
        return view('admin.leave_types.index');
    }

    public function list(Request $request): JsonResponse
    {
        $leaveTypes = LeaveType::query()
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $leaveTypes->map(fn (LeaveType $leaveType): array => [
                'id' => $leaveType->id,
                'name' => $leaveType->name,
                'default_allocation' => $leaveType->default_allocation,
                'created_at' => $leaveType->created_at->format('d-m-Y'),
            ]),
        ]);
    }

    public function show(LeaveType $leaveType): JsonResponse
    {
        return response()->json([
            'data' => [
                'id' => $leaveType->id,
                'name' => $leaveType->name,
                'default_allocation' => $leaveType->default_allocation,
            ],
        ]);
    }

    public function store(StoreLeaveTypeRequest $request): JsonResponse
    {
        $leaveType = LeaveType::query()->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Leave type created successfully.',
            'data' => $leaveType,
        ], 201);
    }

    public function update(UpdateLeaveTypeRequest $request, LeaveType $leaveType): JsonResponse
    {
        $leaveType->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Leave type updated successfully.',
            'data' => $leaveType->fresh(),
        ]);
    }
}