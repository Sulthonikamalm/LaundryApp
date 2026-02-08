<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send a WhatsApp message via Fonnte.
     *
     * @param string $target The recipient's phone number.
     * @param string $message The message content.
     * @return array|null The API response.
     */
    public static function send(string $target, string $message): ?array
    {
        $token = env('FONNTE_TOKEN');
        
        if (empty($token)) {
            Log::warning('[WhatsAppService] Fonnte token is missing in .env');
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post(env('FONNTE_ENDPOINT', 'https://api.fonnte.com/send'), [
                'target' => $target,
                'message' => $message,
                // 'countryCode' => '62', // Optional
            ]);

            if ($response->successful()) {
                Log::info('[WhatsAppService] Message sent to ' . $target);
                return $response->json();
            } else {
                Log::error('[WhatsAppService] Failed to send message: ' . $response->body());
                return $response->json(); // Return error response for debugging
            }
        } catch (\Exception $e) {
            Log::error('[WhatsAppService] Exception: ' . $e->getMessage());
            return null;
        }
    }
}
