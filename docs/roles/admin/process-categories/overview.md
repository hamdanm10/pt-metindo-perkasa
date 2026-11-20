# Kelola Kategori Proses (A3)

Admin menambah dan mengubah kategori proses dari dashboard. Kategori punya **nama** (satu bahasa), **slug**, **urutan**, **status aktif**, dan **deskripsi per bahasa** (A9, [`../process-content/overview.md`](../process-content/overview.md)). Belum tampil di landing: katalog landing masih memakai kategori di `config/landing.php` sampai A10. Status fase di [`../implementation-plan.md`](../implementation-plan.md).

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | Hanya admin yang mengelola, di `/app/process-categories`. Staf dan customer mendapat 403, dijaga middleware dan `ProcessCategoryPolicy` | Keputusan client untuk pengelolaan konten |
| 2 | Kolom `name` (wajib, unik, maks 255), `slug` (otomatis dari nama saat ditambah, tidak berubah), `position`, `is_active`. Deskripsi (maks 5000) per bahasa di `process_category_translations`, bahasa Inggris wajib | Mengikuti pola layanan |
| 3 | Lima bahasa landing dalam tab, tiap bahasa ditulis admin sendiri. Bahasa tanpa teks tidak disimpan | Aturan teks dinamis landing |
| 4 | Belum ada hapus dan gambar. Urutan lewat tombol Naik/Turun (`MoveProcessCategory`) | Ditunda sampai kebutuhannya jelas |
| 5 | Livewire: `ProcessCategories\Index` dan `ProcessCategories\Editor` (tambah dan ubah satu komponen), logic di `SaveProcessCategory`, `ListProcessCategories`, dan `MoveProcessCategory` | Mengikuti pola A1 dan artikel |

## Halaman

| Halaman | Alamat | Isi |
| --- | --- | --- |
| Daftar | `/app/process-categories` | Nama, slug, lima badge bahasa, jumlah proses, status, tombol Ubah/Naik/Turun, pencarian nama (tanpa membedakan huruf), 15 per halaman |
| Tambah | `/app/process-categories/create` | Nama, status, dan tab lima bahasa untuk deskripsi |
| Ubah | `/app/process-categories/{processCategory}/edit` | Sama dengan Tambah |

## Aturan keamanan dan kasus tepi

- Setiap aksi Livewire memanggil `authorize`. Persistent middleware dari A1 berlaku.
- Id kategori proses di komponen dikunci (`#[Locked]`). Nama dan deskripsi dipangkas spasi, bahasa tanpa teks tidak disimpan.
- Teks di `lang/{en,id}/process_categories.php`. Kolom `slug` unik, `position` dan `locale` diberi indeks, dan daftar memuat terjemahan lewat `with()` serta jumlah proses lewat `withCount()`.

## Test

Akses per peran dan tamu (halaman dan aksi Livewire), menu hanya untuk admin, tambah dan ubah, validasi (wajib, unik, panjang, bahasa Inggris wajib), slug dan urutan, geser naik/turun, pencarian, penomoran dan jumlah query tetap, kunci terjemahan identik, serta unit test `SaveProcessCategory`. Migration perluasan memindahkan deskripsi lama ke terjemahan bahasa Inggris dan mengisi slug dari nama.

## Ditunda

- Hapus, gambar, dan tampil di landing (A10).

## Perubahan nama

Fitur ini semula bernama "Produk" (tabel `products`). Sejak 2026-11-20 namanya **kategori proses**: tabel `process_categories`, model `ProcessCategory`, URL `/app/process-categories`, kolom `inquiries.process_category_id`. Migration `rename_products_to_process_categories` mengganti nama tabel dan kolom tanpa menghapus data.

Perluasan (slug, urutan, status, deskripsi per bahasa, proses, langkah, produk) dirancang di [`../process-content/overview.md`](../process-content/overview.md).
