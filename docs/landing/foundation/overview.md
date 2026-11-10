# Kerangka Landing (L1)

Fondasi landing page publik: layout, navigasi, footer, penanganan locale lewat awalan URL untuk lima bahasa (`en`, `id`, `zh`, `ar`, `ja`), dukungan RTL untuk Arab, dan token tema serta font. Belum ada konten beranda sungguhan (L2) dan belum ada database. Fase dan statusnya di [`../implementation-plan.md`](../implementation-plan.md). Fondasi visual di [`../../shared/design-system/overview.md`](../../shared/design-system/overview.md).

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | Alamat landing `/{locale}/...` dengan `locale` salah satu dari lima kode. Nilai lain menghasilkan 404 | SEO dan tautan yang bisa dibagikan |
| 2 | `/` mengarah ke `/en` (redirect 302) | Locale bawaan `en`, tanpa deteksi bahasa browser. 302 karena bawaan bisa berubah |
| 3 | Locale hanya dari URL, tanpa cookie atau sesi di landing | Satu alamat satu bahasa, mudah diuji dan di-cache |
| 4 | Daftar locale, nama bahasa, dan arah tulis ada di satu berkas `config/locales.php` | Tidak ditulis ulang di tempat lain. Dashboard memakai berkas yang sama dengan daftar sendiri |
| 5 | Mandarin dianggap **Mandarin Sederhana**: URL `/zh`, atribut `lang="zh-Hans"`, font Noto Sans SC | Asumsi, perlu dikonfirmasi client |
| 6 | Menu hanya menampilkan halaman yang sudah ada (kini tanpa Home, karena logo menuju beranda). Menu lain muncul saat fasenya selesai | Tidak ada tautan mati |
| 7 | Teks navigasi dan footer di `lang/<locale>/landing.php` untuk kelima bahasa | Aturan teks pengguna di `CLAUDE.md`. Dipindah ke database hanya untuk konten dinamis (L4) |
| 8 | Token tema di satu berkas CSS yang dipakai bersama, landing punya entri CSS sendiri | Dashboard nanti memakai token yang sama tanpa menyalin |
| 9 | Font dipasang sendiri lewat paket npm `@fontsource-variable/*` (Figtree, Bricolage Grotesque untuk judul dashboard, Noto Sans SC, JP, Arabic), satu berkas CSS per bahasa di `resources/css/fonts/` yang hanya dimuat di locale terkait | Tanpa CDN pihak ketiga. Versi variabel dipilih karena versi bobot tetap (400 dan 700) membuat CSS Mandarin dan Jepang sekitar 70 KB gzip per bahasa, sedangkan versi variabel sekitar 34 KB |
| 10 | Navigasi desktop menampilkan menu dan lima bahasa sebaris. Di ponsel, tombol hamburger membuka panel layar penuh (`#mobile-menu`) yang bisa ditutup dengan tombol, Escape, atau memilih tautan (`resources/js/landing/mobile-menu.js`); pemilih bahasa desktop tetap `<details>` | Mengikuti referensi; menu ponsel butuh JavaScript, tautan yang sama tetap ada di footer |
| 11 | Logo, logo putih, dan favicon dari client ada di `public/images/brand/` dan `public/favicon.ico` | Aset resmi dari client, dipakai apa adanya |

## Alur

1. Pengunjung membuka `/` lalu diarahkan ke `/en`. Membuka `/ar` langsung menampilkan versi Arab.
2. Middleware landing membaca parameter `locale`, memastikan ada di `config('locales.landing')`, lalu memanggil `app()->setLocale()`. Middleware juga mengisi `URL::defaults(['locale' => ...])` supaya `route()` selalu membawa locale aktif.
3. Layout menulis `<html lang dir>` dari locale aktif (`dir="rtl"` hanya untuk `ar`), tag `<link rel="alternate" hreflang>` untuk lima bahasa ditambah `x-default` yang menunjuk `en`, dan `canonical` ke alamat locale aktif.
4. Pemilih bahasa menampilkan nama tiap bahasa dalam bahasanya sendiri (English, Bahasa Indonesia, 中文, العربية, 日本語) dan menautkan ke **halaman yang sama** di locale lain, bukan ke beranda.

## Aturan keamanan dan kasus tepi

