<?php

namespace App\Http\Controllers\Concerns;

use App\Models\School;
use Illuminate\Http\Request;

trait ResolvesSchoolScope
{
    protected function schoolId(Request $request): int
    {
        $user = $request->user();

        if ($user->role !== 'school_admin' || ! $user->school_id) {
            abort(403, 'School administrator access is not configured.');
        }

        return (int) $user->school_id;
    }

    protected function school(Request $request): School
    {
        return School::findOrFail($this->schoolId($request));
    }
}
