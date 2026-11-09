# Gerak Landing

Gerak landing mengikuti situs referensi `newengen.com` (putaran kedua redesign, Nov 2026). Referensi tidak membuka kodenya, jadi polanya dibaca dari pengamatan langsung (tangkapan layar berurutan, nilai `transform` saat gulir, dan nama kelas), lalu ditulis ulang dengan GSAP. Desain umum: [`design.md`](design.md). Animasi karakter memakai berkas `.lottie`, di luar dokumen ini.

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | GSAP (core, ScrollTrigger, SplitText) dan **Lenis** untuk gulir halus, semua dari npm dan ikut `landing.js` | Referensi memakai gulir halus (kelas `lenis`) dan reveal terkoordinasi |
| 2 | Hanya `transform`, `opacity`, dan `height` untuk akordeon | Tidak memicu tata letak berat |
| 3 | `prefers-reduced-motion: reduce`: tanpa Lenis, tanpa animasi GSAP, semua konten langsung terlihat. Akordeon tetap bisa dibuka tanpa animasi | Aksesibilitas |
| 4 | Konten selalu ada di HTML. Keadaan awal disembunyikan lewat `anim-hide` yang dilepas setelah GSAP siap, dengan pengaman 3,5 detik. Akordeon terbuka semua tanpa JavaScript | Terbaca mesin pencari dan tanpa halaman kosong |
| 5 | **Judul tidak dipecah per huruf di bahasa Arab** (huruf bersambung). Di Arab judul naik-memudar utuh. Mandarin dan Jepang boleh dipecah per huruf | Huruf Arab rusak bila dipisah |
| 6 | **Tidak ada gerak abadi.** Semua gerak dipicu muat halaman, gulir, atau klik. Ticker sektor di hero pernah ada dan sudah dihapus | Gerak berulang mengganggu bacaan dan menguras baterai |
| 7 | Arah gerak ikut `dir` halaman (panah dibalik di RTL) | Arab |

## Daftar gerak

| Atribut atau kelas | Tempat | Gerak | Padanan di referensi |
| --- | --- | --- | --- |
| `data-anim="hero-title"` | Judul hero dan kepala halaman | Huruf tumbuh dari garis dasar (`scaleY` 0 ke 1, asal bawah) berurutan 0,035 detik, lalu `SplitText` dikembalikan | Judul "CREATIVE IS OUR EDGE." |
| `data-anim="hero-item"` | Paragraf, tombol, dan panel hero | Naik dan muncul berurutan setelah judul | Teks `js-t-fade-up` |
| `data-hero-panel` | Panel monogram hero | Mengecil ke 86 persen saat hero digulir keluar (`scrub`) | Kartu hero yang menyusut (`js-flip`) |
| `data-anim="grow"` | Judul besar (Services, Clients, ajakan) | Huruf meninggi dari 0 ke penuh mengikuti gulir (`scrub`) | Judul "CLIENTS SHINE" |
| `data-anim="heading"`, `card` | Judul biasa dan blok | Naik dan muncul saat terlihat | `js-t-fade-up` |
| `data-anim="rows"`, `steps` | Kartu katalog, klien, artikel, langkah | Muncul berurutan (batch) | Kartu karya |
| `data-catalog-stage`, `data-catalog-panel` | Kartu katalog | Hanya di lebar 1024 px ke atas. Kartu ada di daftar produk, tetapi mulai sebagai tumpukan di slot kosong section "Controlled process": kartu kedua lebar di depan (z-index lebih tinggi), tiga lainnya mengintip sebagai irisan (14, 13,5, dan 13,5 persen lebar slot), semuanya dipipihkan lewat `scaleX` dan `scaleY` tanpa isi. Label segitiga `data-catalog-badge` menempel di dasar kartu depan dan memudar di awal animasi. Tumpukan **diam** selama section pertama terlihat. Animasi (`scrub`) mulai saat panggung mencapai atas layar dan selesai saat baris kartu ada di 20 persen tinggi layar: kartu menyebar, membesar, dan turun ke barisnya. Isi dan keterangan kartu muncul di akhir. Di RTL panel mengikuti arah baca | Kartu hero yang menjadi deretan kartu karya (`js-flip`) |
| `data-header` | Header | Menyingkir ke atas saat gulir ke bawah (setelah 160 px), kembali saat gulir ke atas atau fokus masuk. Tidak menyingkir bila menu terbuka | Header `sh__content` |
| `data-accordion` | Layanan di beranda | Satu panel terbuka, tinggi dan opacity beranimasi 0,55 detik, ikon plus menjadi minus | Akordeon layanan |
| CSS | Panah, kartu katalog | Panah maju 4 px saat hover, kartu naik 6 px | Transisi 0,3 sampai 0,75 detik |
| `data-intro-item` | Layar intro | Logo dan tombol memudar masuk, seluruh layar memudar saat ditutup | Layar pembuka titik |

Header, footer, menu, dan teks isi lain tidak dianimasikan GSAP. Peristiwa `intro:closed` dikirim saat intro selesai, dan Lenis berhenti selama intro terbuka.

## Yang belum ditiru

- Logo klien yang berganti bergantian dan teks yang bergeser di judul "Creating impact for".
- Logo header yang menyusut menjadi ubin saat gulir (logo kita sudah monogram).

## Struktur kode

- `resources/js/landing/motion.js`: Lenis, hero, reveal gulir, header pintar, dan akordeon. Seluruh gerak GSAP dibungkus `gsap.matchMedia()`.
- `resources/js/landing/intro.js`: layar intro, `inert`, fokus, dan `sessionStorage`.
- `resources/js/landing.js`: mengimpor keduanya, ditambah menu, dropdown, dan pemutar video.
- `resources/css/landing.css`: `anim-hide`, penyembunyian intro, panah, ikon plus, dan aturan Lenis (`html.lenis`, `.lenis-smooth`).
- Jangkar (`#catalog`) memakai `lenis.scrollTo` dan mengikuti `scroll-padding-top`.
