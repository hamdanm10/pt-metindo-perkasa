# Fondasi Akun (D1)

Akun dan login untuk dashboard: satu tabel `users` dengan tiga peran (`admin`, `staff`, `customer`), halaman login terpisah untuk staf dan customer, pembatasan akses per peran, dan perintah untuk membuat admin pertama. Akun dibuat oleh admin, tidak ada pendaftaran mandiri. Dashboard dan fitur tiap peran dikerjakan di fase lain. Fase dan statusnya di [`../implementation-plan.md`](../implementation-plan.md). Aturan peran dan arsitektur ada di `CLAUDE.md`.

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | Satu tabel `users`. Tambah kolom `role` (enum `App\Enums\Role`: `admin`, `staff`, `customer`), `locale` (`en` atau `id`, bawaan `en`), dan `is_active` (bawaan `true`) | Satu pengguna satu peran. `is_active` memungkinkan akun dimatikan tanpa dihapus |
| 2 | Data khusus peran (misalnya profil customer) tidak dibuat di D1. Tabel profil 1:1 dibuat saat fitur peran itu dirancang | Tidak membangun yang belum jelas |
| 3 | Login staf dan admin di `/app/login`, customer di `/customer/login`. Sesi (guard `web`) | Sesuai aturan di `CLAUDE.md` |
| 4 | Login menolak akun yang perannya tidak termasuk area itu, atau yang tidak aktif, dengan **pesan yang sama** seperti kata sandi salah | Tidak membocorkan jenis atau status akun |
| 5 | Tidak ada pendaftaran, verifikasi email, atau login Google. Admin membuat akun dan menetapkan kata sandi awal, dan admin yang mengatur ulang kata sandi | Keputusan client. Tanpa pengiriman email dulu |
| 6 | Admin pertama dibuat lewat `php artisan admin:create` | Tidak ada jalan masuk lain di awal |
| 7 | Kata sandi minimal 8 karakter dengan huruf besar, huruf kecil, dan angka | Standar dasar yang wajar |
| 8 | Pengguna yang sudah masuk dan membuka halaman login diarahkan ke areanya. Pengguna yang masuk dengan peran salah ke area lain mendapat 403 | Jelas dan tidak ambigu |
| 9 | Locale dashboard: `users.locale` bila masuk, lalu `session('locale')`, lalu `en`. Hanya `en` dan `id`. Pemilih bahasa dibuat di D2, dan bahasa yang dipilih di halaman login disimpan ke akun saat masuk | Mengikuti aturan locale di `CLAUDE.md` |
| 10 | Halaman login memakai Blade, bukan Livewire | Satu formulir sederhana. Livewire dimulai di D2 |
| 11 | Halaman setelah login di D1 hanya halaman rangka `/app/dashboard` dan `/customer/dashboard` berisi nama dan tombol keluar, digantikan di D2. `/app` dan `/customer` dialihkan ke sana | Agar login bisa diuji ujung ke ujung |
| 12 | Setelah masuk, pengguna dikembalikan ke halaman yang tadi dituju hanya bila halaman itu termasuk areanya. Halaman area lain diabaikan dan pengguna ke beranda areanya | Tanpa ini customer yang tadi menuju `/app` berakhir di halaman 403 |

## Alur

1. Pengunjung membuka `/app/dashboard` tanpa login, lalu diarahkan ke `/app/login` (area customer ke `/customer/login`).
2. Formulir login (email, kata sandi, ingat saya) dikirim ke Form Request, lalu ke Action `AuthenticateUser`.
3. Action memeriksa batas percobaan, lalu mencocokkan kredensial dengan syarat peran termasuk area itu dan `is_active` benar. Sukses: controller meregenerasi sesi dan mengarahkan pengguna (lihat keputusan 12).
4. Gagal: pesan umum yang sama. Terlalu banyak percobaan: pesan dengan sisa detik.
5. Keluar (`POST`): sesi dibatalkan dan token diperbarui, lalu kembali ke halaman login areanya.

## Aturan keamanan dan kasus tepi

- Batas percobaan 5 per menit per kombinasi email dan IP (`RateLimiter`). Percobaan yang berhasil menghapus hitungannya.
- Pesan gagal login sama untuk email tidak ada, kata sandi salah, peran salah area, dan akun tidak aktif.
- Sesi diregenerasi saat masuk dan dibatalkan saat keluar. Kata sandi di-hash (bcrypt) dan tidak masuk log.
- `role` dan `is_active` tidak boleh diisi lewat input pengguna (bukan `fillable` untuk request biasa). Hanya admin yang mengubahnya lewat fitur kelola akun (D4).
- Akun dinonaktifkan: sesi yang sedang berjalan juga ditolak oleh middleware pada permintaan berikutnya.
- Kasus tepi yang diuji: email berbeda huruf besar-kecil, kata sandi kosong, peran salah, dan akun tidak aktif.
- Pesan validasi dan kesalahan memakai `lang/{en,id}` lewat `__()`, dan kolom baru ditambahkan di `attributes`.

