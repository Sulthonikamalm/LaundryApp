<?php

namespace App\Console\Commands;

use App\Services\FonnteService;
use Illuminate\Console\Command;

class TestWhatsAppCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:test {phone} {--message=Test message from SiLaundry}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test WhatsApp notification via Fonnte';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $phone = $this->argument('phone');
        $message = $this->option('message');

        $this->info("Testing WhatsApp notification...");
        $this->info("Phone: {$phone}");
        $this->info("Message: {$message}");
        $this->newLine();

        $result = FonnteService::sendMessage($phone, $message);

        if ($result['success']) {
            $this->info('✅ SUCCESS: Message sent successfully!');
            return Command::SUCCESS;
        } else {
            $this->error('❌ FAILED: ' . $result['message']);
            
            if (isset($result['device_error']) && $result['device_error']) {
                $this->newLine();
                $this->warn('⚠️  Device Error Detected!');
                $this->warn('Please check your Fonnte dashboard:');
                $this->warn('https://fonnte.com');
                $this->newLine();
                $this->warn('Make sure your WhatsApp device is connected.');
            }
            
            return Command::FAILURE;
        }
    }
}
