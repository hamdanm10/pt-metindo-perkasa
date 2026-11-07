<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Product Categories
    |--------------------------------------------------------------------------
    |
    | Static list until the catalog moves to the database. `slug` also names
    | the icon file at public/images/landing/icon-{slug}.svg.
    |
    */

    'categories' => [
        ['slug' => 'zinc-plating', 'name' => 'Zinc Plating Chemicals'],
        ['slug' => 'nickel-plating', 'name' => 'Nickel Plating Chemicals'],
        ['slug' => 'copper-plating', 'name' => 'Copper Plating Chemicals'],
        ['slug' => 'hard-chrome', 'name' => 'Hard Chrome Chemicals'],
        ['slug' => 'tin-plating', 'name' => 'Tin Plating Chemicals'],
        ['slug' => 'electroless-nickel', 'name' => 'Electroless Nickel Chemicals'],
        ['slug' => 'anodizing', 'name' => 'Anodizing Chemicals'],
        ['slug' => 'phosphating', 'name' => 'Phosphating Chemicals'],
        ['slug' => 'plating-on-plastic', 'name' => 'Plating on Plastic Chemicals'],
        ['slug' => 'metal-polishing', 'name' => 'Metal Polishing Compounds'],
        ['slug' => 'sustainable-steel', 'name' => 'Sustainable Steel Solutions'],
        ['slug' => 'wastewater-treatment', 'name' => 'Wastewater Treatment Chemicals'],
        ['slug' => 'chemical-laboratory', 'name' => 'Chemical Laboratory Services'],
    ],

    'partners' => ['Epclen', 'Hanson SC', 'Jasco', 'Plus Clean', 'Metindo Lab'],

    /*
    |--------------------------------------------------------------------------
    | Intro Video and Contact
    |--------------------------------------------------------------------------
    |
    | Sections that depend on these values are not rendered when they are empty.
    |
    */

    'intro_video_id' => env('LANDING_INTRO_VIDEO_ID'),

    'contact' => [
        'email' => env('LANDING_CONTACT_EMAIL'),
        'phone' => env('LANDING_CONTACT_PHONE'),
    ],

];
