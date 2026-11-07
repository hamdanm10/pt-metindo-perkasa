# Arah Desain Landing: Energik

Arah visual untuk putaran desain landing page berikutnya. Dokumen ini adalah sumber tunggal untuk siapa pun yang mengerjakannya. Fondasi: [`../../shared/design-system/overview.md`](../../shared/design-system/overview.md). Desain umum: [`design.md`](design.md). Gerak: [`motion.md`](motion.md).

## Permintaan client

- Tidak mau tampilan kaku seperti situs perusahaan profesional biasa. Maunya **energetik**.
- Tidak ada situs referensi dan tidak ada foto. Gambar memakai **placeholder** sampai aset tersedia.
- Aset vektor dibuat sendiri di Recraft nanti, setelah desainnya dinilai bagus. Tampilan harus sudah kuat tanpa aset itu.

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | Arah: **lapisan logam (garis miring dan pelat berlapis dari logo) digabung tipografi dan bentuk besar** | Tidak butuh foto, cocok dengan aset Recraft, dan berakar pada logo |
| 2 | Dial: ENERGY 3 / RHYTHM 3 / MOTION 2 | Energik berarti kontras ukuran, warna berani, dan komposisi tidak simetris. Gerak tetap tanpa pengulangan abadi |
| 3 | Merah krimson makin dominan sebagai blok warna besar, dengan `ink` sebagai penyeimbang. Biru dan indigo sebagai aksen kedua | Mengikuti logo. Kontras tiap pasangan baru tetap dicek |
| 4 | Tipografi: judul memakai **Bricolage Grotesque** (Latin) dengan bobot tebal dan jarak huruf rapat, serta lompatan ukuran besar. Mandarin, Arab, dan Jepang memakai Noto Sans seperti teks isi | Karakter lebih kuat tanpa merusak lima bahasa |
| 5 | Bentuk: **potongan miring** antarbagian dan pada kepala halaman (`.cut-top` dan `.cut-bottom`, dibalik di RTL), pola garis miring dari logo di bagian gelap, dan sedikit lengkung | Tidak kaku, khas perusahaan ini, dan tidak ramai |
| 6 | Foto memakai komponen `x-landing.photo`: gambar dimuat dari `public/images/landing/photos/<nama>.webp` (atau `.jpg`, `.jpeg`, `.png`) bila ada. Bila belum ada, tampil placeholder berlabel, hanya saat `LANDING_SAMPLE_CONTENT` aktif. Tidak ada foto stok atau gambar buatan sendiri | Aturan: jangan karang aset |
| 7 | Tempat foto: `about-company`, `about-facilities`, dan `article-<slug>` (beranda, daftar, dan detail artikel). Ilustrasi vektor: hero (`hero-waves`), poster video, ikon kategori, ilustrasi layanan | Bagian yang seharusnya bergambar |

## Batasan yang tetap berlaku

- Lima bahasa, termasuk Arab (RTL). Properti logis, tanpa pemecahan teks Arab per huruf, uji dengan teks panjang.
- Semua teks tetap lewat `lang/<locale>` dan konten tetap dirender di server.
- Kontras WCAG AA. Teks merah di atas latar gelap tidak boleh.
- Tanpa klaim, angka, atau testimoni karangan. Tanpa logo klien.
- Tanpa gerak yang berjalan terus. Semua gerak punya tujuan, ditulis di [`motion.md`](motion.md), dan menghormati `prefers-reduced-motion`.
- Tidak ada tautan atau tombol yang tidak berfungsi.
- Perubahan desain berarti perubahan dokumen di branch yang sama.

## Belum diputuskan

- Apakah coral `#FF5A5F` dari palet "energetic" boleh dipakai untuk grafis besar (kontras putih di atasnya hanya 3,05, jadi tidak untuk teks).
- Apakah slider atau gerak berulang pelan (misalnya untuk mitra) boleh. Saat ini dilarang oleh dial gerak.

## Status putaran pertama

Sudah ada: font judul, potongan miring, pola garis miring (kepala halaman dan katalog), hero memakai `hero-waves`, poster video, 9 dari 13 ikon (sisanya memakai tanda garis miring merah sebagai pengganti), dua ilustrasi layanan di halaman Services, dan placeholder foto. Pola garis di hero dihapus karena menimpa teks dan membuat hero ramai.

## Cara menilai hasil

Tangkapan layar situs Lerd (`https://pt-metindo-perkasa.test`) di 1280 px dan 390 px, Inggris dan Arab, sebelum dan sesudah. Pertanyaannya: apakah terlihat energetik dan khas, bukan hanya rapi, dan apakah tetap terbaca di semua bahasa.
