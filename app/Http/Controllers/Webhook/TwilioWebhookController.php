<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Services\HarvestersAIService;
use App\Services\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TwilioWebhookController extends Controller
{
    public function __construct(
        protected HarvestersAIService $aiService,
        protected TwilioService $twilioService
    ) {}

    /**
     * Handle incoming WhatsApp / SMS from Twilio
     */
    public function handleIncoming(Request $request): Response
    {
        // Validate Twilio request signature (optional but recommended)
        Log::info('Twilio Webhook Received', $request->all());

        $from    = $request->input('From', '');   // e.g. whatsapp:+2348012345678
        $body    = trim($request->input('Body', ''));
        $numMedia = (int) $request->input('NumMedia', 0);
        $mediaUrl = $numMedia > 0 ? $request->input('MediaUrl0') : null;

        // Determine channel
        $channel = str_starts_with($from, 'whatsapp:') ? 'whatsapp' : 'sms';
        $phone   = $from; // keep the full "whatsapp:+234..." format

        try {
            // Get AI response
            $response = $this->aiService->processMessage($phone, $body ?: '(media received)', $channel, $mediaUrl);

            // Send back via Twilio
            if ($channel === 'whatsapp') {
                $this->twilioService->sendWhatsApp($phone, $response);
            } else {
                $this->twilioService->sendSMS($phone, $response);
            }

        } catch (\Exception $e) {
            Log::error('Twilio webhook error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            // Send a fallback message
            $fallback = "I'm sorry, I encountered an error. Please try again or visit harvestersng.org 🙏";
            if ($channel === 'whatsapp') {
                $this->twilioService->sendWhatsApp($phone, $fallback);
            }
        }

        // Twilio expects an empty 200 response (we handle replies via API, not TwiML)
        return response('', 200);
    }
}