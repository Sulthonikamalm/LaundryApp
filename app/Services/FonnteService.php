<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\PhoneHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * FonnteService - WhatsApp Gateway Integration
 * 
 * DeepIntegration: Fonnte API wrapper untuk WhatsApp messaging
 * DeepSecurity: Token validation & error handling
 */
class FonnteService
{
    /**
     * Send a WhatsApp message via Fonnte.
     *
     * @param string $target The recipient's phone number.
     * @param string $message The message content.
     * @return array Returns ['success' => bool, 'message' => string, 'device_error' => bool].
     */
    public static function sendMessage(string $target, string $message): array
    {
        $token = config('services.fonnte.token', env('FONNTE_TOKEN'));
        $endpoint = config('services.fonnte.endpoint', env('FONNTE_ENDPOINT', 'https://api.fonnte.com/send'));

        if (empty($token)) {
            Log::error('[FonnteService] Token is missing in configuration.');
            return [
                'success' => false, 
                'message' => 'Konfigurasi WhatsApp tidak lengkap. Hubungi admin.',
                'device_error' => false,
            ];
        }

        $sanitizedTarget = PhoneHelper::normalize($target);

        try {
            $response = Http::timeout(30)
                ->withHeaders(['Authorization' => $token])
                ->post($endpoint, [
                    'target' => $sanitizedTarget,
                    'message' => $message,
                    'countryCode' => '62',
                ]);

            if ($response->successful()) {
                $body = $response->json();
                
                Log::info('[FonnteService] Response: ' . json_encode($body));
                
                if (isset($body['status']) && $body['status'] == false) {
                   $reason = $body['reason'] ?? 'Unknown Fonnte Error';
                   Log::error('[FonnteService] API returned false status. Reason: ' . $reason);
                   
                   $isDeviceError = str_contains(strtolower($reason), 'disconnected') || 
                                    str_contains(strtolower($reason), 'device');
                   
                   return [
                       'success' => false, 
                       'message' => $reason,
                       'device_error' => $isDeviceError,
                   ];
                }

                return [
                    'success' => true, 
                    'message' => 'Message sent successfully',
                    'device_error' => false,
                ];
            }

            $errorMsg = 'HTTP Error ' . $response->status() . ': ' . $response->body();
            Log::error('[FonnteService] ' . $errorMsg);
            
            return [
                'success' => false, 
                'message' => 'Gagal mengirim pesan WhatsApp. Silakan coba lagi.',
                'device_error' => false,
            ];
            
        } catch (\Exception $e) {
            Log::error('[FonnteService] Connection Exception: ' . $e->getMessage());
            return [
                'success' => false, 
                'message' => 'Koneksi ke server WhatsApp gagal. Silakan coba lagi.',
                'device_error' => false,
            ];
        }
    }
}
