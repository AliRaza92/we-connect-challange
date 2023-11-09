<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Controllers\LinkedinAutomationController;

class LinkedInVisitProfileTest extends TestCase
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
    public function testVisitProfilePage()
    {
        // Mock a successful visit to a profile page
        $profileLinks = ['profile-link-1'];
        Http::fake([
            'profile-link-1' => Http::response(null, 200),
        ]);

        // Call the visitProfilePage function
        $response = $this->connectionService->visitProfilePage($profileLinks);

        // Assert that the response has a 200 status code
        $response->assertStatus(200);

        // Assert that the response contains the expected message
        $response->assertJson([
            'Successfuly visited profile page' => [
                'profile-link-1' => 'Visited successfully.',
            ],
        ]);
    }
}
