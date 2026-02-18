# Harvesters AI 🕊️

> WhatsApp & SMS AI Assistant for **Harvesters International Christian Centre**

Harvesters AI is a Laravel-based conversational AI system that helps church members get information about programs, campuses, services, and more — directly through WhatsApp or SMS. It also gives admins a full dashboard to manage members, newsletters, programs, and the AI's knowledge base.

---

## 🚀 Features

- **AI Chatbot** — Powered by z.ai (OpenAI-compatible API), answers member questions naturally
- **WhatsApp & SMS** — Integrated via Twilio; members just message in
- **Auto-onboarding** — New members are registered automatically when they send their first message
- **Morning Devotionals** — Members can opt-in to receive a personalized AI-generated devotional every morning
- **Newsletter Broadcasting** — Send text + image/video messages to all members or a specific campus
- **Admin Dashboard** — Full CRUD for members, programs, campuses, leaders, and the AI knowledge base
- **Cloudinary Media Storage** — All images and videos are stored on Cloudinary
- **UUID Architecture** — All models use UUID primary keys

---

## 🛠 Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 11 |
| Database | MySQL |
| AI | z.ai API (OpenAI-compatible) |
| Messaging | Twilio (WhatsApp + SMS) |
| Media Storage | Cloudinary |
| Queue | Laravel Queue (sync / database) |
| Scheduler | Laravel Scheduler |

---

## 📁 Project Structure

```
app/
├── Console/Commands/
│   └── SendMorningAlerts.php       # Scheduled morning devotional command
├── Http/Controllers/
│   ├── Admin/
│   │   ├── Auth/AdminLoginController.php
│   │   ├── DashboardController.php
│   │   ├── MembersController.php
│   │   ├── ProgramsController.php
│   │   ├── NewsletterController.php
│   │   ├── CampusController.php
│   │   ├── LeaderController.php
│   │   └── ChurchInfoController.php
│   └── Webhook/
│       └── TwilioWebhookController.php  # Receives incoming WhatsApp/SMS
├── Jobs/
│   └── SendNewsletterJob.php       # Queued newsletter dispatch
├── Models/
│   ├── AdminUser.php
│   ├── ChurchMember.php
│   ├── Conversation.php
│   ├── Message.php
│   ├── Program.php
│   ├── Campus.php
│   ├── Leader.php
│   ├── ChurchInfo.php
│   └── Newsletter.php
└── Services/
    ├── HarvestersAIService.php     # Core AI logic
    ├── TwilioService.php           # WhatsApp/SMS sending
    └── CloudinaryService.php       # Media uploads

database/
├── migrations/                     # 5 migration files
└── seeders/
    └── HarvestersSeeder.php        # Seeds campuses, church info, default admin

resources/views/admin/
├── auth/login.blade.php
├── components/{nav,header,footer}.blade.php
├── dashboard.blade.php
├── members/{index,show,create,edit}.blade.php
├── programs/{index,create,edit}.blade.php
├── newsletters/{index,create}.blade.php
├── campuses/{index,create,edit}.blade.php
├── leaders/{index,create,edit}.blade.php
└── church-info/{index,create,edit}.blade.php
```

---

## ⚙️ Installation

### 1. Copy Files

Copy all provided files to their indicated locations in your Laravel project. Each file has its path commented at the top, e.g.:
```php
// Location: app/Services/HarvestersAIService.php
```

### 2. Environment Variables

Add the contents of `.env.additions` to your `.env` file:

```env
# Z.AI API
ZAI_API_KEY=your_zai_api_key
ZAI_BASE_URL=https://api.z.ai/v1
ZAI_MODEL=gpt-4o

# Twilio
TWILIO_SID=your_twilio_sid
TWILIO_AUTH_TOKEN=your_twilio_auth_token
TWILIO_WHATSAPP_FROM=whatsapp:+14155238886
TWILIO_SMS_FROM=+1234567890

# Cloudinary
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret

# Admin
ADMIN_EMAIL=admin@harvestersai.com
ADMIN_PASSWORD=Admin@1234
```

### 3. Update Config Files

**`config/services.php`** — Add the zai, twilio, and cloudinary entries from the provided `config/services.php` file.

**`config/auth.php`** — Replace your existing file with the provided one. It adds a separate `admin` guard using the `AdminUser` model.

### 4. Update Routes

In `routes/web.php`, add the admin and webhook routes from the provided file.

In `routes/console.php`, add the scheduler line:
```php
Schedule::command('harvesters:morning-alerts')->everyMinute();
```

### 5. Exclude Webhook from CSRF

In `app/Http/Middleware/VerifyCsrfToken.php`, add:
```php
protected $except = [
    'webhook/twilio',
];
```

