<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use App\Mail\TestMail;
use App\Http\Controllers\SendSMSController;

class SettingController extends Controller
{
    /**
     * Get all settings
     */
    public function index(Request $request)
    {
        if ($request->user()->cannot('View System Settings')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $settings = Setting::orderBy('key')->get();

        return response()->json([
            'success' => true,
            'data' => $settings->map(function ($setting) {
                return [
                    'id' => $setting->id,
                    'key' => $setting->key,
                    'value' => $setting->value,
                    'description' => $setting->description,
                ];
            })
        ]);
    }

    /**
     * Update setting
     */
    public function update(Request $request)
    {
        if ($request->user()->cannot('Edit System Settings')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $request->validate([
            'id' => 'required|exists:settings,id',
            'value' => 'required',
            'description' => 'nullable|string',
        ]);

        $setting = Setting::findOrFail($request->id);
        $setting->value = $request->value;
        
        if ($request->has('description')) {
            $setting->description = $request->description;
        }
        
        $setting->save();

        return response()->json([
            'success' => true,
            'message' => 'Setting updated successfully.',
            'data' => [
                'id' => $setting->id,
                'key' => $setting->key,
                'value' => $setting->value,
            ]
        ]);
    }

    /**
     * Get email settings
     */
    public function emailSettings(Request $request)
    {
        if ($request->user()->cannot('View Email Settings')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'mail_mailer' => env('MAIL_MAILER'),
                'mail_host' => env('MAIL_HOST'),
                'mail_port' => env('MAIL_PORT'),
                'mail_username' => env('MAIL_USERNAME'),
                'mail_encryption' => env('MAIL_ENCRYPTION'),
                'mail_from_address' => env('MAIL_FROM_ADDRESS'),
                'mail_from_name' => env('MAIL_FROM_NAME'),
            ]
        ]);
    }

    /**
     * Update email settings
     */
    public function updateEmailSettings(Request $request)
    {
        if ($request->user()->cannot('Edit Email Settings')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $request->validate([
            'mail_mailer' => 'required|string',
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string'
        ]);

        $envFile = base_path('.env');
        if (!File::exists($envFile)) {
            return response()->json([
                'success' => false,
                'message' => '.env file not found.'
            ], 500);
        }

        $envContents = File::get($envFile);

        $envContents = preg_replace("/^MAIL_MAILER=.*$/m", "MAIL_MAILER={$request->mail_mailer}", $envContents);
        $envContents = preg_replace("/^MAIL_HOST=.*$/m", "MAIL_HOST={$request->mail_host}", $envContents);
        $envContents = preg_replace("/^MAIL_PORT=.*$/m", "MAIL_PORT={$request->mail_port}", $envContents);
        $envContents = preg_replace("/^MAIL_USERNAME=.*$/m", "MAIL_USERNAME={$request->mail_username}", $envContents);
        if ($request->mail_password) {
            $envContents = preg_replace('/^MAIL_PASSWORD=.*$/m', 'MAIL_PASSWORD="' . $request->mail_password . '"', $envContents);
        }
        $envContents = preg_replace("/^MAIL_ENCRYPTION=.*$/m", "MAIL_ENCRYPTION={$request->mail_encryption}", $envContents);
        $envContents = preg_replace("/^MAIL_FROM_ADDRESS=.*$/m", "MAIL_FROM_ADDRESS={$request->mail_from_address}", $envContents);
        $envContents = preg_replace("/^MAIL_FROM_NAME=.*$/m", "MAIL_FROM_NAME=\"{$request->mail_from_name}\"", $envContents);

        File::put($envFile, $envContents);
        Artisan::call('config:clear');

        return response()->json([
            'success' => true,
            'message' => 'Email settings updated successfully.'
        ]);
    }

    /**
     * Test email
     */
    public function testEmail(Request $request)
    {
        if ($request->user()->cannot('Edit Email Settings')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            Mail::to($request->email)->send(new TestMail());
            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get SMS settings
     */
    public function smsSettings(Request $request)
    {
        if ($request->user()->cannot('View SMS Settings')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'sms_api_url' => env('SMS_API_URL'),
                'sms_api_token' => env('SMS_API_TOKEN'),
                'sms_sender_id' => env('SMS_SENDER_ID'),
            ]
        ]);
    }

    /**
     * Update SMS settings
     */
    public function updateSmsSettings(Request $request)
    {
        if ($request->user()->cannot('Edit SMS Settings')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $request->validate([
            'sms_api_url' => 'required|string',
            'sms_api_token' => 'required|string',
            'sms_sender_id' => 'required|string'
        ]);

        $envFile = base_path('.env');
        if (!File::exists($envFile)) {
            return response()->json([
                'success' => false,
                'message' => '.env file not found.'
            ], 500);
        }

        $envContents = File::get($envFile);

        $envContents = preg_replace("/^SMS_API_URL=.*/m", "SMS_API_URL=\"{$request->sms_api_url}\"", $envContents);
        $envContents = preg_replace("/^SMS_API_TOKEN=.*/m", "SMS_API_TOKEN=\"{$request->sms_api_token}\"", $envContents);
        $envContents = preg_replace("/^SMS_SENDER_ID=.*/m", "SMS_SENDER_ID=\"{$request->sms_sender_id}\"", $envContents);

        File::put($envFile, $envContents);
        Artisan::call('config:clear');

        return response()->json([
            'success' => true,
            'message' => 'SMS settings updated successfully.'
        ]);
    }

    /**
     * Test SMS
     */
    public function testSms(Request $request)
    {
        if ($request->user()->cannot('Edit SMS Settings')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $sendSms = getSetting('sending_sms', 'disabled');
        if ($sendSms != 'enabled') {
            return response()->json([
                'success' => false,
                'message' => 'Sending SMS is not enabled in the settings.'
            ], 422);
        }

        $request->validate([
            'phone_no' => 'required|string',
            'message' => 'nullable|string',
        ]);

        $phone_no = $request->phone_no;
        $message = $request->message ?? "Hello, this is a test message from " . env('APP_NAME');

        // Format phone number
        if (strpos($phone_no, '0') === 0) {
            $phone_no = '255' . substr($phone_no, 1);
        }

        $response = (new SendSMSController())->__invoke($phone_no, $message);

        if (isset($response['status_code']) && $response['status_code'] == 200) {
            return response()->json([
                'success' => true,
                'message' => 'Test SMS sent successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $response['message'] ?? 'Failed to send test SMS.'
        ], 500);
    }
}

