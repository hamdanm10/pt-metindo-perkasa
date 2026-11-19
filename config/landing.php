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
        ['slug' => 'zinc-plating', 'name' => 'Zinc Plating Chemicals', 'processes' => ['Rack Zinc Plating', 'Barrel Zinc Plating', 'Zinc-Nickel Alloy', 'Clear Passivation', 'Yellow Passivation', 'Black Passivation']],
        ['slug' => 'nickel-plating', 'name' => 'Nickel Plating Chemicals', 'processes' => ['Bright Nickel', 'Satin Nickel', 'Semi-Bright Nickel', 'Nickel Strike']],
        ['slug' => 'copper-plating', 'name' => 'Copper Plating Chemicals', 'processes' => ['Acid Copper Bath', 'Cyanide-Free Copper', 'Copper Strike']],
        ['slug' => 'hard-chrome', 'name' => 'Hard Chrome Chemicals', 'processes' => ['Hard Chrome Plating', 'Decorative Chrome', 'Chrome Stripping', 'Trivalent Chrome']],
        ['slug' => 'tin-plating', 'name' => 'Tin Plating Chemicals', 'processes' => ['Bright Tin', 'Matte Tin', 'Lead-Free Tin Alloy']],
        ['slug' => 'electroless-nickel', 'name' => 'Electroless Nickel Chemicals', 'processes' => ['Low Phosphorus EN', 'Mid Phosphorus EN', 'High Phosphorus EN', 'EN-PTFE Composite', 'EN Stripper']],
        ['slug' => 'anodizing', 'name' => 'Anodizing Chemicals', 'processes' => ['Sulfuric Anodizing', 'Hard Anodizing', 'Color Dyeing', 'Anodize Sealing']],
        ['slug' => 'phosphating', 'name' => 'Phosphating Chemicals', 'processes' => ['Zinc Phosphate', 'Manganese Phosphate', 'Iron Phosphate', 'Phosphate Oil Finish', 'Phosphate Activator']],
        ['slug' => 'plating-on-plastic', 'name' => 'Plating on Plastic Chemicals', 'processes' => ['Plastic Etching', 'Catalyst & Accelerator', 'Electroless Copper', 'Bright Copper & Nickel']],
        ['slug' => 'metal-polishing', 'name' => 'Metal Polishing Compounds', 'processes' => ['Buffing Compound', 'Electropolishing', 'Chemical Polishing']],
        ['slug' => 'sustainable-steel', 'name' => 'Sustainable Steel Solutions', 'processes' => ['Eco Passivation', 'Eco Degreasing', 'Low-Temperature Cleaning', 'Metal Recovery', 'Waste Minimization']],
        ['slug' => 'wastewater-treatment', 'name' => 'Wastewater Treatment Chemicals', 'processes' => ['Heavy Metal Precipitation', 'pH Neutralization', 'Coagulation & Flocculation', 'Ion Exchange', 'Sludge Dewatering']],
        ['slug' => 'chemical-laboratory', 'name' => 'Chemical Laboratory Services', 'processes' => ['Bath Analysis', 'Hull Cell Test', 'Thickness Testing', 'Salt Spray Test']],
        ['slug' => 'chemical-alkaline-type', 'name' => 'Chemical Alkaline Type', 'processes' => ['Alkaline Zinc', 'Alkaline Copper', 'Alkaline Zinc-Nickel', 'Alkaline Tin', 'Alkaline Electro Cleaner', 'Alkaline Degreaser']],
    ],

    /*
    |--------------------------------------------------------------------------
    | Process Details
    |--------------------------------------------------------------------------
    |
    | Steps and materials per process, keyed by the process slug (the name
    | through Str::slug). Only Alkaline Zinc has steps in the prototype, and a
    | process without an entry shows "no steps yet". Moves to the database
    | together with the categories.
    |
    */

    'process_details' => [
        'alkaline-zinc' => [
            'description' => 'Alkaline zinc plating process flow.',
            'steps' => [
                [
                    'name' => 'Degreasing',
                    'description' => 'Remove oil and grease from the surface.',
                    'materials' => ['Epclen 62', 'Epclen 541', 'Hanson SC 821', 'Jasco CA 135G', 'Jasco Plus Clean A', 'Jasco Plus Clean B', 'Epclen 55RT', 'Epclen 20NS'],
                ],
                ['name' => 'Rinsing', 'description' => 'Rinse the surface with water.', 'materials' => []],
                ['name' => 'Activation', 'description' => 'Activate the surface before plating.', 'materials' => []],
                ['name' => 'Plating', 'description' => 'Deposit the zinc layer.', 'materials' => []],
            ],
        ],
        'alkaline-copper' => ['description' => 'Alkaline copper plating process flow.'],
        'alkaline-zinc-nickel' => ['description' => 'Alkaline zinc-nickel alloy plating process flow.'],
        'alkaline-tin' => ['description' => 'Alkaline tin plating process flow.'],
        'alkaline-electro-cleaner' => ['description' => 'Alkaline electrolytic cleaning process flow.'],
        'alkaline-degreaser' => ['description' => 'Alkaline soak degreasing process flow.'],
    ],

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
