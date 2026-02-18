<?php
namespace Database\Seeders;

use App\Models\AdminUser;
use App\Models\Campus;
use App\Models\ChurchInfo;
use App\Models\Leader;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HarvestersSeeder extends Seeder
{
    public function run(): void
    {
        // Create default admin
        AdminUser::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@harvestersai.com')],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'Admin@1234')),
                'role'     => 'super_admin',
            ]
        );

        // Seed Campuses
        $campuses = [
            ['name' => 'Lekki Campus', 'address' => 'Lekki, Lagos State', 'city' => 'Lagos', 'service_times' => 'Sunday: 8:00am & 10:30am | Wednesday: 6:00pm'],
            ['name' => 'Gbagada Campus', 'address' => 'Gbagada, Lagos State', 'city' => 'Lagos', 'service_times' => 'Sunday: 8:00am & 10:30am | Wednesday: 6:00pm'],
            ['name' => 'Anthony Campus', 'address' => 'Anthony Village, Lagos State', 'city' => 'Lagos', 'service_times' => 'Sunday: 8:00am & 10:30am'],
            ['name' => 'Magodo Campus', 'address' => 'Magodo, Lagos State', 'city' => 'Lagos', 'service_times' => 'Sunday: 8:00am & 10:30am'],
            ['name' => 'Ibadan Campus', 'address' => 'Ibadan, Oyo State', 'city' => 'Ibadan', 'state' => 'Oyo', 'service_times' => 'Sunday: 9:00am'],
            ['name' => 'Abuja Campus', 'address' => 'Abuja, FCT', 'city' => 'Abuja', 'state' => 'FCT', 'service_times' => 'Sunday: 8:00am & 10:30am'],
            ['name' => 'London Campus', 'address' => 'London, United Kingdom', 'city' => 'London', 'country' => 'United Kingdom', 'service_times' => 'Sunday: 10:00am'],
            ['name' => 'Ikeja GRA Campus', 'address' => 'Ikeja GRA, Lagos State', 'city' => 'Lagos', 'service_times' => 'Sunday: 8:00am & 10:30am'],
            ['name' => 'Yaba Campus', 'address' => 'Yaba, Lagos State', 'city' => 'Lagos', 'service_times' => 'Sunday: 8:00am & 10:30am'],
            ['name' => 'Ikorodu Campus', 'address' => 'Ikorodu, Lagos State', 'city' => 'Lagos', 'service_times' => 'Sunday: 9:00am'],
            ['name' => 'Alimosho Campus', 'address' => 'Alimosho, Lagos State', 'city' => 'Lagos', 'service_times' => 'Sunday: 9:00am'],
        ];

        foreach ($campuses as $campus) {
            Campus::firstOrCreate(['name' => $campus['name']], array_merge($campus, ['is_active' => true]));
        }

        // Seed Church Info
        $infos = [
            [
                'category' => 'about',
                'title'    => 'About Harvesters',
                'content'  => 'Harvesters International Christian Centre is a vibrant, Spirit-filled church founded by Pastor Bolaji Idowu. What started with just a roomful of people has grown to be one of the most dynamic places of worship in Lagos, with worship centres in Lekki, Gbagada, Anthony, Magodo, Ibadan, Abuja, Alimosho, Ikorodu, Yaba, London and Ikeja GRA drawing in thousands of people! The church is committed to the Word of God, the power of prayer, and transformational encounters.',
                'order'    => 1,
            ],
            [
                'category' => 'about',
                'title'    => 'Our Mission',
                'content'  => 'Harvesters is not just a space, it is an experience. It is a series of impactful and transformational encounters that bring change to the lives of people. Under the intense focus of the Spirit and the Word, change happens.',
                'order'    => 2,
            ],
            [
                'category' => 'contact',
                'title'    => 'Church Contact Details',
                'content'  => 'Phone: +234 (01) 453 2030 | Website: harvestersng.org | Email: info@harvestersng.org | Social Media: @HarvestersNG on Instagram, Facebook, and Twitter/X',
                'order'    => 1,
            ],
            [
                'category' => 'nlp',
                'title'    => 'Next Level Prayers (NLP)',
                'content'  => 'Next Level Prayers (NLP) is one of Harvesters\' flagship prayer initiatives. It is a dedicated prayer gathering where believers come together to intercede, pray, and seek the face of God. NLP sessions are held regularly and are a core part of the Harvesters worship experience. Members can receive daily morning devotional reminders by typing ALERT ON in chat.',
                'order'    => 1,
            ],
            [
                'category' => 'giving',
                'title'    => 'Giving & Tithes',
                'content'  => 'You can give online at harvestersng.org/giving. Account details are available on the website and during church services. The church accepts tithes, offerings, and special seeds. For enquiries about giving, contact the church at +234 (01) 453 2030.',
                'order'    => 1,
            ],
            [
                'category' => 'services',
                'title'    => 'Sunday Services',
                'content'  => 'Sunday services are held at all campuses typically at 8:00am and 10:30am. Service content includes worship, Word of God, and prayer. Services are also streamed online at harvesterng.online.church',
                'order'    => 1,
            ],
            [
                'category' => 'services',
                'title'    => 'Online Church',
                'content'  => 'Harvesters has an online church platform at harvesterng.online.church where you can join live services, watch replays, and connect with the church community from anywhere in the world.',
                'order'    => 2,
            ],
            [
                'category' => 'faq',
                'title'    => 'How to become a member',
                'content'  => 'To become a formal member of Harvesters, you can join the Growth Track program. Visit harvestersng.org/growth-track or speak to any pastor or worker at any campus for guidance.',
                'order'    => 1,
            ],
            [
                'category' => 'faq',
                'title'    => 'Small Groups',
                'content'  => 'Harvesters runs small groups (cell groups) where members can fellowship, study the Word, and build community. To join a small group, visit harvestersng.org/small-groups or contact your nearest campus.',
                'order'    => 2,
            ],
            [
                'category' => 'values',
                'title'    => 'Our Core Values',
                'content'  => 'At Harvesters, we value: The Word of God as final authority, Spirit-led worship and prayer, Transformational encounters, Community and fellowship, Excellence in service, Giving and generosity, Evangelism and soul-winning.',
                'order'    => 1,
            ],
        ];

        foreach ($infos as $info) {
            ChurchInfo::firstOrCreate(
                ['category' => $info['category'], 'title' => $info['title']],
                array_merge($info, ['is_active' => true])
            );
        }

        // Seed senior pastor
        Leader::firstOrCreate(
            ['name' => 'Pastor Bolaji Idowu'],
            [
                'title'     => 'Senior Pastor & Founder',
                'bio'       => 'Pastor Bolaji Idowu is the founder and Senior Pastor of Harvesters International Christian Centre. Under his leadership, the church has grown into one of Nigeria\'s most vibrant and influential Christian communities.',
                'order'     => 1,
                'is_active' => true,
            ]
        );

        $this->command->info('✅ Harvesters AI data seeded successfully!');
        $this->command->info('Admin Login: ' . env('ADMIN_EMAIL', 'admin@harvestersai.com'));
        $this->command->info('Password: ' . env('ADMIN_PASSWORD', 'Admin@1234'));
    }
}