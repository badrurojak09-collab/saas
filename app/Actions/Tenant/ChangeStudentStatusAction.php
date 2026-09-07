<?php

namespace App\Actions\Tenant;

use App\Enums\Tenant\StudentStatus;
use App\Events\Tenant\StudentStatusChanged;
use App\Models\Tenant\AuditLog;
use App\Models\Tenant\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChangeStudentStatusAction
{
    public function execute(
        Student $student,
        StudentStatus $newStatus,
        ?string $reason = null,
        ?string $userId = null
    ): Student {
        return DB::connection('tenant')->transaction(function () use ($student, $newStatus, $reason, $userId) {
            $previousStatus = $student->status;

            $student->update([
                'status' => $newStatus,
            ]);

            // Append-only audit log (Section 43, 63)
            AuditLog::create([
                'user_id' => $userId,
                'action' => 'status_changed',
                'resource_type' => 'Student',
                'resource_id' => $student->id,
                'request_id' => (string) Str::uuid(),
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => request()->userAgent() ?? 'System',
                'before' => ['status' => $previousStatus->value],
                'after' => ['status' => $newStatus->value],
                'metadata' => ['reason' => $reason],
            ]);

            event(new StudentStatusChanged($student, $previousStatus, $newStatus, $reason));

            return $student->fresh();
        });
    }
}
