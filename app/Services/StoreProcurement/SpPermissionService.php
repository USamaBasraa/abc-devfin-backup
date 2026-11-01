<?php

namespace App\Services\StoreProcurement;

use App\Models\Grader\Departments;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SpPermissionService
{
    public function getAccessibleDepartmentIds(User $user): array
    {
        $deptIds = collect([$user->dept_id])->filter();

        if ($user->employee_id) {
            $granted = DB::table('SP_PERMISSIONS')
                ->where('USER_ID', $user->employee_id)
                ->where('STATUS', 'Active')
                ->pluck('dept_id');

            $deptIds = $deptIds->merge($granted);
        }

        return $deptIds->unique()->filter()->values()->all();
    }

    public function getAccessibleDepartments(User $user): Collection
    {
        $deptIds = $this->getAccessibleDepartmentIds($user);

        if (empty($deptIds)) {
            return collect();
        }

        return Departments::whereIn('dept_id', $deptIds)
            ->orderBy('dept_name')
            ->get();
    }

    public function userCanAccessDepartment(User $user, int $deptId): bool
    {
        return in_array($deptId, $this->getAccessibleDepartmentIds($user), true);
    }
}
