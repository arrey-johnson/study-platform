<?php

namespace App\Policies;

use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CourseEnrollmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any enrollments.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the enrollment.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CourseEnrollment  $enrollment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, CourseEnrollment $enrollment)
    {
        return $user->isAdmin() || $user->id === $enrollment->user_id;
    }

    /**
     * Determine whether the user can create enrollments.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the enrollment.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CourseEnrollment  $enrollment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, CourseEnrollment $enrollment)
    {
        return $user->isAdmin();
    }
}