### 6. Run Migrations & Seeder

```bash
php artisan migrate
php artisan db:seed --class=HarvestersSeeder
```

This seeds:
- Default admin account (`admin@harvestersai.com` / `Admin@1234`)
- 11 campuses (Lekki, Gbagada, Anthony, Magodo, Ibadan, Abuja, London, Ikeja GRA, Yaba, Ikorodu, Alimosho)
- Church info entries for AI knowledge base
- Senior Pastor: Pastor Bolaji Idowu

### 7. Configure Twilio Webhook

In your Twilio console, set the incoming message webhook URL for your WhatsApp/SMS number to:
```
POST https://yourdomain.com/webhook/twilio
```

### 8. Start the Scheduler

**Development:**
```bash
php artisan schedule:work
```

**Production (add to crontab):**
```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

### 9. Start the Queue Worker

**Development:**
```bash
php artisan queue:listen
```

**Production (use Supervisor):**
```ini
[program:harvesters-worker]
command=php /path/to/project/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
```

---

## 🤖 How the AI Works

### New Member Flow
1. Someone messages your Twilio WhatsApp number
2. Webhook fires → `HarvestersAIService::processMessage()`
3. No member record found → AI asks for their name
4. Member replies with name → account is created automatically
5. Welcome message is sent with feature overview
6. All future messages get full AI responses

### AI Knowledge
The AI's system prompt is built dynamically from your database every message, including:
- All active **Church Info** entries (About, Values, FAQ, Services, Giving, NLP, Contact)
- All **Campuses** with addresses, service times, and pastor contacts
- All **Leaders** with titles and phone numbers
- Upcoming **Programs** with full metadata (bus locations, meals, dress code, etc.)
- The member's own profile (name, campus, alert status)

### Special Member Commands
| Command | What it does |
|---|---|
| `ALERT ON` | Enables morning devotionals |
| `ALERT OFF` | Disables morning devotionals |
| `SET ALERT TIME 6:30 AM` | Sets alert time and enables it |

### Morning Alert Flow
1. Scheduler runs every minute
2. Finds members whose `alert_time` matches current time
3. AI generates a personalized devotional for each member
4. Sends via WhatsApp or SMS depending on their channel

---

## 🖥 Admin Dashboard

Access the admin panel at: `https://yourdomain.com/admin`

Default credentials (change these before going live):
- **Email:** `admin@harvestersai.com`
- **Password:** `Admin@1234`

### Admin Sections

| Section | Description |
|---|---|
| Dashboard | Stats — total members, alert subscribers, programs, recent messages |
| Members | View all members, see conversation history, manage alert settings |
| Programs & Events | Add church programs with images, dates, locations, and logistics metadata |
| Newsletters | Compose and broadcast messages (with media) to all or specific campus |
| Campuses | Manage all 11 Harvesters campuses |
| Leaders | Add/manage church leaders with photos and contact info |
| AI Knowledge Base | Manage what the AI knows — categorized info the AI uses to answer questions |

---

## 📋 Database Tables

| Table | Description |
|---|---|
| `admin_users` | Separate admin authentication |
| `church_members` | Registered WhatsApp/SMS members |
| `conversations` | Conversation sessions per member |
| `messages` | Individual messages in each conversation |
| `programs` | Church programs and events |
| `campuses` | Church campus locations |
| `leaders` | Church leaders and pastors |
| `church_infos` | AI knowledge base entries |
| `newsletters` | Newsletter records and send history |

---

## 🔒 Security Notes

- Admin authentication uses a **separate guard** (`auth:admin`) — completely isolated from any regular user auth
- The Twilio webhook route is excluded from CSRF protection (required for external webhooks)
- Change the default admin password before deploying to production
- Store all API keys in `.env` — never commit them to version control

---

## 🚨 Important Before Going Live

1. **Change default admin password** in `.env` → `ADMIN_PASSWORD`
2. **Set real Twilio credentials** and configure your WhatsApp sender
3. **Set real z.ai API key** with sufficient credits
4. **Set real Cloudinary credentials**
5. **Set `APP_ENV=production`** and `APP_DEBUG=false`
6. **Set up Supervisor** for the queue worker so newsletters don't fail
7. **Set up the cron job** for the scheduler so morning alerts fire correctly

---

## 🏛 About Harvesters International Christian Centre

Harvesters International Christian Centre is led by Pastor Bolaji Idowu and has grown from a small gathering to one of the most impactful churches in Lagos, with campuses across Nigeria and in London.

> *"Harvesters is not just a space, it is an experience."*

**Website:** [harvestersng.org](https://harvestersng.org)

---

*Built by AfricTech(Joshua Adeyemi) with ❤️ for the Harvesters community*