- `locale` divalidasi dengan batasan route dan daftar di config. Nilainya tidak pernah dipakai langsung untuk membentuk jalur berkas atau query.
- Locale tidak dikenal: 404, bukan jatuh diam-diam ke `en`.
- Teks yang belum diterjemahkan di salah satu bahasa: dicegah oleh test yang membandingkan kunci seluruh `lang/*/landing.php`. Tidak ada cadangan diam-diam untuk teks antarmuka tetap.
- Semua properti tata letak memakai properti logis (`ms-*`, `me-*`, `start-*`, `text-start`) supaya RTL berbalik otomatis. Ikon berarah memakai varian `rtl:`.
- Hanya font untuk locale aktif yang dimuat. Font CJK dan Arab tidak dimuat di halaman Inggris.
- Layout punya tautan "lewati ke konten" dan fokus yang terlihat.

## Struktur kode

- `config/locales.php`: daftar locale landing dan dashboard, bawaan, nama asli, arah tulis.
- `app/Http/Middleware/SetLandingLocale.php`: mengatur locale dari URL.
- `routes/landing/home.php` (dimuat dengan grup `/{locale}`, nama `landing.*`) dan pengalihan `/`. Pemuatan folder `routes/landing` diatur di `bootstrap/app.php`.
- `app/Http/Controllers/Landing/HomeController.php`: satu aksi yang mengembalikan view. Tanpa Action karena belum ada logika bisnis.
- `resources/views/landing/layouts/app.blade.php`, komponen Blade untuk header, footer, dan pemilih bahasa di `resources/views/components/landing/`, dan `resources/views/landing/home.blade.php` (halaman rangka).
- `lang/{en,id,zh,ar,ja}/landing.php`.
- `resources/css/theme.css` (token warna dan font) dan `resources/css/landing.css` (entri landing yang mengimpor tema, memilih keluarga font per atribut `lang`, dan menyetel jarak baris untuk Mandarin, Jepang, dan Arab).
- `resources/css/fonts/{latin,zh,ja,ar}.css` dan `resources/js/landing.js`, didaftarkan di `vite.config.js`.
- `public/images/brand/` (logo) dan `public/favicon.ico`.
- `tests/Feature/Landing/LocaleTest.php`.
- Berkas bawaan `welcome.blade.php`, `tests/Feature/ExampleTest.php`, dan route `/` lama dihapus.

## Langkah dan test

Langkah: config locale, middleware, route dan pengalihan, lang file, token tema dan font, layout dan komponen, halaman rangka, test.

Test (feature) yang harus ada:

- `/` mengarah ke `/en`.
- Tiap locale mengembalikan 200 dengan `lang` yang benar, dan `dir="rtl"` hanya pada `ar`.
- Locale tidak dikenal mengembalikan 404.
- Tag `hreflang` untuk lima bahasa dan `x-default` ada, begitu juga `canonical`.
- Pemilih bahasa menautkan ke halaman yang sama di locale lain.
- Kunci di `lang/*/landing.php` identik di kelima bahasa.
- Teks navigasi muncul dalam bahasa yang sesuai.

Pemeriksaan manual: tampilan di lebar ponsel dan desktop, Arab dalam RTL, font CJK dan Arab termuat hanya di locale-nya.

## Ditunda

- Konten beranda dan layar intro (L2).
- Halaman dalam dan menu lengkap (L3).
- Judul dan deskripsi meta per halaman, sitemap, dan pengukuran performa (L6).
- Terjemahan kelima bahasa untuk teks navigasi dan footer perlu ditinjau penutur asli sebelum rilis.
- Pemilih bahasa menampilkan nama bahasa dalam aksara aslinya. Di halaman yang tidak memuat font bahasa itu (misalnya `中文` di halaman Inggris), tampilannya mengandalkan font sistem pengunjung.
- Tampilan font web di browser nyata belum diperiksa: pemeriksaan visual memakai Firefox tanpa kepala dengan berkas lokal, dan font tidak termuat di sana. Struktur CSS (nama keluarga font, berkas woff2, jalur) sudah dicek, jadi perlu dicek ulang di server atau browser biasa.
- Menu ponsel (hamburger dan panel layar penuh) diuji di Firefox tanpa kepala (buka, tutup, Escape, tautan); belum diuji di perangkat sentuh sungguhan. Tautan lewati-ke-konten diuji lewat kode dan test.
- Mode gelap.
