# Prompt Recraft untuk Aset Vektor Landing

> **Arsip.** Setelah redesign Nov 2026 aset di dokumen ini tidak dipakai lagi ([`direction.md`](direction.md)). Berkas SVG-nya sudah dihapus dari `public/images/landing/` (masih ada di riwayat git); dokumen ini dipertahankan sebagai acuan bila ilustrasi baru dibuat.

Prompt untuk membuat aset vektor di Recraft sesuai arah energetik di [`direction.md`](direction.md), tetapi dengan elemen sedikit. Setiap prompt ditulis lengkap dan berdiri sendiri, tinggal disalin. Prompt berbahasa Inggris karena dipakai langsung di alat. Pemakaian aset ada di [`design.md`](design.md), palet di [`../../shared/design-system/overview.md`](../../shared/design-system/overview.md).

## Pengaturan umum

- Gaya **vector illustration**, keluaran **SVG**, latar **transparan**.
- Warna hanya dari palet: crimson `#D6283A`, ink `#1B1F3B`, indigo `#6D5BD0`, blue `#1F4FB5`, lavender `#F3F2FF`, dan putih. Tiap aset memakai **dua atau tiga warna saja**. Jangan pakai coral.
- Hindari komposisi yang bergantung arah kiri-kanan. Situs dipakai dalam Arab (RTL) dan komposisi akan dibalik.
- Target berat berkas: di bawah 40 KB per ilustrasi, 5 KB per ikon.

## Supaya tidak terlalu ramai

Energik datang dari warna berani, sudut miring, dan skala besar, bukan dari banyak elemen.

- Maksimal **tiga bentuk besar** per ilustrasi. Satu bentuk utama, dan sisanya pendukung.
- Sisakan **ruang kosong** sedikitnya 40 persen kanvas.
- Kata yang membantu: `minimalist`, `very simple`, `large simple shapes`, `lots of empty space`.
- Jika alat menyediakan negative prompt, isi: `extra shapes, decoration, patterns, small details, texture, gradient, shadow, text`.
- Jika hasilnya masih ramai, **hapus satu elemen dari prompt per percobaan**, jangan menambah kata. Hasilkan 4 variasi dan pilih yang paling sepi.

## Aset

Nama berkas mengikuti tempat pemakaiannya. Simpan di `public/images/landing/`. Aset yang belum terhubung ke kode saya pasang saat putaran desain.

### 1. Hero: `hero-waves.svg` (1:1)

Coba dua versi, lalu pilih yang lebih enak.

Versi A, pelat bertumpuk:

```
Minimalist flat vector illustration, square composition, three large rounded rectangles stacked and slightly rotated like metal plates: the back plate ink navy #1B1F3B, the middle plate crimson #D6283A, the front plate white with one thin diagonal stripe. Lots of empty space around them, nothing else in the image, no text, no gradients, no shadows, transparent background.
```

Versi B, lingkaran dan garis:

```
Minimalist flat vector illustration, square composition, one large crimson #D6283A circle with one bold ink navy #1B1F3B diagonal band cutting across it, and one small indigo #6D5BD0 circle near the edge. Lots of empty space, nothing else in the image, no text, no gradients, no shadows, transparent background.
```

### 2. Poster video: `video-poster.svg` (16:9)

```
Minimalist flat vector background, wide 16:9, solid ink navy #1B1F3B with one large crimson #D6283A quarter circle in the bottom left corner and one thin diagonal crimson line crossing the top right corner, empty center, nothing else in the image, no text, no gradients, no shadows.
```

### 3. Ikon 13 kategori: `icon-<slug>.svg` (1:1)

Satu gaya untuk semuanya: garis tebal berujung bulat seperti guratan monogram logo, dua warna (ink dan crimson). Jika paket Recraft punya pembuatan gaya dari gambar acuan, jadikan ikon pertama yang paling bagus sebagai gaya, lalu pakai untuk sisanya.

`icon-zinc-plating.svg`:

```
Single minimalist line icon of a metal bolt above a small plating tank, thick uniform round-capped outline in ink navy #1B1F3B with one small crimson #D6283A accent shape, centered on a square canvas with generous padding, flat, very simple, no background, no text, no gradients.
```

`icon-nickel-plating.svg`:

```
Single minimalist line icon of a shiny faucet-like pipe fitting with two short sparkle lines, thick uniform round-capped outline in ink navy #1B1F3B with one small crimson #D6283A accent shape, centered on a square canvas with generous padding, flat, very simple, no background, no text, no gradients.
```

`icon-copper-plating.svg`:

```
Single minimalist line icon of a coil of copper wire, thick uniform round-capped outline in ink navy #1B1F3B with one small crimson #D6283A accent shape, centered on a square canvas with generous padding, flat, very simple, no background, no text, no gradients.
```

`icon-hard-chrome.svg`:

```
Single minimalist line icon of a hydraulic piston rod inside a cylinder, thick uniform round-capped outline in ink navy #1B1F3B with one small crimson #D6283A accent shape, centered on a square canvas with generous padding, flat, very simple, no background, no text, no gradients.
```

`icon-tin-plating.svg`:

```
Single minimalist line icon of a small electronic connector pin, thick uniform round-capped outline in ink navy #1B1F3B with one small crimson #D6283A accent shape, centered on a square canvas with generous padding, flat, very simple, no background, no text, no gradients.
```

`icon-electroless-nickel.svg`:

```
Single minimalist line icon of a precision gear with a thin coating ring around it, thick uniform round-capped outline in ink navy #1B1F3B with one small crimson #D6283A accent shape, centered on a square canvas with generous padding, flat, very simple, no background, no text, no gradients.
```

