# Desain Landing Page

Tampilan landing page publik PT Metindo Perkasa (electroplating dan metal finishing, pemasok bahan kimia plating) dalam lima bahasa. Fondasi warna, tipografi, dan RTL ada di [`../../shared/design-system/overview.md`](../../shared/design-system/overview.md). Isi dan susunan halaman mengikuti situs prototipe, dan prompt aset vektor ada di [`recraft-prompts.md`](recraft-prompts.md).

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | Susunan beranda mengikuti prototipe `homepage-2`: hero, pengantar video, pendekatan, katalog 13 kategori, layanan, klien dan mitra, artikel, ajakan menghubungi. Tampilannya dirancang ulang | Prototipe adalah versi awal project ini, tetapi desainnya dinilai kurang baik. Fase dan status ada di [`../implementation-plan.md`](../implementation-plan.md) |
| 2 | Tampilan tidak kaku: aset vektor organik dari Recraft sebagai pembatas, latar, dan hiasan | Permintaan client |
| 3 | Layar intro "Click to begin" dipertahankan dengan batasan di bawah | Permintaan client, risiko SEO dikurangi lewat batasan |
| 4 | Konten dirender di server dengan Blade | SEO |

## Layar intro

Aturan supaya layar intro tidak merugikan SEO dan pengunjung:

- Tampil **sekali per sesi browser** (`sessionStorage`) dan hanya di beranda.
- Konten beranda **sudah ada di HTML** di belakang overlay, bukan dimuat setelah overlay ditutup.
- Tersembunyi sampai JavaScript aktif, jadi tanpa JavaScript halaman langsung terlihat.
- Bisa ditutup dengan klik, Enter, atau Escape. Fokus dipindahkan dengan benar.
- Dengan `prefers-reduced-motion`, overlay tidak beranimasi atau dilewati.
- Setelah jadi, ukur LCP dan INP dengan Lighthouse, dengan dan tanpa overlay. Hasilnya dicatat di dokumen ini. Jika skornya turun jauh, bahas penggantinya dengan client (misalnya animasi singkat yang menutup sendiri).

## Penggunaan aset vektor

| Tempat | Aset | Catatan |
| --- | --- | --- |
| Latar hero | Bentuk gelombang organik | Teks hero tetap di atas area dengan kontras cukup |
| Antarbagian | Pembatas gelombang atas dan bawah | Dibalik di RTL bila punya arah |
| Di balik foto atau kartu | Blob lembut | Dekoratif, `aria-hidden` |
| Katalog 13 kategori | Satu ikon per kategori, gaya sama | Subjek per kategori ada di dokumen prompt |
| Hiasan sudut | Pola titik dan garis | Opsional, jangan ramai |

Berkas vektor disimpan di `resources/images/landing/` (SVG), dioptimalkan dengan SVGO, dan dimuat sebagai `<img>` atau di-inline hanya bila perlu diwarnai lewat CSS. Ukuran tiap berkas dijaga kecil karena memengaruhi kecepatan.

## Aturan

- Teks tidak ditanam di dalam gambar atau SVG.
- Setiap bagian diuji dalam kelima bahasa, termasuk Arab (RTL) dan teks panjang bahasa Indonesia.
- Gambar besar memakai format modern, ukuran responsif, dan `loading="lazy"` kecuali yang terlihat pertama.
- Merah tidak dipakai sebagai latar luas di belakang teks kecil. Merek tampil lewat tombol, aksen, dan ilustrasi.

## Ditunda

- Konten database dan struktur data kategori, produk, dan proses.
- Desain halaman dalam (Products, Services, Articles & News, Contact, About Us).
- Mode gelap.
