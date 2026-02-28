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

    public int $tries   = 3;
    public int $timeout = 3600;

    // ─────────────────────────────────────────────────────────────────────
    // PASTE YOUR APPROVED TWILIO TEMPLATE SID HERE (starts with HX...)
    // Get it from: Twilio Console → Messaging → Content Template Builder
    // ─────────────────────────────────────────────────────────────────────
    protected string $newsletterTemplateSid = 'HXe89374a661eb522ed645e23fbb5635bb';
    // ─────────────────────────────────────────────────────────────────────

    public function __construct(protected string $newsletterId) {}

    public function handle(TwilioService $twilioService): void
    {
        $newsletter = Newsletter::findOrFail($this->newsletterId);

        if ($newsletter->status === 'sent') {
            return;
        }

        $newsletter->update(['status' => 'sending']);

        // ── Pull members ──────────────────────────────────────────────────
        $query = ChurchMember::where('is_active', true)
            ->whereNotNull('phone')
            ->where('phone', '!=', '');

        if ($newsletter->target_campus && $newsletter->target_campus !== 'all') {
            $query->where('campus', $newsletter->target_campus);
        }

        $members   = $query->get();
        $sentCount = 0;
        $failCount = 0;

        foreach ($members as $member) {

            // ── Normalise phone to whatsapp:+XXXXXXXXXXXX ─────────────────
            $phone = trim($member->phone);
            $phone = str_replace(['whatsapp:', ' ', '-'], '', $phone);

            if (str_starts_with($phone, '0')) {
                $phone = '+234' . substr($phone, 1);
            } elseif (!str_starts_with($phone, '+')) {
                $phone = '+' . $phone;
            }

            $whatsappPhone = 'whatsapp:' . $phone;

            // ── Decide: free-form (within 24h session) or template ────────
            $lastChat      = $member->last_interaction_at;
            $isColdContact = true;

            if ($lastChat !== null) {
                $isColdContact = $lastChat->lt(now()->subHours(23));
            }

            try {
                $sent = false;

                if ($isColdContact) {
                    // ── COLD CONTACT: must use approved template ──────────
                    $sent = $twilioService->sendWhatsAppTemplate(
                        $whatsappPhone,
                        $this->newsletterTemplateSid,
                        [
                            '1' => $newsletter->title,
                            '2' => $newsletter->message,
                        ]
                    );
                } else {
                    // ── WARM CONTACT: free-form message ───────────────────
                    $body = "*📢 {$newsletter->title}*\n\n{$newsletter->message}";

                    if ($newsletter->media_url) {
                        $sent = $twilioService->sendWhatsAppMedia($whatsappPhone, $body, $newsletter->media_url);
                    } else {
                        $sent = $twilioService->sendWhatsApp($whatsappPhone, $body);
                    }
                }

                if ($sent) {
                    $sentCount++;
                    $type = $isColdContact ? 'template' : 'freeform';
                    Log::info("Newsletter sent ({$type}) to {$whatsappPhone}");
                } else {
                    $failCount++;
                    Log::warning("Newsletter send returned false for {$whatsappPhone}");
                }

            } catch (\Exception $e) {
                $failCount++;
                Log::error("Newsletter send failed for member {$member->id} ({$whatsappPhone}): " . $e->getMessage());
            }

            usleep(200000);
        }

        $newsletter->update([
            'status'     => 'sent',
            'sent_count' => $sentCount,
            'sent_at'    => now(),
        ]);

        Log::info("Newsletter {$newsletter->id} done. Sent: {$sentCount} | Failed: {$failCount}");
    }
}