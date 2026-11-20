# Peran Admin

Admin mengelola sistem lewat dashboard di `/app`, bersama staf. Fondasi akun dan dashboard ada di [`../../shared/implementation-plan.md`](../../shared/implementation-plan.md). Dikerjakan berurutan, satu fase satu branch dari `development`, mengikuti `CLAUDE.md`. Status keseluruhan: **rencana, belum ada kode**.

## Fitur

| Fase | Fitur | Dokumen | Branch | Status |
| --- | --- | --- | --- | --- |
| A1 | Kelola akun staf dan customer (dua halaman, admin dibuat lewat perintah): daftar, membuat, mengubah, atur ulang kata sandi, dan nonaktifkan akun | [`user-management/overview.md`](user-management/overview.md) | `feat/user-management` | Selesai, ter-merge ke `development` |
| A2 | Kelola artikel dan berita landing dalam lima bahasa, terbit per bahasa | [`articles/overview.md`](articles/overview.md) | `feat/articles` | Selesai, ter-merge ke `development` |
| A3 | Kelola kategori proses (sebelumnya "produk"): tambah dan ubah, hanya nama dan deskripsi | [`process-categories/overview.md`](process-categories/overview.md) | `feat/products` (diganti nama oleh `refactor/rename-product-to-process-category`) | Selesai, ter-merge ke `development` |
| A4 | Kelola layanan: tambah dan ubah, data bersama (alamat, gambar, urutan, aktif) dan teks lima bahasa, dibaca landing | [`services/overview.md`](services/overview.md) | `feat/services`, `feat/service-fields` | Selesai di kode, menunggu tinjauan |
| A5 | Kelola konten landing lainnya (klien, mitra, video, kalimat beranda, dan tampilan produk dan layanan di landing) | belum ditulis (fase L4 di [`../../landing/implementation-plan.md`](../../landing/implementation-plan.md)) | `feat/landing-content` | Ditunda, isi landing belum final |
| A6 | Kelola chat berbasis aturan di landing: pertanyaan, jawaban, dan pertanyaan lanjutan bersarang per bahasa | [`../../landing/chat/overview.md`](../../landing/chat/overview.md) | `feat/dynamic-chat` | Selesai di kode, menunggu tinjauan |
| A7 | Kelola nomor WhatsApp customer service di landing: nama, nomor, urutan, aktif atau nonaktif | [`../../landing/whatsapp/overview.md`](../../landing/whatsapp/overview.md) | `feat/dynamic-whatsapp` | Selesai di kode, menunggu tinjauan |
| A8 | Master data material: nama, deskripsi per bahasa, brosur, video, tautan olshop, TDS, harga | [`process-content/overview.md`](process-content/overview.md) | `feat/materials` | Rancangan, belum ada kode |
| A9 | Kategori proses diperluas, proses, dan langkah dengan material; impor data awal dari config | [`process-content/overview.md`](process-content/overview.md) | `feat/processes` | Rancangan, belum ada kode |
| A10 | Landing membaca kategori, proses, langkah, dan material dari database | [`process-content/overview.md`](process-content/overview.md) | `feat/landing-processes` | Rancangan, belum ada kode |

## Keputusan

| # | Keputusan | Nilai |
| --- | --- | --- |
| 1 | Akses | Admin memegang semua fitur. Hak staf yang lebih terbatas ditentukan per fitur |
| 2 | Pengelola konten landing | Hanya `admin` |
| 3 | Pengelola akun | Hanya `admin`. Staf tidak bisa membuat atau mengubah akun apa pun |

## Belum diputuskan

- Daftar fitur admin lainnya dan batas hak staf. Dirancang per fitur saat dikerjakan.
