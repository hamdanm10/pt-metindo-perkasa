<?php

declare(strict_types=1);

return [
    'required' => ':Attribute wajib diisi.',
    'string' => ':Attribute harus berupa teks.',
    'email' => ':Attribute harus berupa alamat email yang valid.',
    'boolean' => ':Attribute harus bernilai benar atau salah.',
    'unique' => ':Attribute sudah dipakai.',
    'max' => [
        'string' => ':Attribute tidak boleh lebih dari :max karakter.',
    ],
    'min' => [
        'string' => ':Attribute minimal :min karakter.',
    ],
    'password' => [
        'letters' => ':Attribute harus mengandung sedikitnya satu huruf.',
        'mixed' => ':Attribute harus mengandung sedikitnya satu huruf besar dan satu huruf kecil.',
        'numbers' => ':Attribute harus mengandung sedikitnya satu angka.',
        'symbols' => ':Attribute harus mengandung sedikitnya satu simbol.',
        'uncompromised' => ':Attribute pernah muncul dalam kebocoran data. Pilih :attribute lain.',
    ],
    'enum' => ':Attribute yang dipilih tidak valid.',
    'in' => ':Attribute yang dipilih tidak valid.',
    'attributes' => [
        'name' => 'nama',
        'email' => 'email',
        'password' => 'kata sandi',
        'role' => 'peran',
        'locale' => 'bahasa',
        'form.name' => 'nama',
        'form.email' => 'email',
        'form.role' => 'peran',
        'form.locale' => 'bahasa',
        'form.password' => 'kata sandi',
        'newPassword' => 'kata sandi baru',
    ],
];
