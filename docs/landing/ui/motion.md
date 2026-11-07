# Gerak Landing

Animasi landing page dengan GSAP, supaya tampilan tidak kaku. Dial gerak naik dari 1 ke **MOTION 2** (reveal saat gulir dan transisi), dan semua gerak punya tujuan di bawah ini. Desain umum: [`design.md`](design.md). Fondasi: [`../../shared/design-system/overview.md`](../../shared/design-system/overview.md).

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | GSAP dari paket npm `gsap` (core, ScrollTrigger, SplitText), ikut berkas `landing.js`. Layar intro tetap transisi CSS | Reveal terkoordinasi, sinkron dengan gulir, dan pemisahan baris teks tidak praktis di CSS. Semua plugin gratis |
| 2 | Hanya `transform` dan `opacity` (lewat `x`, `y`, `scale`, `rotation`, `autoAlpha`, `clipPath`) | Tidak memicu tata letak ulang |
| 3 | Tidak ada gerak yang berjalan terus. Semua gerak dipicu muat halaman atau gulir, dan reveal hanya sekali (`once`) | Gerak abadi mengganggu dan melanggar dial |
| 4 | `prefers-reduced-motion: reduce`: tanpa animasi apa pun, semua konten langsung terlihat | Aksesibilitas |
| 5 | Teks hanya dipecah per **baris**, tidak per huruf | Huruf Arab bersambung dan akan rusak bila dipecah. Pemisahan dikembalikan (`revert`) setelah selesai, jadi teks utuh lagi saat lebar layar berubah |
| 6 | Konten selalu ada di HTML. Keadaan awal disembunyikan lewat kelas `anim-hide` yang dilepas setelah GSAP siap, dan pengaman waktu 3,5 detik melepasnya bila skrip gagal | Terbaca mesin pencari dan tidak ada halaman kosong bila JavaScript gagal |
| 7 | Arah gerak ikut `dir` halaman: nilai `x` dan `rotation` dikali -1 di RTL | Gerak masuk dari sisi yang benar di Arab |

## Daftar gerak

| Atribut `data-anim` | Tempat | Gerak | Tujuan |
| --- | --- | --- | --- |
| `hero-title` | Judul hero dan judul halaman | Baris naik keluar dari topeng | Membuka halaman dengan satu momen terarah |
| `hero-item` | Paragraf dan tombol hero, ringkasan halaman | Naik dan muncul berurutan | Urutan baca: judul, lalu penjelasan, lalu aksi |
| `hero-art` dan `data-plate` | Pelat hero | Pelat masuk bertumpuk dengan putaran, lalu bergeser pelan saat gulir (`scrub`) | Kesan lapisan logam, dan kedalaman |
| `heading` | Semua `h2` tanpa tautan | Baris naik dari topeng saat terlihat | Menandai awal bagian |
| `rows` | Daftar (katalog, proses, klien, mitra, artikel, layanan, anak perusahaan) | Item muncul berurutan | Daftar terbaca satu per satu |
| `card` dengan `data-from` (`start`, `end`, `up`) | Kartu layanan, visi dan misi, ajakan | Masuk dari sisi yang sesuai komposisi, dengan sedikit putaran | Menekankan kartu besar dan kecil yang berbeda bobot |
| `steps` dan `data-step-number` | "How we work" | Garis tiap langkah tergambar berurutan, angka muncul memantul | Menunjukkan urutan langkah |
| `media` | Kotak video | Membesar pelan dari 94 persen ke 100 persen saat gulir | Menarik mata ke video |

Tidak dianimasikan: header, footer, menu, pemilih bahasa, dan teks isi. Interaksi (hover, fokus) tetap CSS.

## Struktur kode

- `resources/js/landing/motion.js`: semua animasi, dibungkus `gsap.matchMedia()` untuk reduced motion.
- `resources/js/landing.js`: mengimpor `motion.js` dan mengirim peristiwa `intro:closed` saat intro ditutup. Animasi hero di beranda menunggu peristiwa itu bila intro tampil.
- `resources/css/landing.css`: aturan `.anim-hide [data-anim] { visibility: hidden }` dan `overflow-x: clip` pada `body` agar gerak ke samping tidak membuat gulir horizontal.
- `resources/views/landing/layouts/app.blade.php`: skrip kecil di `<head>` yang memasang `anim-hide` (kecuali reduced motion) dan pengaman waktunya.
- Atribut `data-anim` ada di view dan komponen landing.

## Aturan menambah gerak

- Setiap gerak baru harus punya tujuan yang bisa ditulis satu kalimat, dan ditambahkan ke tabel di atas.
- Jangan menambah gerak berulang, parallax berat, atau animasi pada teks isi.
- Elemen baru yang dianimasikan diberi `data-anim`, bukan kelas khusus.

## Verifikasi

- Test fitur memastikan atribut `data-anim`, skrip `anim-hide`, dan penanda langkah ada di HTML.
- Klik-tembus di Firefox: intro (klik, Escape, sekali per sesi), animasi hero selesai dengan semua elemen terlihat, semua bagian terungkap setelah gulir, dropdown bahasa, menu ponsel, video, navigasi, tanpa gulir horizontal (desktop dan 390 px Arab), dan reduced motion tanpa penyembunyian.
- Dampak ke LCP dan INP diukur di L6 bersama layar intro. Berkas `landing.js` sekitar 49 KB gzip setelah GSAP ditambahkan.

## Ditunda

- Parallax tambahan dan animasi ikon kategori setelah aset Recraft ada.
- Gerak untuk dashboard.
