<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\User;

class EnrollmentPolicy
{
    /**
     * Determine whether the user can view any enrollments.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['SUPERADMIN', 'ADMIN', 'COLLEGE_ADMIN']);
    }

    /**
     * Determine whether the user can view the specific enrollment.
     */
    public function view(User $user, Enrollment $enrollment): bool
    {
        if (in_array($user->role, ['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        if ($user->role === 'COLLEGE_ADMIN') {
            return $user->college_id && $user->college_id === $enrollment->college_id;
        }

        return $user->role === 'STUDENT' && $user->id === $enrollment->user_id;
    }

    /**
     * Determine whether the user can create enrollments.
     */
    public function create(User $user): bool
    {
        return $user->role === 'STUDENT';
    }

    /**
     * Determine whether the user can update the enrollment.
     */
    public function update(User $user, Enrollment $enrollment): bool
    {
        if (in_array($user->role, ['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        return $user->role === 'STUDENT'
            && $user->id === $enrollment->user_id
            && in_array($enrollment->status, ['DRAFT', 'PENDING']);
    }

    /**
     * Determine whether the user can delete the enrollment.
     */
    public function delete(User $user, Enrollment $enrollment): bool
    {
        if ($user->role === 'SUPERADMIN') {
            return true;
        }

        return $user->role === 'STUDENT'
            && $user->id === $enrollment->user_id
            && $enrollment->status === 'DRAFT';
    }

    /**
     * Determine whether the user can approve the enrollment.
     */
    public function approve(User $user, Enrollment $enrollment): bool
    {
        return in_array($user->role, ['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Determine whether the user can reject the enrollment.
     */
    public function reject(User $user, Enrollment $enrollment): bool
    {
        return in_array($user->role, ['SUPERADMIN', 'ADMIN']);
    }
}
