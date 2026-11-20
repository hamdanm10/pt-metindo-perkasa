<?php

declare(strict_types=1);

return [
    'title' => 'Kategori proses',
    'create' => 'Tambah kategori proses',
    'create_title' => 'Tambah kategori proses',
    'description' => 'Kelola daftar kategori proses yang tampil di situs, lengkap dengan teks di tiap bahasa.',
    'create_description' => 'Isi nama, lalu tulis tiap bahasa sendiri. Bahasa Inggris wajib.',
    'edit_description' => 'Ubah nama atau teks kategori proses ini.',
    'edit_title' => 'Ubah kategori proses',
    'move_up' => 'Naik',
    'move_down' => 'Turun',
    'language_filled' => 'Sudah ditulis',
    'language_empty' => 'Belum ditulis',
    'edit' => 'Ubah',
    'search' => 'Cari nama',
    'actions' => 'Aksi',
    'empty' => 'Belum ada kategori proses.',
    'save' => 'Simpan',
    'cancel' => 'Batal',
    'image_current' => 'Gambar saat ini',
    'status' => 'Status',
    'processes' => 'Proses',
    'active' => 'Aktif',
    'inactive' => 'Disembunyikan',
    'fields' => [
        'image' => 'Gambar',
        'name' => 'Nama',
        'slug' => 'Alamat (slug)',
        'active' => 'Tampil di situs',
        'languages' => 'Bahasa',
        'description' => 'Deskripsi',
    ],
    'hints' => [
        'image' => 'Wajib sampai kategori punya gambar. JPG, PNG, atau WebP maksimal 2 MB, minimal 800x600. Gambar dipotong 4:3 dan tampil di halaman proses dan beranda.',
        'name' => 'Nama yang tampil di situs. Alamat halaman dibuat dari nama ini saat kategori ditambahkan.',
        'active' => 'Matikan untuk menyembunyikan kategori dan prosesnya tanpa menghapusnya.',
        'languages' => 'Bahasa tanpa teks tidak disimpan. Bahasa yang belum diterjemahkan menampilkan teks bahasa Inggris.',
    ],
    'messages' => [
        'name_unusable' => 'Nama ini menghasilkan alamat yang sudah dipakai kategori proses lain atau tidak bisa dijadikan alamat. Pakai nama lain.',
        'created' => 'Kategori proses ditambahkan.',
        'updated' => 'Perubahan disimpan.',
        'default_language_required' => 'Tulis deskripsi :language: teks ini tampil di bahasa yang belum diterjemahkan.',
    ],
];
