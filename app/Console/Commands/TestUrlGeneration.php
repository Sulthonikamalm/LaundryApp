<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Transaction;
use Illuminate\Console\Command;

/**
 * TestUrlGeneration - Test command untuk verify URL generation
 * 
 * Usage: php artisan test:url-generation
 */
class TestUrlGeneration extends Command
{
    protected $signature = 'test:url-generation';
    protected $description = 'Test URL generation for WhatsApp tracking links';

    public function handle(): int
    {
        $this->info('Testing URL Generation...');
        $this->newLine();

        // Get first transaction
        $transaction = Transaction::with('customer')->first();

        if (!$transaction) {
            $this->error('No transactions found in database. Create a transaction first.');
            return self::FAILURE;
        }

        $this->info("Testing with Transaction: {$transaction->transaction_code}");
        $this->newLine();

        // Test route generation
        $url = route('public.tracking.show', ['token' => $transaction->url_token]);

        $this->table(
            ['Config', 'Value'],
            [
                ['APP_URL', config('app.url')],
                ['APP_ENV', config('app.env')],
                ['Generated URL', $url],
            ]
        );

        $this->newLine();

        // Validate URL
        if (str_contains($url, 'localhost') && config('app.env') === 'production') {
            $this->error('❌ CRITICAL: APP_URL is still localhost in production!');
            $this->warn('Set APP_URL to your Koyeb domain in environment variables.');
            $this->warn('Example: APP_URL=https://your-app-name.koyeb.app');
            return self::FAILURE;
        }

        if (str_starts_with($url, 'https://') || str_starts_with($url, 'http://')) {
            $this->info('✅ URL generation looks good!');
            $this->info('Users will be able to access: ' . $url);
            return self::SUCCESS;
        }

        $this->error('❌ Invalid URL format: ' . $url);
        return self::FAILURE;
    }
}
