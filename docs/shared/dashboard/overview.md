# Kerangka Dashboard (D2)

Tata letak dashboard untuk admin, staf, dan customer: sidebar dan bilah atas, menu per peran, pemilih bahasa (`en` dan `id`), halaman beranda, dan desain dasar. Belum ada fitur khusus peran. Fitur ditambahkan satu per satu dan menunya ikut muncul saat fiturnya jadi. Fase dan statusnya di [`../implementation-plan.md`](../implementation-plan.md). Akun dan login: [`../auth/overview.md`](../auth/overview.md). Fondasi visual: [`../design-system/overview.md`](../design-system/overview.md).

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | Satu tata letak dipakai kedua area (`/app` dan `/customer`). Isi menu berbeda per peran | Satu desain, satu perawatan |
| 2 | **Livewire tidak dipasang di D2.** Kerangka memakai Blade biasa dan dibuat sebagai komponen tata letak yang bisa dipakai halaman Livewire. Livewire dipasang saat fitur interaktif pertama (A1, kelola akun) | D2 tidak punya interaksi yang membutuhkannya. Tidak memasang dependensi yang belum dipakai |
| 3 | Pemilih bahasa memakai formulir `POST` biasa ke `/{area}/locale`, bukan komponen Livewire | Berfungsi juga di halaman login (tamu), tanpa JavaScript, dan mudah diuji |
| 4 | Pilihan bahasa disimpan di `users.locale` bila sudah masuk, dan di sesi bila tamu. Bahasa yang dipilih di halaman login (sesi) menjadi bahasa akun saat pengguna masuk | Mengikuti aturan locale di `CLAUDE.md`. Pilihan terbaru yang eksplisit tidak boleh dikalahkan oleh nilai tersimpan |
| 5 | Menu awal hanya "Beranda". Tombol keluar ada di bilah atas | Tidak ada tautan ke halaman yang belum ada |
| 6 | Beranda berisi sapaan, nama, dan peran, plus keadaan kosong yang menjelaskan bahwa fitur akan muncul di sini | Tidak ada angka atau ringkasan karangan |
| 7 | Dial desain dashboard: ENERGY 1 / RHYTHM 1 / MOTION 1. Tenang, padat, dan jelas. Karakter merek hanya lewat warna aksen dan penanda miring pada menu aktif | Dashboard dipakai kerja, bukan dipamerkan |
| 8 | Menu di ponsel memakai panel `<details>` seperti landing, tanpa pustaka JavaScript | Konsisten dan ringan |
| 9 | Komponen yang dibuat hanya yang dipakai di D2: tata letak, kepala halaman, kartu, dan pemilih bahasa. Komponen formulir dan tabel dibuat bersama fitur pertama yang membutuhkannya | Tidak membangun yang belum dipakai |

## Tampilan

- **Desktop:** sidebar tetap di kiri berlatar `ink` dengan logo putih dan menu. Item aktif ditandai garis miring merah (selaras landing). Bilah atas putih di atas konten: judul halaman di kiri, pemilih bahasa dan menu pengguna (nama, peran, keluar) di kanan. Area konten berlatar `lavender` dengan kartu putih.
- **Ponsel:** sidebar disembunyikan. Bilah atas berisi logo dan tombol "Menu" merah yang membuka panel berisi menu, pemilih bahasa, dan keluar.
- Judul memakai font tampilan (Bricolage Grotesque untuk Latin), teks isi Figtree. Token warna sama dengan landing.
- Kontras tiap pasangan baru dicek. Teks merah di atas latar `ink` tidak dipakai. Item aktif di sidebar memakai putih tebal dengan penanda merah.

## Alur

1. Pengguna masuk dan tiba di beranda dashboard areanya.
2. Pemilih bahasa mengirim `POST /{area}/locale` dengan `locale` (`en` atau `id`). Nilai lain ditolak. Pengguna yang sudah masuk disimpan ke `users.locale`, dan semua pengunjung disimpan ke sesi.
3. Pengguna dikembalikan ke halaman sebelumnya, dan seluruh halaman kini berbahasa pilihan.

