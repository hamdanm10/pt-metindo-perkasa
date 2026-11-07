# Prompt Recraft untuk Aset Vektor Landing

Prompt untuk membuat aset vektor di Recraft supaya landing tidak kaku. Prompt ditulis dalam bahasa Inggris karena dipakai langsung di alat. Pemakaian aset ada di [`design.md`](design.md), palet ada di [`../../shared/design-system/overview.md`](../../shared/design-system/overview.md).

## Pengaturan umum

- Keluaran **vektor (SVG)**, latar **transparan**.
- Jika Recraft menyediakan kontrol warna, masukkan palet ini dan jangan tambah warna lain:

| Nama | Hex |
| --- | --- |
| Crimson (utama) | `#D6283A` |
| Indigo | `#6D5BD0` |
| Blue | `#1F4FB5` |
| Lavender | `#F3F2FF` |
| Ink | `#1B1F3B` |
| White | `#FFFFFF` |

- Tanpa teks, huruf, atau logo di dalam gambar.
- Tanpa foto, tekstur realistis, bayangan, atau gradien rumit. Warna datar dan bentuk bersih lebih mudah dioptimalkan.
- Jangan memakai coral atau warna lain di luar palet.
- Hasilkan beberapa variasi per prompt, pilih yang paling sederhana, lalu cek berat berkas SVG (target di bawah 50 KB per berkas, ikon di bawah 5 KB).

## Kalimat gaya (ditempel di semua prompt)

> Flat vector illustration, smooth organic curves, soft rounded shapes, clean geometric lines with round caps, limited palette of crimson #D6283A, indigo #6D5BD0, blue #1F4FB5, lavender #F3F2FF, ink #1B1F3B and white, no text, no gradients, no shadows, transparent background.

## 1. Latar hero

> Wide abstract background for a website hero section, flowing wave shapes layered across the lower and right side, overlapping translucent-looking flat layers in lavender, indigo and crimson, a calm empty area on the left for headline text, feels like liquid metal flowing, 16:9. [kalimat gaya]

Pakai: latar hero. Pastikan area kosong cukup untuk teks.

## 2. Pembatas bagian (atas dan bawah)

> Seamless horizontal section divider, a single smooth wave edge, two layered curves, wide and short, fills the lower edge in lavender with a thin crimson accent layer, repeatable left to right, 1440x200. [kalimat gaya]

Buat dua versi: gelombang menghadap ke atas dan ke bawah. Pakai di antara bagian halaman.

## 3. Blob lembut

> Set of five soft organic blob shapes of different sizes, smooth asymmetric silhouettes, each in a flat single color from the palette (lavender, indigo, crimson, blue, light crimson tint), separated, no outlines. [kalimat gaya]

Pakai: di balik foto, kartu, atau judul bagian.

## 4. Ilustrasi tema electroplating

> Abstract illustration of metal plating: stacked metal sheets with rounded corners, rising bubbles, and a droplet of solution falling into a bath, simplified to flat geometric shapes, indigo and blue for the liquid, crimson accents, white highlights, no machinery detail. [kalimat gaya]

Pakai: bagian pendekatan atau tentang kami.

## 5. Ikon 13 kategori

Satu gaya untuk semuanya: garis tebal sama seperti guratan monogram logo (ujung membulat), dua warna (ink dan crimson), kanvas persegi, objek di tengah, tanpa latar.

Kalimat dasar per ikon:

> Single icon, thick uniform line style with round caps and round joins, two colors only: ink #1B1F3B lines with one crimson #D6283A accent shape, centered on a square canvas, plenty of padding, no fill background, no text. Subject: [subjek]

Subjek (ganti `[subjek]`):

| # | Kategori | Subjek |
| --- | --- | --- |
| 01 | Zinc Plating | a metal bolt in a plating bath |
| 02 | Nickel Plating | a shiny faucet-like fitting with sparkle |
| 03 | Copper Plating | a coil of copper wire |
| 04 | Hard Chrome | a hydraulic piston rod |
| 05 | Tin Plating | a small electronic connector pin |
| 06 | Electroless Nickel | a precision gear with a thin coating layer |
| 07 | Anodizing | an aluminum plate with color layers |
| 08 | Phosphating | a steel screw with a textured surface |
| 09 | Plating on Plastic | a plastic knob with a metallic coating |
| 10 | Metal Polishing | a polishing wheel and shine marks |
| 11 | Sustainable Steel | a steel beam with a leaf |
| 12 | Wastewater Treatment | a water droplet passing through a filter |
| 13 | Chemical Laboratory | a laboratory flask and test tube |

Hasilkan satu per satu dengan seed atau gaya yang sama supaya konsisten. Jika gaya antar ikon mulai berbeda, buat ulang yang menyimpang.

## 6. Pola hiasan sudut (opsional)

> Subtle decorative corner pattern, a loose arrangement of small dots and short rounded lines fading toward one corner, only lavender, indigo and crimson tints, sparse, transparent background, square canvas. [kalimat gaya]

## Setelah aset jadi

- Simpan di `resources/images/landing/` dengan nama deskriptif (contoh `hero-waves.svg`, `divider-wave-top.svg`, `icon-zinc-plating.svg`).
- Jalankan SVGO, buka di browser dengan latar terang dan latar `ink`, dan uji di RTL.
- Jika ada aset yang butuh warna berbeda per tema, bilang dan kita atur warnanya lewat CSS atau variasi berkas.
