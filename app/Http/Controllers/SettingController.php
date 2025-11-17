<?php

namespace App\Http\Controllers;

use App\Mail\TestMail;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\SendSMSController;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        if (Auth()->user()->cannot('View System Settings')) {
            abort(403, 'Access Denied');
        }

        $data = [
            'title' => 'ALL SETTINGS',
            'breadcrumbs' => [
                ['name' => 'Settings', 'url' => route('settings.index'), 'icon' => 'uil uil-estate'],
                ['name' => 'All Settings', 'url' => null, 'active' => true]
            ]
        ];

        // Get all settings
        $settings = Setting::orderBy('key')->get();

        return view('settings.index', compact('data', 'settings'));
    }

    public function update(Request $request)
    {
        if (Auth()->user()->cannot('Edit System Settings')) {
            abort(403, 'Access Denied');
        }

        try {
            $request->validate([
                'id' => 'required|exists:settings,id',
                'value' => 'required',
                'description' => 'required'
            ]);

            $setting = Setting::findOrFail($request->id);

            // Update basic fields (value, description)
            $setting->value = $request->input('value');
            $setting->description = $request->input('description');

            // Handle logo upload if provided
            if ($request->hasFile('value')) {
                // Ensure the storage directory exists
                $storagePath = storage_path('app/public/logos');
                if (!File::exists($storagePath)) {
                    File::makeDirectory($storagePath, 0777, true, true);
                }

                // Delete previous logo if exists
                if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                    Storage::disk('public')->delete($setting->value);
                }

                // Store new logo and update setting value
                $logoPath = $request->file('value')->store('logos', 'public');
                $setting->value = $logoPath;
            }

            // Save updated setting
            $setting->save();

            return back()->with('success', 'Setting updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error in update() function: ' . $e->getMessage());
            return back()->with('error', 'An error occurred. ' . $e->getMessage());
        }
    }

    public function emailSettings()
    {
        if (Auth()->user()->cannot('View Email Settings')) {
            abort(403, 'Access Denied');
        }

        $mail_settings_complete = !empty(env('MAIL_MAILER')) &&
            !empty(env('MAIL_HOST')) &&
            !empty(env('MAIL_PORT')) &&
            !empty(env('MAIL_USERNAME')) &&
            !empty(env('MAIL_PASSWORD')) &&
            !empty(env('MAIL_ENCRYPTION')) &&
            !empty(env('MAIL_FROM_ADDRESS')) &&
            !empty(env('MAIL_FROM_NAME'));

        $data = [
            'title' => 'EMAIL SETTINGS',
            'breadcrumbs' => [
                ['name' => 'Settings', 'url' => route('settings.index'), 'icon' => 'uil uil-estate'],
                ['name' => 'Email Settings', 'url' => null, 'active' => true]
            ],
            'mail_settings_complete' => $mail_settings_complete,
            'mail_mailer' => env('MAIL_MAILER'),
            'mail_host' => env('MAIL_HOST'),
            'mail_port' => env('MAIL_PORT'),
            'mail_username' => env('MAIL_USERNAME'),
            'mail_password' => env('MAIL_PASSWORD'),
            'mail_encryption' => env('MAIL_ENCRYPTION'),
            'mail_from_address' => env('MAIL_FROM_ADDRESS'),
            'mail_from_name' => env('MAIL_FROM_NAME')
        ];

        return view('email-settings.index', compact('data'));
    }

    public function updateEmailSettings(Request $request)
    {
        if (Auth()->user()->cannot('Edit Email Settings')) {
            abort(403, 'Access Denied');
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
        $envContents = File::get($envFile);

        $envContents = preg_replace("/^MAIL_MAILER=.*$/m", "MAIL_MAILER={$request->mail_mailer}", $envContents);
        $envContents = preg_replace("/^MAIL_HOST=.*$/m", "MAIL_HOST={$request->mail_host}", $envContents);
        $envContents = preg_replace("/^MAIL_PORT=.*$/m", "MAIL_PORT={$request->mail_port}", $envContents);
        $envContents = preg_replace("/^MAIL_USERNAME=.*$/m", "MAIL_USERNAME={$request->mail_username}", $envContents);
        $envContents = preg_replace('/^MAIL_PASSWORD=.*$/m', 'MAIL_PASSWORD="' . $request->mail_password . '"', $envContents);
        $envContents = preg_replace("/^MAIL_ENCRYPTION=.*$/m", "MAIL_ENCRYPTION={$request->mail_encryption}", $envContents);
        $envContents = preg_replace("/^MAIL_FROM_ADDRESS=.*$/m", "MAIL_FROM_ADDRESS={$request->mail_from_address}", $envContents);
        $envContents = preg_replace("/^MAIL_FROM_NAME=.*$/m", "MAIL_FROM_NAME=\"{$request->mail_from_name}\"", $envContents);

        File::put($envFile, $envContents);

        Artisan::call('config:clear');

        return redirect()->back()->with('success', 'email Settings updated successfully.');
    }

    public function sendTestEmail(Request $request)
    {
        if (Auth()->user()->cannot('Edit Email Settings')) {
            abort(403, 'Access Denied');
        }

        $request->validate([
            'email' => 'required|email',
        ]);

        $recipient = $request->input('email');

        try {
            Mail::to($recipient)->send(new TestMail());
            return back()->with('success', 'Test email sent successfully to ' . $recipient);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send test email. Error: ' . $e->getMessage());
        }
    }

    public function smsSettings()
    {
        if (Auth()->user()->cannot('View SMS Settings')) {
            abort(403, 'Access Denied');
        }

        $sms_settings_complete = !empty(env('SMS_API_URL')) &&
            !empty(env('SMS_API_TOKEN')) &&
            !empty(env('SMS_SENDER_ID'));

        $data = [
            'title' => 'SMS SETTINGS',
            'breadcrumbs' => [
                ['name' => 'Settings', 'url' => route('settings.index'), 'icon' => 'uil uil-estate'],
                ['name' => 'SMS Settings', 'url' => null, 'active' => true]
            ],
            'sms_settings_complete' => $sms_settings_complete,
            'sms_api_url' => env('SMS_API_URL'),
            'sms_api_token' => env('SMS_API_TOKEN'),
            'sms_sender_id' => env('SMS_SENDER_ID'),
        ];

        return view('sms-settings.index', compact('data'));
    }

    public function updateSmsSettings(Request $request)
    {
        if (auth()->user()->cannot('Edit SMS Settings')) {
            abort(403, 'Access Denied');
        }

        $validatedData = $request->validate([
            'sms_api_url' => 'required',
            'sms_api_token' => 'required',
            'sms_sender_id' => 'required'
        ]);

        $envFile = base_path('.env');
        $envContents = File::get($envFile);

        $envContents = preg_replace("/^SMS_API_URL=.*/m", "SMS_API_URL=\"{$request->sms_api_url}\"", $envContents);
        $envContents = preg_replace("/^SMS_API_TOKEN=.*/m", "SMS_API_TOKEN=\"{$request->sms_api_token}\"", $envContents);
        $envContents = preg_replace("/^SMS_SENDER_ID=.*/m", "SMS_SENDER_ID=\"{$request->sms_sender_id}\"", $envContents);

        File::put($envFile, $envContents);

        Artisan::call('config:clear');

        return redirect()->back()->with('success', 'SMS settings updated successfully!');
    }

    public function sendSms(Request $request)
    {
        if (auth()->user()->cannot('Edit SMS Settings')) {
            abort(403, 'Access Denied');
        }

        $sendSms = getSetting('sending_sms', 'disabled');

        if ($sendSms != 'enabled') {
            return back()->with('warning', 'Sending SMS is not enabled in the settings.');
        }

        $request->validate([
            'phone_no' => 'required',
        ]);

        $phone_no = $request->input('phone_no');

        if ($request->message) {
            $message = $request->message;
        } else {
            $message = "Hello, this is a test message from " . env('APP_NAME') . "";
        }

        // Format phone number
        if (strpos($phone_no, '0') === 0) {
            $phone_no = '255' . substr($phone_no, 1);
        }

        // Dispatch SendSMSController with phone number and message
        $response = (new SendSMSController())->__invoke($phone_no, $message);

        // Handle the response from SendSMSController
        if (isset($response['status_code'])) {
            $statusCode = $response['status_code'];
            $statusMessage = $response['message'] ?? 'No message provided';
            $statusMeaning = $response['status_meaning'] ?? 'Unknown';

            if ($statusCode == 200) {
                return back()->with('success', "$statusMessage ($statusMeaning)");
            } else {
                return back()->with('error', "$statusMessage ($statusMeaning)");
            }
        } else {
            return back()->with('error', 'Failed to send test SMS. Please check logs for details.');
        }
    }
}