## Aturan keamanan dan kasus tepi

- Formulir bahasa dilindungi CSRF, dibatasi 60 per menit, dan hanya menerima locale dari `config('locales.dashboard')`.
- Pengalihan kembali hanya ke alamat dalam situs yang sama. Bila tidak ada halaman sebelumnya, ke beranda area (atau login area bila tamu).
- Menu dibangun dari daftar item yang tiap itemnya menyebut route dan peran yang boleh melihat. Item untuk route yang belum ada tidak dimasukkan. Menyembunyikan menu bukan pengamanan: akses tetap dijaga middleware dan Policy.
- Semua teks lewat `lang/{en,id}/dashboard.php` dan `__()`. Kunci kedua bahasa harus sama.
- Halaman dashboard `noindex`, dan nama pengguna ditampilkan dengan escape bawaan Blade.

## Struktur kode

- `resources/views/components/dashboard/`: `layout.blade.php` (sidebar, bilah atas, panel ponsel, tautan lewati-ke-konten), `page-header.blade.php`, `card.blade.php`, dan `locale-switcher.blade.php`. Pemilih bahasa juga dipakai di halaman login.
- `app/Services/Dashboard/DashboardMenu.php`: daftar item menu dan penyaringan per peran, satu-satunya tempat yang bertambah saat fitur baru jadi.
- `app/Http/Controllers/Dashboard/LocaleController.php` (invokable), `app/Http/Requests/Dashboard/UpdateLocaleRequest.php`, dan Action `app/Actions/Users/UpdateLocale.php`.
- Route `routes/app/locale.php` dan `routes/customer/locale.php` (dapat diakses tamu).
- `resources/views/dashboard/home.blade.php` menggantikan `auth/home.blade.php`. `HomeController` kedua area tetap, hanya mengganti view dan menyerahkan area.
- `resources/js/dashboard.js` (menutup panel dengan Escape dan klik di luar) didaftarkan di `vite.config.js`, dan `resources/css/dashboard.css` diperluas.
- `lang/{en,id}/dashboard.php`.

## Langkah dan test

Langkah: teks dan daftar menu, komponen tata letak, pemilih bahasa dan route, halaman beranda, penyesuaian halaman login, test.

Test (feature) yang harus ada:

- Beranda tiap peran menampilkan sapaan dengan nama, peran dalam bahasa aktif, dan keadaan kosong.
- Menu admin, staf, dan customer memuat hanya item yang diizinkan dan route yang ada. Item aktif bertanda `aria-current="page"`.
- Pemilih bahasa: mengganti ke `id` menyimpan `users.locale` dan sesi untuk pengguna masuk, hanya sesi untuk tamu, menolak `zh` dan nilai tak dikenal, dan mengarahkan kembali.
- Halaman dashboard memakai `<html lang>` sesuai locale, dan teks berganti setelah bahasa diubah.
- Halaman login menampilkan pemilih bahasa dan teksnya ikut berubah untuk tamu. Bahasa yang dipilih sebelum masuk disimpan ke akun, dan masuk tanpa pilihan mempertahankan bahasa tersimpan.
- Kunci `lang/{en,id}/dashboard.php` identik.
- Unit: `DashboardMenu` (penyaringan per peran, item tanpa route diabaikan).

Pemeriksaan manual di browser: sidebar dan bilah atas di desktop, panel di ponsel, ganti bahasa, keluar, dan keyboard (Tab, Escape).

## Ditunda

- Livewire (A1) dan komponen formulir dan tabel.
- Mode gelap, notifikasi, jejak halaman (breadcrumb), pencarian, profil pengguna, dan ganti kata sandi sendiri.
- Isi beranda yang bermakna, menyusul bersama fitur pertama peran masing-masing.
