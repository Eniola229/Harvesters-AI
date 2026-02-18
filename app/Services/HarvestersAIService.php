<?php

namespace App\Services;

use App\Models\Campus;
use App\Models\ChurchInfo;
use App\Models\ChurchMember;
use App\Models\Conversation;
use App\Models\Leader;
use App\Models\Message;
use App\Models\Program;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HarvestersAIService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $model;

    public function __construct()
    {
        $this->apiKey  = config('services.zai.api_key');
        $this->baseUrl = config('services.zai.base_url', 'https://api.z.ai/v1');
        $this->model   = config('services.zai.model', 'gpt-4o');
    }

    /**
     * Process an incoming message and return AI response.
     */
    public function processMessage(
        string $phone,
        string $userMessage,
        string $channel = 'whatsapp',
        ?string $mediaUrl = null
    ): string {
        // 1. Get or create conversation
        $conversation = Conversation::firstOrCreate(
            ['phone' => $phone, 'channel' => $channel],
            ['state' => 'active']
        );

        // 2. If no member linked, check by phone
        if (!$conversation->member_id) {
            $member = ChurchMember::where('phone', $phone)->first();
            if ($member) {
                $conversation->update(['member_id' => $member->id]);
            }
        }

        // 3. Handle onboarding - ask for name if new user
        if ($conversation->state === 'waiting_name') {
            return $this->handleNameRegistration($conversation, $userMessage, $phone, $channel);
        }

        // Check if member exists
        $member = $conversation->member;
        if (!$member) {
            // New user - ask for name
            $conversation->update(['state' => 'waiting_name']);

            $greeting = "👋 Welcome to *Harvesters International Christian Centre*!\n\nI'm *Harvesters AI*, your personal guide to everything Harvesters. 🙏\n\nBefore we get started, could you please share your *name* with me?";
            
            Message::create([
                'conversation_id' => $conversation->id,
                'role' => 'assistant',
                'content' => $greeting,
            ]);

            return $greeting;
        }

        // 4. Save user message
        Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $userMessage,
            'media_url' => $mediaUrl,
        ]);

        $conversation->addToContext('user', $userMessage);

        // 5. Update member last interaction
        $member->update(['last_interaction_at' => now()]);

        // 6. Build system prompt with live church data
        $systemPrompt = $this->buildSystemPrompt($member);

        // 7. Build messages array for AI
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        // Add conversation history
        $context = $conversation->context ?? [];
        foreach ($context as $ctx) {
            $messages[] = $ctx;
        }

        // 8. Call AI
        $aiResponse = $this->callAI($messages);

        // 9. Save assistant message
        Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $aiResponse,
        ]);

        $conversation->addToContext('assistant', $aiResponse);

        // 10. Check for special commands in user message
        $aiResponse = $this->handleSpecialCommands($userMessage, $aiResponse, $member);

        return $aiResponse;
    }

    /**
     * Handle name registration for new users.
     */
