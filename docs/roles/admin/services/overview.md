# Kelola Layanan (A4)

Admin menambah dan mengubah layanan perusahaan dari dashboard. Untuk saat ini layanan hanya punya **nama** dan **deskripsi**, dengan pola yang sama seperti kelola produk ([`../products/overview.md`](../products/overview.md)). Belum tampil di landing: halaman Services masih memakai teks tetap sampai fitur kelola konten landing (A5) dirancang. Status fase di [`../implementation-plan.md`](../implementation-plan.md).

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | Hanya admin yang mengelola, di `/app/services`. Staf dan customer mendapat 403, dijaga middleware dan `ServicePolicy` | Keputusan client untuk pengelolaan konten |
| 2 | Kolom: `name` (wajib, maks 255) dan `description` (opsional, maks 5000) | Cukup untuk tahap ini |
| 3 | Satu bahasa saja, belum diterjemahkan ke lima bahasa landing | Menunggu konsep konten landing (A5) |
| 4 | Belum ada hapus, gambar, urutan, atau tampil di landing | Ditunda sampai kebutuhannya jelas |
| 5 | Livewire `Services\Index` dan `Services\Editor`, logic di `SaveService` dan `ListServices` | Pola yang sama dengan produk |

## Halaman

| Halaman | Alamat | Isi |
| --- | --- | --- |
| Daftar | `/app/services` | Nama dan cuplikan deskripsi, pencarian nama, 15 per halaman |
| Tambah | `/app/services/create` | Nama dan deskripsi |
| Ubah | `/app/services/{service}/edit` | Sama dengan Tambah |

## Aturan keamanan dan kasus tepi

- Setiap aksi Livewire memanggil `authorize`, dan persistent middleware dari A1 berlaku.
- Id layanan di komponen dikunci. Nama dan deskripsi dipangkas spasi, deskripsi kosong disimpan `null`.
- Teks di `lang/{en,id}/services.php`. Kolom `name` diberi indeks untuk pencarian dan urutan.
- Nama `app.services.*` tidak bentrok dengan `landing.services` karena prefix route berbeda.

## Test

Sama dengan produk: akses per peran dan tamu, menu hanya untuk admin, tambah dan ubah, validasi, deskripsi opsional, pencarian, penomoran dan jumlah query tetap, kunci terjemahan identik, dan unit test `SaveService`.

## Ditunda

- Terjemahan lima bahasa, hapus, gambar, urutan tampil, dan tampil di landing (A5).
