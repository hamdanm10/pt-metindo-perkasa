# Kelola Produk (A3)

Admin menambah dan mengubah produk dari dashboard. Untuk saat ini produk hanya punya **nama** dan **deskripsi**. Belum tampil di landing: katalog landing masih memakai kategori di `config/landing.php` sampai fitur kelola konten landing (A5) dirancang. Status fase di [`../implementation-plan.md`](../implementation-plan.md).

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | Hanya admin yang mengelola, di `/app/products`. Staf dan customer mendapat 403, dijaga middleware dan `ProductPolicy` | Keputusan client untuk pengelolaan konten |
| 2 | Kolom: `name` (wajib, maks 255) dan `description` (opsional, maks 5000) | Cukup untuk tahap ini |
| 3 | Satu bahasa saja, belum diterjemahkan ke lima bahasa landing | Rancangan multi-bahasa menunggu konsep konten landing (A5) |
| 4 | Belum ada hapus, kategori, gambar, TDS, atau harga | Ditunda sampai kebutuhannya jelas |
| 5 | Livewire: `Products\Index` dan `Products\Editor` (tambah dan ubah satu komponen), logic di `SaveProduct` dan `ListProducts` | Mengikuti pola A1 dan artikel |

## Halaman

| Halaman | Alamat | Isi |
| --- | --- | --- |
| Daftar | `/app/products` | Nama dan cuplikan deskripsi, pencarian nama (tanpa membedakan huruf), 15 per halaman |
| Tambah | `/app/products/create` | Nama dan deskripsi |
| Ubah | `/app/products/{product}/edit` | Sama dengan Tambah |

## Aturan keamanan dan kasus tepi

- Setiap aksi Livewire memanggil `authorize`. Persistent middleware dari A1 berlaku.
- Id produk di komponen dikunci (`#[Locked]`). Nama dan deskripsi dipangkas spasi, deskripsi kosong disimpan `null`.
- Teks di `lang/{en,id}/products.php`. Kolom `name` dan `description` diberi indeks `name` untuk pencarian dan urutan.

## Test

Akses per peran dan tamu (halaman dan aksi Livewire), menu hanya untuk admin, tambah dan ubah, validasi (wajib, panjang), deskripsi opsional, pencarian, penomoran dan jumlah query tetap, kunci terjemahan identik, serta unit test `SaveProduct`.

## Ditunda

- Terjemahan lima bahasa, hapus, kategori dan proses, gambar, TDS, harga, dan tampil di landing (A5).
