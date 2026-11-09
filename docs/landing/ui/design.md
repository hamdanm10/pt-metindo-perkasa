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

- Logo: monogram "MP" garis (tanpa tulisan nama). Dipotong rapat dan dibuat dalam dua warna: `public/images/brand/logo.svg` (krimson `#D6283A`, untuk latar terang) dan `logo-white.svg` (untuk latar gelap). Satu SVG cukup untuk semua ukuran. Nama perusahaan ada di `alt` dan teks halaman, bukan di gambar. Berkas aslinya tidak diubah (warnanya `#E11E2B`).
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

- **Menempel di atas** (`position: sticky`), tinggi tetap 4,8vw (69 px di 1440 px) sehingga halaman tidak melompat. Logo di kiri: monogram putih dan tulisan **METINDO PERKASA** (teks HTML, huruf kapital berjarak). Di puncak halaman berupa bilah `ink` solid. **Setelah halaman bergulir** (`data-stuck`) bilah menjadi transparan dan tersisa dua ubin `ink` setinggi 44 px (sama dengan tombol) seperti referensi: ubin logo (tulisan melipat hilang, monogram mengecil) dan ubin menu (tautan dan bahasa), dengan tombol \"Let's talk\" di sampingnya. Semua peralihan 0,75 detik dengan kurva `cubic-bezier(0.19, 1, 0.22, 1)` milik referensi. Header menyingkir saat gulir ke bawah **setelah melewati seksi pertama (hero)** dan kembali saat gulir ke atas ([`motion.md`](motion.md)).
- **Hover tautan menu:** garis bawah setebal 0,05em tumbuh dari kiri dan keluar ke kanan (0,75 detik, kurva yang sama), warna teks tidak berubah. Tautan halaman aktif tetap bergaris (kelas `.uline`). Ukuran huruf 1vw (14,4 px), jarak antar tautan 2,78vw.
- Tautan putih, yang aktif bergaris bawah. Tombol "Let's talk" krimson berpanah (ke halaman Contact) menggantikan tautan Contact di menu desktop. Contact tetap ada di menu ponsel dan footer.
- **Menu tanpa item Home** (logo sudah menuju beranda; Home tetap ada di footer). Ukuran dari referensi di 1440 px: huruf menu 0,889vw (12,8 px, berat 400), jarak antar tautan 2,22vw, padding ubin menu 1,94vw, tinggi ubin 44 px. Tulisan **METINDO PERKASA** di logo: huruf kapital (berat 600, 1,9vw, jarak huruf 0,03em), tingginya mengikuti wordmark referensi.
- **Bagian pertama halaman menyambung ke bawah header** (margin negatif setinggi header), sehingga saat header menjadi transparan tidak ada latar putih di atas hero.
- Pemilih bahasa: **kode singkat** (EN, ID, ZH, AR, JA) di tombol dan daftar turun putih; nama lengkap ada di `title` dan `aria-label`. Footer tetap memakai nama lengkap. Di bawah `lg`, tombol "Menu" krimson membuka panel putih berisi menu dan bahasa.
- Jangkar (`#catalog`) tidak tertutup header: `scroll-padding-top` pada `html`. Layar intro (z 80) berada di atas header (z 50), dan header ikut `inert` selama intro.

## Bagian beranda

