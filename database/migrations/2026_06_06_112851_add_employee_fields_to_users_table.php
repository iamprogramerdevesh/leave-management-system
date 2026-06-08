<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_id', 20)->nullable()->unique()->after('id');
            $table->string('mobile', 15)->nullable()->after('email');
            $table->string('department', 50)->nullable()->after('mobile');
            $table->string('designation', 100)->nullable()->after('department');
            $table->enum('role', ['admin', 'manager', 'employee'])->default('employee')->index()->after('designation');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropUnique(['employee_id']);
            $table->dropColumn([
                'employee_id',
                'mobile',
                'department',
                'designation',
                'role',
                'status',
            ]);
        });
    }
};
