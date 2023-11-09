<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Controllers\LinkedinAutomationController;

class LinkedInSendConnectionTest extends TestCase
{

    private $connectionService;
    function __construct() {
        $this->connectionService = new LinkedinAutomationController();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testSendConnection()
    {
        // Mock a successful connection request
        $profileId = 'profile-id-1';
        Http::fake([
            'linkedin-api-url/profile-id-1/connections' => Http::response(null, 200),
        ]);

        // Set the config message
        Config::set('linkedin.connection_message');

        // Call the sendConnection function
        $response = $this->connectionService->sendConnection($profileId);

        // Assert that the response has a 200 status code
        $response->assertStatus(200);

        // Assert that the response contains the expected message
        $response->assertJson(['Invite Send Successfully' => 'Connection request sent successfully.']);
    }
}
