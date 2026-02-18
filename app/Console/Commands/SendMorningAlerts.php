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
    protected $description = 'Send NEXT LEVEL PRAYER alerts to opted-in members';

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
        $this->info("Running morning alerts at {$currentTime}...");

        // Get members whose alert_time matches current time (within the current minute)
        $members = ChurchMember::where('morning_alert', true)
            ->where('is_active', true)
            ->whereRaw("TIME_FORMAT(alert_time, '%H:%i') = ?", [$currentTime])
            ->get();

        if ($members->isEmpty()) {
            $this->info("No alerts scheduled for {$currentTime}.");
            return 0;
        }

        $this->info("Sending alerts to {$members->count()} members...");

        foreach ($members as $member) {
            try {
                $devotional = $this->aiService->generateMorningDevotional($member);

                if ($member->channel === 'whatsapp') {
                    $this->twilioService->sendWhatsApp($member->phone, $devotional);
                } else {
                    $this->twilioService->sendSMS($member->phone, $devotional);
                }

                $this->info("✓ Sent to {$member->name} ({$member->phone})");
                usleep(300000); // 0.3s delay between sends

            } catch (\Exception $e) {
                Log::error("Morning alert failed for {$member->id}: " . $e->getMessage());
                $this->error("✗ Failed for {$member->name}: " . $e->getMessage());
            }
        }

        $this->info('Morning alerts completed!');
        return 0;
    }
}