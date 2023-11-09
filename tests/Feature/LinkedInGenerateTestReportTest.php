<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Controllers\LinkedinAutomationController;

class LinkedInGenerateTestReportTest extends TestCase
{


    private $connectionService;
    function __construct() {
        $this->connectionService = new LinkedinAutomationController();
    }

    /**
     * Test Sample file generated.
     *
     * @return void
     */
    public function testGenerateTestReport()
    {
        // Mock the totalProfiles and failedProfiles values
        $totalProfiles = 10;
        $failedProfiles = 2;

        // Call the generateTestReport function
        $result = $this->connectionService->generateTestReport($totalProfiles, $failedProfiles);

        // Assert that the function returns true (indicating successful file write)
        $this->assertTrue($result);

        // Assert that the report file exists
        $this->assertFileExists('test_report.txt');

        // Read the contents of the report file
        $reportContents = file_get_contents('test_report.txt');

        // Assert that the report contains the expected values
        $this->assertStringContainsString("Total Profiles Processed: $totalProfiles", $reportContents);
        $this->assertStringContainsString("Successful Profiles: " . ($totalProfiles - $failedProfiles), $reportContents);
        $this->assertStringContainsString("Failed Profiles: $failedProfiles", $reportContents);
    }
}
