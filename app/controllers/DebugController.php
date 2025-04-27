<?php

namespace App\Controllers;

use App\Services\GoogleMeetService;

class DebugController extends BaseController
{
    public function testMeet()
    {
        try {
            // Log the start of the test
            error_log("Starting Google Meet integration test");
            
            // Create a Google Meet service instance
            $googleMeetService = new GoogleMeetService();
            
            // Test data
            $summary = "Test Appointment";
            $description = "Test Description";
            $startDateTime = date('Y-m-d H:i:s', strtotime('+1 hour')); // 1 hour from now
            $endDateTime = date('Y-m-d H:i:s', strtotime('+2 hours')); // 2 hours from now
            $attendees = ['test@example.com']; // Test email
            
            // Log the test parameters
            error_log("Test parameters: " . json_encode([
                'summary' => $summary,
                'startDateTime' => $startDateTime,
                'endDateTime' => $endDateTime,
                'attendees' => $attendees
            ]));
            
            // Create a test event
            $eventData = $googleMeetService->createMeetEvent(
                $summary,
                $description,
                $startDateTime,
                $endDateTime,
                $attendees
            );
            
            // Log and display the result
            error_log("Google Meet test result: " . json_encode($eventData));
            
            echo "<h1>Google Meet Test</h1>";
            echo "<pre>" . print_r($eventData, true) . "</pre>";
            
            if (!$eventData) {
                echo "<p style='color:red'>Failed to create Google Meet event.</p>";
                echo "<p>Check the server logs for more details.</p>";
                echo "<p>Look in: " . ROOT_PATH . "/logs/google_meet.log</p>";
            } else {
                echo "<p style='color:green'>Successfully created Google Meet event!</p>";
                if (isset($eventData['meet_link'])) {
                    echo "<p>Meet Link: <a href='" . $eventData['meet_link'] . "' target='_blank'>" . $eventData['meet_link'] . "</a></p>";
                } else {
                    echo "<p style='color:orange'>No Meet link was generated.</p>";
                }
            }
        } catch (\Exception $e) {
            error_log("Error in Google Meet test: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            echo "<h1>Google Meet Test Error</h1>";
            echo "<p style='color:red'>" . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
        
        exit();
    }
    public function testAuth()
{
    try {
        echo "<h1>Google API Direct Auth Test</h1>";
        
        // Load the service account credentials file
        $credentialsPath = ROOT_PATH . '/app/config/medceylon-442811-59334856cb61.json';
        
        if (!file_exists($credentialsPath)) {
            echo "<p style='color:red'>Credentials file not found at: {$credentialsPath}</p>";
            exit;
        }
        
        // Print out the content without private key
        $credentialsContent = file_get_contents($credentialsPath);
        $credentials = json_decode($credentialsContent, true);
        echo "<h2>Credentials Content (Sanitized)</h2>";
        
        // Safety check to make sure we don't display private keys
        if (isset($credentials['private_key'])) {
            $credentials['private_key'] = "[REDACTED]";
        }
        
        echo "<pre>" . json_encode($credentials, JSON_PRETTY_PRINT) . "</pre>";
        
        // Create a new client and test auth
        $client = new \Google\Client();
        $client->setApplicationName('MedCeylon Appointment System');
        $client->setAuthConfig($credentialsPath);
        $client->setScopes(['https://www.googleapis.com/auth/calendar']);
        
        // Try to get an access token
        $accessToken = $client->fetchAccessTokenWithAssertion();
        
        echo "<h2>Access Token Response</h2>";
        echo "<pre>" . json_encode($accessToken, JSON_PRETTY_PRINT) . "</pre>";
        
        if (isset($accessToken['access_token'])) {
            echo "<p style='color:green'>Authentication Successful! Token received.</p>";
            
            // Create the Calendar service
            $service = new \Google\Service\Calendar($client);
            
            echo "<h2>Testing Calendar List</h2>";
            
            try {
                // Try to list calendars
                $calendarList = $service->calendarList->listCalendarList();
                
                echo "<p>Found " . count($calendarList->getItems()) . " calendars</p>";
                
                echo "<ul>";
                foreach ($calendarList->getItems() as $calItem) {
                    echo "<li>" . $calItem->getSummary() . " (" . $calItem->getId() . ")</li>";
                }
                echo "</ul>";
                
                // Try to get the specific calendar
                echo "<h2>Testing Specific Calendar</h2>";
                $calendarId = 'e7942794146ed23f843652599b49a42d4b27becb43fc7de0b3e42969fa99c296@group.calendar.google.com';
                
                try {
                    $calendar = $service->calendars->get($calendarId);
                    echo "<p style='color:green'>Found calendar: " . $calendar->getSummary() . "</p>";
                } catch (\Exception $e) {
                    echo "<p style='color:red'>Error getting specific calendar: " . $e->getMessage() . "</p>";
                }
                
            } catch (\Exception $e) {
                echo "<p style='color:red'>Error listing calendars: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p style='color:red'>Authentication Failed! No token received.</p>";
            if (isset($accessToken['error'])) {
                echo "<p>Error: " . $accessToken['error'] . "</p>";
                if (isset($accessToken['error_description'])) {
                    echo "<p>Description: " . $accessToken['error_description'] . "</p>";
                }
            }
        }
        
    } catch (\Exception $e) {
        echo "<h1>Google API Auth Test Error</h1>";
        echo "<p style='color:red'>" . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
    exit;
}



    public function checkCalendarAccess()
    {
        try {
            $googleMeetService = new GoogleMeetService();
            $calendarInfo = $googleMeetService->checkCalendarAccess();
            
            echo "<h1>Calendar Access Check</h1>";
            echo "<pre>" . print_r($calendarInfo, true) . "</pre>";
            
            if ($calendarInfo['success']) {
                echo "<p style='color:green'>Successfully accessed calendar!</p>";
            } else {
                echo "<p style='color:red'>Failed to access calendar.</p>";
                echo "<p>Error: " . $calendarInfo['error'] . "</p>";
                if (isset($calendarInfo['error_type'])) {
                    echo "<p>Error Type: " . $calendarInfo['error_type'] . "</p>";
                }
            }
            
            echo "<p>Check detailed logs at: " . ROOT_PATH . "/logs/google_meet.log</p>";
        } catch (\Exception $e) {
            echo "<h1>Calendar Access Error</h1>";
            echo "<p style='color:red'>" . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
        
        exit();
    }
    
    public function testBasicEvent()
    {
        try {
            $googleMeetService = new GoogleMeetService();
            $result = $googleMeetService->createTestEvent();
            
            echo "<h1>Test Basic Event (No Meet Link)</h1>";
            echo "<pre>" . print_r($result, true) . "</pre>";
            
            if ($result['success']) {
                echo "<p style='color:green'>Successfully created basic event!</p>";
                echo "<p>Event ID: " . $result['event_id'] . "</p>";
                echo "<p>HTML Link: <a href='" . $result['html_link'] . "' target='_blank'>" . $result['html_link'] . "</a></p>";
            } else {
                echo "<p style='color:red'>Failed to create basic event.</p>";
                echo "<p>Error: " . $result['error'] . "</p>";
            }
            
            echo "<p>Check detailed logs at: " . ROOT_PATH . "/logs/google_meet.log</p>";
        } catch (\Exception $e) {
            echo "<h1>Test Basic Event Error</h1>";
            echo "<p style='color:red'>" . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
        
        exit();
    }
    
    public function serverInfo()
    {
        echo "<h1>Server Info</h1>";
        
        // PHP Version
        echo "<h2>PHP Version</h2>";
        echo "<p>" . phpversion() . "</p>";
        
        // Installed Extensions
        echo "<h2>Installed Extensions</h2>";
        echo "<pre>" . implode(", ", get_loaded_extensions()) . "</pre>";
        
        // Check for required extensions
        $requiredExtensions = ['curl', 'json', 'openssl', 'mbstring'];
        echo "<h2>Required Extensions</h2>";
        echo "<ul>";
        foreach ($requiredExtensions as $ext) {
            echo "<li>";
            echo $ext . ": " . (extension_loaded($ext) ? "<span style='color:green'>Loaded</span>" : "<span style='color:red'>Not Loaded</span>");
            echo "</li>";
        }
        echo "</ul>";
        
        // Google API Client Version (if available)
        echo "<h2>Google API Client</h2>";
        if (class_exists('Google\Client')) {
            echo "<p style='color:green'>Google API Client is available</p>";
            if (defined('Google\Client::LIBVER')) {
                echo "<p>Version: " . \Google\Client::LIBVER . "</p>";
            }
        } else {
            echo "<p style='color:red'>Google API Client is NOT available</p>";
        }
        
        // Check for credentials file
        $credentialsPath = ROOT_PATH . '/app/config/medceylon-442811-59334856cb61.json';
        echo "<h2>Google API Credentials</h2>";
        if (file_exists($credentialsPath)) {
            echo "<p style='color:green'>Credentials file exists at: " . $credentialsPath . "</p>";
            
            // Check if the file is readable
            if (is_readable($credentialsPath)) {
                echo "<p style='color:green'>Credentials file is readable</p>";
                
                // Check the contents (without showing sensitive data)
                $creds = json_decode(file_get_contents($credentialsPath), true);
                if ($creds) {
                    echo "<p>Credentials file contains valid JSON</p>";
                    echo "<p>Project ID: " . (isset($creds['project_id']) ? $creds['project_id'] : 'NOT FOUND') . "</p>";
                    echo "<p>Client Email: " . (isset($creds['client_email']) ? $creds['client_email'] : 'NOT FOUND') . "</p>";
                } else {
                    echo "<p style='color:red'>Credentials file does not contain valid JSON</p>";
                }
            } else {
                echo "<p style='color:red'>Credentials file is not readable</p>";
            }
        } else {
            echo "<p style='color:red'>Credentials file does not exist at: " . $credentialsPath . "</p>";
        }
        
        // Check for logs directory
        $logsDir = ROOT_PATH . '/logs';
        echo "<h2>Logs Directory</h2>";
        if (is_dir($logsDir)) {
            echo "<p style='color:green'>Logs directory exists at: " . $logsDir . "</p>";
            
            // Check if the directory is writable
            if (is_writable($logsDir)) {
                echo "<p style='color:green'>Logs directory is writable</p>";
            } else {
                echo "<p style='color:red'>Logs directory is not writable</p>";
            }
            
            // Check for Google Meet log file
            $googleMeetLogFile = $logsDir . '/google_meet.log';
            if (file_exists($googleMeetLogFile)) {
                echo "<p>Google Meet log file exists at: " . $googleMeetLogFile . "</p>";
                
                // Check if the file is writable
                if (is_writable($googleMeetLogFile)) {
                    echo "<p style='color:green'>Google Meet log file is writable</p>";
                } else {
                    echo "<p style='color:red'>Google Meet log file is not writable</p>";
                }
                
                // Show the last few lines of the log file
                echo "<h3>Last 10 lines of Google Meet log file</h3>";
                $logContents = file($googleMeetLogFile);
                if ($logContents) {
                    $lastLines = array_slice($logContents, -10);
                    echo "<pre>" . implode("", $lastLines) . "</pre>";
                } else {
                    echo "<p>Could not read log file contents</p>";
                }
            } else {
                echo "<p>Google Meet log file does not exist yet. It will be created when needed.</p>";
            }
        } else {
            echo "<p>Logs directory does not exist yet. It will be created when needed.</p>";
        }
        
        exit();
    }
    
    /**
     * Test the service account authentication and token generation
     */
    public function testServiceAccount()
    {
        try {
            echo "<h1>Service Account Test</h1>";
            
            // Load the service account credentials file
            $credentialsPath = ROOT_PATH . '/app/config/medceylon-442811-59334856cb61.json';
            
            if (!file_exists($credentialsPath)) {
                echo "<p style='color:red'>Credentials file not found at: {$credentialsPath}</p>";
                exit;
            }
            
            $credentials = json_decode(file_get_contents($credentialsPath), true);
            
            echo "<h2>Service Account Details</h2>";
            echo "<p>Project ID: " . ($credentials['project_id'] ?? 'Not found') . "</p>";
            echo "<p>Client Email: " . ($credentials['client_email'] ?? 'Not found') . "</p>";
            
            // Test instantiating the client
            $client = new \Google\Client();
            $client->setApplicationName('MedCeylon Appointment System');
            $client->setAuthConfig($credentialsPath);
            $client->setScopes(['https://www.googleapis.com/auth/calendar']);
            
            // Get the access token
            $accessToken = $client->fetchAccessTokenWithAssertion();
            
            echo "<h2>Access Token Result</h2>";
            echo "<pre>" . json_encode($accessToken, JSON_PRETTY_PRINT) . "</pre>";
            
            if (isset($accessToken['access_token'])) {
                echo "<p style='color:green'>Successfully obtained access token!</p>";
                
                // Test token by making a simple API request
                $client->setAccessToken($accessToken);
                $service = new \Google\Service\Calendar($client);
                
                try {
                    echo "<h2>Testing Token with Calendar List Request</h2>";
                    $calendarList = $service->calendarList->listCalendarList();
                    echo "<p style='color:green'>Successfully retrieved calendar list!</p>";
                    echo "<p>Found " . count($calendarList->getItems()) . " calendars</p>";
                    
                    // List first few calendars
                    if (count($calendarList->getItems()) > 0) {
                        echo "<h3>Calendars:</h3>";
                        echo "<ul>";
                        foreach ($calendarList->getItems() as $calendarListEntry) {
                            echo "<li>" . $calendarListEntry->getSummary() . " (" . $calendarListEntry->getId() . ")</li>";
                        }
                        echo "</ul>";
                    }
                } catch (\Exception $e) {
                    echo "<p style='color:red'>Error testing token: " . $e->getMessage() . "</p>";
                }
            } else {
                echo "<p style='color:red'>Failed to obtain access token.</p>";
                if (isset($accessToken['error'])) {
                    echo "<p>Error: " . $accessToken['error'] . "</p>";
                    if (isset($accessToken['error_description'])) {
                        echo "<p>Description: " . $accessToken['error_description'] . "</p>";
                    }
                }
            }
            
        } catch (\Exception $e) {
            echo "<h1>Service Account Test Error</h1>";
            echo "<p style='color:red'>" . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
        exit;
    }
    
    /**
     * Test creating a basic calendar event directly with the API
     */
    public function testBasicCalendarEvent()
    {
        try {
            echo "<h1>Basic Calendar Event Test</h1>";
            
            $credentialsPath = ROOT_PATH . '/app/config/medceylon-442811-59334856cb61.json';
            
            // Create client and set up authentication
            $client = new \Google\Client();
            $client->setApplicationName('MedCeylon Appointment System');
            $client->setAuthConfig($credentialsPath);
            $client->setScopes(['https://www.googleapis.com/auth/calendar']);
            
            // Get service
            $service = new \Google\Service\Calendar($client);
            
            // Create a basic event
            $event = new \Google\Service\Calendar\Event([
                'summary' => 'Test Basic Event',
                'location' => 'Virtual',
                'description' => 'A test event created directly with the API',
                'start' => [
                    'dateTime' => date('c', strtotime('+1 hour')),
                    'timeZone' => 'Asia/Colombo',
                ],
                'end' => [
                    'dateTime' => date('c', strtotime('+2 hours')),
                    'timeZone' => 'Asia/Colombo',
                ],
                'attendees' => [
                    ['email' => 'test@example.com'],
                ],
            ]);
            
            // Calendar ID - try primary first
            $calendarId = 'primary';
            
            try {
                echo "<p>Attempting to insert event into calendar: " . $calendarId . "</p>";
                $createdEvent = $service->events->insert($calendarId, $event);
                echo "<p style='color:green'>Event created successfully!</p>";
                echo "<p>Event ID: " . $createdEvent->getId() . "</p>";
                echo "<p>Link: <a href='" . $createdEvent->getHtmlLink() . "' target='_blank'>" . $createdEvent->getHtmlLink() . "</a></p>";
            } catch (\Exception $e) {
                echo "<p style='color:red'>Error with primary calendar: " . $e->getMessage() . "</p>";
                
                // Try with the specific calendar ID
                $calendarId = 'e7942794146ed23f843652599b49a42d4b27becb43fc7de0b3e42969fa99c296@group.calendar.google.com';
                
                try {
                    echo "<p>Attempting with specific calendar ID: " . $calendarId . "</p>";
                    $createdEvent = $service->events->insert($calendarId, $event);
                    echo "<p style='color:green'>Event created successfully in specific calendar!</p>";
                    echo "<p>Event ID: " . $createdEvent->getId() . "</p>";
                    echo "<p>Link: <a href='" . $createdEvent->getHtmlLink() . "' target='_blank'>" . $createdEvent->getHtmlLink() . "</a></p>";
                } catch (\Exception $e2) {
                    echo "<p style='color:red'>Error with specific calendar: " . $e2->getMessage() . "</p>";
                    throw $e2;
                }
            }
            
        } catch (\Exception $e) {
            echo "<h1>Basic Calendar Event Test Error</h1>";
            echo "<p style='color:red'>" . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
        exit;
    }
    
    /**
     * Test creating a Meet conference directly with the API
     */
    public function testConferenceEvent()
    {
        try {
            echo "<h1>Conference Event Test</h1>";
            
            $credentialsPath = ROOT_PATH . '/app/config/medceylon-442811-59334856cb61.json';
            
            // Create client and set up authentication
            $client = new \Google\Client();
            $client->setApplicationName('MedCeylon Appointment System');
            $client->setAuthConfig($credentialsPath);
            $client->setScopes(['https://www.googleapis.com/auth/calendar']);
            
            // Get service
            $service = new \Google\Service\Calendar($client);
            
            // Create a conference solution key
            $solutionKey = new \Google\Service\Calendar\ConferenceSolutionKey();
            $solutionKey->setType('eventNamedHangout');
            
            // Create a conference request
            $conferenceRequest = new \Google\Service\Calendar\CreateConferenceRequest();
            $conferenceRequest->setRequestId('meet-test-' . uniqid());
            $conferenceRequest->setConferenceSolutionKey($solutionKey);
            
            // Create conference data
            $conferenceData = new \Google\Service\Calendar\ConferenceData();
            $conferenceData->setCreateRequest($conferenceRequest);
            
            // Create the event with conference data
            $event = new \Google\Service\Calendar\Event([
                'summary' => 'Test Conference Event',
                'location' => 'Virtual Meeting',
                'description' => 'A test event with Google Meet integration',
                'start' => [
                    'dateTime' => date('c', strtotime('+1 hour')),
                    'timeZone' => 'Asia/Colombo',
                ],
                'end' => [
                    'dateTime' => date('c', strtotime('+2 hours')),
                    'timeZone' => 'Asia/Colombo',
                ],
                'attendees' => [
                    ['email' => 'sandunsbandara13@gmail.com'],
                ],
            ]);
            $event->setConferenceData($conferenceData);
            
            // Calendar ID
            $calendarId = 'e7942794146ed23f843652599b49a42d4b27becb43fc7de0b3e42969fa99c296@group.calendar.google.com';
            
            // Insert the event with conferenceDataVersion=1
            $createdEvent = $service->events->insert(
                $calendarId, 
                $event, 
                ['conferenceDataVersion' => 1]
            );
            
            echo "<p style='color:green'>Conference event created successfully!</p>";
            echo "<p>Event ID: " . $createdEvent->getId() . "</p>";
            echo "<p>Link: <a href='" . $createdEvent->getHtmlLink() . "' target='_blank'>" . $createdEvent->getHtmlLink() . "</a></p>";
            
            $meetLink = $createdEvent->getHangoutLink();
            if ($meetLink) {
                echo "<p>Google Meet Link: <a href='" . $meetLink . "' target='_blank'>" . $meetLink . "</a></p>";
            } else {
                echo "<p style='color:orange'>No Meet link was generated!</p>";
                
                // Check for conference data
                if ($createdEvent->getConferenceData()) {
                    echo "<h3>Conference Data Details:</h3>";
                    $confData = $createdEvent->getConferenceData();
                    
                    // Check for entry points
                    if ($confData->getEntryPoints()) {
                        echo "<p>Entry Points Found:</p>";
                        echo "<ul>";
                        foreach ($confData->getEntryPoints() as $entryPoint) {
                            echo "<li>Type: " . $entryPoint->getEntryPointType();
                            if ($entryPoint->getUri()) {
                                echo " - URI: <a href='" . $entryPoint->getUri() . "' target='_blank'>" . $entryPoint->getUri() . "</a>";
                            }
                            echo "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<p>No entry points found in conference data</p>";
                    }
                    
                    // Check conference status
                    if ($confData->getConferenceId()) {
                        echo "<p>Conference ID: " . $confData->getConferenceId() . "</p>";
                    }
                    
                    // Print raw conference data
                    echo "<h4>Raw Conference Data:</h4>";
                    echo "<pre>" . json_encode($confData, JSON_PRETTY_PRINT) . "</pre>";
                } else {
                    echo "<p>No conference data was returned in the event</p>";
                }
            }
            
        } catch (\Exception $e) {
            echo "<h1>Conference Event Test Error</h1>";
            echo "<p style='color:red'>" . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
        exit;
    }
}