<?php

namespace App\Jobs;

use App\Models\ChurchMember;
use App\Models\Newsletter;
use App\Services\TwilioService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected string $newsletterId) {}

    public function handle(TwilioService $twilioService): void
    {
        $newsletter = Newsletter::findOrFail($this->newsletterId);
        $newsletter->update(['status' => 'sending']);

        // Get target members
        $query = ChurchMember::where('is_active', true);
        if ($newsletter->target_campus !== 'all') {
            $query->where('campus', $newsletter->target_campus);
        }
        $members = $query->get();

        $sentCount = 0;
        foreach ($members as $member) {
            try {
                if ($newsletter->media_url) {
                    $twilioService->sendWhatsAppMedia(
                        $member->phone,
                        "*📢 {$newsletter->title}*\n\n{$newsletter->message}",
                        $newsletter->media_url
                    );
                } else {
                    $twilioService->sendWhatsApp(
                        $member->phone,
                        "*📢 {$newsletter->title}*\n\n{$newsletter->message}"
                    );
                }
                $sentCount++;
                usleep(200000);
            } catch (\Exception $e) {
                Log::error("Newsletter send failed for {$member->id}: " . $e->getMessage());
            }
        }

        $newsletter->update([
            'status'     => 'sent',
            'sent_count' => $sentCount,
            'sent_at'    => now(),
        ]);
    }
}