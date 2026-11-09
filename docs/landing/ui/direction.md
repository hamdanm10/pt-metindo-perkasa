# Arah Desain Landing: Mengikuti newengen.com

Arah visual landing setelah redesign (Nov 2026). Sumber tunggal untuk siapa pun yang mengerjakannya. Fondasi: [`../../shared/design-system/overview.md`](../../shared/design-system/overview.md). Keputusan rinci: [`design.md`](design.md). Gerak: [`motion.md`](motion.md).

## Permintaan client

- Mengikuti gaya situs referensi `https://newengen.com/`, dengan **palet warna yang sama** (krimson, `ink`, lavender, aksen biru dan indigo).
- Logo memakai monogram `public/images/brand/logo.svg`, dan favicon dibuat ulang dari logo itu.
- Tidak memaksakan ilustrasi Recraft yang lama. Ilustrasi atau foto baru diminta bila dibutuhkan.
- Animasi disamakan dengan referensi (putaran kedua), layar intro tetap sederhana, dan animasi karakter memakai `.lottie`. Fitur newengen yang tidak punya padanan (spanduk promo, testimoni, buletin) dilewati.

## Yang diambil dari newengen

| Ciri newengen | Padanan di sini |
| --- | --- |
| Hero gelap, judul raksasa huruf kapital Figtree 900, paragraf pendek, dua tombol, panel gambar di kanan | Hero `ink`, panel lavender berisi monogram logo (foto bila sudah ada) |
| Deretan logo klien di bawah hero | Tidak dipakai (baris sektor di hero dihapus). Klien ada di bagian Clients & Partners |
| Kalimat besar sebagai pembuka bagian, lalu kartu | Katalog 13 kategori sebagai kartu bergaris tipis |
| Layanan sebagai baris besar di latar gelap dengan tombol berpanah | Tiga layanan bernomor 01 sampai 03 |
| Dinding logo klien | Kotak sektor dan mitra (teks, sampai logo asli tersedia) |
| Tombol "Let's talk" berulang | Tombol di header, hero, dan blok ajakan krimson |
| Footer lengkap | Logo, deskripsi, menu, kontak |

## Batasan yang tetap berlaku

- Lima bahasa, termasuk Arab (RTL). Properti logis, uji dengan teks panjang.
- Semua teks lewat `lang/<locale>` dan konten dirender di server.
- Kontras WCAG AA. Teks merah di atas `ink` tidak boleh (hanya angka besar).
- Tanpa klaim, angka, atau testimoni karangan. Tanpa logo klien karangan.
- Gerak hanya yang ada di [`motion.md`](motion.md), dan menghormati `prefers-reduced-motion`.
- Foto memakai komponen `x-landing.photo` (`public/images/landing/photos/<nama>.webp`, placeholder hanya saat `LANDING_SAMPLE_CONTENT` aktif). Tidak ada foto stok atau gambar buatan sendiri.
- Perubahan desain berarti perubahan dokumen di branch yang sama.

## Aset yang akan memperkuat tampilan (belum wajib)

| Aset | Dipakai di | Keterangan |
| --- | --- | --- |
| Foto proses plating atau pabrik | Panel hero (menggantikan monogram) | Arah vertikal 4:5, pencahayaan gelap selaras `ink` |
| `about-company`, `about-facilities` | Halaman About Us | Placeholder berlabel sampai ada foto |
| `product-zinc-plating`, `product-nickel-plating`, `product-copper-plating`, `product-hard-chrome` | Kartu katalog di beranda (tumpukan dan baris) | Portrait 365:620 (sekitar 3:5), minimal 730x1240, WebP atau JPG, di `public/images/landing/photos/`. Placeholder berlabel tampil sampai berkasnya ada. Kartu dipotong `object-cover` |
| `gallery-medan`, `gallery-jakarta`, `gallery-surabaya`, `gallery-balikpapan`, `gallery-makassar`, `gallery-jayapura` | Galeri foto layar penuh di beranda | Foto lokasi atau kegiatan di tiap kota, **milik perusahaan sendiri**. Lanskap 16:9, minimal 2400x1350, WebP atau JPG, di `public/images/landing/photos/` (nama mengikuti `gallery-<kunci anak perusahaan>`). Placeholder berlabel tampil sampai berkasnya ada. Jangan memakai foto yang diambil dari mesin pencari: bukan milik perusahaan dan melanggar hak cipta |
| `video-thumbnail` | Kotak video di beranda | Rasio 16:9, minimal 1600x900, WebP atau JPG. Diganti dengan menimpa berkas di `public/images/landing/photos/`. Tanpa teks di dalam gambar. Bagian bawahnya ditutup gradasi gelap |
| `service-electroplating`, `service-chemical-supply`, `service-consultation` | Panel kanan layanan di beranda | Ilustrasi diam rasio 16:9 (minimal 1600x900), SVG, WebP, JPG, atau PNG, di `public/images/landing/photos/`. Placeholder berlabel tampil sampai berkasnya ada. Bisa diganti animasi `.lottie` kelak |
| Logo klien dan logo mitra | Slider klien di beranda | Maksimal 9 per slide, SVG atau PNG transparan, dengan izin. Taruh di `public/images/landing/clients/` dan `public/images/landing/partners/`, dengan nama berkas yang menjadi nama (`acme-motors.svg` menjadi "Acme Motors"). Tanpa logo mitra, nama mitra di `config/landing.php` tampil sebagai teks. Slide Clients baru muncul di production bila folder `clients/` berisi logo |
| Testimoni klien (kutipan, nama, jabatan, logo) | Kartu di kanan slider | Dari client, dengan izin. Belum ada, jadi hanya placeholder |

Ilustrasi Recraft lama (`hero-waves`, `slash-pattern`, ikon kategori, `service-*`, `video-poster`, dan lainnya) masih ada di `public/images/landing/` tetapi **tidak dipakai lagi**, begitu pula komponen `x-landing.art` dan `x-landing.category-icon` yang sudah dihapus. Prompt lama di [`recraft-prompts.md`](recraft-prompts.md) bersifat arsip.
