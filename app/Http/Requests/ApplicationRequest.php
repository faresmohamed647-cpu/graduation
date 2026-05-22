<?php

namespace App\Http\Requests;

use App\Enums\ApplicationRole;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $role = strtolower((string) $this->input('role'));

        $emailExists = false;
        if ($this->has('email')) {
            $emailExists = \App\Models\User::where('email', $this->input('email'))->exists();
        }
        $isUserAuth = $this->user() !== null;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => (!$isUserAuth && !$emailExists)
                ? ['required', 'string', 'min:8', 'confirmed']
                : ['nullable', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::in(ApplicationRole::values())],
            'experience' => ['required', 'string', 'max:4000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];

        if ($role === ApplicationRole::Parent->value) {
            $rules = array_merge($rules, [
                'student_state' => ['required', 'string', 'max:255'],
                'student_relationship' => ['required', 'string', 'max:100'],
                'student_count' => ['required', 'integer', 'min:1', 'max:20'],
                'student_degree' => ['required', 'string', 'max:255'],
                'student_education_system' => ['required', 'string', 'max:255'],
                'school_name' => ['required', 'string', 'max:255'],
                'school_address' => ['required', 'string', 'max:255'],
                'school_starting' => ['required', 'string', 'max:255'],
            ]);
        }

        if ($role === ApplicationRole::Driver->value) {
            $rules = array_merge($rules, [
                'owner_state' => ['required', 'string', 'max:255'],
                'owner_age' => ['required', 'integer', 'min:18', 'max:100'],
                'owner_gender' => ['required', 'string', 'max:50'],
                'car_type' => ['required', 'string', 'max:255'],
                'car_model' => ['required', 'string', 'max:255'],
                'car_plate' => ['required', 'string', 'max:255'],
            ]);
        }

        if ($role === ApplicationRole::Admin->value) {
            $rules = array_merge($rules, [
                'admin_department' => ['required', 'string', 'max:255'],
                'years_experience' => ['required', 'integer', 'min:0', 'max:50'],
                'highest_qualification' => ['required', 'string', 'max:255'],
                'availability' => ['required', 'string', 'max:255'],
            ]);
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'role' => strtolower((string) $this->input('role')),
        ]);
    }

    protected function failedValidation(Validator $validator): void
    {
        Log::warning('Application request validation failed', [
            'ip' => $this->ip(),
            'role' => $this->input('role'),
            'errors' => $validator->errors()->toArray(),
        ]);

        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422));
    }
}
