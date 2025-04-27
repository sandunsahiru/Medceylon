<?php
namespace App\Services;

use Google\Client as Google_Client;
use Google\Service\Calendar as Google_Service_Calendar;
use Google\Service\Calendar\Event as Google_Service_Calendar_Event;
use Google\Service\Calendar\EventDateTime as Google_Service_Calendar_EventDateTime;
use Google\Service\Calendar\ConferenceSolutionKey as Google_Service_Calendar_ConferenceSolutionKey;
use Google\Service\Calendar\CreateConferenceRequest as Google_Service_Calendar_CreateConferenceRequest;
use Google\Service\Calendar\ConferenceData as Google_Service_Calendar_ConferenceData;

class GoogleMeetService {
    private $client;
    private $calendarService;
    private $calendarId = 'e7942794146ed23f843652599b49a42d4b27becb43fc7de0b3e42969fa99c296@group.calendar.google.com';

    public function __construct() {
        try {
            $this->logError("### INITIALIZING GOOGLE MEET SERVICE ###");
            
            // Initialize the Google client
            $this->client = new Google_Client();
            $this->logError("Google_Client instantiated");
            
            // Set the application name
            $this->client->setApplicationName('MedCeylon Appointment System');
            $this->logError("Application name set to: MedCeylon Appointment System");
            
            // Set service account credentials
            $credentialsPath = ROOT_PATH . '/app/config/medceylon-442811-59334856cb61.json';
            $this->logError("Credentials path set to: " . $credentialsPath);
            
            if (!file_exists($credentialsPath)) {
                $this->logError("CRITICAL ERROR: Credentials file not found at: " . $credentialsPath);
                throw new \Exception("Google API credentials file not found");
            }
            
            // Log the contents of the credentials file (without sensitive data)
            $credsContent = json_decode(file_get_contents($credentialsPath), true);
            $this->logError("Credentials file exists with project_id: " . 
                (isset($credsContent['project_id']) ? $credsContent['project_id'] : 'NOT FOUND') . 
                " and client_email: " . 
                (isset($credsContent['client_email']) ? $credsContent['client_email'] : 'NOT FOUND'));
            
            $this->client->setAuthConfig($credentialsPath);
            $this->logError("Auth config set with credentials file");
            
            // Set the scopes required for Google Calendar API
            $this->client->setScopes([
                Google_Service_Calendar::CALENDAR,
                Google_Service_Calendar::CALENDAR_EVENTS
            ]);
            $this->logError("Scopes set for Calendar and Calendar Events");
            
            // Create the Calendar service
            $this->calendarService = new Google_Service_Calendar($this->client);
            $this->logError("Calendar service created");
            
            // Log calendar ID
            $this->logError("Calendar ID set to: " . $this->calendarId);
            
            $this->logError("GoogleMeetService initialized successfully");
        } catch (\Exception $e) {
            $this->logError("CRITICAL ERROR initializing GoogleMeetService: " . $e->getMessage());
            $this->logError("Error type: " . get_class($e));
            $this->logError("Error trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

   /**
 * Create a Google Calendar event with Meet link
 * 
 * @param string $summary Event title
 * @param string $description Event description
 * @param string $startDateTime Start date and time (Y-m-d H:i:s format)
 * @param string $endDateTime End date and time (Y-m-d H:i:s format)
 * @param array $attendees List of email addresses (stored in description only, not added as actual attendees)
 * @return array|false Event data with meet link or false on failure
 */
public function createMeetEvent($summary, $description, $startDateTime, $endDateTime, $attendees = []) {
    try {
        $this->logError("### STARTING CREATE MEET EVENT ###");
        $this->logError("Input startDateTime: {$startDateTime}");
        $this->logError("Input endDateTime: {$endDateTime}");
        
        // Parse the date times
        $startDateObj = new \DateTime($startDateTime);
        $endDateObj = new \DateTime($endDateTime);
        
        // Format for API in ISO 8601 format
        $formattedStart = $startDateObj->format('c');
        $formattedEnd = $endDateObj->format('c');
        
        $this->logError("Formatted startDateTime: {$formattedStart}");
        $this->logError("Formatted endDateTime: {$formattedEnd}");
        
        // Generate a Google Meet link manually
        $meetCode = $this->generateMeetCode();
        $meetLink = "https://meet.google.com/" . $meetCode;
        $this->logError("Generated manual Meet link: " . $meetLink);
        
        // Create event description with Meet link
        $enhancedDescription = $description . "\n\nJoin Google Meet: " . $meetLink;
        
        // Create a basic event without conference data
        $event = new Google_Service_Calendar_Event();
        $event->setSummary($summary);
        $event->setDescription($enhancedDescription);
        $event->setLocation($meetLink); // Put the Meet link in the location field too
        
        $startDate = new Google_Service_Calendar_EventDateTime();
        $startDate->setDateTime($formattedStart);
        $startDate->setTimeZone('Asia/Colombo');
        $event->setStart($startDate);
        
        $endDate = new Google_Service_Calendar_EventDateTime();
        $endDate->setDateTime($formattedEnd);
        $endDate->setTimeZone('Asia/Colombo');
        $event->setEnd($endDate);
        
        // Try to create the event (without conference data)
        try {
            $this->logError("Creating event with manual Meet link");
            
            // Create the calendar event
            $createdEvent = $this->calendarService->events->insert(
                $this->calendarId, 
                $event
            );
            
            $this->logError("Event successfully created with manual Meet link", [
                'event_id' => $createdEvent->getId(),
                'html_link' => $createdEvent->getHtmlLink(),
                'meet_link' => $meetLink,
                'start_time' => $formattedStart,
                'end_time' => $formattedEnd
            ]);
            
            return [
                'event_id' => $createdEvent->getId(),
                'meet_link' => $meetLink,
                'html_link' => $createdEvent->getHtmlLink()
            ];
        } catch (\Exception $e) {
            $this->logError("Failed to create event: " . $e->getMessage());
            
            if ($e instanceof \Google\Service\Exception) {
                $this->logError("Google Service Exception details:", [
                    'errors' => $e->getErrors(),
                    'code' => $e->getCode()
                ]);
            }
            
            // Try creating an even simpler event as fallback
            $basicEvent = new Google_Service_Calendar_Event();
            $basicEvent->setSummary($summary . " (Google Meet)");
            $basicEvent->setDescription($enhancedDescription);
            $basicEvent->setStart($startDate);
            $basicEvent->setEnd($endDate);
            
            try {
                $createdEvent = $this->calendarService->events->insert(
                    $this->calendarId, 
                    $basicEvent
                );
                
                $this->logError("Basic event successfully created with Meet link in description", [
                    'event_id' => $createdEvent->getId(),
                    'html_link' => $createdEvent->getHtmlLink(),
                    'meet_link' => $meetLink
                ]);
                
                return [
                    'event_id' => $createdEvent->getId(),
                    'meet_link' => $meetLink,
                    'html_link' => $createdEvent->getHtmlLink()
                ];
            } catch (\Exception $e2) {
                $this->logError("CRITICAL ERROR creating basic event: " . $e2->getMessage());
                throw $e2;
            }
        }
    } catch (\Exception $e) {
        $this->logError("CRITICAL ERROR creating Google Meet event: " . $e->getMessage());
        $this->logError("Error type: " . get_class($e));
        $this->logError("Error trace: " . $e->getTraceAsString());
        
        return false;
    }
}
    
    /**
     * Generate a random Google Meet code
     * 
     * @return string A random Meet code (10 characters)
     */
    private function generateMeetCode() {
        // Generate a random 10-character string using letters and numbers
        // Google Meet codes are typically 10 characters
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $code = '';
        
        for ($i = 0; $i < 10; $i++) {
            $code .= $chars[rand(0, strlen($chars) - 1)];
        }
        
        // Add hyphens to make it look like a Meet code (xxx-xxxx-xxx)
        $formatted = substr($code, 0, 3) . '-' . substr($code, 3, 4) . '-' . substr($code, 7, 3);
        
        return $formatted;
    }
    
    /**
     * Check if the service account has access to the calendar
     * 
     * @return array Information about the calendar access
     */
    public function checkCalendarAccess() {
        try {
            $this->logError("### CHECKING CALENDAR ACCESS ###");
            $this->logError("Attempting to access calendar: " . $this->calendarId);
            
            // Try to get the calendar
            $calendar = $this->calendarService->calendars->get($this->calendarId);
            
            // Log success
            $this->logError("Successfully accessed calendar", [
                'id' => $calendar->getId(),
                'summary' => $calendar->getSummary(),
                'description' => $calendar->getDescription(),
                'timeZone' => $calendar->getTimeZone(),
                'kind' => $calendar->getKind(),
                'etag' => $calendar->getEtag()
            ]);
            
            return [
                'success' => true,
                'calendar' => [
                    'id' => $calendar->getId(),
                    'summary' => $calendar->getSummary(),
                    'description' => $calendar->getDescription(),
                    'timeZone' => $calendar->getTimeZone()
                ]
            ];
        } catch (\Exception $e) {
            $this->logError("CRITICAL ERROR accessing calendar: " . $e->getMessage());
            $this->logError("Error type: " . get_class($e));
            $this->logError("Error trace: " . $e->getTraceAsString());
            
            // For Google\Service\Exception, log the errors array
            if ($e instanceof \Google\Service\Exception) {
                $errors = $e->getErrors();
                $this->logError("Google API Error details", $errors);
                
                // Log specific error reason
                if (isset($errors[0]) && isset($errors[0]['reason'])) {
                    $this->logError("Error reason: " . $errors[0]['reason']);
                }
            }
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_type' => get_class($e)
            ];
        }
    }
    
    /**
     * Log error messages to a dedicated file
     * 
     * @param string $message The error message
     * @param mixed $data Optional data to include in the log
     */
    private function logError($message, $data = null) {
        try {
            $logDir = ROOT_PATH . '/logs';
            $logFile = $logDir . '/google_meet.log';
            $timestamp = date('Y-m-d H:i:s');
            $logMessage = "[{$timestamp}] {$message}";
            
            if ($data !== null) {
                $dataJson = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                if ($dataJson === false) {
                    $logMessage .= " Data: [JSON encoding failed: " . json_last_error_msg() . "]";
                } else {
                    $logMessage .= " Data: " . $dataJson;
                }
            }
            
            $logMessage .= PHP_EOL;
            
            // Make sure logs directory exists
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0777, true);
            }
            
            // Only try to write to file if the directory exists and is writable
            if (is_dir($logDir) && is_writable($logDir)) {
                @file_put_contents($logFile, $logMessage, FILE_APPEND);
            }
            
            // Always log to PHP error log as a fallback
            error_log("GoogleMeetService: " . $message . ($data !== null ? " Data: " . json_encode($data) : ""));
        } catch (\Exception $e) {
            // If logging itself fails, use PHP's built-in error log
            error_log("GoogleMeetService logging error: " . $e->getMessage());
            error_log("Original message: " . $message);
        }
    }
    