`icon-anodizing.svg`:

```
Single minimalist line icon of an aluminum plate seen at an angle with three stacked color bands on its edge, thick uniform round-capped outline in ink navy #1B1F3B with one small crimson #D6283A accent shape, centered on a square canvas with generous padding, flat, very simple, no background, no text, no gradients.
```

`icon-phosphating.svg`:

```
Single minimalist line icon of a steel screw with a few short texture lines on its surface, thick uniform round-capped outline in ink navy #1B1F3B with one small crimson #D6283A accent shape, centered on a square canvas with generous padding, flat, very simple, no background, no text, no gradients.
```

`icon-plating-on-plastic.svg`:

```
Single minimalist line icon of a round plastic control knob with a glossy metallic ring, thick uniform round-capped outline in ink navy #1B1F3B with one small crimson #D6283A accent shape, centered on a square canvas with generous padding, flat, very simple, no background, no text, no gradients.
```

`icon-metal-polishing.svg`:

```
Single minimalist line icon of a round polishing wheel with three short shine lines, thick uniform round-capped outline in ink navy #1B1F3B with one small crimson #D6283A accent shape, centered on a square canvas with generous padding, flat, very simple, no background, no text, no gradients.
```

`icon-sustainable-steel.svg`:

```
Single minimalist line icon of a steel I-beam with a single leaf growing beside it, thick uniform round-capped outline in ink navy #1B1F3B with one small crimson #D6283A accent shape, centered on a square canvas with generous padding, flat, very simple, no background, no text, no gradients.
```

`icon-wastewater-treatment.svg`:

```
Single minimalist line icon of a water droplet above a simple filter funnel, thick uniform round-capped outline in ink navy #1B1F3B with one small crimson #D6283A accent shape, centered on a square canvas with generous padding, flat, very simple, no background, no text, no gradients.
```

`icon-chemical-laboratory.svg`:

```
Single minimalist line icon of a laboratory flask next to a test tube, thick uniform round-capped outline in ink navy #1B1F3B with one small crimson #D6283A accent shape, centered on a square canvas with generous padding, flat, very simple, no background, no text, no gradients.
```

### 4. Ilustrasi tiga layanan (4:3)

`service-electroplating.svg`:

```
Minimalist flat vector spot illustration of three metal parts hanging above a plating tank, only simple geometric shapes, ink navy #1B1F3B and crimson #D6283A with one lavender #F3F2FF background shape, centered on a 4:3 canvas with lots of empty space around, no text, no people, no gradients, no shadows, transparent background.
```

`service-chemical-supply.svg`:

```
Minimalist flat vector spot illustration of two chemical drums and one bottle standing together, only simple geometric shapes, ink navy #1B1F3B and crimson #D6283A with one lavender #F3F2FF background shape, centered on a 4:3 canvas with lots of empty space around, no text, no people, no gradients, no shadows, transparent background.
```

`service-consultation.svg`:

```
Minimalist flat vector spot illustration of a round measuring gauge next to a clipboard with a simple checklist of plain lines, only simple geometric shapes, ink navy #1B1F3B and crimson #D6283A with one lavender #F3F2FF background shape, centered on a 4:3 canvas with lots of empty space around, no readable text, no people, no gradients, no shadows, transparent background.
```

### 5. About: `about-layers.svg` (4:3)

```
Minimalist flat vector side view of a plated metal sheet: one thick ink navy #1B1F3B base band with three thin horizontal coating bands above it in crimson #D6283A, lavender #F3F2FF and indigo #6D5BD0, and one clean diagonal cut across the right end revealing the layers, centered on a 4:3 canvas with lots of empty space, no labels, no text, no gradients, no shadows, transparent background.
```

### 6. Sampul artikel (16:9)

`article-cover-1.svg`:

```
Minimalist abstract flat vector cover, 16:9, solid crimson #D6283A background with one large ink navy #1B1F3B circle partly cropped at the right edge and one thin white diagonal line, empty space on the left for a headline, no text, no gradients, no shadows.
```

`article-cover-2.svg`:

```
Minimalist abstract flat vector cover, 16:9, solid ink navy #1B1F3B background with one large crimson #D6283A rounded rectangle tilted slightly at the right and one thin lavender #F3F2FF diagonal line, empty space on the left for a headline, no text, no gradients, no shadows.
```

`article-cover-3.svg`:

```
Minimalist abstract flat vector cover, 16:9, solid lavender #F3F2FF background with one large indigo #6D5BD0 quarter circle in the bottom right corner and one thin crimson #D6283A diagonal line, empty space on the left for a headline, no text, no gradients, no shadows.
```

### 7. Ornamen

`slash-pattern.svg` (1:1):

```
Minimalist flat vector pattern of five parallel diagonal lines of different thickness in crimson #D6283A on a transparent background, sparse and evenly spaced, square canvas, nothing else in the image, no text, no gradients, no shadows.
```

`corner-curve.svg` (1:1):

```
Minimalist flat vector large quarter circle in crimson #D6283A with one thin concentric arc in lavender #F3F2FF, placed in the corner of a square canvas, nothing else in the image, no text, no gradients, no shadows, transparent background.
```

## Setelah aset jadi

- Simpan di `public/images/landing/` dengan nama di atas dan jalankan SVGO.
- Buka di browser dengan latar terang dan latar `ink`, dan uji dicerminkan untuk RTL.
- Kabari saya berkas mana yang sudah ada. Saya hubungkan ke komponen aset, atur ukurannya, dan menyesuaikan dokumen desain.
- Gambar yang meniru foto tidak dibuat di Recraft. Foto asli dan placeholder dibahas di [`direction.md`](direction.md).
