<?php

declare(strict_types=1);

return [
    'home' => 'Back to home',
    '403' => [
        'title' => 'Access denied',
        'text' => 'You do not have permission to open this page.',
    ],
    '404' => [
        'title' => 'Page not found',
        'text' => 'The page you are looking for does not exist or has moved.',
    ],
    '419' => [
        'title' => 'Page expired',
        'text' => 'Your session expired. Reload the page and try again.',
    ],
    '429' => [
        'title' => 'Too many requests',
        'text' => 'Please wait a moment and try again.',
    ],
    '500' => [
        'title' => 'Something went wrong',
        'text' => 'We could not complete your request. Please try again later.',
    ],
    '503' => [
        'title' => 'Back soon',
        'text' => 'The site is being updated. Please try again in a few minutes.',
    ],
];
