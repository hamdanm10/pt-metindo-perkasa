# Panduan Agent: pt-metindo-perkasa

Aplikasi Laravel fullstack (Blade, tanpa API untuk saat ini) untuk PT Metindo Perkasa: landing page publik dalam 5 bahasa dan dashboard untuk admin, staf, dan customer. Sebelum mengerjakan fitur, baca indeks peran dan dokumen fiturnya lewat bagian **Referensi Dokumen** di bawah. Prasyarat mesin: [`docs/setup.md`](docs/setup.md).

Repo: `github.com/hamdanm10/pt-metindo-perkasa`.

## Konvensi Bahasa

- **Dokumentasi** (file `.md` seperti README, docs, changelog, dan catatan): gunakan **bahasa Indonesia**.
- **Kode**: gunakan **bahasa Inggris** untuk nama variabel, fungsi, class, method, route, dan komentar kode.
- **Database**: gunakan **bahasa Inggris** untuk nama tabel, kolom, index, dan migration.
- **Peran**: istilah "staf" dipakai di dokumen dan tampilan, tetapi identifier di kode, tabel, route, dan URL ditulis `staff`.
- **Nama folder dan berkas**: bahasa Inggris, termasuk di `docs/`.
- **Lainnya**: pesan commit, nama branch, dan identifier teknis lain juga menggunakan bahasa Inggris.

### Teks untuk pengguna dan locale

- Teks untuk pengguna **tidak ditulis langsung di kode atau view**. Teks antarmuka tetap (pesan validasi, label dashboard, email, notifikasi) ada di `lang/<locale>` dan dipanggil lewat `__()`/`trans()`. Kunci terjemahan tetap bahasa Inggris. Nama kolom di pesan validasi diatur di `attributes` pada `lang/<locale>/validation.php`: kolom baru wajib ditambahkan di sana.
- **Konten landing yang dinamis** (judul, paragraf, judul halaman, deskripsi meta, dan sebagainya) disimpan di database, per bahasa.
- **Locale bawaan: `en`**, untuk landing maupun dashboard.

| Area | Bahasa | Cara memilih locale |
| --- | --- | --- |
| Landing | `en`, `id`, `zh` (Mandarin), `ar` (Arab), `ja` (Jepang) | Awalan URL (`/en`, `/id`, ...). `/` mengarah ke `/en`. Tanpa deteksi bahasa browser |
| Dashboard | `en`, `id` | Kolom `users.locale`; sebelum login memakai cookie atau sesi; cadangan `en`. Tanpa awalan URL |

- Daftar locale yang diizinkan didefinisikan **sekali** di `config`, per area, dan tidak ditulis ulang di tempat lain.
- Satu middleware untuk landing dan satu untuk dashboard, masing-masing hanya mengatur locale. Nilai di luar daftar area itu (misalnya `zh` dari cookie landing masuk ke dashboard) jatuh ke `en`.
- Teks baru di landing wajib tersedia dalam kelima bahasa. Teks baru di dashboard cukup `en` dan `id`.
- Arab ditulis dari kanan ke kiri: layout landing mengatur `dir="rtl"` dari locale aktif. Font Mandarin, Arab, dan Jepang harus dipertimbangkan di desain.

## Aturan Commit