| Bagian | Latar | Isi |
| --- | --- | --- |
| Pengantar video | Putih | Hanya tampil bila `landing.intro_video_id` terisi. Dua kolom di desktop (judul dan paragraf 5/12 di kiri, kotak video 16:9 7/12 di kanan, sekitar 770 px di layar 1440), bertumpuk di ponsel. Jarak vertikal lebih rapat dari bagian lain. Kotak video berisi thumbnail dengan gradasi gelap di bawah, lingkaran putar putih di tengah, label "Watch intro video" terlihat. Hover memperbesar thumbnail sedikit dan memberi halo pada lingkaran putar (mati dengan reduced motion). Animasinya (kotak membuka, thumbnail bergeser, tombol memantul, transisi klik) di [`motion.md`](motion.md) |
| Controlled process | Lavender | **Satu section.** Blok atas: judul dan deskripsi di kiri, dan di kanan **tumpukan empat kartu seukuran** seperti hero referensi (tiap kartu 59 persen lebar panel, bergeser 13,7 persen satu sama lain, panel persegi setinggi sekitar 68 persen layar). Kartu depan berganti sendiri tiap 4 detik. **Tanpa label**: hanya kartu. Di bawahnya baris empat kartu bernomor dengan nama dan dua proses, lalu tombol "View all processes". Setelah blok atas digulir lewat, kartu dari tumpukan menyebar dan turun ke baris itu ([`motion.md`](motion.md)). Di ponsel tanpa tumpukan dan animasi, kisi 2x2. Di RTL susunan dicerminkan |
| Layanan | `ink` | **Disamakan dengan section Services referensi.** Di atas daftar: label kecil "SERVICES" (huruf kapital 11 px, berat 800) dan kalimat deskripsi besar (sekitar 78 px di 1440, berat 700, rapat). Lalu tiga baris layanan dengan garis tipis di atasnya dan judul 30 px berat 700 (tinggi baris 90 px). Satu layanan terbuka pada satu waktu: deskripsi 17,6 px dan tombol "Learn more" krimson di kiri (55 persen), **gambar ilustrasi statis** 16:9 di kanan (32 persen) dalam panel membulat (placeholder `service-<nama>` sampai gambar Recraft siap). Tinggi tiap layanan **mengikuti isinya** (di referensi ada ruang kosong di bawahnya, di sini tidak). Daftar menempel di layar dan gulir membuka layanan satu per satu ([`motion.md`](motion.md)). Di ponsel semua layanan terbuka dan diam. Yang berbeda dari referensi: kanan memakai gambar diam (referensi memakai animasi), dan tanpa penanda (tag) karena belum ada datanya |
| Klien dan mitra | Lavender | **Disamakan dengan section Clients referensi.** Label kecil "CLIENTS". Baris pertama judul: tulisan "OUR" dengan **chip krimson** di sampingnya yang berganti **hanya antara "Clients" dan "Partners"** (tanpa sektor). Baris kedua: "CLIENTS & PARTNERS" (huruf kapital 9vw, berat 900). Di bawahnya grid logo 3x3 (kotak bergaris tipis, rasio 218:124, sudut membulat) dan di kanan kartu testimoni, dengan panah kiri-kanan yang muncul saat hover. Dua slide: Clients (logo klien) dan Partners (logo atau nama dalam teks), **berganti sendiri tiap 5 detik** ([`motion.md`](motion.md)). **Ukuran dari referensi (1440 px):** kotak logo 218x124 (bergaris 1 px `ink`, sudut 8 px), logo berada di kotak 50 persen lebar kotak dengan rasio 3:2 (108x72, `object-contain`), jarak antar kotak dan ke kartu 2,5vw. Kartu testimoni: lebar 2/5 grid, padding 2,78vw x 2,22vw, sudut 8 px, tanda kutip miring di kiri atas dan kanan bawah, kutipan 1,11vw (garis 1,42), lalu nama (tebal) dan jabatan 0,83vw. Logo dibaca dari `public/images/landing/clients` dan `partners` (kini contoh, lihat [`../home/overview.md`](../home/overview.md)); tanpa logo klien, placeholder berlabel `[LOGO] client` hanya tampil saat `LANDING_SAMPLE_CONTENT` aktif, dan tanpa logo slide Clients tidak tampil di production. **Ada dua testimoni, satu per slide** (klien dan mitra) dan berganti bersama slide (memudar). Di kanan bawah tiap kartu ada **logo perusahaan** (tinggi 2,22vw); contohnya satu berkas saja, `clients/client-3.png`. Isi kutipan, nama, dan jabatan adalah **teks contoh** (atas izin client, bebas dituliskan) dan hanya tampil di mode contoh (tanpanya grid melebar ke lima kolom). Kutipan, nama, jabatan, dan logo asli menyusul bersama pengelolaan konten landing (A5). Kata "OUR" diterjemahkan: id "Kami" di belakang chip, ar memakai kata berpemilik di dalam chip, zh dan ja di depan chip |
| Galeri | `ink` (foto), lavender (keterangan) | **Mengikuti blok foto layar penuh di bawah Clients pada referensi.** Foto setinggi layar (`100svh`) dengan `object-cover`: **ukuran mengikuti seksi, bukan ukuran foto**, jadi foto dipotong dari tengah (subjek utama harus di tengah). Di bawahnya satu baris tiga kolom huruf kapital 11 px berat 800: label (kiri), keterangan foto (tengah), dan penghitung `01 / 03` (kanan). Label dan keterangan **mengikuti isi foto** dan ada di lima bahasa (`home.gallery.slides.<kunci>`). Foto berganti sendiri tiap 3 detik ([`motion.md`](motion.md)). Berkas: `public/images/landing/photos/gallery-<kunci>.webp` dengan kunci `celebration`, `teamwork`, `togetherness` (placeholder berlabel di mode contoh; slide tanpa foto dilewati di production). Foto sekarang adalah foto tim dari client, bukan foto lokasi. Referensi menampilkan kota, perusahaan, dan jam lokal; di sini tidak dipakai karena foto bukan foto lokasi |
| Artikel dan berita | Putih | **Disamakan dengan section "Insights" referensi.** Judul besar di tengah ("Articles & news", huruf kapital 9vw, berat 900, hurufnya tumbuh seperti judul lain) dan satu kalimat pengantar 17,6 px di bawahnya. Tiga artikel terbaru yang terbit di bahasa itu dalam tiga kolom (jarak 3,6vw): gambar rasio 1:0,575 bersudut membulat, lalu baris penanda jenis (lingkaran garis tipis "Article" atau "News", huruf 0,78vw, tinggi 1,875vw, padding 0,67vw) dengan tanggal (0,83vw, warna `ink`; ukuran mengikuti lebar layar seperti referensi) `dd.mm.yy`, lalu judul 17,6 px berat normal. Tombol "View all" memakai komponen tombol bersama (di bawah). Kartu tidak punya animasi masuk atau hover gambar, sama seperti referensi. **Hover kartu:** judulnya mendapat sorotan satu baris (`brand-200`) dan garis bawah tipis yang tumbuh dari awal teks sampai ujung (`.title-hover`). Bila bahasa itu belum punya artikel terbit, di mode contoh (`LANDING_SAMPLE_CONTENT`) tampil **tiga kartu statis** (gambar placeholder berlabel, penanda jenis, tanggal hari ini, judul `[SAMPLE] Article title`) tanpa tautan, dengan tombol "View all" yang tetap tampil (dan punya efek hover) tetapi tidak bisa diklik, supaya tata letak dan efeknya bisa dinilai. Di production section tersembunyi sampai ada artikel terbit |
| Tentang perusahaan | Lavender | **Disamakan dengan section About referensi (di bawah Insights).** Label kecil "ABOUT" (11 px, berat 800). Judul tiga baris besar (9,06vw, berat 900, huruf kapital, garis 0,9) yang ditulis sendiri per bahasa (`home.about.lines`): baris 1 di kiri dengan grafik di kanan, baris 2 rata kanan dengan grafik di kiri, baris 3 di kiri dengan grafik di kanan. Grafik berukuran 3,69em x 0,705em (481x92 px di 1440 px), sudut 8 px, berupa tiga gambar vektor sederhana berpalet kita (batang tiga warna, grafik area biru, pola segi enam krimson); **masih gambar diam**, referensi menganimasikannya dan itu menunggu berkas animasi. Di bawah judul dua kolom (1fr dan 46,1%): teks 1,11vw (lebar maksimum 38,9vw) dengan tombol "About Us" ke halaman About, dan di kanan setumpuk foto persegi (kartu sama ukuran, tiap kartu bergeser 13,7 persen, lebar kartu menyesuaikan jumlah foto: 58,9 persen untuk empat) yang memakai foto galeri. Di ponsel grafik disembunyikan dan kolom menumpuk |
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

