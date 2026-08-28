<?php

namespace App\Policies;

use App\Models\Fee;
use App\Models\User;

class FeePolicy
{
    /**
     * Determine whether the user can view the fee.
     */
    public function view(User $user, Fee $fee): bool
    {
        if (in_array($user->role, ['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        $enrollment = $fee->enrollment;
        if (! $enrollment) {
            return false;
        }

        if ($user->role === 'COLLEGE_ADMIN') {
            return $user->college_id && $user->college_id === $enrollment->college_id;
        }

        return $user->role === 'STUDENT' && $user->id === $enrollment->user_id;
    }

    /**
     * Determine whether the user can pay or submit payment proof for the fee.
     */
    public function pay(User $user, Fee $fee): bool
    {
        $enrollment = $fee->enrollment;

        return $user->role === 'STUDENT'
            && $enrollment
            && $user->id === $enrollment->user_id
            && ! $fee->isPaid();
    }

    /**
     * Determine whether the user can verify the fee.
     */
    public function verify(User $user, Fee $fee): bool
    {
        return in_array($user->role, ['SUPERADMIN', 'ADMIN']);
    }
}
