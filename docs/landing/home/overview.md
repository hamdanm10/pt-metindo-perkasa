# Beranda Landing (L2)

Beranda landing page dalam lima bahasa, dengan semua bagian dari prototipe `homepage-2` dan konten statis. Belum ada database (L4), halaman dalam (L3), dan formulir (L5). Tujuannya agar tampilannya bisa dilihat dan dinilai. Fase dan statusnya di [`../implementation-plan.md`](../implementation-plan.md). Kerangka yang dipakai: [`../foundation/overview.md`](../foundation/overview.md). Desain: [`../ui/design.md`](../ui/design.md).

**Beranda L2 tidak dirilis ke production** sebelum konten contoh diganti data asli (L4) dan kontak diverifikasi.

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | Dial desain: ENERGY 2 / RHYTHM 3 / MOTION mengikuti referensi ([`../ui/motion.md`](../ui/motion.md)). Bagian berganti komposisi, tidak semuanya grid kartu yang sama | Permintaan client agar tidak kaku. Gerak (layar intro dan animasi GSAP) dijelaskan di [`../ui/motion.md`](../ui/motion.md) |
| 2 | Teks di `lang/<locale>/home.php` untuk kelima bahasa. Kunci tetap bahasa Inggris. Terjemahan `id`, `zh`, `ar`, `ja` memakai teks prototipe client bila ada, sisanya (judul dan deskripsi layanan, catatan contoh artikel) ditulis sendiri dan perlu ditinjau penutur asli | Aturan teks pengguna. Dipindah ke database di L4 |
| 3 | Katalog di beranda menampilkan **empat dari 13 kategori** (nama dan dua proses), tetap dalam bahasa Inggris di semua bahasa seperti di prototipe. Semua kategori ada di halaman Products. Produk, proses, TDS, dan harga tidak tampil | Datanya besar dan akan hidup di database (L4). Harga dan TDS belum diputuskan (L5) |
| 4 | Tanpa tautan ke halaman yang belum ada. Kategori, layanan, dan artikel belum bisa diklik sampai L3 | Aturan navigasi: tidak ada tautan mati |
| 5 | Daftar kategori (slug dan nama) dan nama mitra ada di `config/landing.php`. Slug dipakai untuk nama berkas ikon. Diganti database di L4 | Satu sumber untuk 13 kategori |
| 6 | Klien ditampilkan sebagai **nama sektor** tanpa logo, mitra sebagai **nama** tanpa logo, tanpa animasi berjalan terus | Logo klien tidak tersedia dan tidak boleh dikarang. Tidak ada gerak yang berjalan terus ([`../ui/motion.md`](../ui/motion.md)) |
| 7 | Bagian artikel menampilkan tiga artikel terbaru yang terbit di bahasa itu (dari database, A2) dan hilang bila belum ada | Artikel contoh sudah dihapus |
| 8 | Video: tidak ada permintaan ke YouTube sebelum diklik. Tampil poster lokal dan tombol putar, lalu iframe `youtube-nocookie.com` dibuat saat diklik. ID video di `config/landing.php` (env `LANDING_INTRO_VIDEO_ID`), bagian disembunyikan bila kosong | Kecepatan, privasi, dan SEO. ID di prototipe belum dipastikan video asli |
| 9 | Kontak (email, telepon) dari `config/landing.php` (env). Bagian dan tombol kontak hanya muncul bila diisi. Tombol "Talk to our team" memakai `mailto:` | Tombol harus berfungsi. Nilai di prototipe tampak contoh, belum dikonfirmasi |
| 10 | Ilustrasi Recraft lama tidak dipakai lagi sejak redesign (Nov 2026). Gambar hero adalah monogram logo | Tampilan jalan tanpa aset, dan aset tinggal diletakkan |
| 11 | Widget chat tidak dibuat | Belum diputuskan (L7) |
| 12 | Tidak ada gambar buatan sendiri selain logo. Foto dan ilustrasi baru diminta ke client bila dibutuhkan ([`../ui/direction.md`](../ui/direction.md)) | Aturan: jangan karang aset |
| 13 | Kata kunci `.env`: `LANDING_INTRO_VIDEO_ID`, `LANDING_CONTACT_EMAIL`, `LANDING_CONTACT_PHONE` (kosong di `.env.example`) | Data kontak dan video belum dikonfirmasi |