## Struktur kode

- `app/Enums/Role.php`, `app/Models/User.php` (cast `role`, helper peran), migrasi untuk kolom baru, dan factory dengan state `admin()`, `staff()`, `customer()`.
- `app/Actions/Auth/AuthenticateUser.php`: tidak memakai `request()` maupun sesi. Menerima email, kata sandi, ingat saya, daftar peran yang diizinkan, dan kunci pembatasan. `app/Actions/Users/CreateUser.php` membuat akun (dipakai perintah `admin:create` dan kelak fitur kelola akun).
- `app/Http/Requests/Auth/LoginRequest.php` (dipakai kedua area).
- Controller: kelas dasar `app/Http/Controllers/Auth/AuthenticatedSessionController.php` memuat alur masuk dan keluar. `app/Http/Controllers/App/Auth/LoginController.php` dan `app/Http/Controllers/Customer/Auth/LoginController.php` hanya mengisi peran, nama route, dan judulnya. `HomeController` di tiap area menampilkan halaman rangka.
- Route: `routes/app/auth.php` (prefix `/app`, nama `app.*`) dan `routes/customer/auth.php` (prefix `/customer`, nama `customer.*`), dimuat di `bootstrap/app.php`.
- Middleware: `EnsureRole` (alias `role`, memeriksa peran dan keaktifan akun) dan `SetDashboardLocale`. Pengalihan tamu dan pengguna masuk diatur di `bootstrap/app.php`. Route `POST /{area}/login` juga dibatasi `throttle:30,1` per IP sebagai lapis kedua.
- Perintah `app/Console/Commands/CreateAdmin.php` (interaktif, bisa juga lewat opsi, menolak email yang sudah ada).
- View: `resources/views/layouts/auth.blade.php`, `resources/views/auth/login.blade.php` (satu formulir untuk kedua area, bergaya merek), dan `resources/views/auth/home.blade.php` (halaman rangka). CSS lewat entri `resources/css/dashboard.css` yang memakai token tema yang sama. Halaman memakai `noindex`.
- `lang/{en,id}/auth.php` dan `lang/id/validation.php` (isi yang dipakai saja, bertambah sesuai kebutuhan).

## Langkah dan test

Langkah: enum dan migrasi, model dan factory, Action, Form Request, controller dan route, middleware, perintah admin, view dan teks, test.

Test yang harus ada:

- Admin dan staf bisa masuk di `/app/login`, customer di `/customer/login`, dan masing-masing diarahkan ke areanya.
- Customer ditolak di `/app/login`, admin dan staf ditolak di `/customer/login`, dengan pesan yang sama dengan kata sandi salah.
- Akun tidak aktif ditolak. Sesi akun yang dinonaktifkan ditolak pada permintaan berikutnya.
- Kata sandi salah, email tidak ada, dan input kosong: kesalahan validasi dan pesan sesuai locale.
- Batas percobaan: percobaan ke-6 ditolak, dan berhasil masuk mereset hitungan.
- Tamu ke area terlindungi diarahkan ke login yang benar. Pengguna masuk ke halaman login diarahkan ke areanya. Peran salah ke area lain mendapat 403.
- Keluar membatalkan sesi dan kembali ke login yang benar. Sesi diregenerasi saat masuk.
- Halaman yang dituju sebelum masuk dipakai bila termasuk area itu, dan diabaikan bila dari area lain.
- Perintah `admin:create`: membuat admin, menolak email ganda, dan menolak kata sandi lemah. `role`, `is_active`, dan `locale` tidak bisa diisi lewat mass assignment.
- Locale dashboard mengikuti `users.locale`, lalu sesi, lalu `en`, dan nilai di luar `en` dan `id` jatuh ke `en`.
- Unit: enum `Role`. Action `AuthenticateUser` diuji lewat test fitur login karena butuh database dan sesi.

Pemeriksaan manual: alur login di browser (kedua area, `en` dan `id`) dan tampilan di ponsel.

## Ditunda

- Kelola akun oleh admin (membuat staf dan customer, atur ulang kata sandi, nonaktifkan): fase D4 dan seterusnya.
- Pengguna mengganti kata sandinya sendiri, dan atur ulang lewat email: belum diputuskan.
- Pemilih bahasa dashboard dan tampilan dashboard (D2).
- Profil khusus peran, izin yang lebih rinci antara admin dan staf, dan 2FA.
