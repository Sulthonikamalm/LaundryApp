<?php

declare(strict_types=1);

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

/**
 * CloudinaryService - Image Upload & Optimization
 * 
 * DeepCode: Abstraksi upload ke Cloudinary.
 * DeepPerformance: Otomatis resize gambar untuk mobile.
 * DeepScale: Direct upload untuk mengurangi beban server.
 */
class CloudinaryService
{
    protected ?Cloudinary $cloudinary = null;

    public function __construct()
    {
        // Initialize Cloudinary SDK
        if (config('services.cloudinary.cloud_name')) {
            $this->cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => config('services.cloudinary.cloud_name'),
                    'api_key' => config('services.cloudinary.api_key'),
                    'api_secret' => config('services.cloudinary.api_secret'),
                ],
                'url' => [
                    'secure' => true,
                ],
            ]);
        }
    }

    /**
     * Upload delivery proof photo.
     * 
     * DeepPerformance: Auto-resize, format WebP, quality 80%.
     * 
     * @param UploadedFile $file
     * @param string $transactionCode
     * @return string|null URL of uploaded image
     */
    public function uploadDeliveryProof(UploadedFile $file, string $transactionCode): ?string
    {
        // Fallback ke local storage jika Cloudinary tidak dikonfigurasi
        if (!$this->cloudinary) {
            return $this->uploadToLocal($file, $transactionCode);
        }

        try {
            $result = $this->cloudinary->uploadApi()->upload($file->getRealPath(), [
                'folder' => 'laundry/delivery-proofs',
                'public_id' => $transactionCode . '_' . time(),
                'transformation' => [
                    // DeepPerformance: Resize untuk mobile (max 800px)
                    'width' => 800,
                    'height' => 800,
                    'crop' => 'limit',
                    'quality' => 'auto:good',
                    'fetch_format' => 'auto', // WebP jika browser support
                ],
                'resource_type' => 'image',
            ]);

            Log::info("Cloudinary upload success: {$result['secure_url']}");

            return $result['secure_url'];
        } catch (\Exception $e) {
            Log::error("Cloudinary upload failed: {$e->getMessage()}");
            
            // Fallback ke local
            return $this->uploadToLocal($file, $transactionCode);
        }
    }

    /**
     * Upload to local storage as fallback.
     * 
     * @param UploadedFile $file
     * @param string $transactionCode
     * @return string
     */
    protected function uploadToLocal(UploadedFile $file, string $transactionCode): string
    {
        $filename = $transactionCode . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('delivery-proofs', $filename, 'public');
        
        return asset('storage/' . $path);
    }

    /**
     * Get optimized URL for display.
     * 
     * DeepPerformance: Transform untuk thumbnail.
     * 
     * @param string $url
     * @param int $width
     * @return string
     */
    public function getOptimizedUrl(string $url, int $width = 400): string
    {
        // Jika URL Cloudinary, tambahkan transformation on-the-fly
        if (str_contains($url, 'cloudinary.com')) {
            // Insert transformation before /upload/
            return preg_replace(
                '/(\/upload\/)/',
                "/upload/w_{$width},c_limit,q_auto,f_auto/",
                $url
            );
        }

        return $url;
    }

    /**
     * Delete image from Cloudinary.
     * 
     * @param string $publicId
     * @return bool
     */
    public function delete(string $publicId): bool
    {
        if (!$this->cloudinary) {
            return false;
        }

        try {
            $this->cloudinary->uploadApi()->destroy($publicId);
            return true;
        } catch (\Exception $e) {
            Log::error("Cloudinary delete failed: {$e->getMessage()}");
            return false;
        }
    }
}
