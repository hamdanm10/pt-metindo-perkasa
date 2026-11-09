# Gerak Landing

Animasi landing disederhanakan (redesign Nov 2026): satu gerak naik-memudar untuk semua elemen, tanpa pemisahan teks, paralaks, atau efek berlapis. Dial **MOTION 1**. Desain umum: [`design.md`](design.md).

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | GSAP (core dan ScrollTrigger) dari paket `gsap`, ikut `landing.js`. SplitText tidak dipakai lagi | Gerak sederhana tidak butuh pemisahan baris |
| 2 | Hanya `transform` dan `opacity` (`y`, `autoAlpha`) | Tidak memicu tata letak ulang |
| 3 | Semua gerak sekali (`once`), dipicu muat halaman atau gulir. Tidak ada gerak berulang, paralaks, atau pin | Tenang dan terbaca |
| 4 | `prefers-reduced-motion: reduce`: tanpa animasi, semua konten langsung terlihat | Aksesibilitas |
| 5 | Konten selalu ada di HTML. Keadaan awal disembunyikan lewat kelas `anim-hide` yang dilepas setelah GSAP siap, dengan pengaman 3,5 detik | Terbaca mesin pencari dan tidak ada halaman kosong bila skrip gagal |
| 6 | `<main>` memakai `overflow-x: clip` | Mencegah halaman bergeser ke samping di ponsel |

## Daftar gerak

| Atribut | Tempat | Gerak |
| --- | --- | --- |
| `hero-title`, `hero-item` | Judul, paragraf, tombol, dan panel hero, serta kepala halaman | Naik dan muncul berurutan saat muat. Di beranda menunggu intro selesai |
| `heading`, `card` | Judul bagian, blok ajakan | Naik dan muncul saat terlihat |
| `rows`, `steps` | Daftar kartu, baris layanan, langkah | Anak-anaknya muncul berurutan (batch) |
| `data-intro-item` | Layar intro | Logo dan tombol memudar masuk. Menutup: seluruh layar memudar |

Header, footer, menu, dan teks isi tidak dianimasikan GSAP. Peristiwa `intro:closed` dikirim saat intro selesai (atau langsung bila reduced motion).

## Struktur kode

- `resources/js/landing/intro.js`: gerak intro, `inert` pada isi halaman, fokus ke tombol, dan `sessionStorage`. Klik, Enter, dan Escape menutupnya.
- `resources/js/landing/motion.js`: gerak halaman, dibungkus `gsap.matchMedia()` untuk reduced motion.
- `resources/js/landing.js`: mengimpor keduanya, ditambah menu, dropdown, dan pemutar video.
- `resources/css/landing.css`: `.anim-hide [data-anim] { visibility: hidden }` dan penyembunyian isi intro sampai GSAP siap.