Mengikuti [Angular Commit Message Convention](https://gist.github.com/nyancodeid/63f19941c81252bb0cca9c14497cf9f7), ditulis dalam bahasa Inggris. Format: `<type>(<scope>): <subject>`, tiap baris maksimal 100 karakter, `scope` opsional (bagian yang terdampak, contoh `auth` atau `landing`; kosongkan bila mencakup banyak bagian).

- **Type:** `build`, `ci`, `docs`, `feat`, `fix`, `perf`, `refactor`, `style`, `test`.
- **Subject:** kalimat perintah present tense, huruf kecil di awal, tanpa titik di akhir ("add ...", bukan "added ...").
- **Body** (opsional): jelaskan motivasi dan bandingkan dengan perilaku sebelumnya, dalam imperative present tense.
- **Footer:** `BREAKING CHANGE: <penjelasan>` untuk perubahan yang memutus, `Closes #123` untuk menutup issue.
- **Revert:** `revert: <header yang di-revert>` dengan body `This reverts commit <hash>.`
- Tambahkan berkas dengan nama (`git add <jalur>`), bukan `git add -A` atau `git add .`, supaya berkas yang belum diputuskan pemiliknya tidak ikut ter-commit.
- **Jangan menambahkan baris `Co-Authored-By` (termasuk Claude)** di pesan commit. Hal yang sama berlaku untuk atribusi "Generated with Claude Code" di deskripsi pull request. Aturan ini mengalahkan atribusi bawaan sesi.
- Contoh: `feat(landing): add language switcher`, `fix(auth): reject customer on staff login`, `docs: update setup instructions`.

## Aturan Branch

Setiap fitur dikerjakan di **branch tersendiri**, dibuat dari branch `development`. Nama branch menggunakan bahasa Inggris, huruf kecil, dan kata dipisahkan tanda hubung (`kebab-case`).

| Branch | Fungsi |
| --- | --- |
| `main` | Kode yang sudah dirilis (production). Tidak dikerjakan langsung |
| `development` | Integrasi seluruh fitur dan basis untuk semua branch kerja. Tidak dikerjakan langsung |
| `<type>/<name>` | Pengerjaan satu fitur atau perubahan |

Awalan branch (`<type>`) mengikuti type commit: `feat/`, `fix/`, `docs/`, `build/`, `refactor/`, `test/`. Contoh: `feat/landing-content`, `fix/staff-login-redirect`.

### Alur Kerja Agent

Saat memulai pekerjaan sebuah fitur, agent harus otomatis mengikuti langkah berikut:

1. Cek branch aktif dengan `git branch --show-current`.
2. Jika branch aktif sudah sesuai dengan fitur yang dikerjakan, lanjutkan.
3. Jika belum, pindah ke `development`, ambil perubahan terbaru, lalu buat dan pindah ke branch kerja: `git checkout development && git pull && git checkout -b feat/<name>`.
4. Satu branch hanya untuk satu fitur. Jangan mencampur beberapa fitur dalam satu branch.
5. Jika ada perubahan yang belum di-commit saat berpindah branch, jangan dibuang. Commit atau stash terlebih dahulu, dan beri tahu pengguna.

### Aturan Merge

- Branch kerja digabung ke `development` melalui pull request setelah fitur selesai dan `composer check` lulus. Belum ada CI/CD, jadi pemeriksaan dijalankan secara lokal.
- Fitur tanpa test dianggap belum selesai dan tidak di-merge.
- `main` hanya menerima merge dari `development`, untuk menandai rilis.
- Jangan melakukan force push ke `main` maupun `development`.
- Hapus branch kerja setelah berhasil di-merge.
- Merge, push, dan pembuatan PR hanya dilakukan atas permintaan pengguna.

## Peran dan Cakupan

Tiga peran dengan antarmuka Blade (dashboard nanti mungkin memakai Livewire). Belum ada API.

| Peran | Halaman login | Area setelah masuk | Keterangan |
| --- | --- | --- | --- |
| `admin` | `/app/login` | `/app/*` | Mengelola seluruh sistem, termasuk konten landing |
| `staff` | `/app/login` | `/app/*` | Hak akses lebih terbatas dari admin |
| `customer` | `/customer/login` | `/customer/*` | Area customer |

- Tetap satu tabel `users` dan satu mekanisme sesi. `/app/login` menolak akun `customer`, dan `/customer/login` menolak `admin` dan `staff`, dengan pesan yang sama seperti kredensial salah supaya tidak membocorkan jenis akun.
- Middleware memastikan `/app/*` hanya untuk `admin` dan `staff`, dan `/customer/*` hanya untuk `customer`.
- **Pengelolaan konten landing hanya untuk `admin`**, dijaga lewat Policy.
- `app` dan `customer` dicadangkan di level root, sehingga tidak bentrok dengan awalan locale landing yang hanya menerima kode bahasa pada daftar.
- Logic bisnis (Action dan Service) dipakai bersama oleh semua peran. Yang berbeda hanya lapisan controller, Form Request, dan view.

### Data Pengguna dan Peran

- **Satu tabel `users`** menyimpan identitas dan autentikasi semua peran: `name`, `email`, `password`, `role`, `locale`.
- `role` bertipe backed enum `App\Enums\Role` dengan nilai `admin`, `staff`, `customer`. **Satu pengguna hanya memiliki satu peran.**
- Data khusus per peran disimpan di **tabel profil terpisah** dengan relasi 1:1 ke `users`. Jangan menambah kolom khusus peran ke tabel `users`, dan jangan membuat tabel akun terpisah per peran.
- Hak akses berdasarkan `role` dijaga lewat middleware dan Policy.

## Arsitektur Kode

- **Controller tetap bersih.** Controller hanya menerima request, memanggil Action atau Service, lalu mengembalikan view atau redirect. Tidak ada logika bisnis dan validasi di dalamnya.
- **Action untuk satu use case.** Satu class Action menangani satu use case. Letakkan di `app/Actions`.
- **Service untuk logic yang dipakai berulang.** Letakkan di `app/Services`. Action boleh memanggil Service.
- **Action dan Service tidak boleh bergantung pada request maupun response.** Jangan memanggil `request()`, `view()`, atau `redirect()` di dalamnya.
- **Validasi lewat Form Request**, jangan validasi langsung di controller.
- **Typesafe.** Pakai `declare(strict_types=1);`, type hint pada parameter, return type, dan typed property. Hindari `mixed` bila tipe yang lebih spesifik memungkinkan.
- **Konfigurasi.** Jangan memanggil `env()` di luar `config/*` karena `config:cache` membuatnya `null`.
- **Hindari N+1.** Relasi yang dipakai view wajib di-`with()` di Action. Kolom yang dicari, disaring, atau diurutkan harus punya index di migration (PostgreSQL tidak membuat index otomatis untuk foreign key).
- **Data sensitif.** Token, password, dan nomor telepon tidak boleh masuk log maupun tampil di tempat yang tidak semestinya.

### Lokasi Berkas

| Lapisan | Landing | Dashboard (`/app`) | Customer (`/customer`) |
| --- | --- | --- | --- |
| Controller | `app/Http/Controllers/Landing` | `app/Http/Controllers/App` | `app/Http/Controllers/Customer` |
| Form Request | `app/Http/Requests/Landing` | `app/Http/Requests/App` | `app/Http/Requests/Customer` |
| View | `resources/views/landing` | `resources/views/app` | `resources/views/customer` |
| Action / Service | `app/Actions`, `app/Services` (dipakai bersama) | sama | sama |

- View tidak berisi logika bisnis dan query. Data disiapkan oleh controller melalui Action atau Service.
- **Livewire** belum dipakai. Bila nanti dipakai untuk dashboard, komponen tetap tipis dan logic bisnis tetap di Action/Service. Catat keputusannya di dokumen fitur terkait.

## Landing Page dan SEO

- Landing dirender di server dengan Blade. **Konten utama tidak boleh bergantung pada JavaScript atau Livewire** supaya terbaca mesin pencari.
- Tiap bahasa punya alamat sendiri, `<html lang>` dan `dir` sesuai locale, serta tag `hreflang` dan `canonical`. Sitemap mencakup semua bahasa.
- Judul halaman (`<title>`) dan deskripsi meta disimpan per bahasa di database bersama kontennya.
- Konten dinamis disimpan per bahasa memakai kolom JSON (`spatie/laravel-translatable`, belum terpasang, putuskan saat fitur dimulai). Bila terjemahan suatu bahasa belum ada, cadangannya `en`.
- Konten landing dari database di-cache, dan cache dibersihkan saat admin mengubah konten.

## Route

- Utamakan `Route::resource()` (atau `Route::singleton()`) dibanding mendefinisikan `GET`, `POST`, dan sebagainya satu per satu. Definisi satu per satu hanya jika aksi tidak cocok dengan pola resource.
- Jangan memakai route closure karena menghalangi `route:cache`. Selalu arahkan ke controller.

| Lokasi | Isi | Prefix URL | Nama route | Middleware dasar |
| --- | --- | --- | --- | --- |
| `routes/landing/*` | Landing publik | `/{locale}` | `landing.*` | `web`, middleware locale landing |
| `routes/app/*` | Dashboard admin dan staf | `/app` | `app.*` | `web`, `auth`, peran admin atau staf |
| `routes/customer/*` | Area customer | `/customer` | `customer.*` | `web`, `auth`, peran customer |

- Satu file per **fitur** di dalam subfolder (contoh: `routes/app/landing-content.php`), bukan satu file besar.
- Route dari area yang berbeda tidak boleh bercampur dalam satu file.

## Testing

- **Setiap fitur wajib memiliki test**, ditulis di branch yang sama dengan fiturnya.
- Framework: PHPUnit (`php artisan test` atau `composer test`).
- **Feature test** (`tests/Feature`) untuk alur HTTP: route, middleware, Form Request, dan response. **Unit test** (`tests/Unit`) untuk logic murni di Action dan Service.
- Cakup jalur berhasil, jalur gagal (validasi, akses ditolak, tidak terautentikasi), dan kasus batas. Untuk fitur yang punya teks, uji juga locale yang berlaku.
- Perbaikan bug disertai test yang gagal sebelum perbaikan dan lulus sesudahnya.
- Semua test harus lulus sebelum commit dan sebelum merge.
- **Semua test berjalan di PostgreSQL**, jenis database yang sama dengan aplikasi, bukan SQLite. Database test adalah `pt_metindo_perkasa_testing` (diatur paksa di `phpunit.xml`) dan dibangun ulang di setiap run.
- `tests/TestCase.php` menolak menjalankan test pada database yang namanya tidak berakhiran `_testing`, supaya data asli tidak pernah terhapus. Jangan menghapus pengaman ini.
- Buat database test sekali di tiap mesin: `create database pt_metindo_perkasa_testing`.
- Jangan menulis migration atau query yang hanya jalan di SQLite.
- Perintah yang menghapus data (`migrate:fresh`, `migrate:reset`, `migrate:rollback`, `db:wipe`) ditolak di luar test. Jangan menjalankannya terhadap database lokal. Menguji migration cukup lewat `composer test`. Untuk sengaja memakainya sekali, set `DB_ALLOW_DESTRUCTIVE_COMMANDS=true` sementara di `.env` (variabel dari shell tidak diteruskan ke kontainer PHP), lalu hapus lagi.
- Setiap daftar data yang punya relasi wajib punya test yang memastikan jumlah query tidak bertambah saat baris bertambah.


## Tooling

| Tool | Perintah | Fungsi |
| --- | --- | --- |
| Pint | `composer lint` | Memeriksa gaya kode (tanpa mengubah file) |
| Larastan (PHPStan level 8) | `composer analyse` | Analisis tipe statis |
| PHPUnit | `composer test` | Menjalankan test |
| Semua di atas | `composer check` | Pint, Larastan, lalu test |

- Sebelum commit jalankan `composer check`. Sebelum merge fitur jalankan juga `composer audit`.
- Untuk merapikan gaya kode, jalankan `vendor/bin/pint`.
- Larastan level 8 (menganalisis `app`, `routes`, dan `tests`) harus tetap lulus. Jangan menambahkan `@phpstan-ignore` atau baseline untuk menyembunyikan galat, perbaiki penyebabnya.


## Panduan Perilaku Coding

Mengikuti [Karpathy Guidelines](https://x.com/karpathy/status/2015883857489522876) (plugin `andrej-karpathy-skills`): condong ke kehati-hatian daripada kecepatan; untuk tugas sepele, gunakan pertimbangan.

1. **Pikirkan sebelum menulis kode.** Nyatakan asumsi, tanyakan jika ragu, sampaikan semua interpretasi dan pendekatan yang lebih sederhana, jangan memilih secara diam-diam.
2. **Sederhana dulu.** Kode seminimal mungkin. Jangan menambah fitur, abstraksi, atau konfigurasi yang tidak diminta, dan jangan menangani skenario error yang mustahil.
3. **Perubahan bedah.** Ubah hanya yang diperlukan dan ikuti gaya yang ada. Jangan merapikan kode di sekitarnya yang tidak terkait. Hapus hanya yang menjadi tidak terpakai akibat perubahan sendiri. Setiap baris yang berubah harus bisa ditelusuri ke permintaan pengguna.
4. **Eksekusi berbasis tujuan.** Ubah tugas menjadi tujuan yang bisa diverifikasi ("perbaiki bug" menjadi "tulis test yang mereproduksi bug, lalu buat lulus"). Untuk tugas multi-langkah, tulis rencana singkat beserta cara verifikasi tiap langkah.

## Desain

Warna merek dominan adalah **merah krimson** (`#D6283A`, sesuai logo), dengan teks gelap `#1B1F3B` dan latar lavender `#F3F2FF` sebagai netral, serta biru dan indigo sebagai aksen kedua. Warna dipakai lewat token tema Tailwind, bukan nilai hex di view. Landing dibuat tidak kaku dengan aset vektor dari Recraft. Fokus saat ini adalah landing page, dashboard menyusul.

- Fondasi (warna, tipografi, RTL, kontras): [`docs/shared/design-system/overview.md`](docs/shared/design-system/overview.md)
- Landing (layar intro, pemakaian aset): [`docs/landing/ui/design.md`](docs/landing/ui/design.md)
- Prompt aset vektor Recraft: [`docs/landing/ui/recraft-prompts.md`](docs/landing/ui/recraft-prompts.md)
- Aset acuan (logo, palet): `docs/references/`
- Bagian dan fitur landing mengikuti situs prototipe `http://103.125.180.65/~wwwmetin/en/homepage-2`. Tampilan dan UX dirancang ulang, prototipe hanya acuan isi dan fungsi. Fase dan statusnya di [`docs/landing/implementation-plan.md`](docs/landing/implementation-plan.md).
- Mengubah keputusan desain berarti mengubah dokumen di atas di branch yang sama.

## Referensi Dokumen

**Mengubah fitur berarti mengubah dokumennya, di branch yang sama.** Fitur boleh berubah di tengah jalan: perubahan dicatat langsung di dokumen fitur yang sudah ada (bukan dokumen baru), dan hal yang sengaja belum dikerjakan masuk bagian "Ditunda". Setiap perubahan perilaku (route, field, aturan, alur, status, locale) harus diikuti pembaruan semua dokumen yang membahasnya. Sebelum membuka PR, cari dengan `grep -rn` nama route atau istilah terkait di `docs/` (termasuk `docs/shared` dan indeks peran), lalu perbarui yang menyebutnya. Sebut di ringkasan PR dokumen mana yang diubah.

Dokumen rancangan ada di `docs/`, per area dan per fitur, dengan satu `implementation-plan.md` sebagai indeks di tiap area. **Baca indeks, lalu hanya dokumen fitur yang relevan**, bukan seluruh folder. Kapan menulis dokumen baru dan seberapa panjang: bagian "Kapan menulis dokumen" di [`docs/README.md`](docs/README.md).

| Dokumen | Isi |
| --- | --- |
| [`docs/README.md`](docs/README.md) | Peta dokumentasi dan panduan menulis dokumen |
| [`docs/setup.md`](docs/setup.md) | Prasyarat lingkungan |
| [`docs/shared/design-system/overview.md`](docs/shared/design-system/overview.md) | Sistem desain: warna, tipografi, RTL, kontras |
| [`docs/landing/ui/design.md`](docs/landing/ui/design.md) | Desain landing page dan layar intro |
| [`docs/landing/ui/recraft-prompts.md`](docs/landing/ui/recraft-prompts.md) | Prompt aset vektor Recraft |
| [`docs/shared/implementation-plan.md`](docs/shared/implementation-plan.md) | Indeks lintas peran: fondasi dashboard, akun dan login |
| [`docs/shared/auth/overview.md`](docs/shared/auth/overview.md) | Rancangan akun, peran, dan login (D1) |
| [`docs/landing/implementation-plan.md`](docs/landing/implementation-plan.md) | Indeks landing page: fase dan status |
| [`docs/roles/admin/implementation-plan.md`](docs/roles/admin/implementation-plan.md) | Indeks admin |
| `docs/roles/<peran>/implementation-plan.md` | Indeks staff dan customer (dibuat saat fitur pertama) |
