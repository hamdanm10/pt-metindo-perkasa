# Arah Desain Landing: Mengikuti newengen.com

Arah visual landing setelah redesign (Nov 2026). Sumber tunggal untuk siapa pun yang mengerjakannya. Fondasi: [`../../shared/design-system/overview.md`](../../shared/design-system/overview.md). Keputusan rinci: [`design.md`](design.md). Gerak: [`motion.md`](motion.md).

## Permintaan client

- Mengikuti gaya situs referensi `https://newengen.com/`, dengan **palet warna yang sama** (krimson, `ink`, lavender, aksen biru dan indigo).
- Logo memakai monogram `docs/references/image.svg`, dan favicon dibuat ulang dari logo itu.
- Tidak memaksakan ilustrasi Recraft yang lama. Ilustrasi atau foto baru diminta bila dibutuhkan.
- Animasi dan layar intro disederhanakan. Fitur newengen yang tidak punya padanan (spanduk promo, testimoni, buletin) dilewati.

## Yang diambil dari newengen

| Ciri newengen | Padanan di sini |
| --- | --- |
| Hero gelap, judul raksasa huruf kapital Figtree 900, paragraf pendek, dua tombol, panel gambar di kanan | Hero `ink`, panel lavender berisi monogram logo (foto bila sudah ada) |
| Deretan logo klien di bawah hero | Baris sektor yang dilayani |
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
| Gambar tiap layanan (3) | Baris layanan di beranda dan halaman Services | Ilustrasi atau foto, rasio sekitar 4:3 |
| Logo klien dan mitra | Dinding klien | Format SVG atau PNG transparan, dengan izin |

Ilustrasi Recraft lama (`hero-waves`, `slash-pattern`, ikon kategori, `service-*`, `video-poster`, dan lainnya) masih ada di `public/images/landing/` tetapi **tidak dipakai lagi**, begitu pula komponen `x-landing.art` dan `x-landing.category-icon` yang sudah dihapus. Prompt lama di [`recraft-prompts.md`](recraft-prompts.md) bersifat arsip.
