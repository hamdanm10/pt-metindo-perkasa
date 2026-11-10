<?php

declare(strict_types=1);

return [
    'open_chat' => 'Open the chat assistant',
    'open_whatsapp' => 'Chat with customer service on WhatsApp',
    'close' => 'Close',
    'chat' => [
        'title' => 'Chat with us',
        'subtitle' => 'Quick answers about PT Metindo Perkasa',
        'placeholder' => 'Type your question...',
        'send' => 'Send',
        'welcome' => 'Hi! I can answer common questions. Pick a topic below or type your question.',
        'fallback' => 'Sorry, I can\'t answer that yet. Pick a topic below, or contact our team.',
        'topics' => 'Topics',
        'you' => 'You',
        'bot' => 'Assistant',
    ],
    'whatsapp' => [
        'title' => 'WhatsApp customer service',
        'hint' => 'Choose a number to start a chat.',
        'chat' => 'Chat',
        'default_name' => 'Customer service',
        'message' => 'Hello PT Metindo Perkasa, I would like to ask about your services.',
    ],
    'topics' => [
        'processes' => [
            'label' => 'Processes',
            'answer' => 'We run electroplating processes in :count categories, such as zinc, nickel, copper, hard chrome and tin plating, anodizing and phosphating.',
            'link' => 'See all processes',
            'keywords' => ['process', 'plating', 'zinc', 'nickel', 'copper', 'chrome', 'tin', 'anodiz', 'phosphat', 'polish', 'categor'],
        ],
        'services' => [
            'label' => 'Services',
            'answer' => 'We offer electroplating services, plating chemical supply and technical consultation.',
            'link' => 'See our services',
            'keywords' => ['service', 'offer', 'what do you', 'supply', 'consult', 'chemical'],
        ],
        'quote' => [
            'label' => 'Ask for advice',
            'answer' => 'Tell us the process and substrate, and our team will walk you through the options.',
            'link' => 'Contact us',
            'keywords' => ['quote', 'price', 'cost', 'advice', 'spec', 'help', 'order', 'request'],
        ],
        'contact' => [
            'label' => 'Contact',
            'email' => 'Email: :email.',
            'phone' => 'Phone: :phone.',
            'link' => 'Open the contact page',
            'keywords' => ['contact', 'email', 'phone', 'call', 'reach', 'number'],
        ],
        'hours' => [
            'label' => 'Opening hours',
            'answer' => 'Our opening hours: :hours.',
            'link' => 'Open the contact page',
            'keywords' => ['hour', 'open', 'time', 'schedule', 'when'],
        ],
        'location' => [
            'label' => 'Location',
            'answer' => 'Address: :address',
            'link' => 'See our subsidiaries',
            'keywords' => ['where', 'address', 'location', 'map', 'city', 'subsidiar', 'office'],
        ],
        'company' => [
            'label' => 'About the company',
            'answer' => 'PT Metindo Perkasa is a metal finishing and electroplating specialist in Indonesia.',
            'link' => 'About us',
            'keywords' => ['about', 'company', 'who', 'metindo'],
        ],
    ],
];