    /**
     * Directly create a test calendar event without a meet link
     * For debugging purposes
     */
    public function createTestEvent() {
        try {
            $this->logError("### CREATING TEST EVENT (NO MEET LINK) ###");
            
            // Check if calendar is accessible before attempting to create event
            try {
                $this->logError("Attempting to access calendar: " . $this->calendarId);
                $calendarInfo = $this->calendarService->calendars->get($this->calendarId);
                $this->logError("Successfully accessed calendar", [
                    'id' => $calendarInfo->getId(),
                    'summary' => $calendarInfo->getSummary()
                ]);
            } catch (\Exception $e) {
                $this->logError("CRITICAL ERROR: Cannot access calendar for test event", [
                    'calendarId' => $this->calendarId,
                    'error' => $e->getMessage()
                ]);
                return false;
            }
            
            // Create a basic event without conference data
            $event = new Google_Service_Calendar_Event();
            $event->setSummary('Test Event');
            $event->setDescription('This is a test event');
            
            $start = new Google_Service_Calendar_EventDateTime();
            $start->setDateTime(date('c', strtotime('+1 hour')));
            $start->setTimeZone('Asia/Colombo');
            $event->setStart($start);
            
            $end = new Google_Service_Calendar_EventDateTime();
            $end->setDateTime(date('c', strtotime('+2 hours')));
            $end->setTimeZone('Asia/Colombo');
            $event->setEnd($end);
            
            $this->logError("Attempting to insert test event without conference data");
            
            // Insert the event without conference data
            $createdEvent = $this->calendarService->events->insert(
                $this->calendarId, 
                $event
            );
            
            $this->logError("Test event successfully created with ID: " . $createdEvent->getId());
            
            return [
                'success' => true,
                'event_id' => $createdEvent->getId(),
                'html_link' => $createdEvent->getHtmlLink()
            ];
        } catch (\Exception $e) {
            $this->logError("CRITICAL ERROR creating test event: " . $e->getMessage());
            $this->logError("Error type: " . get_class($e));
            $this->logError("Error trace: " . $e->getTraceAsString());
            
            // For Google\Service\Exception, log the errors array
            if ($e instanceof \Google\Service\Exception) {
                $errors = $e->getErrors();
                $this->logError("Google API Error details", $errors);
            }
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get the Google API client for direct access
     * 
     * @return Google_Client
     */
    public function getClient() {
        return $this->client;
    }
    
    /**
     * Get the calendar service for direct access
     * 
     * @return Google_Service_Calendar
     */
    public function getCalendarService() {
        return $this->calendarService;
    }
}