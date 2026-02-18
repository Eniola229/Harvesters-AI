<?php


return [

    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'twilio' => [
        'sid'            => env('TWILIO_SID'),
        'auth_token'     => env('TWILIO_AUTH_TOKEN'),
        'whatsapp_from'  => env('TWILIO_WHATSAPP_NUMBER'),
        'sms_from'       => env('TWILIO_SMS_NUMBER'),
    ],

    'zai' => [
        'api_key'  => env('ZAI_API_KEY'),
        'base_url' => env('ZAI_BASE_URL', 'https://api.z.ai/v1'),
        'model'    => env('ZAI_MODEL', 'z-model-latest'),
    ],

    'cloudinary' => [
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
        'api_key'    => env('CLOUDINARY_API_KEY'),
        'api_secret' => env('CLOUDINARY_API_SECRET'),
        'url'        => env('CLOUDINARY_URL'),
    ],

];