## Susunan

| Bagian | Isi | Catatan desain |
| --- | --- | --- |
| Layar intro | Logo putih di atas `ink`, tombol krimson "Click to begin" | Lihat aturan di bawah |
| Hero | Judul, satu paragraf, dua tombol: "Explore processes" (ke bagian katalog) dan "Contact us" (`mailto:`) | Latar `ink`, judul raksasa, panel monogram logo di kanan. Lihat [`../ui/design.md`](../ui/design.md) |
| Video pengantar | "See how we work": judul dan paragraf di kiri, kotak video (7/12 lebar kontainer) di kanan dengan **thumbnail**, lingkaran putar, dan label "Watch intro video" yang terlihat | Hanya tampil bila ID video terisi. Thumbnail dari `public/images/landing/photos/video-thumbnail.{webp,jpg,jpeg,png}` (placeholder berlabel bila belum ada). Diklik baru memuat YouTube (`youtube-nocookie`), thumbnail tidak diambil dari YouTube supaya tidak ada permintaan ke pihak ketiga sebelum klik |
| Controlled process | **Satu section** berlatar lavender. Blok atas: judul, deskripsi, dan tumpukan empat kartu seukuran di kanan (di desktop; kartu depan berganti sendiri tiap 4 detik, tanpa label). Di bawahnya baris **empat dari 13 kategori** sebagai kartu bergambar (nama dan dua proses di bawahnya) dan tombol "View all processes" ke `/{locale}/processes` | Isi kartu adalah gambar placeholder `product-<slug>` sampai fotonya ada. Kartu turun dari tumpukan ke baris saat digulir ([`../ui/motion.md`](../ui/motion.md)). Sembilan kategori lain ada di halaman Products |
| Layanan | Label "Services" dan kalimat deskripsi besar, lalu tiga layanan (Electroplating Service, Chemical Supply, Technical Consultation) yang terbuka satu per satu mengikuti gulir, masing-masing dengan deskripsi, tombol "Learn more", dan gambar ilustrasi diam | Disamakan dengan section Services referensi. Rinciannya di [`../ui/design.md`](../ui/design.md) dan [`../ui/motion.md`](../ui/motion.md) |
| Klien dan mitra | Label "Clients", baris "OUR" dengan chip yang berganti antara "Clients" dan "Partners", judul "Clients & Partners", grid logo 3x3 yang **berganti sendiri tiap 5 detik** (dua slide), panah, dan kartu testimoni | Disamakan dengan section Clients referensi, rinciannya di [`../ui/design.md`](../ui/design.md) dan [`../ui/motion.md`](../ui/motion.md). Logo dibaca dari `public/images/landing/clients/` dan `partners/` oleh `LandingClients`. Placeholder dan kartu testimoni hanya di mode contoh |
| Artikel | Tiga artikel terbaru terbit di bahasa itu | Disembunyikan bila belum ada |
| Ajakan | "Have a plating specification in mind?" | Satu tombol `mailto:` |
| Footer | Deskripsi singkat, kontak, hak cipta | Footer L1 diperluas, menu tetap Home |

Teks asli bahasa Inggris dari prototipe dipakai apa adanya sebagai sumber, lalu diterjemahkan ke empat bahasa lain.

## Layar intro

Aturan dari [`../ui/design.md`](../ui/design.md), diwujudkan begini:

