<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\ParentProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    /**
     * Register a new parent account.
     * Creates a User (role=parent) + ParentProfile row, then redirects to login.
     */
    public function registerParent(Request $request)
    {
        $data = $request->validate([
            'student_address'          => ['required', 'string', 'max:255'],
            'student_state'            => ['required', 'string', 'max:255'],
            'student_phone'            => ['required', 'string', 'max:30'],
            'student_relationship'     => ['required', 'string', 'max:50'],
            'student_count'            => ['required', 'integer', 'min:1'],
            'student_degree'           => ['required', 'string', 'max:255'],
            'student_education_system' => ['required', 'string', 'max:255'],
            'school_name'              => ['required', 'string', 'max:255'],
            'school_address'           => ['required', 'string', 'max:255'],
            'school_starting'          => ['required', 'string', 'max:255'],
            'student_email'            => ['required', 'email', 'unique:users,email'],
            'student_password'         => ['required', 'string', 'min:8'],
            'student_password_confirm' => ['required', 'string', 'same:student_password'],
            'student_message'          => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name'           => 'Parent',
                'email'          => $data['student_email'],
                'password'       => Hash::make($data['student_password']),
                'plain_password' => $data['student_password'],
                'role'           => 'parent',
            ]);

            ParentProfile::create([
                'user_id'          => $user->id,
                'phone'            => $data['student_phone'],
                'address'          => $data['student_address'],
                'state'            => $data['student_state'],
                'relationship'     => $data['student_relationship'],
                'student_count'    => $data['student_count'],
                'degree'           => $data['student_degree'],
                'education_system' => $data['student_education_system'],
                'school_name'      => $data['school_name'],
                'school_address'   => $data['school_address'],
                'school_starting'  => $data['school_starting'],
                'message'          => $data['student_message'] ?? null,
                'active'           => true,
            ]);

            \App\Models\Application::create([
                'user_id'    => $user->id,
                'full_name'  => $user->name,
                'email'      => $user->email,
                'phone'      => $data['student_phone'],
                'address'    => $data['student_address'],
                'role'       => 'parent',
                'experience' => $data['student_message'] ?? 'Parent registration',
                'notes'      => 'meta:{"student_state":"' . $data['student_state'] . '","student_relationship":"' . $data['student_relationship'] . '","student_count":' . $data['student_count'] . ',"student_degree":"' . $data['student_degree'] . '","student_education_system":"' . $data['student_education_system'] . '","school_name":"' . $data['school_name'] . '","school_address":"' . $data['school_address'] . '","school_starting":"' . $data['school_starting'] . '"}',
                'status'     => 'pending',
            ]);
        });

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Registration completed successfully. You can log in now.',
                'redirect' => url('/login'),
            ], 201);
        }

        return redirect('/login')->with('success', 'تم التسجيل بنجاح! سجّل دخولك الآن بالإيميل والباسورد.');
    }

    /**
     * Register a new driver account.
     * Creates a User (role=driver) + Driver row (status=pending), then redirects to login.
     */
    public function registerDriver(Request $request)
    {
        $data = $request->validate([
            'owner_state'            => ['required', 'string', 'max:255'],
            'owner_full_name'        => ['required', 'string', 'max:255'],
            'owner_age'              => ['required', 'integer', 'min:18'],
            'owner_gender'           => ['required', 'string', 'in:Male,Female'],
            'owner_phone'            => ['required', 'string', 'max:30'],
            'car_type'               => ['required', 'string', 'max:255'],
            'car_model'              => ['required', 'string', 'max:255'],
            'owner_address'          => ['required', 'string', 'max:255'],
            'car_plate'              => ['required', 'string', 'max:50'],
            'owner_email'            => ['required', 'email', 'unique:users,email'],
            'owner_password'         => ['required', 'string', 'min:8'],
            'owner_password_confirm' => ['required', 'string', 'same:owner_password'],
            'owner_message'          => ['nullable', 'string', 'max:1000'],
        ]);

        // Generate an interview date (3 business days from now at 10:00 AM)
        $interviewDate = now()->addWeekdays(3)->setTime(10, 0, 0);

        DB::transaction(function () use ($data, $interviewDate) {
            $user = User::create([
                'name'           => $data['owner_full_name'],
                'email'          => $data['owner_email'],
                'password'       => Hash::make($data['owner_password']),
                'plain_password' => $data['owner_password'],
                'role'           => 'driver',
            ]);

            Driver::create([
                'user_id'        => $user->id,
                'phone'          => $data['owner_phone'],
                'state'          => $data['owner_state'],
                'full_name'      => $data['owner_full_name'],
                'age'            => $data['owner_age'],
                'gender'         => $data['owner_gender'],
                'car_type'       => $data['car_type'],
                'car_model'      => $data['car_model'],
                'car_plate'      => $data['car_plate'],
                'address'        => $data['owner_address'],
                'message'        => $data['owner_message'] ?? null,
                'active'         => false,
                'status'         => 'interview_scheduled',
                'interview_date' => $interviewDate,
            ]);

            \App\Models\Application::create([
                'user_id'    => $user->id,
                'full_name'  => $data['owner_full_name'],
                'email'      => $user->email,
                'phone'      => $data['owner_phone'],
                'address'    => $data['owner_address'],
                'role'       => 'driver',
                'experience' => $data['owner_message'] ?? 'Driver registration',
                'notes'      => 'meta:{"owner_state":"' . $data['owner_state'] . '","owner_age":' . $data['owner_age'] . ',"owner_gender":"' . $data['owner_gender'] . '","car_type":"' . $data['car_type'] . '","car_model":"' . $data['car_model'] . '","car_plate":"' . $data['car_plate'] . '"}',
                'status'     => 'pending',
            ]);
        });

        $formattedDate = $interviewDate->format('Y-m-d h:i A');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => "Registration completed successfully. Interview date: {$formattedDate}.",
                'redirect' => url('/login'),
            ], 201);
        }

        return redirect('/login')->with('success', "تم التسجيل بنجاح! تم تحديد ميعاد المقابلة: {$formattedDate}. سيتم تفعيل حسابك بعد المقابلة.");
    }
}
