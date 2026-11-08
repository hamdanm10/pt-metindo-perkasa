<?php

declare(strict_types=1);

return [
    'title' => 'Akun',
    'create' => 'Tambah akun',
    'create_title' => 'Tambah akun',
    'edit_title' => 'Detail akun',
    'edit' => 'Ubah',
    'search' => 'Cari nama atau email',
    'all_roles' => 'Semua peran',
    'all_statuses' => 'Semua status',
    'actions' => 'Aksi',
    'empty' => 'Tidak ada akun yang cocok.',
    'save' => 'Simpan',
    'cancel' => 'Batal',
    'back' => 'Kembali ke akun',
    'fields' => [
        'name' => 'Nama',
        'email' => 'Email',
        'role' => 'Peran',
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
        'self_role' => 'Anda tidak bisa mencabut peran admin Anda sendiri.',
        'last_admin' => 'Admin aktif terakhir tidak boleh kehilangan peran admin.',
        'self_active' => 'Anda tidak bisa menonaktifkan akun Anda sendiri.',
        'last_admin_active' => 'Admin aktif terakhir tidak boleh dinonaktifkan.',
    ],
];
