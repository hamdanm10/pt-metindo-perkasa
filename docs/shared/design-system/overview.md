# Sistem Desain

Fondasi visual yang dipakai landing dan dashboard: warna, tipografi, bentuk, dukungan RTL dan multi-skrip, serta aksesibilitas. Komponen dan tata letak khusus ada di `docs/landing/ui/` dan `docs/roles/admin/ui/`. Status ada di indeks area masing-masing.

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | **Merah krimson** adalah warna merek dominan | Logo (`docs/references/logo.svg`) dan prototipe berwarna merah. Palet "energetic" yang berbasis biru hanya diambil sebagian |
| 2 | Teks gelap `#1B1F3B` dan latar lavender `#F3F2FF` dari palet dipakai sebagai netral | Kontras tinggi dan cocok dengan merah |
| 3 | Biru dan indigo dari palet menjadi aksen kedua, bukan dominan | Supaya tidak berbenturan dengan logo merah |
| 4 | Coral `#FF5A5F` tidak dipakai | Terlalu dekat dengan merah merek, dan kontras putih di atasnya hanya 3,05 |
| 5 | Landing tidak kaku: bentuk organik dan aset vektor buatan Recraft | Permintaan client |
| 6 | Satu fondasi (warna, tipografi, jarak) untuk landing dan dashboard | Komponen dibedakan: landing lega dan ekspresif, dashboard padat dan jelas |

## Warna

Token dipakai lewat tema Tailwind 4 (`@theme` di `resources/css/app.css`), bukan nilai hex di view.

| Token | Nilai | Dipakai untuk |
| --- | --- | --- |
| `brand-600` | `#D6283A` | Warna merek, tombol utama, sama dengan logo |
| `brand-700` | `#B01022` | Teks merah di atas latar terang, hover tombol |
| `brand-50` sampai `brand-300` | skala dari prototipe (`#fef2f3` sampai `#f7a3ab`) | Latar lembut, garis, ilustrasi |
| `ink` | `#1B1F3B` | Teks utama, latar gelap (footer, hero gelap) |
| `lavender` | `#F3F2FF` | Latar bagian selang-seling |
| `muted` | `#545D61` | Teks sekunder |
| `accent-blue` | `#1F4FB5` | Tautan, status informasi |
| `accent-indigo` | `#6D5BD0` | Dekorasi dan ilustrasi, bukan teks kecil |

Skala `brand-100`, `brand-400`, `brand-500`, `brand-800` dan netral lain difinalkan saat implementasi, mengikuti skala prototipe sebagai titik awal.

### Kontras (WCAG, rasio terhitung)

| Pasangan | Rasio | Hasil |
| --- | --- | --- |
| Putih di atas `brand-600` | 4,97 | Lulus AA untuk teks biasa |
| Putih di atas `brand-700` | 7,14 | Lulus AAA |
| `ink` di atas putih | 16,08 | Lulus AAA |
| `ink` di atas `lavender` | 14,52 | Lulus AAA |
| `brand-600` di atas `lavender` | 4,75 | Lulus AA, tipis |
| `muted` di atas `lavender` | 6,08 | Lulus AA |
| Putih di atas `accent-blue` | 7,38 | Lulus AAA |
| Putih di atas `accent-indigo` | 5,18 | Lulus AA |
| `brand-600` di atas `ink` | 3,23 | **Gagal untuk teks**, hanya untuk elemen besar atau grafis |

Aturan: teks merah di atas latar terang memakai `brand-700`. Di atas latar `ink`, teks memakai putih atau `brand-300`, bukan `brand-600`. Cek ulang tiap menambah pasangan warna baru.

## Tipografi

- Latin (`en`, `id`): **Figtree**, sama dengan prototipe.
- Mandarin (`zh`): Noto Sans SC. Arab (`ar`): Noto Sans Arabic. Jepang (`ja`): Noto Sans JP.
- Font dipasang sendiri (self-host), bukan dari CDN pihak ketiga, supaya cepat dan tidak mengirim data pengunjung ke luar.
- Hanya font untuk locale aktif yang dimuat. Font CJK besar, jadi dipakai subset atau `unicode-range`, dengan `font-display: swap`.
- Tumpukan font diatur per locale lewat atribut `lang` pada `<html>`. Jangan memaksa satu font untuk semua bahasa.
- Tinggi baris Arab dan CJK perlu lebih longgar daripada Latin. Nilainya ditentukan di tema dan diperiksa langsung di tiap bahasa.

## RTL dan teks panjang

- Arab ditampilkan RTL: `<html dir="rtl">` diatur dari locale aktif.
- Pakai properti logis Tailwind (`ms-*`, `me-*`, `ps-*`, `pe-*`, `start-*`, `end-*`, `text-start`, `text-end`). Jangan memakai `ml-*`, `mr-*`, `left-*`, `right-*`, `text-left`, atau `text-right` untuk tata letak.
- Ikon dan ilustrasi yang punya arah (panah, gelombang yang bergerak) dibalik di RTL dengan varian `rtl:`.
- Komponen tidak boleh bergantung pada panjang teks tertentu. Indonesia dan Arab biasanya lebih panjang dari Inggris, sedangkan Mandarin dan Jepang lebih pendek. Uji tiap komponen dengan kelima bahasa.

## Bentuk dan gerak

- Sudut membulat dan bentuk organik, bukan kotak dengan garis tepi tegas. Pembatas antarbagian boleh melengkung.
- Tata letak tidak harus simetris. Gambar dan ilustrasi boleh bertumpuk atau keluar dari kolom, selama teks tetap terbaca dan tidak tertutup.
- Gerak memakai GSAP seperti prototipe. Semua animasi menghormati `prefers-reduced-motion`.

## Aksesibilitas

- Kontras mengikuti tabel di atas.
- Semua elemen interaktif bisa dipakai lewat keyboard dengan fokus yang terlihat.
- Gambar dekoratif (aset vektor) memakai `alt=""` atau `aria-hidden="true"`. Gambar yang membawa informasi punya `alt` per bahasa.
- Teks tidak boleh ada di dalam gambar. Teks selalu berupa HTML supaya bisa diterjemahkan dan dibaca mesin pencari.

## Ditunda

- Mode gelap (belum diputuskan).
- Sistem desain dashboard (komponen, tabel, form).
- Skala `brand` dan netral final, ditentukan saat implementasi.
