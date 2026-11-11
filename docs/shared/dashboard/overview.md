# Kerangka Dashboard (D2)

Tata letak dashboard untuk admin, staf, dan customer: sidebar dan bilah atas, menu per peran, pemilih bahasa (`en` dan `id`), halaman beranda, dan desain dasar. Belum ada fitur khusus peran. Fitur ditambahkan satu per satu dan menunya ikut muncul saat fiturnya jadi. Fase dan statusnya di [`../implementation-plan.md`](../implementation-plan.md). Akun dan login: [`../auth/overview.md`](../auth/overview.md). Fondasi visual: [`../design-system/overview.md`](../design-system/overview.md).

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | Satu tata letak dipakai kedua area (`/app` dan `/customer`). Isi menu berbeda per peran | Satu desain, satu perawatan |
| 2 | **Livewire tidak dipasang di D2.** Kerangka memakai Blade biasa dan dibuat sebagai komponen tata letak yang bisa dipakai halaman Livewire. Livewire dipasang saat fitur interaktif pertama (A1, kelola akun) | D2 tidak punya interaksi yang membutuhkannya. Tidak memasang dependensi yang belum dipakai |
| 3 | Pemilih bahasa memakai formulir `POST` biasa ke `/{area}/locale`, bukan komponen Livewire. Di dashboard tampil sebagai **dropdown bahasa**, di halaman login sebagai tombol berdampingan | Berfungsi juga di halaman login (tamu), tanpa JavaScript, dan mudah diuji |
| 4 | Pilihan bahasa disimpan di `users.locale` bila sudah masuk, dan di sesi bila tamu. Bahasa yang dipilih di halaman login (sesi) menjadi bahasa akun saat pengguna masuk | Mengikuti aturan locale di `CLAUDE.md`. Pilihan terbaru yang eksplisit tidak boleh dikalahkan oleh nilai tersimpan |
| 5 | Menu awal hanya "Dashboard" (di bahasa Indonesia "Dasbor"). Tombol keluar ada di dropdown pengguna pada navbar | Tidak ada tautan ke halaman yang belum ada |
| 6 | Beranda berisi sapaan, nama, dan peran, plus keadaan kosong yang menjelaskan bahwa fitur akan muncul di sini | Tidak ada angka atau ringkasan karangan |
| 7 | Dial desain dashboard: ENERGY 1 / RHYTHM 1 / MOTION 1. Tenang, padat, dan jelas. Karakter merek hanya lewat warna aksen dan penanda miring pada menu aktif | Dashboard dipakai kerja, bukan dipamerkan |
| 8 | Di bawah lebar `lg`, sidebar menjadi **laci** yang meluncur masuk dari kiri ke kanan (transisi CSS 0,3 detik) saat tombol tiga garis di **sisi kiri** bilah atas ditekan, dengan tampilan yang sama seperti sidebar desktop dan latar gelap di belakangnya. Ditutup dengan tombol tutup, klik latar, atau Escape | Tampilan menu di ponsel sama dengan desktop, dan gerak sederhana tanpa pustaka |
| 9 | Isi halaman berada dalam **kontainer** berlebar maksimal `max-w-6xl` dan di tengah, supaya tetap terbaca di layar lebar | Isi tidak melebar tanpa batas di monitor besar |
| 10 | Bilah atas memakai dua dropdown: **bahasa** (nama bahasa aktif, atau kodenya di ponsel) dan **pengguna** (inisial, nama, peran, lalu email, peran, dan Keluar di panelnya). Tombol Keluar tidak lagi berdiri sendiri | Bilah atas lebih ringkas dan memberi tempat untuk menu akun kelak |
| 11 | Komponen yang dibuat hanya yang dipakai: tata letak, kepala halaman, kartu, pemilih bahasa untuk halaman login, dropdown bahasa, dan dropdown pengguna. Komponen formulir dan tabel dibuat bersama fitur pertama yang membutuhkannya | Tidak membangun yang belum dipakai |

## Tampilan

