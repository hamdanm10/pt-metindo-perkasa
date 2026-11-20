# Kelola Konten Proses (A8 sampai A10)

Admin mengelola alur **kategori proses → proses → langkah → material** dari dashboard, dan landing membacanya dari database (menggantikan `config/landing.php` dan teks di `lang`). **Material adalah master data sendiri** (brosur, video, tautan olshop, TDS, harga, penjelasan), dipakai ulang di banyak langkah. Alur di landing tidak berubah: daftar kategori (`/{locale}/processes`), proses dalam satu kategori (`/{locale}/processes/{kategori}`), detail proses (`/{locale}/process/{proses}`) dengan tombol **View diagram** dan bahan yang bisa diklik ([`../../../landing/pages/overview.md`](../../../landing/pages/overview.md)). Perluasan dari A3 ([`../process-categories/overview.md`](../process-categories/overview.md)). Status fase di [`../implementation-plan.md`](../implementation-plan.md).

## Pembagian pekerjaan

Tiga branch berurutan dari `development`, masing-masing dengan test dan dokumen yang diperbarui.

| Fase | Isi | Branch |
| --- | --- | --- |
| A8 | Master data material: `/app/materials` (daftar, tambah, ubah) | `feat/materials` |
| A9 | Kategori proses diperluas (slug, urutan, aktif, deskripsi per bahasa), proses, dan langkah (alur diagram dengan material dari A8). | `feat/processes` |
| A10 | Landing membaca dari database dengan cache, menghapus data statis di config dan `lang` | `feat/landing-processes` |

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | Hanya admin yang mengelola (middleware dan Policy per model). Staf dan customer mendapat 403 | Keputusan client untuk konten landing |
| 2 | **Nama** kategori, proses, langkah, dan material satu bahasa saja (Inggris, tampil `lang="en"` di landing seperti sekarang). **Deskripsi** per bahasa di tabel terjemahan (`<tabel>_translations`: kunci asing + `locale` unik), lima bahasa landing dalam tab, **Inggris wajib** sebagai cadangan, bahasa kosong tidak disimpan | Mengikuti pola layanan dan chat. **Tanpa `spatie/laravel-translatable` dan tanpa terjemahan otomatis**: tiap bahasa ditulis admin sendiri supaya kalimatnya natural |
| 3 | `slug` dibuat otomatis dari nama saat ditambahkan dan tidak berubah sesudahnya. Slug kategori dan proses **unik di seluruh tabel** karena URL-nya (`/process/{slug}`) tidak memuat kategori | Alamat landing stabil |
| 4 | Urutan lewat kolom `position` dan tombol Naik/Turun (pola `MoveService`). Nilai kecil lebih dulu. Berlaku untuk kategori, proses dalam satu kategori, dan langkah dalam satu proses | Sama dengan layanan |
| 5 | Tanpa hapus untuk kategori, proses, dan material: pakai **aktif/nonaktif** (`is_active`). Yang nonaktif tidak tampil di landing. Langkah boleh dihapus karena hanya bagian dari proses | Riwayat tautan aman. Hapus ditunda |
| 6 | **Langkah dikelola di dalam form proses**: tambah, hapus, geser, lalu pilih material tiap langkah lewat dropdown pencarian `Lookup` (sumber baru `material`, banyak pilihan). Satu transaksi di `SaveProcess` | Admin tidak perlu halaman terpisah |
| 7 | Satu material boleh dipakai di banyak langkah (tabel pivot `process_step_material`, pasangan unik, ada `position`). Material nonaktif disembunyikan dari langkah di landing tetapi tidak dilepas dari pivot | Mudah diaktifkan lagi |
| 8 | **Tanpa impor data awal dan tanpa seeder.** Admin mengisi kategori, proses, langkah, dan material lewat dashboard. Satu-satunya migrasi data: deskripsi kategori A3 yang sudah ada dipindah ke terjemahan bahasa Inggris. Saat A10 selesai, data statis di `config/landing.php` dan teks kategori di `lang/*/pages.php` dihapus, dan landing tidak menampilkan kategori atau proses (menu, bagian beranda, halaman) sampai ada data aktif | Aturan proyek: tanpa data contoh. Sama dengan layanan (A4) |
| 9 | Foto kategori tetap berkas statis `public/images/landing/photos/product-<slug>` dengan cadangan yang sudah ada. Unggah foto lewat dashboard ditunda | Cakupan |

## Data

