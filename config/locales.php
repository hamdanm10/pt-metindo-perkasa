<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Locale
    |--------------------------------------------------------------------------
    |
    | Used by the landing root redirect and as the hreflang "x-default".
    |
    */

    'default' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    |
    | `name` is written in the language itself, `html_lang` goes on the <html>
    | lang attribute and hreflang, `dir` is the writing direction.
    |
    */

    'locales' => [
        'en' => ['name' => 'English', 'html_lang' => 'en', 'dir' => 'ltr'],
        'id' => ['name' => 'Bahasa Indonesia', 'html_lang' => 'id', 'dir' => 'ltr'],
        'zh' => ['name' => '中文', 'html_lang' => 'zh-Hans', 'dir' => 'ltr'],
        'ar' => ['name' => 'العربية', 'html_lang' => 'ar', 'dir' => 'rtl'],
        'ja' => ['name' => '日本語', 'html_lang' => 'ja', 'dir' => 'ltr'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Locales Per Area
    |--------------------------------------------------------------------------
    */

    'landing' => ['en', 'id', 'zh', 'ar', 'ja'],

    'dashboard' => ['en', 'id'],

];
