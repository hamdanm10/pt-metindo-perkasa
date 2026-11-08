# Peran Admin

Admin mengelola sistem lewat dashboard di `/app`, bersama staf. Fondasi akun dan dashboard ada di [`../../shared/implementation-plan.md`](../../shared/implementation-plan.md). Dikerjakan berurutan, satu fase satu branch dari `development`, mengikuti `CLAUDE.md`. Status keseluruhan: **rencana, belum ada kode**.

## Fitur

| Fase | Fitur | Dokumen | Branch | Status |
| --- | --- | --- | --- | --- |
| A1 | Kelola akun: daftar, membuat, mengubah, atur ulang kata sandi, dan nonaktifkan akun | [`user-management/overview.md`](user-management/overview.md) | `feat/user-management` | Selesai, ter-merge ke `development` |
| A2 | Kelola artikel dan berita landing dalam lima bahasa, terbit per bahasa | [`articles/overview.md`](articles/overview.md) | `feat/articles` | Selesai, ter-merge ke `development` |
| A3 | Kelola produk: tambah dan ubah, hanya nama dan deskripsi | [`products/overview.md`](products/overview.md) | `feat/products` | Selesai, ter-merge ke `development` |
| A4 | Kelola layanan: tambah dan ubah, hanya nama dan deskripsi | [`services/overview.md`](services/overview.md) | `feat/services` | Selesai di kode, menunggu tinjauan |
| A5 | Kelola konten landing lainnya (kategori, klien, mitra, video, kalimat beranda, dan tampilan produk dan layanan di landing) | belum ditulis (fase L4 di [`../../landing/implementation-plan.md`](../../landing/implementation-plan.md)) | `feat/landing-content` | Ditunda, isi landing belum final |

## Keputusan

| # | Keputusan | Nilai |
| --- | --- | --- |
| 1 | Akses | Admin memegang semua fitur. Hak staf yang lebih terbatas ditentukan per fitur |
| 2 | Pengelola konten landing | Hanya `admin` |
| 3 | Pengelola akun | Hanya `admin`. Staf tidak bisa membuat atau mengubah akun apa pun |

## Belum diputuskan

- Daftar fitur admin lainnya dan batas hak staf. Dirancang per fitur saat dikerjakan.
