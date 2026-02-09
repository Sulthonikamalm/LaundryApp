<?php

namespace App\Services;

use App\Helpers\PhoneHelper;
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

        // DeepSecurity: Jangan expose detail konfigurasi ke user
        if (empty($token)) {
            Log::error('[FonnteService] Token is missing in configuration.');
            return ['success' => false, 'message' => 'Konfigurasi WhatsApp tidak lengkap. Hubungi admin.'];
        }

        // DeepCode: Gunakan PhoneHelper untuk normalisasi
        $sanitizedTarget = PhoneHelper::normalize($target);

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
                // DeepSecurity: Log detail error, tapi jangan expose ke user
                $errorMsg = 'HTTP Error ' . $response->status() . ': ' . $response->body();
                Log::error('[FonnteService] ' . $errorMsg);
                return ['success' => false, 'message' => 'Gagal mengirim pesan WhatsApp. Silakan coba lagi.'];
            }
        } catch (\Exception $e) {
            // DeepSecurity: Log exception detail, tapi jangan expose ke user
            Log::error('[FonnteService] Connection Exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Koneksi ke server WhatsApp gagal. Silakan coba lagi.'];
        }
    }
}
