# Prasyarat Lingkungan

Yang perlu ada sebelum mengerjakan proyek ini di mesin baru. Aturan kerja ada di [`CLAUDE.md`](../CLAUDE.md) (`AGENTS.md` adalah symlink ke berkas yang sama).

Pastikan PHP dan Composer tersedia:

```sh
php -v
composer -V
```

Jika salah satu perintah tidak tersedia, deteksi sistem operasi pengguna lalu instal prasyarat dengan perintah yang sesuai:

macOS:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.5)"
```

Windows PowerShell:

```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```

Linux:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.5)"
```

Setelah instalasi, minta pengguna me-restart terminal. Jika agent membutuhkan shell yang sudah di-restart untuk melanjutkan, minta pengguna membuka ulang terminal dan menjalankan kembali prompt awalnya.

## Catatan

- Laravel Boost belum dipakai di proyek ini. Instruksi bootstrap-nya (`composer require laravel/boost` dan `php artisan boost:install`) dihapus dari `CLAUDE.md` karena tidak pernah dijalankan dan menyuruh agent memasangnya di setiap sesi. Pasang hanya bila tim memutuskan memakainya.
- Database pengembangan memakai PostgreSQL (`DB_CONNECTION=pgsql`, database `pt_metindo_perkasa`) lewat layanan `postgres` di `.lerd.yaml`.
- Database test (nama `pt_metindo_perkasa_testing`, harus berakhiran `_testing`) dibuat sekali di mesin masing-masing: `create database pt_metindo_perkasa_testing`. Peralihan `phpunit.xml` ke PostgreSQL belum dilakukan.