1. Markup ada di HTML dan disembunyikan lewat CSS. Skrip kecil di `<head>` menandai `<html>` bila sesi ini belum melihat intro (`sessionStorage`), dan CSS menampilkannya. Tanpa JavaScript, intro tidak tampil.
2. Elemen `role="dialog"` dengan `aria-modal`, fokus dipindahkan ke tombol setelah animasi masuk selesai, dan isi halaman lainnya diberi `inert` selama intro tampil. Klik, Enter, atau Escape menutupnya (pemicu ganda diabaikan), lalu fokus pindah ke konten utama. Bila skrip intro tidak jalan, pengaman 4 detik melepas intro supaya halaman tidak terkunci.
3. Animasi masuk dan keluar memakai GSAP (`resources/js/landing/intro.js`): logo dan tombol memudar masuk, dan saat ditutup seluruh layar memudar lalu hero mulai. Rinciannya di [`../ui/motion.md`](../ui/motion.md). Dengan `prefers-reduced-motion`, isi langsung terlihat dan intro langsung hilang tanpa animasi.
4. Hanya di beranda dan sekali per sesi. Konten beranda sudah ada di HTML di belakangnya.
5. Dampak pada LCP dan INP diukur di L6.

## Aturan keamanan dan kasus tepi

- Tidak ada permintaan pihak ketiga saat halaman dimuat. YouTube baru dipanggil setelah klik.
- Bagian yang bergantung pada konfigurasi (video, kontak) tidak dirender bila kosong, dan tombolnya ikut hilang. Tidak ada tautan ke `mailto:` kosong.
- Semua teks lewat `lang/<locale>/home.php`. Test memastikan kunci identik di kelima bahasa.
- Semua properti tata letak memakai properti logis dan diuji dalam RTL (Arab) serta dengan teks panjang (Indonesia). Dekorasi berarah dibalik di RTL.
- Gambar dekoratif memakai `alt=""`. Tombol putar dan intro punya nama aksesibel per bahasa.
- Kontras tiap pasangan warna baru dicek dengan alat kontras sebelum dipakai.

## Struktur kode

- `config/landing.php`: daftar slug kategori, `intro_video_id`, `contact.email`, `contact.phone`.
- `lang/{en,id,zh,ar,ja}/home.php`: teks beranda.
- `resources/views/landing/home.blade.php` menyusun bagian dari `resources/views/landing/home/*.blade.php` (satu berkas per bagian).
- `resources/views/components/landing/art.blade.php`: memuat aset dari `public/images/landing/` bila ada.
- `resources/js/landing.js` ditambah logika intro dan pemuat video. Tanpa pustaka tambahan.
- Footer di `resources/views/components/landing/footer.blade.php` diperluas.

## Langkah dan test

Langkah: konfigurasi, teks lima bahasa, komponen aset, bagian demi bagian, intro, video, ikon kategori, uji RTL dan ponsel.

Test (feature) yang harus ada:

- Beranda di tiap locale memuat judul hero dalam bahasanya dan `lang`/`dir` yang benar.
- Kunci `lang/*/home.php` identik di kelima bahasa (test kunci yang sudah ada diperluas ke semua berkas landing).
- 13 nama kategori muncul, dan jumlahnya sama dengan `config('landing.categories')`.
- Tanpa `intro_video_id`: bagian video tidak ada. Dengan nilai: poster dan tombol ada, dan tidak ada `iframe` atau URL YouTube di HTML awal.
- Tanpa kontak: tidak ada tautan `mailto:`. Dengan kontak: ada.
- Intro: elemen `role="dialog"` ada dengan tombol tutup berlabel dalam bahasa aktif.
- Komponen aset: tidak merender apa pun bila berkas tidak ada, merender `<img alt="">` bila ada.

Pemeriksaan manual di browser: intro (klik, Enter, Escape, dan sekali per sesi), putar video, lebar ponsel sampai desktop, Arab dalam RTL, dan keterbacaan tiap bagian di kelima bahasa.

## Ditunda

- Tautan kategori, layanan, dan artikel (L3), produk, proses, TDS, dan harga (L4 dan L5).
- Artikel asli, logo klien dan mitra, dan data kontak resmi.
- Widget chat (L7). Di prototipe, tiap produk punya tombol "Ask on WhatsApp", jadi WhatsApp tampaknya jalur permintaan harga dan TDS. Itu dibahas di L5.
- Pengukuran Lighthouse dan dampak intro (L6).
- Terjemahan perlu ditinjau penutur asli.
