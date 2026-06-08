<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(): View
    {
        return view('admin.audit_logs.index');
    }

    public function list(Request $request): JsonResponse
    {
        $query = AuditLog::query()->with('user');

        if ($request->filled('module')) {
            $query->where('module', $request->input('module'));
        }

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->input('from_date'));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->input('to_date'));
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $logs->map(fn (AuditLog $log): array => [
                'id' => $log->id,
                'user' => $log->user?->name ?? 'System',
                'action' => $log->action,
                'module' => $log->module,
                'description' => $log->description,
                'created_at' => $log->created_at->format('d-m-Y H:i:s'),
            ]),
        ]);
    }

    public function show(AuditLog $auditLog): JsonResponse
    {
        $auditLog->load('user');

        return response()->json([
            'data' => [
                'id' => $auditLog->id,
                'user' => $auditLog->user?->name ?? 'System',
                'action' => $auditLog->action,
                'module' => $auditLog->module,
                'description' => $auditLog->description,
                'request' => $auditLog->request,
                'response' => $auditLog->response,
                'created_at' => $auditLog->created_at->format('d-m-Y H:i:s'),
            ],
        ]);
    }
}