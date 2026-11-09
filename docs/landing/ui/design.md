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
| 8 | Gerak mengikuti referensi (judul tumbuh per huruf, gulir halus Lenis, header yang menyingkir, layanan yang terbuka mengikuti gulir), dijelaskan di [`motion.md`](motion.md) | Permintaan client. Sebelumnya sempat disederhanakan, lalu diminta disamakan dengan referensi |
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
| Pengantar video | Putih | Hanya tampil bila `landing.intro_video_id` terisi. Dua kolom di desktop (judul dan paragraf 5/12 di kiri, kotak video 16:9 7/12 di kanan, sekitar 770 px di layar 1440), bertumpuk di ponsel. Jarak vertikal lebih rapat dari bagian lain. Kotak video berisi thumbnail dengan gradasi gelap di bawah, lingkaran putar putih di tengah, label "Watch intro video" terlihat. Hover memperbesar thumbnail sedikit dan memberi halo pada lingkaran putar (mati dengan reduced motion). Animasinya (kotak membuka, thumbnail bergeser, tombol memantul, transisi klik) di [`motion.md`](motion.md) |
| Controlled process | Lavender | **Satu section.** Blok atas: judul dan deskripsi di kiri, dan di kanan **tumpukan empat kartu seukuran** seperti hero referensi (tiap kartu 59 persen lebar panel, bergeser 13,7 persen satu sama lain, panel persegi setinggi sekitar 68 persen layar). Kartu depan berganti sendiri tiap 4 detik. **Tanpa label**: hanya kartu. Di bawahnya baris empat kartu bernomor dengan nama dan dua proses, lalu tombol "View all processes". Setelah blok atas digulir lewat, kartu dari tumpukan menyebar dan turun ke baris itu ([`motion.md`](motion.md)). Di ponsel tanpa tumpukan dan animasi, kisi 2x2. Di RTL susunan dicerminkan |
| Layanan | `ink` | **Disamakan dengan section Services referensi.** Di atas daftar: label kecil "SERVICES" (huruf kapital 11 px, berat 800) dan kalimat deskripsi besar (sekitar 78 px di 1440, berat 700, rapat). Lalu tiga baris layanan dengan garis tipis di atasnya dan judul 30 px berat 700 (tinggi baris 90 px). Satu layanan terbuka pada satu waktu: deskripsi 17,6 px dan tombol "Learn more" krimson di kiri (55 persen), **gambar ilustrasi statis** 16:9 di kanan (32 persen) dalam panel membulat (placeholder `service-<nama>` sampai gambar Recraft siap). Tinggi tiap layanan **mengikuti isinya** (di referensi ada ruang kosong di bawahnya, di sini tidak). Daftar menempel di layar dan gulir membuka layanan satu per satu ([`motion.md`](motion.md)). Di ponsel semua layanan terbuka dan diam. Yang berbeda dari referensi: kanan memakai gambar diam (referensi memakai animasi), dan tanpa penanda (tag) karena belum ada datanya |
| Klien dan mitra | Lavender | **Disamakan dengan section Clients referensi.** Label kecil "CLIENTS". Baris pertama judul: tulisan "OUR" dengan **chip krimson** di sampingnya yang berganti **hanya antara "Clients" dan "Partners"** (tanpa sektor). Baris kedua: "CLIENTS & PARTNERS" (huruf kapital 9vw, berat 900). Di bawahnya grid logo 3x3 (kotak bergaris tipis, rasio 218:124, sudut membulat) dan di kanan kartu testimoni, dengan panah kiri-kanan yang muncul saat hover. Dua slide: Clients (logo klien) dan Partners (logo atau nama dalam teks), **berganti sendiri tiap 5 detik** ([`motion.md`](motion.md)). Logo klien belum ada: placeholder berlabel `[LOGO] client` hanya tampil saat `LANDING_SAMPLE_CONTENT` aktif, dan tanpa logo slide Clients tidak tampil di production. Kartu testimoni hanya placeholder dan hanya di mode contoh, karena belum ada kutipan asli dan tidak boleh dikarang (tanpanya grid melebar ke lima kolom). Kata "OUR" diterjemahkan: id "Kami" di belakang chip, ar memakai kata berpemilik di dalam chip, zh dan ja di depan chip |
| Galeri | `ink` (foto), lavender (keterangan) | **Mengikuti blok foto layar penuh di bawah Clients pada referensi.** Foto setinggi layar (`100svh`) per lokasi perusahaan (anak perusahaan), dan di bawahnya satu baris tiga kolom huruf kapital 11 px berat 800: kota (kiri), nama perusahaan (tengah), dan **jam lokal sungguhan** (kanan, dihitung dari zona waktu lokasi dan diperbarui tiap 20 detik; referensi juga menampilkan suhu lewat API, di sini tidak dipakai). Foto berganti sendiri tiap 3 detik ([`motion.md`](motion.md)). Foto dari `public/images/landing/gallery/<kunci>` (placeholder berlabel di mode contoh). Lokasi memakai data anak perusahaan, yang masih contoh, jadi galeri tidak tampil di production sampai ada data dan foto asli |
| Artikel dan berita | Putih | **Disamakan dengan section "Insights" referensi.** Judul besar di tengah ("Articles & news", huruf kapital 9vw, berat 900, hurufnya tumbuh seperti judul lain) dan satu kalimat pengantar 17,6 px di bawahnya. Tiga artikel terbaru yang terbit di bahasa itu dalam tiga kolom (jarak 3,6vw): gambar rasio 1:0,575 bersudut membulat, lalu baris penanda jenis (lingkaran garis tipis "Article" atau "News") dengan tanggal `dd.mm.yy`, lalu judul 17,6 px berat normal. Tombol "View all" krimson di tengah dengan efek hover: latar `ink` naik dari bawah dan label menggulir ke atas (`.btn-roll`). Kartu tidak punya animasi masuk atau hover gambar, sama seperti referensi. Tidak tampil bila bahasa itu belum punya artikel terbit |
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
