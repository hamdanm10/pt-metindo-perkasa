<?php

declare(strict_types=1);

return [
    'title' => 'Asisten chat',
    'description' => 'Pertanyaan yang dijawab chat di situs. Tiap jawaban bisa mengarah ke pertanyaan lanjutan, sedalam yang Anda mau. Pengunjung hanya melihat bahasa yang Anda isi.',
    'create' => 'Tambah pertanyaan',
    'create_title' => 'Tambah pertanyaan',
    'create_description' => 'Tulis pertanyaan yang diketuk pengunjung dan jawabannya, di tiap bahasa yang ingin ditawarkan.',
    'edit_title' => 'Ubah pertanyaan',
    'edit_description' => 'Ubah pertanyaan, jawaban, atau tautan di bahasa mana pun.',
    'filled' => 'Sudah diisi',
    'follows' => 'Lanjutan dari:',
    'add_follow_up' => 'Tambah lanjutan',
    'edit' => 'Ubah',
    'move_up' => 'Naik',
    'move_down' => 'Turun',
    'delete' => 'Hapus',
    'confirm_delete' => 'Hapus pertanyaan ini, jawabannya di semua bahasa, dan semua pertanyaan lanjutannya? Tindakan ini tidak bisa dibatalkan.',
    'actions' => 'Aksi',
    'level' => 'Pertanyaan :number',
    'empty' => 'Belum ada pertanyaan. Tombol chat disembunyikan di situs sampai Anda menambahkan satu.',
    'save' => 'Simpan',
    'cancel' => 'Batal',
    'fields' => [
        'question' => 'Pertanyaan',
        'answer' => 'Jawaban',
        'link_label' => 'Teks tautan',
        'link_url' => 'Alamat tautan',
        'languages' => 'Bahasa',
    ],
    'hints' => [
        'languages' => 'Isi hanya bahasa yang diinginkan. Bahasa yang dikosongkan tidak tampil bagi pengunjung bahasa itu, begitu juga pertanyaan lanjutannya.',
        'link_url' => 'Opsional. Halaman situs ini seperti /id/contact, atau alamat lengkap yang diawali https://.',
    ],
    'messages' => [
        'created' => 'Pertanyaan ditambahkan.',
        'updated' => 'Perubahan disimpan.',
        'deleted' => 'Pertanyaan dihapus.',
        'language_required' => 'Isi setidaknya satu bahasa.',
        'bad_link' => 'Pakai halaman situs ini yang diawali / atau alamat yang diawali http:// atau https://.',
    ],
];
