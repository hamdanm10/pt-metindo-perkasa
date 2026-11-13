<?php

declare(strict_types=1);

return [
    'title' => 'Nomor WhatsApp',
    'description' => 'Nomor customer service untuk tombol WhatsApp di situs. Hanya nomor yang aktif ditampilkan, sesuai urutan ini. Tanpa nomor aktif, tombolnya disembunyikan.',
    'create' => 'Tambah nomor',
    'create_title' => 'Tambah nomor',
    'create_description' => 'Tambah nomor customer service. Pengunjung akan mengobrol dengannya lewat WhatsApp.',
    'edit_title' => 'Ubah nomor',
    'edit_description' => 'Ubah nama atau nomornya.',
    'edit' => 'Ubah',
    'activate' => 'Aktifkan',
    'deactivate' => 'Nonaktifkan',
    'move_up' => 'Naik',
    'move_down' => 'Turun',
    'delete' => 'Hapus',
    'confirm_delete' => 'Hapus nomor ini? Nomor hilang dari situs. Untuk menyembunyikannya sementara, nonaktifkan saja.',
    'actions' => 'Aksi',
    'empty' => 'Belum ada nomor. Tombol WhatsApp disembunyikan di situs sampai Anda menambahkan satu.',
    'save' => 'Simpan',
    'cancel' => 'Batal',
    'fields' => [
        'name' => 'Nama',
        'number' => 'Nomor WhatsApp',
        'status' => 'Status',
    ],
    'hints' => [
        'number' => 'Dengan kode negara, misalnya +62 812-0000-0001. Spasi, strip, dan tanda plus diabaikan.',
    ],
    'status' => [
        'active' => 'Aktif',
        'inactive' => 'Nonaktif',
    ],
    'messages' => [
        'created' => 'Nomor ditambahkan.',
        'updated' => 'Perubahan disimpan.',
        'deleted' => 'Nomor dihapus.',
        'bad_number' => 'Isi nomor lengkap dengan kode negara, 8 sampai 15 angka, misalnya +62 812-0000-0001.',
    ],
];
