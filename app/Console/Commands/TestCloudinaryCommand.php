<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\CloudinaryService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * TestCloudinaryCommand - Test Cloudinary Integration
 * 
 * DeepReasoning: Command untuk test upload tanpa perlu UI.
 * DeepTeknik: Generate dummy image untuk test.
 */
class TestCloudinaryCommand extends Command
{
    protected $signature = 'test:cloudinary';
    protected $description = 'Test Cloudinary upload integration';

    public function handle(): int
    {
        $this->info('🧪 Testing Cloudinary Integration...');
        $this->newLine();

        // Check config
        $this->info('📋 Checking Configuration:');
        $cloudName = config('services.cloudinary.cloud_name');
        $apiKey = config('services.cloudinary.api_key');
        
        if (!$cloudName || !$apiKey) {
            $this->error('❌ Cloudinary credentials not found in .env');
            $this->warn('Please add CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY, and CLOUDINARY_API_SECRET to your .env file');
            return self::FAILURE;
        }

        $this->line("   Cloud Name: {$cloudName}");
        $this->line("   API Key: " . substr($apiKey, 0, 6) . '***');
        $this->newLine();

        // Create test image
        $this->info('🖼️  Creating test image...');
        $testImagePath = $this->createTestImage();
        
        if (!$testImagePath) {
            $this->error('❌ Failed to create test image');
            return self::FAILURE;
        }

        $this->line("   Test image created: {$testImagePath}");
        $this->newLine();

        // Test upload
        $this->info('☁️  Uploading to Cloudinary...');
        
        try {
            $service = new CloudinaryService();
            
            // Create UploadedFile instance
            $file = new UploadedFile(
                $testImagePath,
                'test-image.jpg',
                'image/jpeg',
                null,
                true
            );

            $url = $service->uploadActivityPhoto($file, 'TEST-2026-0001', 'washing');
            
            if ($url) {
                $this->newLine();
                $this->info('✅ Upload successful!');
                $this->line("   URL: {$url}");
                $this->newLine();
                
                // Check if it's Cloudinary or local
                if (str_contains($url, 'cloudinary.com')) {
                    $this->info('🎉 Cloudinary integration is working perfectly!');
                } else {
                    $this->warn('⚠️  Upload fell back to local storage');
                    $this->warn('   This means Cloudinary credentials might be incorrect');
                }
                
                return self::SUCCESS;
            } else {
                $this->error('❌ Upload failed - no URL returned');
                return self::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('❌ Upload failed with exception:');
            $this->error('   ' . $e->getMessage());
            return self::FAILURE;
        } finally {
            // Cleanup test image
            if (file_exists($testImagePath)) {
                unlink($testImagePath);
            }
        }
    }

    /**
     * Create a simple test image.
     * 
     * DeepTeknik: Generate image programmatically tanpa dependency eksternal.
     */
    protected function createTestImage(): ?string
    {
        $width = 400;
        $height = 300;
        
        // Create image
        $image = imagecreatetruecolor($width, $height);
        
        if (!$image) {
            return null;
        }

        // Fill with gradient
        $blue = imagecolorallocate($image, 59, 130, 246);
        $white = imagecolorallocate($image, 255, 255, 255);
        
        imagefilledrectangle($image, 0, 0, $width, $height, $blue);
        
        // Add text
        $text = 'Cloudinary Test';
        imagestring($image, 5, 120, 140, $text, $white);
        
        // Save to temp
        $tempPath = storage_path('app/temp-test-image.jpg');
        
        // Ensure directory exists
        if (!is_dir(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }
        
        imagejpeg($image, $tempPath, 90);
        imagedestroy($image);
        
        return file_exists($tempPath) ? $tempPath : null;
    }
}
