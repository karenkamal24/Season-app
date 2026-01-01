<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FirebaseService
{
    protected $projectId;
    protected $credentialsPath;

    public function __construct()
    {
        // Use config() instead of env() directly (Laravel best practice)
        // config/services.php has default values
        $this->projectId = config('services.firebase.project_id', 'season-9ede3');
        $credentialsPath = config('services.firebase.credentials', 'firebase/season-9ede3-firebase-adminsdk-fbsvc-c1b9e2f2e7.json');
        
        // Build full path
        if (empty($credentialsPath)) {
            throw new \Exception(
                'FIREBASE_CREDENTIALS is not configured. ' .
                'Please add FIREBASE_CREDENTIALS=firebase/season-9ede3-firebase-adminsdk-fbsvc-c1b9e2f2e7.json to your .env file ' .
                'or ensure config/services.php has the correct default value.'
            );
        }
        
        // Handle both absolute and relative paths
        if (str_starts_with($credentialsPath, '/') || str_starts_with($credentialsPath, storage_path())) {
            // Absolute path
            $this->credentialsPath = $credentialsPath;
        } else {
            // Relative path from storage/app/
            $this->credentialsPath = storage_path('app/' . ltrim($credentialsPath, '/'));
        }
        
        // Validate that the path is a file, not a directory
        // Check if parent directory exists
        $parentDir = dirname($this->credentialsPath);
        if (!is_dir($parentDir)) {
            Log::warning('Firebase credentials parent directory does not exist', [
                'parent_dir' => $parentDir,
                'full_path' => $this->credentialsPath,
                'env_value' => $credentialsPath,
            ]);
        }
        
        // If the path exists and is a directory, throw error
        if (file_exists($this->credentialsPath) && is_dir($this->credentialsPath)) {
            throw new \Exception("Firebase credentials path is a directory, not a file: {$this->credentialsPath}. Please ensure FIREBASE_CREDENTIALS points to a JSON file, not a directory.");
        }
    }

    /**
     * Get OAuth 2.0 Access Token (cached for 50 minutes)
     */
    protected function getAccessToken()
    {
        return Cache::remember('firebase_access_token', 50 * 60, function () {
            try {
                // Check if credentials file exists
                if (!file_exists($this->credentialsPath)) {
                    throw new \Exception("Firebase credentials file not found at: {$this->credentialsPath}");
                }
                
                // Double-check it's a file, not a directory
                if (is_dir($this->credentialsPath)) {
                    throw new \Exception("Firebase credentials path is a directory, not a file: {$this->credentialsPath}. Please check your FIREBASE_CREDENTIALS environment variable.");
                }
                
                // Verify it's readable
                if (!is_readable($this->credentialsPath)) {
                    throw new \Exception("Firebase credentials file is not readable: {$this->credentialsPath}");
                }

                $client = new GoogleClient();
                $client->setAuthConfig($this->credentialsPath);
                $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

                $token = $client->fetchAccessTokenWithAssertion();

                if (isset($token['error'])) {
                    $errorMsg = 'Error fetching access token: ' . $token['error'];
                    if (isset($token['error_description'])) {
                        $errorMsg .= ' - ' . $token['error_description'];
                    }
                    throw new \Exception($errorMsg);
                }

                if (!isset($token['access_token'])) {
                    throw new \Exception('Access token not found in response');
                }

                Log::info('Firebase Access Token generated successfully');

                return $token['access_token'];
            } catch (\Exception $e) {
                $errorDetails = [
                    'credentials_path' => $this->credentialsPath,
                    'file_exists' => file_exists($this->credentialsPath),
                    'is_file' => is_file($this->credentialsPath),
                    'is_dir' => is_dir($this->credentialsPath),
                    'is_readable' => is_readable($this->credentialsPath),
                    'env_value' => env('FIREBASE_CREDENTIALS'),
                ];
                
                // If it's a directory, provide helpful error message
                if (is_dir($this->credentialsPath)) {
                    Log::error('Firebase credentials path is a directory, not a file', $errorDetails);
                    throw new \Exception("Firebase credentials path is a directory: {$this->credentialsPath}. Please check your FIREBASE_CREDENTIALS environment variable. It should point to a JSON file, not a directory.");
                }
                
                Log::error('Firebase Access Token Error: ' . $e->getMessage(), $errorDetails);
                throw $e;
            }
        });
    }

    /**
     * Send notification to specific device token
     */
    public function sendToDevice($fcmToken, $title, $body, $data = [])
    {
        $message = [
            'message' => [
                'token' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $this->convertDataToStrings($data),
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                        'channel_id' => 'season_app_channel',
                        'color' => '#092C4C',
                    ]
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'badge' => 1,
                        ]
                    ]
                ]
            ]
        ];

        return $this->sendMessage($message);
    }

    /**
     * Send notification to topic (all subscribed users)
     */
    public function sendToTopic($topic, $title, $body, $data = [])
    {
        $message = [
            'message' => [
                'topic' => $topic,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $this->convertDataToStrings($data),
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                        'channel_id' => 'season_app_channel',
                    ]
                ],
            ]
        ];

        return $this->sendMessage($message);
    }

    /**
     * Send notification to all users
     */
    public function sendToAllUsers($title, $body, $data = [])
    {
        return $this->sendToTopic('all_users', $title, $body, $data);
    }

    /**
     * Send notification to multiple devices
     */
    public function sendToMultipleDevices($fcmTokens, $title, $body, $data = [])
    {
        $responses = [];

        foreach ($fcmTokens as $token) {
            try {
                $response = $this->sendToDevice($token, $title, $body, $data);
                $responses[] = [
                    'token' => $token,
                    'success' => isset($response['name']),
                    'response' => $response
                ];
            } catch (\Exception $e) {
                $responses[] = [
                    'token' => $token,
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $responses;
    }

    /**
     * Send safety radius alarm notification (for group admin only)
     * Uses data-only messages (NO notification object) for full client control
     * This ensures guaranteed alarm sound playback
     */
    public function sendSafetyRadiusAlarm($fcmToken, $title, $body, $data = [])
    {
        // Add title and body to data payload (client extracts these for display)
        $dataWithTitleBody = array_merge($data, [
            'title' => $title,
            'body' => $body,
        ]);

        $message = [
            'message' => [
                'token' => $fcmToken,
                // NO notification object - data-only message for full client control
                'data' => $this->convertDataToStrings($dataWithTitleBody),
                'android' => [
                    'priority' => 'high', // High priority for immediate delivery
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default', // Required for iOS alarm sound
                            'interruption-level' => 'critical', // Critical level for alarm
                            'badge' => 1,
                        ]
                    ]
                ]
            ]
        ];

        return $this->sendMessage($message);
    }

    /**
     * Send custom message with full control
     */
    public function sendCustomMessage($message)
    {
        return $this->sendMessage(['message' => $message]);
    }

    /**
     * Send message to Firebase
     */
    protected function sendMessage($payload)
    {
        try {
            $accessToken = $this->getAccessToken();
            $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

            Log::info('Attempting to send FCM notification', [
                'project_id' => $this->projectId,
                'url' => $url
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            if ($response->successful()) {
                Log::info('FCM Notification sent successfully', [
                    'response' => $response->json()
                ]);
                return $response->json();
            } else {
                $errorBody = $response->json() ?? ['error' => $response->body()];

                Log::error('FCM Notification failed', [
                    'status' => $response->status(),
                    'error' => $errorBody,
                    'project_id' => $this->projectId
                ]);

                // More helpful error message
                $errorMessage = 'FCM Error (' . $response->status() . '): ';
                if (isset($errorBody['error']['message'])) {
                    $errorMessage .= $errorBody['error']['message'];
                } else {
                    $errorMessage .= $response->body();
                }

                // Add suggestion for 401 error
                if ($response->status() === 401) {
                    $errorMessage .= "\n\nSuggestion: Make sure Firebase Cloud Messaging API is enabled in Google Cloud Console.";
                    $errorMessage .= "\nVisit: https://console.cloud.google.com/apis/library/fcm.googleapis.com?project={$this->projectId}";
                }

                throw new \Exception($errorMessage);
            }
        } catch (\Exception $e) {
            Log::error('FCM Send Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Convert data array values to strings (FCM requirement)
     */
    protected function convertDataToStrings($data)
    {
        return array_map(function ($value) {
            return is_array($value) ? json_encode($value) : (string) $value;
        }, $data);
    }

    /**
     * Validate FCM token format
     */
    public function isValidToken($token)
    {
        return is_string($token) && strlen($token) > 100;
    }
}

