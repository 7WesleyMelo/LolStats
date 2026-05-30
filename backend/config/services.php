<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'riot' => [
        'data_dragon_url' => env('RIOT_DATA_DRAGON_URL', 'https://ddragon.leagueoflegends.com'),
        'locale' => env('RIOT_DATA_DRAGON_LOCALE', 'pt_BR'),
        'api_key' => env('RIOT_API_KEY'),
        'region' => env('RIOT_REGION', 'br1'),
        'routing_region' => env('RIOT_ROUTING_REGION', 'americas'),
    ],
];
