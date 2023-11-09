<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LinkedInLoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testLoginToLinkedIn()
    {
        // Mock a successful login response
        Http::fake([
            'your-login-url' => Http::response(['data' => 'login successful'], 200),
        ]);

        // Call the login function (replace with your actual URL)
        $response = $this->post('/login-to-linkedin');

        // Assert that the response has a 200 status code
        $response->assertStatus(200);

        // Assert that the response contains the expected message
        $response->assertJson(['Login successfully' => ['data' => 'login successful']]);
    }
}