- `materials`: `name` (unik, maks 255), `slug` (unik), `brochure_url`, `video_url` (maks 2048, harus `https`), `price` (rupiah bulat, boleh kosong), `tds_path` (PDF maks 5 MB di disk `public`, nama acak), `is_active`. Terjemahan: `description` (penjelasan penggunaan, maks 2000).
- `material_links`: `material_id`, `label` (nama olshop, contoh Tokopedia), `url` (`https`), `position`. Tautan e-commerce bisa lebih dari satu.
- `process_categories` (sudah ada): tambah `slug`, `position`, `is_active`. `description` dipindah ke terjemahan bahasa Inggris oleh migration lalu kolomnya dihapus.
- `processes`: `process_category_id` (index, `restrictOnDelete`), `name`, `slug` (unik), `position`, `is_active`. Terjemahan: `description`.
- `process_steps`: `process_id` (`cascadeOnDelete`), `name`, `position`. Terjemahan: `description`.
- Index pada kolom yang dicari atau diurutkan: `name`, `slug`, `position`, `locale`, dan kunci asing.

## Halaman

| Halaman | Alamat | Isi |
| --- | --- | --- |
| Material | `/app/materials`, `/create`, `/{material}/edit` | Daftar (nama, slug, lima badge bahasa, status, pencarian nama, 15 per halaman; kolom jumlah langkah yang memakai ditambah di A9). Form: data bersama, tautan olshop (tambah/hapus baris, maks 10, baris kosong diabaikan), unggah TDS (bisa dihapus), tab bahasa. Slug dibuat dari nama saat ditambahkan dan tidak berubah |
| Kategori proses | `/app/process-categories` (sudah ada) | Ditambah urutan, status, dan tab bahasa untuk deskripsi |
| Proses | `/app/processes`, `/create`, `/{process}/edit` | Daftar dengan saringan kategori dan pencarian. Form: nama, kategori, status, tab bahasa, dan editor langkah |

Menu ketiganya di grup konten, induk `catalog`, hanya untuk admin.

## Landing (A10)

- Satu service membaca kategori, proses, langkah, dan material aktif dengan `with()` (tanpa N+1), diberi cache per locale dan dibersihkan tiap admin menyimpan atau menggeser data di fase ini.
- Route kategori tidak lagi dibatasi daftar slug dari config: slug yang tidak ada atau nonaktif menjadi 404.
- Dialog bahan menampilkan penjelasan (cadangan: kalimat umum seperti sekarang bila belum ada), brosur, video, tautan olshop (satu tombol per tautan, `rel="noopener noreferrer"`, `target="_blank"`), TDS, dan harga. TDS atau harga kosong tetap menautkan ke halaman kontak ("Request to admin") sampai L5.
- Tanpa data aktif, menu Processes, bagian katalog beranda, dan halaman prosesnya disembunyikan (pola layanan A4).
- Beranda menampilkan empat kategori pertama menurut `position`. Kategori tanpa proses aktif tetap muncul dengan jumlah 0.

## Aturan keamanan dan kasus tepi

- Setiap aksi Livewire memanggil `authorize`. Id di komponen dikunci (`#[Locked]`). Isian dipangkas, teks kosong disimpan `null`.
- URL hanya `https`. Tautan olshop dan video ditampilkan sebagai tautan keluar, bukan di-embed.
- TDS diperiksa jenis (PDF) dan ukuran. Berkas lama dihapus saat diganti.
- Langkah tanpa material dan proses tanpa langkah tetap valid. Landing menampilkan "belum ada langkah" dan menyembunyikan tombol diagram, seperti sekarang.
- Kategori nonaktif menyembunyikan proses di dalamnya dari landing. Proses nonaktif hilang dari daftar kategori dan "proses lain".
- Memindahkan proses ke kategori lain menempatkannya di akhir kategori tujuan.
- Teks di `lang/{en,id}/`, nama kolom lewat `validationAttributes()` form.

## Test

- **A8:** akses per peran dan tamu, tambah/ubah, validasi (nama unik, URL `https`, ukuran dan jenis PDF), tautan olshop (tambah, hapus, urutan), bahasa Inggris wajib, jumlah query datar saat baris bertambah.
- **A9:** kategori dengan terjemahan, proses dan langkah (tambah, hapus, geser, material per langkah), pindah kategori, slug unik lintas tabel, migration deskripsi kategori lama ke terjemahan bahasa Inggris, jumlah query datar.
- **A10:** halaman landing per locale dari database, cadangan `en`, nonaktif disembunyikan, 404 untuk slug tidak dikenal, dialog bahan memuat tautan, cache dibersihkan saat admin menyimpan, jumlah query datar.

## Ditunda

- Hapus kategori, proses, dan material. Unggah foto kategori. Nama yang diterjemahkan. Seret dan lepas. `<title>` dan meta per proses. Permintaan harga dan unduhan TDS lewat formulir (L5). Gambar atau diagram unggahan per langkah.
