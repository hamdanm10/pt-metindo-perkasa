# Desain Landing Page

Tampilan landing page publik PT Metindo Perkasa (electroplating dan metal finishing, pemasok bahan kimia plating) dalam lima bahasa. Fondasi warna, tipografi, dan RTL ada di [`../../shared/design-system/overview.md`](../../shared/design-system/overview.md). Isi dan susunan halaman mengikuti situs prototipe, dan prompt aset vektor ada di [`recraft-prompts.md`](recraft-prompts.md).

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | Susunan beranda mengikuti prototipe `homepage-2` (hero, pengantar video, katalog 13 kategori, layanan, klien dan mitra, artikel, ajakan menghubungi), tetapi **gayanya mengikuti newengen.com**: hero gelap dengan judul raksasa, bagian terang dan gelap berselang-seling, kartu bergaris tipis, tombol berpanah | Permintaan client untuk redesign (Nov 2026). Fase dan status di [`../implementation-plan.md`](../implementation-plan.md) |
| 2 | Palet tetap: `ink` `#1B1F3B` sebagai permukaan gelap (pengganti hijau tua newengen), `lavender` `#F3F2FF` sebagai permukaan terang (pengganti krem), krimson `#D6283A` untuk tombol dan blok ajakan | Permintaan client: palet yang sama |
| 3 | Judul memakai **Figtree 900 huruf kapital**, rapat (`tracking-tight`), ukuran fluid sampai sekitar 7 rem. Bricolage Grotesque tidak lagi dipakai di landing (masih di dashboard). Huruf kapital hanya berpengaruh pada Latin | Meniru judul newengen. Font Mandarin, Arab, dan Jepang tetap seperti di sistem desain |
| 4 | **Kontainer meniru situs referensi** (diukur langsung): selebar layar dengan padding samping sekitar 3,33vw (48 px di 1440, 64 px di 1920, 12 px di ponsel), maksimal 1920 px. Kelas `.container-site`, bukan `max-w-7xl` yang berhenti di 1280 px. Jarak vertikal antarbagian `.section-y`: 90 px di ponsel sampai sekitar 150 px (10,4vw) di desktop. Judul besar ikut lebar layar (`clamp` dengan `vw`) | Permintaan client: tampilan harus mirip referensi, termasuk lebar isi |
| 5 | Sudut membulat kecil (`rounded-lg`), tanpa potongan miring, blok asimetris, atau latar berbentuk organik | Gaya newengen yang bersih. Bentuk miring lama dihapus |
| 6 | **Tidak memakai ilustrasi Recraft yang lama.** Hero memakai monogram logo besar sebagai gambar. Bagian lain murni tipografi, angka, dan kartu. Ilustrasi atau foto baru diminta ke client bila memang dibutuhkan ([`direction.md`](direction.md)) | Permintaan client: jangan memaksakan ilustrasi lama |
| 7 | Konten dirender di server dengan Blade | SEO |
| 8 | Gerak mengikuti referensi (judul tumbuh per huruf, gulir halus Lenis, header yang menyingkir, akordeon layanan), dijelaskan di [`motion.md`](motion.md) | Permintaan client. Sebelumnya sempat disederhanakan, lalu diminta disamakan dengan referensi |
| 9 | Layar intro dipertahankan dalam bentuk sederhana (logo dan tombol memudar masuk dan keluar) | Permintaan client |
| 10 | Fitur newengen yang tidak punya padanan (spanduk promo, testimoni, buletin, logo klien) **dilewati** | Belum ada data atau fitur |

## Logo dan favicon

- Logo: monogram "MP" garis (`docs/references/image.svg`, tanpa tulisan nama). Dipotong rapat dan dibuat dalam dua warna: `public/images/brand/logo.svg` (krimson `#D6283A`, untuk latar terang) dan `logo-white.svg` (untuk latar gelap). Satu SVG cukup untuk semua ukuran. Nama perusahaan ada di `alt` dan teks halaman, bukan di gambar. Berkas aslinya tidak diubah (warnanya `#E11E2B`).
- Favicon: petak krimson dengan monogram putih yang ditebalkan (garis tipis tidak terbaca di 16 piksel). `public/favicon.svg` dipakai peramban modern, `favicon.ico` (16, 32, 48) sebagai cadangan, dan `apple-touch-icon.png` (180). Ketiganya dibuat dari logo yang sama dan terpasang di semua layout (landing, dashboard, login, halaman galat).
- Gambar berbagi Open Graph (1200x630) dibuat bersama L6.

## Layar intro

Aturan supaya layar intro tidak merugikan SEO dan pengunjung:

