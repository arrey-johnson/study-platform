<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\Exercise;
use App\Models\User;

class ExercisePolicy
{
    public function view(User $user, Exercise $exercise): bool
    {
        return $user->isAdmin() || $user->isEnrolledIn($exercise->chapter->module->course);
    }

    public function create(User $user, Course $course): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Exercise $exercise): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Exercise $exercise): bool
    {
        return $user->isAdmin();
    }

    public function submit(User $user, Exercise $exercise): bool
    {
        if (!$exercise->is_active) {
            return false;
        }

        if ($exercise->isOverdue()) {
            return false;
        }

        return $user->isEnrolledIn($exercise->chapter->module->course);
    }

    public function grade(User $user, Exercise $exercise): bool
    {
        return $user->isAdmin();
    }
} 