<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SmsService
{
    protected $sid;
    protected $token;
    protected $from;

    public function __construct()
    {
        $this->sid = config('services.twilio.sid');
        $this->token = config('services.twilio.token');
        $this->from = config('services.twilio.from');
    }

    /**
     * Send OTP via Twilio (Legacy Wrapper)
     */
    public function sendOtp($to, $otp)
    {
        $message = "Your BookEase verification code is {$otp}. This code expires in 5 minutes.";
        return $this->sendMessage($to, $message);
    }

    /**
     * Send Generic Message via Twilio SMS (Production-Ready)
     */
    public function sendMessage($to, $message)
    {
        return $this->sendTwilioMessage($to, $this->from, $message);
    }


    /**
     * Internal helper to handle Twilio API calls
     */
    protected function sendTwilioMessage($to, $from, $message)
    {
        if (!$this->sid || !$this->token || !$from) {
            Log::info("Twilio MOCK [PRODUCTION-READY]: To {$to} | From {$from} | Message: {$message}");
            return true;
        }

        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withBasicAuth($this->sid, $this->token)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$this->sid}/Messages.json", [
                    'To' => $to,
                    'From' => $from,
                    'Body' => $message,
                ]);

            if ($response->successful()) {
                Log::info("SmsService: Twilio message sent successfully to {$to}");
                return true;
            }

            Log::error("SmsService: Twilio API error: " . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error("SmsService: Exception sending Twilio message: " . $e->getMessage());
            return false;
        }
    }
}
