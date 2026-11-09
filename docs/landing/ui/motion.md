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
| `data-deck`, `data-deck-card` | Tumpukan kartu katalog | Hanya di lebar 1024 px ke atas. **Diukur dari hero referensi:** empat kartu seukuran, bergeser 13,7 persen, ditumpuk dengan z-index. Tiap **4 detik** kartu depan berganti, bolak-balik 0 ke 3 lalu kembali ke 0. Setiap pergantian: seluruh tumpukan mengecil ke 85 persen (asal bawah tengah) dalam 0,28 detik (`power2.inOut`) lalu kembali dalam 0,35 detik (`power2.out`). Kartu baru miring ke -4,75 derajat dengan skala 1,047 selama 0,28 detik, lalu melampaui ke +0,55 derajat dan skala 0,994, lalu menetap di nol dan satu dalam sekitar 1,35 detik. Di puncak penyusutan (0,34 detik) kartu baru mendapat z-index tertinggi dan label berganti (label lama naik memudar, label baru muncul dari bawah). Label juga **bergeser ke tengah kartu depan** yang baru (tween `left` 0,4 detik), jadi selalu berada tepat di tengah kartu | Hero referensi (`carousel-cards`) |
| `data-catalog-stage` | Dari tumpukan ke baris | Mulai saat panggung mencapai atas layar dan selesai saat baris kartu ada di 20 persen tinggi layar (`scrub`). Kartu tumpukan bergeser dan mengecil ke posisi kartu barisnya (gaya Flip), lalu kartu baris menggantikannya. Keterangan kartu dan tombol muncul di akhir. Putar otomatis berhenti selama gulir berjalan dan lanjut bila kembali ke atas. Di RTL tumpukan ada di sisi kiri dan geserannya dicerminkan | Kartu hero yang menjadi deretan kartu karya (`js-flip`) |
| `data-header` | Header | Menyingkir ke atas saat gulir ke bawah (setelah 160 px), kembali saat gulir ke atas atau fokus masuk. Tidak menyingkir bila menu terbuka | Header `sh__content` |
| `data-accordion` | Layanan di beranda | Satu panel terbuka, tinggi dan opacity beranimasi 0,55 detik, ikon plus menjadi minus | Akordeon layanan |
| CSS | Panah, kartu katalog | Panah maju 4 px saat hover, kartu naik 6 px | Transisi 0,3 sampai 0,75 detik |
| `data-intro-item` | Layar intro | Logo dan tombol memudar masuk, seluruh layar memudar saat ditutup | Layar pembuka titik |

Header, footer, menu, dan teks isi lain tidak dianimasikan GSAP. Peristiwa `intro:closed` dikirim saat intro selesai, dan Lenis berhenti selama intro terbuka.

## Yang belum ditiru

- Animasi pergantian teks label hanya perkiraan (naik memudar). Gerak topeng teks referensi tidak diukur.
- Logo klien yang berganti bergantian dan teks yang bergeser di judul "Creating impact for".
- Logo header yang menyusut menjadi ubin saat gulir (logo kita sudah monogram).

## Struktur kode

- `resources/js/landing/motion.js`: Lenis, hero, reveal gulir, header pintar, dan akordeon. Seluruh gerak GSAP dibungkus `gsap.matchMedia()`.
- `resources/js/landing/intro.js`: layar intro, `inert`, fokus, dan `sessionStorage`.
- `resources/js/landing.js`: mengimpor keduanya, ditambah menu, dropdown, dan pemutar video.
- `resources/css/landing.css`: `anim-hide`, penyembunyian intro, panah, ikon plus, dan aturan Lenis (`html.lenis`, `.lenis-smooth`).
- Jangkar (`#catalog`) memakai `lenis.scrollTo` dan mengikuti `scroll-padding-top`.
