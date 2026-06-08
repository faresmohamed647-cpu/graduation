<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy extends SchoolScopedPolicy
{
    public function view(User $user, Student $student): bool
    {
        return $this->sameSchool($user, $student);
    }

    public function update(User $user, Student $student): bool
    {
        return $this->sameSchool($user, $student);
    }

    public function delete(User $user, Student $student): bool
    {
        return $this->sameSchool($user, $student);
    }
}
