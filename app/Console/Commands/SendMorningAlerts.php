<?php

namespace App\Console\Commands;

use App\Models\ChurchMember;
use App\Services\HarvestersAIService;
use App\Services\TwilioService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendMorningAlerts extends Command
{
    protected $signature   = 'harvesters:morning-alerts';
    protected $description = 'Send morning NLP devotional alerts to subscribed members';

    public function __construct(
        protected HarvestersAIService $aiService,
        protected TwilioService $twilioService
    ) {
        parent::__construct();
    }

public function handle(): int
{
    // Skip Saturday (6) and Sunday (0)
    if (in_array(now()->dayOfWeek, [0, 6])) {
        $this->info('Skipping morning alerts — weekend.');
        return 0;
    }

    $currentTime = now()->format('H:i');

    $members = ChurchMember::where('morning_alert', true)
        ->whereRaw("TIME_FORMAT(alert_time, '%H:%i') = ?", [$currentTime])
        ->get();

    if ($members->isEmpty()) {
        return 0;  // ← this line
    }

    $this->info("Sending morning alerts to {$members->count()} members at {$currentTime}");

    foreach ($members as $member) {
        try {
            $result   = $this->aiService->generateMorningDevotional($member);
            $text     = $result['text'];
            $mediaUrl = $result['media_url'] ?? null;

            if ($member->channel === 'whatsapp') {
                $this->twilioService->sendWhatsApp($member->phone, $text, $mediaUrl);
            } else {
                $this->twilioService->sendSMS($member->phone, $text, $mediaUrl);
            }

            $this->info("✓ Sent to {$member->name} ({$member->phone})");

        } catch (\Exception $e) {
            Log::error("Morning alert failed for {$member->phone}: " . $e->getMessage());
            $this->error("✗ Failed for {$member->phone}: " . $e->getMessage());
        }

        usleep(300000);
    }

    $this->info('Morning alerts done.');
    return 0;  // ← and this line
}
}