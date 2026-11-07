# Prompt Recraft untuk Aset Vektor Landing

Prompt untuk membuat aset vektor di Recraft sesuai arah energetik di [`direction.md`](direction.md). Prompt ditulis dalam bahasa Inggris karena dipakai langsung di alat. Pemakaian aset ada di [`design.md`](design.md), palet di [`../../shared/design-system/overview.md`](../../shared/design-system/overview.md).

## Pengaturan umum

- Gaya **vector illustration**, keluaran **SVG**, latar **transparan**.
- Jika Recraft menyediakan kontrol warna, masukkan palet ini dan jangan tambah warna lain:

| Nama | Hex |
| --- | --- |
| Crimson (utama) | `#D6283A` |
| Ink | `#1B1F3B` |
| Indigo | `#6D5BD0` |
| Blue | `#1F4FB5` |
| Lavender | `#F3F2FF` |
| White | `#FFFFFF` |

- Tanpa teks, huruf, logo, orang, atau wajah di dalam gambar.
- Warna datar. Tanpa foto, tekstur realistis, bayangan, atau gradien rumit.
- Hasilkan 4 variasi per prompt, pilih yang paling sederhana, lalu cek berat berkas (target di bawah 60 KB per ilustrasi, 5 KB per ikon).
- Jangan memakai coral atau warna di luar palet.
- Hindari bentuk yang bergantung arah kiri-kanan. Situs dipakai dalam Arab (RTL) dan komposisi akan dibalik, jadi aset sebaiknya tetap enak dilihat bila dicerminkan.

## Kalimat gaya (ditempel di awal semua prompt)

```
Bold flat vector illustration, energetic and dynamic, strong diagonal composition, layered metal plates with sharp diagonal cuts combined with large smooth curves, geometric and confident, high contrast, flat colors only, limited palette of crimson #D6283A, ink navy #1B1F3B, indigo #6D5BD0, blue #1F4FB5, lavender #F3F2FF and white, no text, no people, no gradients, no shadows, transparent background.
```

## Aset

Nama berkas mengikuti tempat pemakaiannya. Simpan di `public/images/landing/`. Aset yang belum terhubung ke kode saya pasang saat putaran desain.

### 1. Hero: `hero-waves.svg` (1:1)

```
[kalimat gaya] A square composition of five stacked metal plates with rounded corners, fanned out and tilted at different angles like a deck of cards, the front plate crimson with one clean diagonal highlight stripe, the others ink navy, indigo and lavender, a few large curved arcs sweeping behind them, lots of empty space around the edges, centered.
```

Pakai: sisi kanan hero, menggantikan pelat CSS.

### 2. Poster video: `video-poster.svg` (16:9)

```
[kalimat gaya] Wide 16:9 background for a video thumbnail, ink navy base filled with bold diagonal crimson and indigo slashes and one large sweeping curve in the lower left, a calm empty area in the center for a play button, strong sense of motion.
```

### 3. Ikon 13 kategori: `icon-<slug>.svg` (1:1)

Satu gaya untuk semuanya: garis tebal berujung bulat seperti guratan monogram logo, dua warna saja (ink dan crimson).

```
Single bold geometric icon, thick uniform line style with round caps and round joins, two colors only: ink navy #1B1F3B lines with one crimson #D6283A accent shape, centered on a square canvas, generous padding, flat, no background, no text. Subject: [subjek]
```

| Berkas | Subjek |
| --- | --- |
| `icon-zinc-plating.svg` | a metal bolt in a plating bath |
| `icon-nickel-plating.svg` | a shiny faucet-like fitting with sparkle lines |
| `icon-copper-plating.svg` | a coil of copper wire |
| `icon-hard-chrome.svg` | a hydraulic piston rod |
| `icon-tin-plating.svg` | a small electronic connector pin |
| `icon-electroless-nickel.svg` | a precision gear with a thin coating layer |
| `icon-anodizing.svg` | an aluminum plate with layered color bands |
| `icon-phosphating.svg` | a steel screw with a textured surface |
| `icon-plating-on-plastic.svg` | a plastic knob with a metallic coating |
| `icon-metal-polishing.svg` | a polishing wheel with shine marks |
| `icon-sustainable-steel.svg` | a steel beam with a leaf |
| `icon-wastewater-treatment.svg` | a water droplet passing through a filter |
| `icon-chemical-laboratory.svg` | a laboratory flask and test tube |

Jika paket Recraft menyediakan pembuatan gaya dari gambar acuan, buat satu ikon yang paling bagus, jadikan gaya, lalu pakai gaya itu untuk 12 ikon sisanya supaya konsisten.

### 4. Ilustrasi tiga layanan (4:3)

Dasarnya sama: `[kalimat gaya] A simple spot illustration of [subjek], built from flat geometric shapes, centered with space around it.`

| Berkas | Subjek |
| --- | --- |
| `service-electroplating.svg` | metal parts hanging on a rack above a plating tank with rising bubbles |
| `service-chemical-supply.svg` | chemical drums and bottles stacked in a neat group |
| `service-consultation.svg` | a measuring gauge and a clipboard with a checklist, no readable text |

### 5. About: `about-layers.svg` (4:3)

```
[kalimat gaya] A cross-section of a plated metal sheet seen from the side: a thick ink navy base layer with thinner crimson, lavender and indigo coating layers stacked on top, drawn as clean horizontal bands with one diagonal cut revealing the layers, no labels.
```

### 6. Sampul artikel: `article-cover-1.svg` sampai `article-cover-3.svg` (16:9)

```
[kalimat gaya] Abstract 16:9 cover background, a few overlapping plates and one large circle cut by a diagonal line, balanced composition with empty space on the left for a headline. Variation [1/2/3]: change which color dominates (crimson, ink navy, indigo).
```

### 7. Ornamen

| Berkas | Prompt |
| --- | --- |
| `slash-pattern.svg` | `[kalimat gaya] A seamless-looking pattern of parallel diagonal lines in varying thickness, like the strokes of a logo monogram, only crimson and lavender, sparse, square canvas.` |
| `corner-curve.svg` | `[kalimat gaya] A large quarter-circle corner shape in crimson with a thinner concentric arc in lavender, designed to sit in the corner of a section, square canvas.` |

## Setelah aset jadi

- Simpan di `public/images/landing/` dengan nama di atas dan jalankan SVGO.
- Buka di browser dengan latar terang dan latar `ink`, dan uji dicerminkan untuk RTL.
- Kabari saya berkas mana yang sudah ada. Saya hubungkan ke komponen aset, atur ukurannya, dan menyesuaikan dokumen desain.
- Gambar yang meniru foto tidak dibuat di Recraft. Foto asli dan placeholder dibahas di [`direction.md`](direction.md).
