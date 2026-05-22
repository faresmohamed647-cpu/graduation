<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Services\AdminSubmissionNotifier;
use Illuminate\Http\Request;

class PublicInquiryController extends Controller
{
    public function contact(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $serviceRequest = ServiceRequest::create([
            'user_id'      => null,
            'role'         => 'guest',
            'request_type' => 'contact',
            'subject'      => $data['subject'],
            'description'  => "From: {$data['name']} <{$data['email']}>\n\n{$data['message']}",
            'notes'        => $data['email'],
            'priority'     => 'medium',
            'status'       => 'pending',
        ]);

        AdminSubmissionNotifier::notify(
            'service_request',
            'New website contact message',
            "{$data['name']}: {$data['subject']}",
            ['id' => $serviceRequest->id, 'role' => 'guest', 'action' => 'requests']
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Message sent successfully. We will contact you soon.',
            'data'    => $serviceRequest,
        ], 201);
    }

    public function newsletter(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $serviceRequest = ServiceRequest::create([
            'user_id'      => null,
            'role'         => 'guest',
            'request_type' => 'newsletter',
            'subject'      => 'Newsletter subscription',
            'description'  => "Subscribe: {$data['email']}",
            'notes'        => $data['email'],
            'priority'     => 'low',
            'status'       => 'pending',
        ]);

        AdminSubmissionNotifier::notify(
            'service_request',
            'Newsletter signup',
            $data['email'],
            ['id' => $serviceRequest->id, 'role' => 'guest', 'action' => 'requests']
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Subscribed successfully.',
            'data'    => $serviceRequest,
        ], 201);
    }
}
