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

    'export' => [
        // Grants the new app's page import read access to the legacy
        // filemanager bridge routes; must match the new app's value.
        'token' => env('LEGACY_EXPORT_TOKEN'),
    ],

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'placekit' => [
        'key' => env('PLACEKIT_KEY'),
    ],

    'mailcoach' => [
        'url' => env('MAILCOACH_URL'),
        'token' => env('MAILCOACH_TOKEN'),
        'lists' => [
            'default' => env('MAILCOACH_DEFAULT_LIST_UUID'),
        ]
    ],

];
