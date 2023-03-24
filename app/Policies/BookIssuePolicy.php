<?php

namespace App\Policies;

use App\Models\BookIssue;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookIssuePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BookIssue  $bookIssue
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, BookIssue $bookIssue)
    {
        //

    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->role === 'librarian' || $user->role === 'user';
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BookIssue  $bookIssue
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, BookIssue $bookIssue)
    {
        return ($user->library_id === $bookIssue->library_id && $user->role === 'librarian') || (auth()->check() && $bookIssue->user_id == auth()->id());
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BookIssue  $bookIssue
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, BookIssue $bookIssue)
    {
        return ($user->library_id === $bookIssue->library_id && $user->role === 'librarian') || (auth()->check() && $bookIssue->user_id == auth()->id());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BookIssue  $bookIssue
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, BookIssue $bookIssue)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BookIssue  $bookIssue
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, BookIssue $bookIssue)
    {
        //
    }
}
