<?php

declare(strict_types=1);

return [
    'open_chat' => 'Buka asisten obrolan',
    'open_whatsapp' => 'Hubungi layanan pelanggan lewat WhatsApp',
    'close' => 'Tutup',
    'chat' => [
        'title' => 'Ngobrol dengan kami',
        'subtitle' => 'Jawaban cepat tentang PT Metindo Perkasa',
        'placeholder' => 'Tulis pertanyaan Anda...',
        'send' => 'Kirim',
        'welcome' => 'Halo! Saya bisa menjawab pertanyaan umum. Pilih topik di bawah atau tulis pertanyaan Anda.',
        'fallback' => 'Maaf, saya belum bisa menjawab itu. Pilih topik di bawah, atau hubungi tim kami.',
        'topics' => 'Topik',
        'you' => 'Anda',
        'bot' => 'Asisten',
    ],
    'whatsapp' => [
        'title' => 'Layanan pelanggan WhatsApp',
        'hint' => 'Pilih nomor untuk memulai obrolan.',
        'chat' => 'Chat',
        'default_name' => 'Layanan pelanggan',
        'message' => 'Halo PT Metindo Perkasa, saya ingin bertanya tentang layanan Anda.',
    ],
    'topics' => [
        'processes' => [
            'label' => 'Proses',
            'answer' => 'Kami menjalankan proses elektroplating dalam :count kategori, seperti pelapisan seng, nikel, tembaga, krom keras, dan timah, anodizing, serta fosfating.',
            'link' => 'Lihat semua proses',
            'keywords' => ['proses', 'pelapisan', 'plating', 'seng', 'nikel', 'tembaga', 'krom', 'timah', 'anodi', 'fosfat', 'poles', 'kategori'],
        ],
        'services' => [
            'label' => 'Layanan',
            'answer' => 'Kami menawarkan jasa elektroplating, pasokan bahan kimia plating, dan konsultasi teknis.',
            'link' => 'Lihat layanan kami',
            'keywords' => ['layanan', 'jasa', 'tawarkan', 'pasokan', 'konsultasi', 'bahan kimia'],
        ],
        'quote' => [
            'label' => 'Minta saran',
            'answer' => 'Ceritakan proses dan substrat Anda, lalu tim kami akan menjelaskan pilihan yang ada.',
            'link' => 'Hubungi kami',
            'keywords' => ['harga', 'penawaran', 'biaya', 'saran', 'spesifikasi', 'bantuan', 'pesan', 'minta'],
        ],
        'contact' => [
            'label' => 'Kontak',
            'email' => 'Email: :email.',
            'phone' => 'Telepon: :phone.',
            'link' => 'Buka halaman kontak',
            'keywords' => ['kontak', 'hubungi', 'email', 'telepon', 'telpon', 'nomor'],
        ],
        'hours' => [
            'label' => 'Jam buka',
            'answer' => 'Jam operasional kami: :hours.',
            'link' => 'Buka halaman kontak',
            'keywords' => ['jam', 'buka', 'operasional', 'jadwal', 'kapan'],
        ],
        'location' => [
            'label' => 'Lokasi',
            'answer' => 'Alamat: :address',
            'link' => 'Lihat anak perusahaan kami',
            'keywords' => ['dimana', 'di mana', 'alamat', 'lokasi', 'peta', 'kota', 'anak perusahaan', 'kantor'],
        ],
        'company' => [
            'label' => 'Tentang perusahaan',
            'answer' => 'PT Metindo Perkasa adalah spesialis finishing logam dan elektroplating di Indonesia.',
            'link' => 'Tentang kami',
            'keywords' => ['tentang', 'perusahaan', 'siapa', 'metindo'],
        ],
    ],
];
