<?php
// Location: app/Services/TwilioService.php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    protected string $sid;
    protected string $authToken;
    protected string $whatsappFrom;
    protected string $smsFrom;
    protected string $baseUrl;
    protected string $contentBaseUrl;

    public function __construct()
    {
        $this->sid            = config('services.twilio.sid');
        $this->authToken      = config('services.twilio.auth_token');
        $this->whatsappFrom   = config('services.twilio.whatsapp_from', 'whatsapp:+14155238886');
        $this->smsFrom        = config('services.twilio.sms_from');
        $this->baseUrl        = "https://api.twilio.com/2010-04-01/Accounts/{$this->sid}";
        $this->contentBaseUrl = "https://api.twilio.com/2010-04-01/Accounts/{$this->sid}";
    }

    /**
     * Send a WhatsApp message (only works within 24hr session window)
     */
    public function sendWhatsApp(string $to, string $message, ?string $mediaUrl = null): bool
    {
        $to = str_starts_with($to, 'whatsapp:') ? $to : "whatsapp:{$to}";

        $params = [
            'From' => $this->whatsappFrom,
            'To'   => $to,
            'Body' => $message,
        ];

        if ($mediaUrl) {
            $params['MediaUrl'] = $mediaUrl;
        }

        return $this->sendMessage($params);
    }

    /**
     * Send a WhatsApp Template Message (works for cold contacts / outside 24hr window)
     *
     * @param string $to            Phone in any format — will be normalised
     * @param string $templateSid   The HX... SID from your Twilio console
     * @param array  $variables     e.g. ['1' => 'Title here', '2' => 'Body text here']
     */
    public function sendWhatsAppTemplate(string $to, string $templateSid, array $variables = []): bool
    {
        $to = str_starts_with($to, 'whatsapp:') ? $to : "whatsapp:{$to}";

        $params = [
            'From'             => $this->whatsappFrom,
            'To'               => $to,
            'ContentSid'       => $templateSid,
            'ContentVariables' => json_encode($variables),
        ];

        try {
            $response = Http::withBasicAuth($this->sid, $this->authToken)
                ->asForm()
                ->post("{$this->contentBaseUrl}/Messages.json", $params);

            if ($response->successful()) {
                return true;
            }

            Log::error('Twilio template send failed', [
                'status' => $response->status(),
                'body'   => $response->json(),
                'to'     => $to,
                'sid'    => $templateSid,
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('Twilio template exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send an SMS message
     */
    public function sendSMS(string $to, string $message, ?string $mediaUrl = null): bool
    {
        $params = [
            'From' => $this->smsFrom,
            'To'   => $to,
            'Body' => $message,
        ];

        if ($mediaUrl) {
            $params['MediaUrl'] = $mediaUrl;
        }

        return $this->sendMessage($params);
    }

    /**
     * Send WhatsApp with image/video
     */
    public function sendWhatsAppMedia(string $to, string $message, string $mediaUrl): bool
    {
        return $this->sendWhatsApp($to, $message, $mediaUrl);
    }

    /**
     * Broadcast to multiple recipients
     */
    public function broadcast(array $phones, string $message, ?string $mediaUrl = null, string $channel = 'whatsapp'): int
    {
        $successCount = 0;

        foreach ($phones as $phone) {
            try {
                if ($channel === 'whatsapp') {
                    $sent = $this->sendWhatsApp($phone, $message, $mediaUrl);
                } else {
                    $sent = $this->sendSMS($phone, $message);
                }

                if ($sent) $successCount++;
                usleep(200000);

            } catch (\Exception $e) {
                Log::error("Broadcast failed for {$phone}: " . $e->getMessage());
            }
        }

        return $successCount;
    }

    protected function sendMessage(array $params): bool
    {
        try {
            $response = Http::withBasicAuth($this->sid, $this->authToken)
                ->asForm()
                ->post("{$this->baseUrl}/Messages.json", $params);

            if ($response->successful()) {
                return true;
            }

            Log::error('Twilio send failed', [
                'status' => $response->status(),
                'body'   => $response->json(),
                'params' => $params,
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('Twilio exception: ' . $e->getMessage());
            return false;
        }
    }
}