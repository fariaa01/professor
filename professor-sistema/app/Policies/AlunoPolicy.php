<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Aluno;
use App\Models\User;

class AlunoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Aluno $aluno): bool
    {
        return $aluno->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Aluno $aluno): bool
    {
        return $aluno->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Aluno $aluno): bool
    {
        return $aluno->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Aluno $aluno): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Aluno $aluno): bool
    {
        return false;
    }
}
