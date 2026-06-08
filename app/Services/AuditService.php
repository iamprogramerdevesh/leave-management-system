<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditService
{
    public function log(int $userId, string $action, string $module, ?string $description = null, mixed $requestData = null, mixed $responseData = null): AuditLog
    {
        return AuditLog::query()->create([
            'user_id' => $userId,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'request' => $requestData ? (is_string($requestData) ? $requestData : json_encode($requestData)) : null,
            'response' => $responseData ? (is_string($responseData) ? $responseData : json_encode($responseData)) : null,
        ]);
    }

    public function logLogin(int $userId, string $description = 'User logged in'): void
    {
        $this->log($userId, 'login', 'auth', $description);
    }

    public function logLogout(int $userId, string $description = 'User logged out'): void
    {
        $this->log($userId, 'logout', 'auth', $description);
    }

    public function logUserCreation(int $userId, mixed $requestData, mixed $responseData, string $description = 'User created'): void
    {
        $this->log($userId, 'create', 'user_management', $description, $requestData, $responseData);
    }

    public function logUserUpdate(int $userId, mixed $requestData, mixed $responseData, string $description = 'User updated'): void
    {
        $this->log($userId, 'update', 'user_management', $description, $requestData, $responseData);
    }

    public function logUserStatusChange(int $userId, mixed $requestData, string $description = 'User status changed'): void
    {
        $this->log($userId, 'status_update', 'user_management', $description, $requestData);
    }

    public function logLeaveApplication(int $userId, mixed $requestData, mixed $responseData, string $description = 'Leave application submitted'): void
    {
        $this->log($userId, 'apply', 'leave', $description, $requestData, $responseData);
    }

    public function logLeaveApproval(int $userId, mixed $requestData, string $description = 'Leave request approved'): void
    {
        $this->log($userId, 'approve', 'leave', $description, $requestData);
    }

    public function logLeaveRejection(int $userId, mixed $requestData, string $description = 'Leave request rejected'): void
    {
        $this->log($userId, 'reject', 'leave', $description, $requestData);
    }
}