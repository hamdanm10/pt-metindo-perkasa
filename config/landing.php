<?php

declare(strict_types=1);

return [

    'partners' => ['Epclen', 'Hanson SC', 'Jasco', 'Plus Clean', 'Metindo Lab'],

    // Folder that holds the clients/ and partners/ logo folders. Tests point it at a temporary folder.
    'logo_directory' => public_path('images/landing'),

    /*
    |--------------------------------------------------------------------------
    | Subsidiaries
    |--------------------------------------------------------------------------
    |
    | Texts live in lang/{locale}/pages.php.
    | Coordinates are city centers, not real addresses.
    |
    */

    'subsidiaries' => [
        ['key' => 'medan', 'name' => 'PT Metindo Surface Medan', 'lat' => 3.5952, 'lng' => 98.6722, 'timezone' => 'Asia/Jakarta'],
        ['key' => 'jakarta', 'name' => 'PT Metindo Chemical Jakarta', 'lat' => -6.2088, 'lng' => 106.8456, 'timezone' => 'Asia/Jakarta'],
        ['key' => 'surabaya', 'name' => 'PT Metindo Plating Surabaya', 'lat' => -7.2575, 'lng' => 112.7521, 'timezone' => 'Asia/Jakarta'],
        ['key' => 'balikpapan', 'name' => 'PT Metindo Coating Balikpapan', 'lat' => -1.2379, 'lng' => 116.8529, 'timezone' => 'Asia/Makassar'],
        ['key' => 'makassar', 'name' => 'PT Metindo Finishing Makassar', 'lat' => -5.1477, 'lng' => 119.4327, 'timezone' => 'Asia/Makassar'],
        ['key' => 'jayapura', 'name' => 'PT Metindo Technical Jayapura', 'lat' => -2.5337, 'lng' => 140.7181, 'timezone' => 'Asia/Jayapura'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Intro Video and Contact
    |--------------------------------------------------------------------------
    |
    | Sections that depend on these values are not rendered when they are empty.
    | The phone and WhatsApp numbers are not here: the admin manages them in the dashboard.
    |
    */

    'intro_video_id' => env('LANDING_INTRO_VIDEO_ID'),

    'contact' => [
        'email' => env('LANDING_CONTACT_EMAIL'),
        'address' => env('LANDING_CONTACT_ADDRESS'),
        'hours' => env('LANDING_CONTACT_HOURS'),
    ],

];
