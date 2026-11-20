# Kelola Kategori Proses (A3)

Admin menambah dan mengubah kategori proses dari dashboard. Untuk saat ini kategori proses hanya punya **nama** dan **deskripsi**. Belum tampil di landing: katalog landing masih memakai kategori di `config/landing.php` sampai fitur kelola konten landing (A5) dirancang. Status fase di [`../implementation-plan.md`](../implementation-plan.md).

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | Hanya admin yang mengelola, di `/app/process-categories`. Staf dan customer mendapat 403, dijaga middleware dan `ProcessCategoryPolicy` | Keputusan client untuk pengelolaan konten |
| 2 | Kolom: `name` (wajib, maks 255) dan `description` (opsional, maks 5000) | Cukup untuk tahap ini |
| 3 | Satu bahasa saja, belum diterjemahkan ke lima bahasa landing | Rancangan multi-bahasa menunggu konsep konten landing (A5) |
| 4 | Belum ada hapus, kategori, gambar, TDS, atau harga | Ditunda sampai kebutuhannya jelas |
| 5 | Livewire: `ProcessCategories\Index` dan `ProcessCategories\Editor` (tambah dan ubah satu komponen), logic di `SaveProcessCategory` dan `ListProcessCategories` | Mengikuti pola A1 dan artikel |

## Halaman

| Halaman | Alamat | Isi |
| --- | --- | --- |
| Daftar | `/app/process-categories` | Nama dan cuplikan deskripsi, pencarian nama (tanpa membedakan huruf), 15 per halaman |
| Tambah | `/app/process-categories/create` | Nama dan deskripsi |
| Ubah | `/app/process-categories/{processCategory}/edit` | Sama dengan Tambah |

## Aturan keamanan dan kasus tepi

- Setiap aksi Livewire memanggil `authorize`. Persistent middleware dari A1 berlaku.
- Id kategori proses di komponen dikunci (`#[Locked]`). Nama dan deskripsi dipangkas spasi, deskripsi kosong disimpan `null`.
- Teks di `lang/{en,id}/process_categories.php`. Kolom `name` dan `description` diberi indeks `name` untuk pencarian dan urutan.

## Test

Akses per peran dan tamu (halaman dan aksi Livewire), menu hanya untuk admin, tambah dan ubah, validasi (wajib, panjang), deskripsi opsional, pencarian, penomoran dan jumlah query tetap, kunci terjemahan identik, serta unit test `SaveProcessCategory`.

## Ditunda

- Terjemahan lima bahasa, hapus, kategori dan proses, gambar, TDS, harga, dan tampil di landing (A5).

## Perubahan nama

Fitur ini semula bernama "Produk" (tabel `products`). Sejak 2026-11-20 namanya **kategori proses**: tabel `process_categories`, model `ProcessCategory`, URL `/app/process-categories`, kolom `inquiries.process_category_id`. Migration `rename_products_to_process_categories` mengganti nama tabel dan kolom tanpa menghapus data.

Perluasan (slug, urutan, status, deskripsi per bahasa, proses, langkah, material) dirancang di [`../process-content/overview.md`](../process-content/overview.md).
