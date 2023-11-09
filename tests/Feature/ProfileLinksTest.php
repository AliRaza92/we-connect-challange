<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfileLinksTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testReadProfileLinksFromFile()
    {
        // Mock a sample Excel file
        $file = UploadedFile::fake()->create('profiles.xlsx', 100);
        
        // Set up a request with the mock file
        $request = [
            'profilesList' => $file,
        ];

        // Call the API endpoint (replace with your actual endpoint)
        $response = $this->post('/api/import/list', $request);

        // Assert that the response has a 200 status code
        $response->assertStatus(200);

        // Assert that the response contains the expected message
        $response->assertJson(['File uploaded successfully']);
    }
}