## Tombol

**Satu komponen untuk semua tombol landing:** `<x-landing.button>` (`resources/views/components/landing/button.blade.php`). Jangan menulis tombol dengan kelas sendiri. Ukurannya mengikuti tombol referensi dan ikut lebar layar: tinggi 44 px di 1440 (`clamp(2.75rem, 3.06vw, 4.25rem)`), sudut 8 px, label tebal 14,4 px (`1vw`), jarak isi 12 px, jarak label ke panah 16 px, panah tebal 16 px. Animasinya di [`motion.md`](motion.md).

| Props | Isi |
| --- | --- |
| `href` | Alamat. Bila kosong tombol menjadi `span` biasa (dipakai untuk kartu contoh yang belum punya halaman) |
| `variant` | `primary` (krimson, pengisi indigo supaya terlihat di atas latar gelap maupun terang), `light` (putih untuk latar krimson, pengisi `ink`), `outline` (garis putih untuk latar gelap, pengisi putih), `dark` (`ink`, pengisi krimson) |
| `arrow` | `true` bawaan, `false` untuk tombol tanpa panah (menu ponsel dan "Contact us" di hero) |
| `as` | `a`, `button`, `span`, atau `summary` (tombol "Menu" di ponsel) |

Atribut lain (`class`, `target`, `data-*`, `type`) diteruskan ke elemen tombol. Yang bukan tombol dan tetap memakai gaya sendiri: tautan menu, penanda (tag) dan penyaring, panah slider klien, dan lingkaran putar video.

## Aturan

- Teks tidak ditanam di dalam gambar atau SVG.
- Setiap bagian diuji dalam kelima bahasa, termasuk Arab (RTL) dan teks panjang bahasa Indonesia.
- Gambar besar memakai format modern, ukuran responsif, dan `loading="lazy"` kecuali yang terlihat pertama.
- Merah boleh menjadi latar luas hanya untuk teks besar atau tebal (blok ajakan). Teks kecil di atas krimson memakai putih tebal (4,97).

## Ditunda

- Konten database dan struktur data kategori, produk, dan proses.
- Foto dan ilustrasi baru (daftar kebutuhan di [`direction.md`](direction.md)).
- Mode gelap.
