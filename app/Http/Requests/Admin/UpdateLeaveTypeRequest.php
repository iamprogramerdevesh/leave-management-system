<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeaveTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $leaveTypeId = $this->route('leaveType')?->id;

        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('leave_types', 'name')->ignore($leaveTypeId)],
            'default_allocation' => ['required', 'integer', 'min:1', 'max:365'],
        ];
    }
}