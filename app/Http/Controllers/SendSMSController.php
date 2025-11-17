<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class SendSMSController extends Controller
{
    public function __invoke($phone = null, $message = null)
    {
        if (is_null($phone) || is_null($message)) {
            return [
                'message' => 'Failed to send SMS: Required parameters missing',
                'status_code' => 400,
                'status_meaning' => 'Bad Request'
            ];
        }

        // Get the SMS API details from .env
        $apiUrl = env('SMS_API_URL');
        $apiToken = env('SMS_API_TOKEN');
        $senderId = env('SMS_SENDER_ID');

        try {
            // Prepare the request data
            $response = Http::post($apiUrl, [
                'api_token' => $apiToken,
                'recipient' => $phone,
                'sender_id' => $senderId,
                'type' => 'plain',
                'message' => $message,
            ]);

            // Log the request details
            Log::info('SMS sent', [
                'phone' => $phone,
                'message' => $message,
                'response' => $response->json()
            ]);

            // Check the response status and handle accordingly
            $responseData = $response->json();
            if ($response->successful()) {
                return [
                    'message' => $responseData['message'] ?? 'SMS sent successfully',
                    'status_code' => 200,
                    'status_meaning' => 'OK',
                    'data' => $responseData['data'] ?? null
                ];
            } else {
                return [
                    'message' => $responseData['message'] ?? 'Failed to send SMS',
                    'status_code' => $response->status(),
                    'status_meaning' => 'Error',
                    'data' => $responseData['data'] ?? null
                ];
            }
        } catch (Exception $e) {
            // Log the error
            Log::error("Error sending SMS: " . $e->getMessage(), [
                'phone' => $phone,
                'message' => $message
            ]);

            return [
                'message' => 'Failed to send SMS: ' . $e->getMessage(),
                'status_code' => 500,
                'status_meaning' => 'Internal Server Error'
            ];
        }
    }
}

