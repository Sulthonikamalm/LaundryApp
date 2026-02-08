<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    /**
     * Send a WhatsApp message via Fonnte.
     *
     * @param string $target The recipient's phone number.
     * @param string $message The message content.
     * @return array Returns ['success' => bool, 'message' => string].
     */
    public static function sendMessage(string $target, string $message): array
    {
        $token = env('FONNTE_TOKEN');
        $endpoint = env('FONNTE_ENDPOINT', 'https://api.fonnte.com/send');

        if (empty($token)) {
            Log::error('[FonnteService] Token is missing in configuration.');
            return ['success' => false, 'message' => 'Token is missing in configuration'];
        }

        $sanitizedTarget = self::sanitizePhoneNumber($target);

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post($endpoint, [
                'target' => $sanitizedTarget,
                'message' => $message,
                'countryCode' => '62', // Optional but good for local numbers
            ]);

            // Fonnte returns JSON. We check if the request was successful HTTP-wise.
            // Note: Fonnte might return 200 even if message fails to deliver, 
            // but the API call itself was successful.
            if ($response->successful()) {
                $body = $response->json();
                
                // DeepDebug: Log full response for troubleshooting
                Log::info('[FonnteService] Success: ' . json_encode($body));
                
                // Check internal Fonnte status
                if (isset($body['status']) && $body['status'] == false) {
                   $reason = $body['reason'] ?? 'Unknown Fonnte Error';
                   Log::error('[FonnteService] API returned false status. Reason: ' . $reason);
                   
                   // DeepFix: Check if device is disconnected
                   if (str_contains(strtolower($reason), 'disconnected') || 
                       str_contains(strtolower($reason), 'device')) {
                       return ['success' => false, 'message' => $reason, 'device_error' => true];
                   }
                   
                   return ['success' => false, 'message' => $reason]; 
                }

                return ['success' => true, 'message' => 'Message sent successfully'];
            } else {
                // DeepDebug: Log HTTP error details
                $errorMsg = 'HTTP Error ' . $response->status() . ': ' . $response->body();
                Log::error('[FonnteService] ' . $errorMsg);
                return ['success' => false, 'message' => $errorMsg];
            }
        } catch (\Exception $e) {
            $errorMsg = 'Connection Exception: ' . $e->getMessage();
            Log::error('[FonnteService] ' . $errorMsg);
            return ['success' => false, 'message' => $errorMsg];
        }
    }

    /**
     * Sanitize phone number to ensure it starts with 62.
     *
     * @param string $phone
     * @return string
     */
    private static function sanitizePhoneNumber(string $phone): string
    {
        // Remove non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // If starts with 0, replace with 62
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        // If starts with 62, keep it. 
        // If it doesn't start with 62 (and didn't start with 0), we assume it's valid or can't be fixed safely.
        
        return $phone;
    }
}