- Tampil **sekali per sesi browser** (`sessionStorage`) dan hanya di beranda.
- Konten beranda **sudah ada di HTML** di belakang overlay, bukan dimuat setelah overlay ditutup.
- Tersembunyi sampai JavaScript aktif, jadi tanpa JavaScript halaman langsung terlihat.
- Bisa ditutup dengan klik, Enter, atau Escape. Fokus dipindahkan dengan benar. Latar `ink`, logo putih, dan tombol krimson.
- Dengan `prefers-reduced-motion`, overlay tidak beranimasi.
- Setelah jadi, ukur LCP dan INP dengan Lighthouse, dengan dan tanpa overlay (di L6). Jika skornya turun jauh, bahas penggantinya dengan client.

## Hero

- Latar `ink`, dua kolom di `lg`: judul besar (`font-black`, huruf kapital, `clamp(3rem, 8.2vw, 7rem)`), paragraf, dan dua tombol (krimson berpanah, dan garis putih). Di kanan, panel lavender berisi monogram logo besar yang terpotong tepinya, sebagai pengganti foto.

## Navbar

- **Menempel di atas** (`position: sticky`), latar `ink` solid, logo putih di kiri. Menyingkir saat gulir ke bawah dan kembali saat gulir ke atas ([`motion.md`](motion.md)). Garis tipis muncul di bawahnya setelah halaman mulai bergulir.
- Tautan putih, yang aktif bergaris bawah merah muda. Tombol "Let's talk" krimson berpanah (ke halaman Contact) menggantikan tautan Contact di menu desktop. Contact tetap ada di menu ponsel dan footer.
- Pemilih bahasa: teks bahasa aktif dengan daftar turun putih. Di bawah `lg`, tombol "Menu" krimson membuka panel putih berisi menu dan bahasa.
- Jangkar (`#catalog`) tidak tertutup header: `scroll-padding-top` pada `html`. Layar intro (z 80) berada di atas header (z 50), dan header ikut `inert` selama intro.

## Bagian beranda

| Bagian | Latar | Isi |
| --- | --- | --- |
| Pengantar video | Putih | Hanya tampil bila `landing.intro_video_id` terisi. Dua kolom di desktop (judul dan paragraf 5/12 di kiri, kotak video 16:9 7/12 di kanan, sekitar 770 px di layar 1440), bertumpuk di ponsel. Jarak vertikal lebih rapat dari bagian lain. Kotak video berisi thumbnail dengan gradasi gelap di bawah, lingkaran putar putih di tengah, label "Watch intro video" terlihat. Hover memperbesar thumbnail sedikit (mati dengan reduced motion) |
| Katalog | Lavender | Kalimat besar, lalu kartu 13 kategori (4 kolom di desktop): nomor, nama, panah. Hover membalik ke `ink` |
| Layanan | `ink` | Akordeon 01 sampai 03: judul dan ikon plus, panel berisi deskripsi dan tombol "View all" |
| Klien dan mitra | Lavender | Sektor dan mitra sebagai kotak bergaris tipis |
| Artikel | Putih | Tiga artikel terbaru (gambar, jenis, tanggal, judul), hanya bila ada |
| Ajakan | Krimson | Judul kapital besar dan tombol putih |
| Footer | `ink` | Logo, deskripsi, menu, kontak |

Halaman dalam memakai kepala halaman `ink` dengan judul kapital besar (seragam dengan header), lalu isi terang. Kontras diperiksa: teks putih di atas krimson 4,97, di atas `ink` 16:1. Angka krimson di atas `ink` hanya untuk teks besar, dan memakai `brand-400` bila kecil.

## Halaman galat

Halaman galat 403, 404, 419, 429, 500, dan 503 memakai satu komponen (`components/error-page`) dan view tipis di `resources/views/errors/`. Area dan bahasa dibaca dari URL oleh `App\Services\Errors\ErrorContext`, karena 404 tidak melewati middleware locale.

- URL berawalan locale landing: tampilan landing dalam bahasa itu (Arab dengan `dir="rtl"`), tombol kembali ke `/{locale}`.
- URL `/app/*` dan `/customer/*`: tampilan dashboard dalam `en` atau `id` (pengguna, sesi, lalu `en`), tombol kembali ke dashboard area itu.
- URL lain: landing berbahasa `en`.
- Galat 5xx tidak menyentuh sesi atau database, jadi memakai `en`. Pesan galat dan jejak tidak pernah ditampilkan. Halaman diberi `noindex`.
- Teks di `lang/<locale>/errors.php` (lima bahasa). Tidak ada tombol "coba lagi" pada 503.

## Aturan

- Teks tidak ditanam di dalam gambar atau SVG.
- Setiap bagian diuji dalam kelima bahasa, termasuk Arab (RTL) dan teks panjang bahasa Indonesia.
- Gambar besar memakai format modern, ukuran responsif, dan `loading="lazy"` kecuali yang terlihat pertama.
- Merah boleh menjadi latar luas hanya untuk teks besar atau tebal (blok ajakan). Teks kecil di atas krimson memakai putih tebal (4,97).

## Ditunda

- Konten database dan struktur data kategori, produk, dan proses.
- Foto dan ilustrasi baru (daftar kebutuhan di [`direction.md`](direction.md)).
- Mode gelap.
