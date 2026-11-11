<?php

declare(strict_types=1);

return [
    'kinds' => [
        'staff' => [
            'title' => 'Staf',
            'create' => 'Tambah staf',
            'create_title' => 'Tambah staf',
            'edit_title' => 'Detail staf',
            'empty' => 'Tidak ada staf yang cocok.',
            'back' => 'Kembali ke staf',
        ],
        'customer' => [
            'title' => 'Customer',
            'create' => 'Tambah customer',
            'create_title' => 'Tambah customer',
            'edit_title' => 'Detail customer',
            'empty' => 'Tidak ada customer yang cocok.',
            'back' => 'Kembali ke customer',
        ],
    ],
    'edit' => 'Ubah',
    'search' => 'Cari nama atau email',
    'all_statuses' => 'Semua status',
    'actions' => 'Aksi',
    'save' => 'Simpan',
    'cancel' => 'Batal',
    'fields' => [
        'name' => 'Nama',
        'email' => 'Email',
        'status' => 'Status',
        'initial_password' => 'Kata sandi awal',
        'new_password' => 'Kata sandi baru',
    ],
    'status' => [
        'active' => 'Aktif',
        'inactive' => 'Nonaktif',
    ],
    'password_hint' => 'Minimal 8 karakter dengan huruf besar, huruf kecil, dan angka.',
    'reset_title' => 'Atur ulang kata sandi',
    'reset_text' => 'Tetapkan kata sandi baru. Akun akan keluar dari semua perangkat.',
    'reset_password' => 'Tetapkan kata sandi baru',
    'status_title' => 'Status akun',
    'deactivate_text' => 'Akun nonaktif tidak bisa masuk dan langsung dikeluarkan.',
    'activate_text' => 'Akun ini nonaktif. Aktifkan agar bisa masuk lagi.',
    'deactivate' => 'Nonaktifkan akun',
    'activate' => 'Aktifkan akun',
    'confirm_deactivate' => 'Nonaktifkan akun ini? Akun langsung dikeluarkan.',
    'messages' => [
        'created' => 'Akun dibuat.',
        'updated' => 'Akun diperbarui.',
        'password_reset' => 'Kata sandi diperbarui.',
        'activated' => 'Akun diaktifkan.',
        'deactivated' => 'Akun dinonaktifkan.',
    ],
    'errors' => [
        'self_active' => 'Anda tidak bisa menonaktifkan akun Anda sendiri.',
        'last_admin_active' => 'Admin aktif terakhir tidak boleh dinonaktifkan.',
    ],
];