- **Desktop:** **sidebar `ink` setinggi layar** (lebar 18 rem, tetap saat gulir) di kiri, berisi logo dengan nama "Metindo Perkasa" dua baris di atas, lalu menu **dikelompokkan per label** ("Menu": Dashboard; "Content": Articles, Products, Services; "User Management": Staff dan Customers; label kecil huruf kapital putih), tiap item dengan **ikon** garis 20 px di kirinya. Item aktif berupa blok krimson penuh, dan garis krimson ada di dasar sidebar. Di sebelah kanannya **navbar putih** setinggi 4 rem (tetap saat gulir, garis tipis di bawahnya) memuat dropdown bahasa dan dropdown pengguna di kanan (chip lavender berbingkai tipis berteks `ink`, panel putih berbingkai tipis). Area konten berlatar `lavender` dengan kartu putih di dalam kontainer.
- **Ponsel:** navbar berisi tombol tiga garis merah di kiri (diikuti monogram logo), serta dropdown bahasa (kode `EN` atau `ID`) dan avatar pengguna di kanan. Tombol tiga garis membuka sidebar setinggi layar sebagai laci (tombol tutup di barisnya sendiri di atas logo): latar gelap di belakangnya meredup, gulir halaman dikunci, fokus pindah ke laci dan terkurung di dalamnya, dan Escape atau klik latar menutupnya lalu mengembalikan fokus ke tombol. Laci yang tertutup tidak bisa difokuskan. Dengan `prefers-reduced-motion`, laci muncul tanpa animasi. Bila layar melebar ke `lg`, laci otomatis menutup dan sidebar menjadi tetap.
- **Font Figtree** untuk semua teks, sama dengan landing (Bricolage Grotesque dihapus). **Judul halaman** (`x-dashboard.page-header`) huruf kapital, bobot 900, rapat (`tracking-tight`), berukuran sedang (24 sampai 32 px) supaya tidak menyaingi isi, tanpa hiasan. **Setiap halaman punya deskripsi singkat** satu kalimat di bawah judul (props `description`, teks di `lang/{en,id}` dengan kunci `description`, `create_description`, dan `edit_description`); halaman baru wajib mengisinya. Token warna sama dengan landing.
- **Perombakan gaya landing**: item menu aktif berupa blok krimson penuh bersudut 0,5 rem (tanpa penanda `::before`) dan item lain `white/70` yang menyala saat disorot. Dropdown bahasa dan pengguna membuka panel putih dengan animasi singkat (mati bila reduced motion). Kartu berbingkai tipis `ink/10`, sudut 0,5 rem, tanpa bayangan.
- **Komponen isi** (`resources/views/components/dashboard/`, gaya di `resources/css/dashboard.css`): isian dan pilihan memakai gaya formulir landing (`.field-input`, `.field-label`: label huruf kapital kecil, garis bawah yang memerah saat fokus, panah kustom pada pilihan); tabel (`.data-table`) berkepala huruf kapital kecil dengan garis `ink` tebal di bawahnya, baris lega yang menyala lembut saat disorot; aksi baris (`.row-action`) berupa tombol kecil berbingkai; badge huruf kapital (peran `ink`, aktif krimson, nonaktif abu-abu); pesan status berlatar `ink` dengan ikon centang; tombol sekunder berbingkai 2 px. Halaman Livewire (akun, artikel, produk, layanan) memakai komponen ini tanpa kelas gaya sendiri. **Kolom wajib** ditandai bintang merah (`*`, `brand-600`, disembunyikan dari pembaca layar) di labelnya: beri atribut `required` pada `x-dashboard.field` atau `x-dashboard.select`, atau tambahkan bintangnya sendiri pada label buatan sendiri (login, formulir Contact). Kolom yang wajibnya bersyarat (judul dan slug artikel hanya wajib untuk bahasa yang diisi, deskripsi meta dan isi hanya bila terbit) tidak diberi bintang. **Kolom kata sandi** (`x-dashboard.field type="password"`, dipakai kata sandi awal dan atur ulang di halaman akun, serta halaman login) punya tombol mata untuk melihat atau menyembunyikan kata sandi (`x-dashboard.password-toggle`, JS delegasi di `dashboard.js` yang menerapkan ulang keadaannya setelah Livewire merender ulang kolom).
- Kontras tiap pasangan baru dicek. Teks merah di atas latar `ink` tidak dipakai (label grup memakai putih). Item aktif di sidebar memakai putih di atas krimson.

## Alur

1. Pengguna masuk dan tiba di beranda dashboard areanya.
2. Pemilih bahasa (dropdown di dashboard) mengirim `POST /{area}/locale` dengan `locale` (`en` atau `id`). Nilai lain ditolak. Pengguna yang sudah masuk disimpan ke `users.locale`, dan semua pengunjung disimpan ke sesi.
3. Pengguna dikembalikan ke halaman sebelumnya, dan seluruh halaman kini berbahasa pilihan.

## Aturan keamanan dan kasus tepi

