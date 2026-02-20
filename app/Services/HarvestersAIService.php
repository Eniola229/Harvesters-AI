<?php
// Location: app/Services/HarvestersAIService.php

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
     * Returns: ['text' => string, 'media_url' => string|null]
     */
    public function processMessage(
        string $phone,
        string $userMessage,
        string $channel = 'whatsapp',
        ?string $mediaUrl = null
    ): array {
        $conversation = Conversation::firstOrCreate(
            ['phone' => $phone, 'channel' => $channel],
            ['state' => 'active']
        );

        if (!$conversation->member_id) {
            $member = ChurchMember::where('phone', $phone)->first();
            if ($member) {
                $conversation->update(['member_id' => $member->id]);
            }
        }

        if ($conversation->state === 'waiting_name') {
            return $this->handleNameRegistration($conversation, $userMessage, $phone, $channel);
        }

        $member = $conversation->member;
        if (!$member) {
            $conversation->update(['state' => 'waiting_name']);
            $greeting = "👋 Welcome to *Harvesters International Christian Centre*!\n\nI'm *Harvesters AI*, your personal guide to everything Harvesters. 🙏\n\nBefore we get started, could you please share your *name* with me?";
            Message::create(['conversation_id' => $conversation->id, 'role' => 'assistant', 'content' => $greeting]);
            return ['text' => $greeting, 'media_url' => null];
        }

        Message::create([
            'conversation_id' => $conversation->id,
            'role'            => 'user',
            'content'         => $userMessage,
            'media_url'       => $mediaUrl,
        ]);

        $conversation->addToContext('user', $userMessage);
        $member->update(['last_interaction_at' => now()]);

        $commandResult = $this->checkSpecialCommands($userMessage, $member);
        if ($commandResult) {
            Message::create(['conversation_id' => $conversation->id, 'role' => 'assistant', 'content' => $commandResult]);
            $conversation->addToContext('assistant', $commandResult);
            return ['text' => $commandResult, 'media_url' => null];
        }

        $relevantMedia = $this->detectRelevantMedia($userMessage);
        $systemPrompt  = $this->buildSystemPrompt($member);
        $messages      = [['role' => 'system', 'content' => $systemPrompt]];

        foreach ($conversation->context ?? [] as $ctx) {
            $messages[] = $ctx;
        }

        $aiText = $this->callAI($messages);

        Message::create([
            'conversation_id' => $conversation->id,
            'role'            => 'assistant',
            'content'         => $aiText,
            'media_url'       => $relevantMedia,
        ]);

        $conversation->addToContext('assistant', $aiText);

        return ['text' => $aiText, 'media_url' => $relevantMedia];
    }

    /**
     * Detect relevant media URL based on message content.
     * Priority: Leaders > Campuses > Programs
     */
    protected function detectRelevantMedia(string $userMessage): ?string
    {
        $msg        = strtolower($userMessage);
        $allLeaders = Leader::where('is_active', true)->get();

        // ── LEADERS: triggered by pastor/leader keywords ──────────────────
        $leaderTriggers = ['pastor', 'leader', 'founder', 'reverend', 'bishop', 'overseer', 'who is'];

        if ($this->containsAny($msg, $leaderTriggers)) {
            $cleanMsg = preg_replace('/\b(pst|pastor|rev|reverend|bishop|dr|prof)\.?\s*/i', '', $msg);

            foreach ($allLeaders as $leader) {
                $cleanName = preg_replace('/\b(pst|pastor|rev|reverend|bishop|dr|prof)\.?\s*/i', '', strtolower($leader->name));
                $nameParts = array_filter(explode(' ', trim($cleanName)), fn($p) => strlen($p) >= 3);
                foreach ($nameParts as $part) {
                    if (str_contains($cleanMsg, $part)) {
                        return !empty($leader->image_url) ? $leader->image_url : null;
                    }
                }
            }
        }

        // ── CAMPUSES: triggered by campus/location keywords ───────────────
        $campusTriggers = ['campus', 'church', 'location', 'branch', 'address',
                           'near', 'around', 'close', 'service time', 'worship',
                           'where is', 'directions'];

        if ($this->containsAny($msg, $campusTriggers)) {
            $allCampuses = Campus::where('is_active', true)->get();
            $msgWords    = array_filter(explode(' ', $msg), fn($w) => strlen($w) >= 3);

            foreach ($allCampuses as $campus) {
                $campusWords = array_filter(
                    explode(' ', strtolower($campus->name)),
                    fn($w) => strlen($w) >= 4
                );

                $matched = false;
                foreach ($campusWords as $campusWord) {
                    foreach ($msgWords as $msgWord) {
                        similar_text($campusWord, $msgWord, $percent);
                        if ($percent >= 75) {
                            $matched = true;
                            break 2;
                        }
                    }
                }

                if ($matched) {
                    // Asking about campus pastor
                    if ($this->containsAny($msg, ['pastor', 'overseer', 'leader'])) {
                        if (!empty($campus->pastor_name)) {
                            $cleanPastor = preg_replace('/\b(pst|pastor|rev|reverend|bishop|dr|prof)\.?\s*/i', '', strtolower($campus->pastor_name));
                            $pastorParts = array_filter(explode(' ', trim($cleanPastor)), fn($p) => strlen($p) >= 3);
                            foreach ($allLeaders as $leader) {
                                $cleanLeader = preg_replace('/\b(pst|pastor|rev|reverend|bishop|dr|prof)\.?\s*/i', '', strtolower($leader->name));
                                foreach ($pastorParts as $part) {
                                    if (str_contains($cleanLeader, $part)) {
                                        return !empty($leader->image_url) ? $leader->image_url : null;
                                    }
                                }
                            }
                        }
                        return null;
                    }

                    return !empty($campus->image_url) ? $campus->image_url : null;
                }
            }
        }

        // ── PROGRAMS ──────────────────────────────────────────────────────
        $programTitles = Program::where('is_active', true)
            ->where('start_date', '>=', now())
            ->pluck('title')
            ->map(fn($t) => strtolower($t))
            ->toArray();

        $programKeywords = array_merge($programTitles, [
            'program', 'event', 'nlp', 'next level prayer', 'conference',
            'flyer', 'registration', 'upcoming', 'revival', 'concert', 'seminar',
        ]);

        if ($this->containsAny($msg, $programKeywords)) {
            $programs = Program::where('is_active', true)
                ->whereNotNull('image_url')
                ->where('image_url', '!=', '')
                ->where('start_date', '>=', now())
                ->orderBy('start_date')
                ->get();

            foreach ($programs as $program) {
                if (str_contains($msg, strtolower($program->title))) {
                    return $program->image_url;
                }
            }

            return $programs->first()?->image_url;
        }

        return null;
    }

    /**
     * Helper: check if haystack contains any of the needles.
     */
    protected function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) return true;
        }
        return false;
    }

    /**
     * Handle name registration for new users.
     */
    protected function handleNameRegistration(
        Conversation $conversation,
        string $name,
        string $phone,
        string $channel
    ): array {
        $name = trim($name);

        $nonNames = ['hello', 'hi', 'hey', 'helo', 'good morning', 'good evening',
                     'good afternoon', 'ok', 'okay', 'yes', 'no', 'start', 'help'];

        if (in_array(strtolower($name), $nonNames) || strlen($name) < 2 || strlen($name) > 60) {
            return ['text' => "😊 Please share your *full name* so I can address you properly (e.g., *John Doe*).", 'media_url' => null];
        }

        if (str_word_count($name) > 4) {
            return ['text' => "That doesn't look like a name. Please enter your *full name* (e.g., *John Doe*) 😊", 'media_url' => null];
        }

        $member = ChurchMember::create([
            'name'                => ucwords(strtolower($name)),
            'phone'               => $phone,
            'channel'             => $channel,
            'last_interaction_at' => now(),
        ]);

        $conversation->update(['member_id' => $member->id, 'state' => 'active']);

        $welcome  = "🎉 Welcome, *{$member->name}*! It's wonderful to have you here!\n\n";
        $welcome .= "I'm *Harvesters AI*, and I'm here to help you with:\n";
        $welcome .= "• 📅 Upcoming programs & events\n";
        $welcome .= "• 🏛️ Campus locations & service times\n";
        $welcome .= "• 👥 Church leaders & contacts\n";
        $welcome .= "• 🙏 Next Level Prayers (NLP) info\n";
        $welcome .= "• 📖 Church news & announcements\n\n";
        $welcome .= "Type *ALERT ON* to receive daily NEXT LEVEL PRAYER reminders! 🌅\n\n";
        $welcome .= "How can I help you today?";

        Message::create(['conversation_id' => $conversation->id, 'role' => 'assistant', 'content' => $welcome]);

        return ['text' => $welcome, 'media_url' => null];
    }

    /**
     * Check for special commands.
     */
    protected function checkSpecialCommands(string $userMessage, ChurchMember $member): ?string
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

        return null;
    }

    /**
     * Build system prompt with live church data.
     */
    protected function buildSystemPrompt(ChurchMember $member): string
    {
        $upcomingPrograms = Program::where('is_active', true)
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->take(5)
            ->get();

        $campuses    = Campus::where('is_active', true)->get();
        $leaders     = Leader::where('is_active', true)->orderBy('created_at')->take(10)->get();
        $churchInfos = ChurchInfo::where('is_active', true)->get()->groupBy('category');

        // Build campus names list for AI reference
        $campusNamesList = $campuses->pluck('name')->join(', ');

        $prompt  = "You are *Harvesters AI*, the official AI assistant for Harvesters International Christian Centre.\n\n";
        $prompt .= "You are warm, friendly, Spirit-filled, and knowledgeable. You speak conversationally, use emojis naturally.\n\n";

        $prompt .= "**About Harvesters:**\n";
        $prompt .= "Founded by Pastor Bolaji Idowu. Multi-campus church in Lagos, Nigeria and London.\n\n";

        $prompt .= "**Current Member:** {$member->name} | Alerts: " . ($member->morning_alert ? 'On at ' . $member->alert_time : 'Off') . "\n\n";

        if ($churchInfos->isNotEmpty()) {
            $prompt .= "**Church Information:**\n";
            foreach ($churchInfos as $category => $infos) {
                $prompt .= "[{$category}]\n";
                foreach ($infos as $info) {
                    $prompt .= "- {$info->title}: {$info->content}\n";
                }
            }
            $prompt .= "\n";
        }

        if ($campuses->isNotEmpty()) {
            $prompt .= "**OFFICIAL Campus Locations (ONLY these campuses exist — do NOT invent others):**\n";
            foreach ($campuses as $campus) {
                $prompt .= "• {$campus->name} - {$campus->address}";
                if ($campus->service_times) $prompt .= " | Times: {$campus->service_times}";
                if ($campus->pastor_name)   $prompt .= " | Pastor: {$campus->pastor_name}";
                if ($campus->pastor_phone)  $prompt .= " ({$campus->pastor_phone})";
                if ($campus->image_url)     $prompt .= " [Has photo]";
                $prompt .= "\n";
            }
            $prompt .= "\n";
        }

        if ($leaders->isNotEmpty()) {
            $prompt .= "**Church Leaders:**\n";
            foreach ($leaders as $leader) {
                $prompt .= "• {$leader->name}";
                if ($leader->title)     $prompt .= " ({$leader->title})";
                if ($leader->phone)     $prompt .= " - {$leader->phone}";
                if ($leader->image_url) $prompt .= " [Has photo]";
                $prompt .= "\n";
            }
            $prompt .= "\n";
        }

        if ($upcomingPrograms->isNotEmpty()) {
            $prompt .= "**Upcoming Programs:**\n";
            foreach ($upcomingPrograms as $program) {
                $prompt .= "• *{$program->title}*";
                if ($program->start_date) $prompt .= " - " . $program->start_date->format('D, M j Y g:ia');
                if ($program->venue)      $prompt .= " at {$program->venue}";
                if ($program->image_url)  $prompt .= " [Has flyer]";
                $prompt .= "\n";
                if ($program->description) $prompt .= "  {$program->description}\n";
                $metadata = is_array($program->metadata) ? $program->metadata : [];
                if (!empty($metadata)) {
                    $labels = [
                        'bus_locations' => 'Bus Pickup Locations',
                        'free_meal'     => 'Free Meal',
                        'dress_code'    => 'Dress Code',
                        'registration'  => 'Registration Required',
                        'contact'       => 'Contact Person',
                        'extra'         => 'Additional Info',
                    ];
                    foreach ($labels as $key => $label) {
                        if (!empty($metadata[$key])) {
                            $prompt .= "  - {$label}: {$metadata[$key]}\n";
                        }
                    }
                }
            }
            $prompt .= "\n";
        }

        $prompt .= "**Special Commands:** ALERT ON | ALERT OFF | SET ALERT TIME [time]\n\n";

        $prompt .= "**STRICT RULES — follow these exactly:**\n";
        $prompt .= "1. NEVER mention sending a photo, image, or flyer. NEVER say 'I am sending you a photo'. Images are attached automatically — just answer the question.\n";
        $prompt .= "2. NEVER invent campus addresses, facilities, or details not listed above. Only use data provided.\n";
        $prompt .= "3. CAMPUS RULE: The ONLY campuses that exist are: {$campusNamesList}. If someone asks about ANY other location not in this list, say 'We don't currently have a Harvesters campus in that area' and suggest the nearest campus from the list above.\n";
        $prompt .= "4. Keep ALL responses under 300 words. Be concise and WhatsApp-friendly.\n";
        $prompt .= "5. If info is missing, say 'contact us at harvestersng.org or +234 (01) 453 2030'.\n";
        $prompt .= "6. Always be warm, encouraging, and Spirit-filled.\n";

        return $prompt;
    }

    /**
     * Call the Z.ai API
     */
    protected function callAI(array $messages): string
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post("{$this->baseUrl}/chat/completions", [
                    'model'       => $this->model,
                    'messages'    => $messages,
                    'max_tokens'  => 350,
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                if (!$content) {
                    Log::error('HarvestersAI Empty Response', ['body' => $response->body()]);
                }
                return $content ?? 'I apologize, I had trouble generating a response. Please try again. 🙏';
            }

            Log::error('HarvestersAI API Error', ['status' => $response->status(), 'body' => $response->body()]);
            return "I'm having a little trouble right now. Please try again in a moment. You can also reach us at harvestersng.org 🙏";

        } catch (\Exception $e) {
            Log::error('HarvestersAI Exception', ['error' => $e->getMessage()]);
            return "I'm having a little trouble right now. Please try again in a moment. You can also reach us at harvestersng.org 🙏";
        }
    }

    /**
     * Generate NLP morning devotional.
     */
    public function generateMorningDevotional(ChurchMember $member): array
    {
        $text  = "🌅 *NEXT LEVEL PRAYER ALERT!*\n\n";
        $text .= "Good morning, *{$member->name}*! 🙏\n\n";
        $text .= "Next Level Prayer (NLP) is live *RIGHT NOW!* Join thousands of believers praying together.\n\n";
        $text .= "📺 *Join us on:*\n";
        $text .= "• YouTube: *Harvesters International Christian Centre*\n";
        $text .= "• X (Twitter): *@bolajiid*\n";
        $text .= "• Facebook: *Pastor Bolaji Idowu*\n";
        $text .= "• Instagram: *@bolajiid*\n\n";
        $text .= "🔥 *It's time to pray! Join now and be blessed!* 🙏";

        $nextProgram = Program::where('is_active', true)
            ->whereNotNull('image_url')
            ->where('image_url', '!=', '')
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->first();

        return ['text' => $text, 'media_url' => $nextProgram?->image_url];
    }
}