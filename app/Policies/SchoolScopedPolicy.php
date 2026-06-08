<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class SchoolScopedPolicy
{
    protected function sameSchool(User $user, Model $model): bool
    {
        if ($user->role !== 'school_admin' || ! $user->school_id) {
            return false;
        }

        if (! isset($model->school_id)) {
            return false;
        }

        return (int) $model->school_id === (int) $user->school_id;
    }

    public function viewAny(User $user): bool
    {
        return $user->role === 'school_admin' && (bool) $user->school_id;
    }

    public function view(User $user, Model $model): bool
    {
        return $this->sameSchool($user, $model);
    }

    public function create(User $user): bool
    {
        return $user->role === 'school_admin' && (bool) $user->school_id;
    }

    public function update(User $user, Model $model): bool
    {
        return $this->sameSchool($user, $model);
    }

    public function delete(User $user, Model $model): bool
    {
        return $this->sameSchool($user, $model);
    }
}