- Formulir bahasa dilindungi CSRF, dibatasi 60 per menit, dan hanya menerima locale dari `config('locales.dashboard')`.
- Pengalihan kembali hanya ke alamat dalam situs yang sama. Bila tidak ada halaman sebelumnya, ke beranda area (atau login area bila tamu).
- Menu dibangun dari daftar item yang tiap itemnya menyebut route dan peran yang boleh melihat. Item untuk route yang belum ada tidak dimasukkan. Menyembunyikan menu bukan pengamanan: akses tetap dijaga middleware dan Policy. Tiap item punya `group` (`menu`, `content`, atau `users`; "Master" tidak dipakai karena artikel, produk, dan layanan adalah konten landing, label di `dashboard.groups.*`) dan `icon` (nama untuk `x-dashboard.icon`, daftar jalurnya di komponen itu). `DashboardMenu::groupsForUser()` mengelompokkan item dan membuang grup kosong. Ikon baru: tambahkan jalurnya di `components/dashboard/icon.blade.php`.
- Semua teks lewat `lang/{en,id}/dashboard.php` dan `__()`. Kunci kedua bahasa harus sama.
- Halaman dashboard `noindex`, dan nama pengguna ditampilkan dengan escape bawaan Blade.

## Struktur kode

- `resources/views/components/dashboard/`: `layout.blade.php` (sidebar yang menjadi laci di ponsel, bilah atas, kontainer isi, tautan lewati-ke-konten), `page-header.blade.php`, `card.blade.php`, `language-dropdown.blade.php`, `user-menu.blade.php`, dan `locale-switcher.blade.php` (tombol berdampingan, dipakai di halaman login).
- `app/Services/Dashboard/DashboardMenu.php`: daftar item menu dan penyaringan per peran, satu-satunya tempat yang bertambah saat fitur baru jadi.
- `app/Http/Controllers/Dashboard/LocaleController.php` (invokable), `app/Http/Requests/Dashboard/UpdateLocaleRequest.php`, dan Action `app/Actions/Users/UpdateLocale.php`.
- Route `routes/app/locale.php` dan `routes/customer/locale.php` (dapat diakses tamu).
- `resources/views/dashboard/home.blade.php` menggantikan `auth/home.blade.php`. `HomeController` kedua area tetap, hanya mengganti view dan menyerahkan area.
- `resources/js/dashboard.js` (menutup dropdown dengan Escape dan klik di luar, serta membuka dan menutup laci sidebar dengan pengurungan fokus) didaftarkan di `vite.config.js`, dan `resources/css/dashboard.css` diperluas.
- `lang/{en,id}/dashboard.php`.

## Langkah dan test

Langkah: teks dan daftar menu, komponen tata letak, pemilih bahasa dan route, halaman beranda, penyesuaian halaman login, test.

Test (feature) yang harus ada:

- Beranda tiap peran menampilkan sapaan dengan nama, peran dalam bahasa aktif, dan keadaan kosong.
- Bilah atas memuat dropdown pengguna (inisial, email, peran, dan formulir Keluar), dropdown bahasa dengan kedua bahasa dan penanda aktif, dan isi berada dalam kontainer.
- Menu admin, staf, dan customer memuat hanya item yang diizinkan dan route yang ada. Item aktif bertanda `aria-current="page"`.
- Pemilih bahasa: mengganti ke `id` menyimpan `users.locale` dan sesi untuk pengguna masuk, hanya sesi untuk tamu, menolak `zh` dan nilai tak dikenal, dan mengarahkan kembali.
- Halaman dashboard memakai `<html lang>` sesuai locale, dan teks berganti setelah bahasa diubah.
- Halaman login menampilkan pemilih bahasa dan teksnya ikut berubah untuk tamu. Bahasa yang dipilih sebelum masuk disimpan ke akun, dan masuk tanpa pilihan mempertahankan bahasa tersimpan.
- Kunci `lang/{en,id}/dashboard.php` identik.
- Unit: `DashboardMenu` (penyaringan per peran, item tanpa route diabaikan).

Pemeriksaan manual di browser: sidebar dan bilah atas di desktop, laci sidebar di ponsel (animasi, tutup lewat klik latar dan Escape, fokus), ganti bahasa, keluar, dan keyboard (Tab, Escape).

## Halaman galat

Galat di `/app/*` dan `/customer/*` memakai halaman galat bersama dalam `en` atau `id`. Rancangannya di bagian "Halaman galat" pada [`../../landing/ui/design.md`](../../landing/ui/design.md).

## Ditunda

- Livewire (A1) dan komponen formulir dan tabel.
- Mode gelap, notifikasi, jejak halaman (breadcrumb), pencarian, profil pengguna, dan ganti kata sandi sendiri.
- Isi beranda yang bermakna, menyusul bersama fitur pertama peran masing-masing.