protected function handleNameRegistration(
    Conversation $conversation,
    string $name,
    string $phone,
    string $channel
    ): string {
        $name = trim($name);

        // Reject common greetings/non-name inputs
        $nonNames = ['hello', 'hi', 'hey', 'helo', 'good morning', 'good evening',
                     'good afternoon', 'ok', 'okay', 'yes', 'no', 'start', 'help'];

        if (in_array(strtolower($name), $nonNames) || strlen($name) < 2 || strlen($name) > 60) {
            return "😊 Please share your *full name* so I can address you properly (e.g., *John Doe*).";
        }

        // Reject if it looks like a sentence (more than 4 words)
        if (str_word_count($name) > 4) {
            return "That doesn't look like a name. Please enter your *full name* (e.g., *John Doe*) 😊";
        }

        // Create member
        $member = ChurchMember::create([
            'name'    => ucwords(strtolower($name)),
            'phone'   => $phone,
            'channel' => $channel,
            'last_interaction_at' => now(),
        ]);

        $conversation->update([
            'member_id' => $member->id,
            'state'     => 'active',
        ]);

        $welcome = "🎉 Welcome, *{$member->name}*! It's wonderful to have you here!\n\n";
        $welcome .= "I'm *Harvesters AI*, and I'm here to help you with:\n";
        $welcome .= "• 📅 Upcoming programs & events\n";
        $welcome .= "• 🏛️ Campus locations & service times\n";
        $welcome .= "• 👥 Church leaders & contacts\n";
        $welcome .= "• 🙏 Next Level Prayers (NLP) info\n";
        $welcome .= "• 📖 Church news & announcements\n\n";
        $welcome .= "Type *ALERT ON* to receive daily NEXT LEVEL PRAYER reminders! 🌅\n\n";
        $welcome .= "How can I help you today?";

        Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $welcome,
        ]);

        return $welcome;
    }

    /**
     * Handle special commands like ALERT ON/OFF
     */
    protected function handleSpecialCommands(string $userMessage, string $aiResponse, ChurchMember $member): string
    {
        $msg = strtoupper(trim($userMessage));

        if (in_array($msg, ['ALERT ON', 'ENABLE ALERT', 'SET ALERT ON', 'MORNING ALERT ON'])) {
            $member->update(['morning_alert' => true]);
            return "✅ *Morning alerts enabled!* You'll receive a daily NEXT LEVEL PRAYER reminder at {$member->alert_time}.\n\nType *ALERT OFF* anytime to disable. 🙏";
        }

        if (in_array($msg, ['ALERT OFF', 'DISABLE ALERT', 'MORNING ALERT OFF'])) {
            $member->update(['morning_alert' => false]);
            return "🔕 Morning alerts have been *disabled*. You can re-enable anytime by typing *ALERT ON*.";
        }

        if (str_starts_with($msg, 'SET ALERT TIME')) {
            preg_match('/(\d{1,2}:\d{2}(?:\s*(?:AM|PM))?)/i', $userMessage, $matches);
            if (!empty($matches[1])) {
                $time = date('H:i:s', strtotime($matches[1]));
                $member->update(['alert_time' => $time, 'morning_alert' => true]);
                return "⏰ Alert time set to *{$matches[1]}*. Morning alerts are now enabled! 🌅";
            }
        }

        return $aiResponse;
    }

    /**
     * Build a rich system prompt with live church data.
     */
    protected function buildSystemPrompt(ChurchMember $member): string
    {
        // Fetch live data
        $upcomingPrograms = Program::upcoming()->orderBy('start_date')->take(5)->get();
        $campuses         = Campus::where('is_active', true)->get();
        $leaders          = Leader::where('is_active', true)->orderBy('order')->take(10)->get();
        $churchInfos      = ChurchInfo::where('is_active', true)->get()->groupBy('category');

        $prompt = "You are *Harvesters AI*, the official AI assistant for Harvesters International Christian Centre — a vibrant, Spirit-filled church based in Lagos, Nigeria with locations across the country and in London.\n\n";

        $prompt .= "You are warm, friendly, Spirit-filled, and knowledgeable. You speak in a conversational tone, use emojis naturally, and always point people closer to God and the church community. You respond in a helpful, encouraging, and Christ-centred manner.\n\n";

        $prompt .= "**About Harvesters:**\n";
        $prompt .= "Harvesters International Christian Centre was founded by Pastor Bolaji Idowu. The church has grown from a small gathering to thousands of worshippers across multiple campuses. The church believes in the Word of God, the power of prayer, and transformational encounters.\n\n";

        $prompt .= "**Current Member:**\n";
        $prompt .= "Name: {$member->name}\n";
        $prompt .= "Phone: {$member->phone}\n";
        $prompt .= "Morning Alerts: " . ($member->morning_alert ? 'Enabled at ' . $member->alert_time : 'Disabled') . "\n\n";

        // Church info from database
        if ($churchInfos->isNotEmpty()) {
            $prompt .= "**Church Information:**\n";
            foreach ($churchInfos as $category => $infos) {
                $prompt .= "[$category]\n";
                foreach ($infos as $info) {
                    $prompt .= "- {$info->title}: {$info->content}\n";
                }
            }
            $prompt .= "\n";
        }

        // Campuses
        if ($campuses->isNotEmpty()) {
            $prompt .= "**Campus Locations:**\n";
            foreach ($campuses as $campus) {
                $prompt .= "• {$campus->name} - {$campus->address}";
                if ($campus->service_times) {
                    $prompt .= " | Service Times: {$campus->service_times}";
                }
                if ($campus->pastor_name) {
                    $prompt .= " | Pastor: {$campus->pastor_name}";
                    if ($campus->pastor_phone) {
                        $prompt .= " ({$campus->pastor_phone})";
                    }
                }
                $prompt .= "\n";
            }
            $prompt .= "\n";
        }

        // Leaders
        if ($leaders->isNotEmpty()) {
            $prompt .= "**Church Leaders:**\n";
            foreach ($leaders as $leader) {
                $prompt .= "• {$leader->name}";
                if ($leader->title) $prompt .= " ({$leader->title})";
                if ($leader->phone) $prompt .= " - Contact: {$leader->phone}";
                $prompt .= "\n";
            }
            $prompt .= "\n";
        }

        // Upcoming programs
        if ($upcomingPrograms->isNotEmpty()) {
            $prompt .= "**Upcoming Programs & Events:**\n";
            foreach ($upcomingPrograms as $program) {
                $prompt .= "• *{$program->title}* - {$program->start_date->format('D, M j Y g:ia')}";
                if ($program->venue) $prompt .= " at {$program->venue}";
                if ($program->campus && $program->campus !== 'all') $prompt .= " ({$program->campus} campus)";
                $prompt .= "\n  {$program->description}\n";
                // Metadata
                 if ($program->metadata) {
                    foreach ($program->metadata as $key => $value) {
                        if ($value) $prompt .= "  - " . ucfirst(str_replace('_', ' ', $key)) . ": {$value}\n";
                    }
                }
            }
            $prompt .= "\n";
        }

        $prompt .= "**Special Commands (tell users about these):**\n";
        $prompt .= "- ALERT ON → Enable morning NEXT LEVEL PRAYER reminders\n";
        $prompt .= "- ALERT OFF → Disable reminders\n";
        $prompt .= "- SET ALERT TIME [time] → Change reminder time (e.g., SET ALERT TIME 6:30 AM)\n\n";

        $prompt .= "**Important Guidelines:**\n";
        $prompt .= "- Always be positive, warm, and encouraging.\n";
        $prompt .= "- If you don't have specific information, refer them to call the church or visit harvestersng.org\n";
        $prompt .= "- For prayer requests, encourage them and offer to pray with them.\n";
        $prompt .= "- For urgent pastoral needs, give the church's contact: +234 (01) 453 2030\n";
        $prompt .= "- Keep responses concise and WhatsApp-friendly (use *bold* for key points, short paragraphs).\n";
        $prompt .= "- Always end with a question or encouragement to keep the conversation going.\n";
        $prompt .= "- Remind people about NLP (Next Level Prayers) when relevant — it's a key Harvesters prayer initiative.\n";
        
        // Added specific instruction for gratitude/compliments
        $prompt .= "- If the user says 'thank you', 'thanks', 'sharp', 'wow', or anything that looks like a compliment, respond properly by saying 'You are welcome' or acknowledging the gratitude politely.\n";

        return $prompt;
    }


    /**
     * Call the Z.ai API (OpenAI-compatible)
     */
    protected function callAI(array $messages): string
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post("{$this->baseUrl}/chat/completions", [
                    'model'       => $this->model,
                    'messages'    => $messages,
                    'max_tokens'  => 800,
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                // Attempt to get content
                $content = $response->json('choices.0.message.content');

                // DEBUG: Log the actual response if content is missing
                if (!$content) {
                    Log::error('HarvestersAI Empty Response', [
                        'body' => $response->body(), 
                        'json' => $response->json()
                    ]);
                }

                return $content ?? 'I apologize, I had trouble generating a response. Please try again. 🙏';
            }

            Log::error('HarvestersAI API Error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return "I'm having a little trouble right now. Please try again in a moment. You can also reach us at harvestersng.org 🙏";

        } catch (\Exception $e) {
            Log::error('HarvestersAI Exception', ['error' => $e->getMessage()]);
            return "I'm having a little trouble right now. Please try again in a moment. You can also reach us at harvestersng.org 🙏";
        }
    }
    /**
     * Generate NLP morning devotional message
     */
    public function generateMorningDevotional(ChurchMember $member): string
    {
        return "🌅 *NEXT LEVEL PRAYER ALERT!*\n\n"
            . "Good morning, *{$member->name}*! 🙏\n\n"
            . "Next Level Prayer (NLP) is live *RIGHT NOW!* Don't miss it — join thousands of believers praying together this morning.\n\n"
            . "📺 *Join us on:*\n"
            . "• YouTube: *Harvesters International Christian Centre*\n"
            . "• X (Twitter): *@bolajiid*\n"
            . "• Facebook: *Pastor Bolaji Idowu*\n"
            . "• Instagram: *@bolajiid*\n\n"
            . "🔥 *It's time to pray! Join now and be blessed!* 🙏";
    }
}