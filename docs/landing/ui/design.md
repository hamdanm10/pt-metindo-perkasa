# Desain Landing Page

Tampilan landing page publik PT Metindo Perkasa (electroplating dan metal finishing, pemasok bahan kimia plating) dalam lima bahasa. Fondasi warna, tipografi, dan RTL ada di [`../../shared/design-system/overview.md`](../../shared/design-system/overview.md). Isi dan susunan halaman mengikuti situs prototipe, dan prompt aset vektor ada di [`recraft-prompts.md`](recraft-prompts.md).

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | Susunan beranda mengikuti prototipe `homepage-2`: hero, pengantar video, pendekatan, katalog 13 kategori, layanan, klien dan mitra, artikel, ajakan menghubungi. Tampilannya dirancang ulang | Prototipe adalah versi awal project ini, tetapi desainnya dinilai kurang baik. Fase dan status ada di [`../implementation-plan.md`](../implementation-plan.md) |
| 2 | Tampilan tidak kaku: aset vektor organik dari Recraft sebagai pembatas, latar, dan hiasan | Permintaan client |
| 3 | Layar intro "Click to begin" dipertahankan dengan batasan di bawah | Permintaan client, risiko SEO dikurangi lewat batasan |
| 4 | Konten dirender di server dengan Blade | SEO |
| 5 | Gerak dengan GSAP pada dial MOTION 2, dijelaskan di [`motion.md`](motion.md) | Tampilan tidak kaku, tetap terbaca dan ramah reduced motion |

## Layar intro

Aturan supaya layar intro tidak merugikan SEO dan pengunjung:

- Tampil **sekali per sesi browser** (`sessionStorage`) dan hanya di beranda.
- Konten beranda **sudah ada di HTML** di belakang overlay, bukan dimuat setelah overlay ditutup.
- Tersembunyi sampai JavaScript aktif, jadi tanpa JavaScript halaman langsung terlihat.
- Bisa ditutup dengan klik, Enter, atau Escape. Fokus dipindahkan dengan benar.
- Animasi masuk dan keluar memakai GSAP ([`motion.md`](motion.md)). Dengan `prefers-reduced-motion`, overlay tidak beranimasi.
- Setelah jadi, ukur LCP dan INP dengan Lighthouse, dengan dan tanpa overlay. Hasilnya dicatat di dokumen ini. Jika skornya turun jauh, bahas penggantinya dengan client (misalnya animasi singkat yang menutup sendiri).

## Hero

Latar hero dibuat berwarna dengan elemen pendukung yang sedikit, tanpa menambah teks atau lencana:

- **Blok indigo miring** di sisi kanan (`.hero-wedge`, tepi kirinya miring selaras garis logo, dibalik di RTL) sebagai latar pelat. Hanya tampil mulai lebar `lg`.
- **Lingkaran merah** setengah tenggelam di dasar hero, menimpa tepi blok indigo.
- **Pelat bertumpuk** (`hero-waves.svg`) di atasnya, dengan pelat putih, merah, dan biru tua yang kontras terhadap indigo.
- Di bawah lebar `lg`, blok indigo menjadi ubin membulat di belakang pelat.
- Pola garis miring tidak dipakai di hero karena menimpa teks.

## Navbar

- **Menempel di atas** saat gulir (`position: sticky`). Saat halaman mulai bergulir (diamati lewat `IntersectionObserver` pada penanda di puncak halaman, tanpa pendengar gulir), tinggi header mengecil dari sekitar 69 ke 57 piksel dan muncul bayangan tipis. Transisinya CSS dan dimatikan oleh `prefers-reduced-motion`.
- Latar putih solid. Efek kaca (blur) sengaja tidak dipakai.
- Tautan aktif ditandai garis miring merah di bawahnya (selaras garis logo), yang juga muncul saat hover. Di RTL, garis tumbuh dari sisi kanan.
- Pemilih bahasa di desktop berupa tombol lavender berisi bahasa aktif dengan daftar turun berbayang. Di bawah lebar `lg`, tombol "Menu" berwarna merah membuka panel berisi menu dan bahasa.
- Tujuan jangkar (`#catalog`) tidak tertutup header: `scroll-padding-top` pada `html`.
- Layar intro (z 80) berada di atas header (z 50), dan header ikut `inert` selama intro.

## Penggunaan aset vektor

Komponen `x-landing.art` memuat `public/images/landing/<nama>.svg` bila berkasnya ada, dan menampilkan isi cadangan (slot) bila belum. Gambar dimuat malas (`loading="lazy"`), kecuali yang diberi `eager` (hero dan kepala halaman). Gambar yang diposisikan sebagian di luar layar harus `eager`, karena peramban tidak memuat gambar malas yang berada di luar layar sehingga ukurannya nol.

| Tempat | Aset | Cadangan bila belum ada |
| --- | --- | --- |
| Hero | `hero-waves.svg` (pelat bertumpuk dengan garis miring) | Tiga pelat dari CSS |
| Poster video | `video-poster.svg` | Blok warna dan lingkaran dari CSS |
| Kepala halaman dan katalog | `slash-pattern.svg` (garis miring merah) | Tidak ada |
| Katalog dan daftar produk | `icon-<slug>.svg` dalam ubin putih | Tanda garis miring merah |
| Halaman Services | `service-electroplating.svg`, `service-chemical-supply.svg`, `service-consultation.svg` | Tidak ada |

Aset yang sudah dibuat tetapi belum dipakai: `hero-waves-2.svg`, `corner-curve.svg`, `about-layers.svg`, dan `article-cover-1.svg` (warnanya merah murni di luar palet dan kadrnya terpotong, jadi perlu dibuat ulang). Belum dibuat: ikon `metal-polishing`, `sustainable-steel`, `wastewater-treatment`, `chemical-laboratory`, ilustrasi `service-consultation`, dan sampul artikel 2 dan 3. Warna hasil Recraft sedikit meleset dari palet (misalnya merah poster lebih terang), dan itu diterima selama tidak mengganggu.

Foto memakai `x-landing.photo` ([`direction.md`](direction.md)). Berkas vektor dioptimalkan dengan SVGO (`npx svgo --multipass`), yang menurunkan ukuran total dari sekitar 495 KB menjadi 186 KB dengan perbedaan piksel di bawah 0,1 persen.

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
- Merah tidak dipakai sebagai latar luas di belakang teks kecil. Merek tampil lewat tombol, aksen, dan ilustrasi.

## Ditunda

- Konten database dan struktur data kategori, produk, dan proses.
- Desain halaman dalam (Products, Services, Articles & News, Contact, About Us).
- Mode gelap.
