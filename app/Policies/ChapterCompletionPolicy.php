<?php

namespace App\Policies;

use App\Models\ChapterCompletion;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChapterCompletionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any chapter completions.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the chapter completion.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ChapterCompletion  $completion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ChapterCompletion $completion)
    {
        return $user->isAdmin() || $user->id === $completion->user_id;
    }

    /**
     * Determine whether the user can create chapter completions.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        // Any authenticated user can mark a chapter as completed
        return true;
    }

    /**
     * Determine whether the user can update the chapter completion.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ChapterCompletion  $completion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, ChapterCompletion $completion)
    {
        // Only the user who created the completion can update it
        return $user->id === $completion->user_id;
    }

    /**
     * Determine whether the user can delete the chapter completion.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ChapterCompletion  $completion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, ChapterCompletion $completion)
    {
        return $user->isAdmin() || $user->id === $completion->user_id;
    }
}
