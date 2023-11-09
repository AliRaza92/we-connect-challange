<?php

namespace App\Http\Controllers;

use App\Imports\ProfileImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use App\Config\linkedin;

class LinkedinAutomationController extends Controller
{
    private $baseUrl;
    private $email;
    private $password;

    function __construct() {
        $this->baseUrl = 'https://www.linkedin.com/login';
        $this->email = env('LINKEDIN_EMAIL');
        $this->password = env('LINKEDIN_PASSWORD');
    }

    
    /**
     * Read data from a file, validate it, and insert into the database.
    *
    * @param \Illuminate\Http\Request $request The HTTP request containing 'profilesList'.
    * @return \Illuminate\Http\Response Returns an HTTP response.
    */ 
    public function readProfileLinksFromFile(Request $request): Response
    {
        
        try {

            $validator = Validator::make($request->all(), [
                'profilesList' => 'required|mimes:xls,xlsx'
            ]);
        
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }
        
            $path = $request->file('profilesList')->getRealPath();
            $collection = Excel::toArray(new ProfileImport, $request->file('profilesList'));
            return response()->json(['File uploaded successfuly' => $validator->errors()], 200);
            //step to insert into database

        }  catch (GuzzleException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ],500);
        }
    }  

    /**
     * Fetching login credentail ENV to Login user account
    *
    * 
    * @return \Illuminate\Http\Response Returns an HTTP response.
    */ 
    public function loginToLinkedIn(): Response
    {
        try {
            $response = Http::post($this->baseUrl, [
                'email' => $this->email,
                'password' => $this->password,
            ]);
    
            if ($response->status() !== 200) {
                throw new Exception('Login failed');
            }
    
            return response()->json(['Login successfuly' => $response], 200);
        } catch (GuzzleException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ],500);
        }
    }

    /**
     * Visit each user profile 
    *
    * 
    * @return \Illuminate\Http\Response Returns an HTTP response.
    */ 
    public function visitProfilePage(array $profileLinks): Response
    {
        $results = [];
        foreach ($profileLinks as $link) {
            try {
                // Here, we'd use the LinkedIn API to visit each profile page
                $response = $this->client->request('GET', $link);

                if ($response->getStatusCode() === 200) {
                    // Successfully visited the profile
                    $results[$link] = 'Visited successfully.';
                } else {
                    // The profile page exists but the response is not successful
                    $results[$link] = 'Profile returned a non-successful HTTP status.';
                }
                return response()->json(['Successfuly visited profile page' => $results], 200);
            } catch (GuzzleException $exception) {
                // Catch any Guzzle exceptions and log them
                return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],500);
            }
        }
    }


    /**
     * Send new invite to people 
    *
    * 
    * @return \Illuminate\Http\Response Returns an HTTP response.
    */ 
    public function sendConnection($profileId)
    {
        $results = [];
               
        try {                      
        
        // Sending profile id to Linkedin API for sending the connection
            $alreadyConnected = $this->checkIfAlreadyConnected($profileId); 
            if ($alreadyConnected) {
                return response()->json(['Already connected.'], 200);
            }

            // Sending connection  with config  message file path
            $response = $this->sendConnectionRequest($profileId, Config::get('linkedin.connection_message'));

            if ($response->getStatusCode() === 200) {
                $result = 'Connection request sent successfully.';
            } else {          
                $results = 'Failed to send connection request, non-successful status code received.';
            }        
            return response()->json(['Invite Send Successfully' => $results], 200);
        } catch (GuzzleException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ],500);
        }  
    }

    /**
     * Check profile is already connected 
    *
    * 
    * @return \response.
    */
    public function checkIfAlreadyConnected($profileId): bool
    {
        // Let assume Linkedin profile connection check api
        $response = Http::post($this->baseUrl, [
            'id' => $profileId,
        ]);

        $result = ($response->getStatusCode() === 200) ? true:false;
        return $result;
    }

    /**
     * Generate Report with result
    *
    * 
    * @return \file.
    */ 
    public function generateTestReport($totalProfiles, $failedProfiles)
    {
        $successfulProfiles = $totalProfiles - $failedProfiles;
        $report = "Total Profiles Processed: $totalProfiles\n";
        $report .= "Successful Profiles: $successfulProfiles\n";
        $report .= "Failed Profiles: $failedProfiles\n";
    
        return file_put_contents('test_report.txt', $report);
    }
}
