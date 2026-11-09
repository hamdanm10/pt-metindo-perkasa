# Gerak Landing

Gerak landing mengikuti situs referensi `newengen.com` (putaran kedua redesign, Nov 2026). Referensi tidak membuka kodenya, jadi polanya dibaca dari pengamatan langsung (tangkapan layar berurutan, nilai `transform` saat gulir, dan nama kelas), lalu ditulis ulang dengan GSAP. Desain umum: [`design.md`](design.md). Animasi karakter memakai berkas `.lottie`, di luar dokumen ini.

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | GSAP (core, ScrollTrigger, SplitText) dan **Lenis** untuk gulir halus, semua dari npm dan ikut `landing.js` | Referensi memakai gulir halus (kelas `lenis`) dan reveal terkoordinasi |
| 2 | Hanya `transform`, `opacity`, dan `height` untuk daftar layanan | Tidak memicu tata letak berat |
| 3 | `prefers-reduced-motion: reduce`: tanpa Lenis, tanpa animasi GSAP, semua konten langsung terlihat. Daftar layanan tampil terbuka semua dan tidak menempel | Aksesibilitas |
| 4 | Konten selalu ada di HTML. Keadaan awal disembunyikan lewat `anim-hide` yang dilepas setelah GSAP siap, dengan pengaman 3,5 detik. Daftar layanan terbuka semua tanpa JavaScript | Terbaca mesin pencari dan tanpa halaman kosong |
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
| `data-deck`, `data-deck-card` | Tumpukan kartu katalog | Hanya di lebar 1024 px ke atas. **Diukur dari hero referensi:** empat kartu seukuran, bergeser 13,7 persen, ditumpuk dengan z-index. Tiap **4 detik** kartu depan berganti, bolak-balik 0 ke 3 lalu kembali ke 0. Setiap pergantian: seluruh tumpukan mengecil ke 85 persen (asal bawah tengah) dalam 0,28 detik (`power2.inOut`) lalu kembali dalam 0,35 detik (`power2.out`). Kartu baru miring ke -4,75 derajat dengan skala 1,047 selama 0,28 detik, lalu melampaui ke +0,55 derajat dan skala 0,994, lalu menetap di nol dan satu dalam sekitar 1,35 detik. Di puncak penyusutan (0,34 detik) kartu baru mendapat z-index tertinggi. Tidak ada label di tumpukan | Hero referensi (`carousel-cards`) |
| `data-catalog-stage` | Dari tumpukan ke baris | Mulai saat panggung mencapai atas layar dan selesai saat baris kartu ada di 20 persen tinggi layar (`scrub`). Kartu tumpukan bergeser dan mengecil ke posisi kartu barisnya (gaya Flip), lalu kartu baris menggantikannya. Keterangan kartu dan tombol muncul di akhir. Putar otomatis berhenti selama gulir berjalan dan lanjut bila kembali ke atas. Di RTL tumpukan ada di sisi kiri dan geserannya dicerminkan | Kartu hero yang menjadi deretan kartu karya (`js-flip`) |
| `data-video`, `data-video-parallax`, `data-video-circle` | Kotak video di beranda | Kotak **membuka** dari bingkai kecil membulat (`clip-path` dari 14 persen dan skala 0,9 ke penuh) mengikuti gulir (`scrub`, `top 95%` sampai `top 40%`). Thumbnail bergeser pelan di dalamnya (`yPercent` -5 ke 5, `scrub`). Lingkaran putar muncul memantul (`back.out`, putar -90 derajat) dan labelnya naik saat kotak mencapai 62 persen layar (sekali). Judul section memakai `grow` | Media yang membesar saat gulir |
| Klik tombol putar | Kotak video | Lingkaran dan label membengkak ke skala 1,8 dan memudar (0,35 detik), lalu pemutar YouTube diganti masuk dan memudar tampil (0,5 detik). Hover memberi lingkaran halo yang melebar. Dengan reduced motion pemutar langsung dimuat tanpa animasi | Transisi ke pemutar |
| `data-clients`, `data-clients-slide`, `data-chip` | Klien dan mitra | **Diukur dari section Clients referensi.** Sembilan kotak logo slide lama **mengecil ke skala 0, turun 54 px, dan memudar** satu per satu dari kiri atas (0,35 detik tiap kotak, `power2.in`, jeda 0,035 detik antarkotak). Chip (hanya Clients dan Partners) menggeser teks lamanya ke atas (0,2 detik) dan teks baru naik dari bawah (0,35 detik) sementara lebarnya menyesuaikan teks baru (0,5 detik, `power2.inOut`). Pada 0,68 detik slide baru muncul **memantul masuk** dari skala 0 dengan sedikit melampaui (`back.out(1.7)`, 0,7 detik, jeda 0,05 detik). Dua slide bergantian **sendiri tiap 5 detik** (referensi: 5,0 detik) selama section terlihat, dan panah kiri-kanan memindahkannya sambil memulai ulang penghitung. Panah muncul saat hover atau fokus (di layar sentuh selalu terlihat). Di referensi putar otomatis baru mulai setelah klik panah pertama, di sini langsung berjalan. Dengan reduced motion slide berganti seketika dan tidak pernah berganti sendiri | Slider logo klien |
| `data-gallery`, `data-gallery-slide` | Galeri foto layar penuh | **Diukur dari referensi:** tiap **3,0 detik** foto berikutnya menggeser masuk selama **1,45 detik** dengan `expo.inOut`. Foto baru mulai di -50 persen lebar dan foto lama bergeser sejauh yang sama ke arah yang sama (+50 persen), jadi keduanya tampak berdampingan di tengah gerakan. Foto baru memudar masuk selama 0,35 detik mulai 0,07 detik. Kota, nama perusahaan, dan jam berganti (teks lama naik 0,2 detik, teks baru naik dari bawah 0,35 detik) sekitar 0,3 detik setelah gerakan mulai. Putar otomatis hanya saat galeri terlihat, urutan melingkar, dan tidak berjalan dengan reduced motion. Di RTL arah gerak dicerminkan | Blok foto layar penuh di bawah Clients |
| `data-btn` (`x-landing.button`) | **Semua tombol** landing | **Diukur dari tombol referensi** (`buttons.js`). Latar mengisi **naik dari bawah** ke posisi nol dalam sekitar 0,6 detik (`expo.out`, jeda 0,04 detik) dan **keluar lewat atas** saat pointer pergi (jeda 0,07 detik), lalu kembali ke bawah. Label punya dua salinan: yang tampil menggulir naik 32 px sementara yang lain datang dari 32 px di bawah, **per kata** (jeda 0,04 detik, 1 detik, `expo.out`). Gulir yang sama jalan saat masuk dan saat keluar, selalu ke atas. Panah mundur 4 px (0,5 detik, `power3.out`). Warna teks berganti ke warna `data-hover-text` bila latar pengisi berlawanan (varian `light` dan `outline`). Hanya perangkat dengan pointer dan tanpa reduced motion. Selain itu tombol hanya menggelap sedikit saat hover | Tombol `btn` referensi |
| `.title-hover` | Judul kartu artikel (hover atau fokus) | **Diukur dari referensi:** dua lapis `background` pada judul tumbuh dari kiri ke kanan: sorotan setinggi satu baris (`1em`, posisi 90 persen) dan garis bawah `0.05em` (posisi 95 persen), `background-size` dari 0 ke 100 persen dalam 0,75 detik dengan `cubic-bezier(0.19, 1, 0.22, 1)`, dan menyusut kembali dengan kurva yang sama saat pointer pergi. Referensi memakai sorotan hijau limau, di sini `brand-200`. Bekerja di judul beberapa baris, dan di RTL tumbuh dari kanan. Hanya CSS, mati dengan reduced motion. Kartu contoh statis juga memilikinya | Judul `h-main-multi` referensi | Tombol `btn` referensi |
| `data-header` | Header | Menyingkir ke atas saat gulir ke bawah (setelah 160 px), kembali saat gulir ke atas atau fokus masuk. Tidak menyingkir bila menu terbuka | Header `sh__content`. Bentuk bilah menjadi ubin melayang saat gulir diatur CSS lewat `data-stuck`, lihat [`design.md`](design.md) |
| `data-services-track`, `data-acc-item` | Layanan di beranda | **Diukur dari section Services referensi.** Pembungkus tinggi (`300vh` di referensi, di sini `100vh + 55vh per pergantian + 25vh`) berisi daftar yang `position: sticky` setinggi layar. Gulir saja yang membuka layanan satu per satu: panel yang terbuka menutup (tinggi dan opacity ke nol) sementara panel berikutnya membuka (ke tinggi aslinya, yang mengikuti isi), bersamaan dan `scrub`. Tiap pergantian berdurasi 1 satuan dan diselingi jeda 0,25 satuan, dengan jeda 0,25 di awal supaya layanan pertama terbaca. Tidak ada klik. Hanya lebar 1024 px ke atas tanpa reduced motion. Di tempat lain semua layanan terbuka dan diam | Section Services referensi (`acc-services-item`) |
| CSS | Panah, kartu katalog | Panah maju 4 px saat hover, kartu naik 6 px | Transisi 0,3 sampai 0,75 detik |
| `data-intro-item` | Layar intro | Logo dan tombol memudar masuk, seluruh layar memudar saat ditutup | Layar pembuka titik |

Header, footer, menu, dan teks isi lain tidak dianimasikan GSAP. Peristiwa `intro:closed` dikirim saat intro selesai, dan Lenis berhenti selama intro terbuka.

## Yang belum ditiru

- Label segitiga berisi nama klien di dasar tumpukan referensi (dihapus atas permintaan client).
- Logo klien yang berganti bergantian dan teks yang bergeser di judul "Creating impact for".

## Struktur kode

- `resources/js/landing/motion.js`: Lenis, hero, reveal gulir, header pintar, kartu katalog, video, dan daftar layanan. Seluruh gerak GSAP dibungkus `gsap.matchMedia()`.
- `resources/js/landing/intro.js`: layar intro, `inert`, fokus, dan `sessionStorage`.
- `resources/js/landing.js`: mengimpor keduanya, ditambah menu, dropdown, dan pemutar video.
- `resources/css/landing.css`: `anim-hide`, penyembunyian intro, panah, ikon plus, dan aturan Lenis (`html.lenis`, `.lenis-smooth`).
- Jangkar (`#catalog`) memakai `lenis.scrollTo` dan mengikuti `scroll-padding-top`.
