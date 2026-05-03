<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiJsonBehaviorTest extends TestCase
{
    public function test_protected_api_routes_return_json_unauthenticated_response_without_accept_header(): void
    {
        $this->get('/api/parent/children')
            ->assertStatus(401)
            ->assertHeader('content-type', 'application/json')
            ->assertJsonPath('status', 'error')
            ->assertJsonPath('message', 'Unauthenticated');
    }

    public function test_post_login_validation_errors_are_json_without_accept_header(): void
    {
        $this->post('/api/auth/login', [])
            ->assertStatus(422)
            ->assertHeader('content-type', 'application/json')
            ->assertJsonStructure([
                'status',
                'message',
                'errors',
            ]);
    }
}
