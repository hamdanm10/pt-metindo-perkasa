# Kelola Konten Proses (A8 sampai A10)

Admin mengelola alur **kategori proses → proses → langkah → produk** dari dashboard, dan landing membacanya dari database (data statis di `config/landing.php` dan `lang` tetap ada sebagai cadangan, lihat bagian Landing). **Produk adalah master data sendiri** (brosur, video, tautan olshop, TDS, harga, penjelasan), dipakai ulang di banyak langkah. Alur di landing tidak berubah: daftar kategori (`/{locale}/processes`), proses dalam satu kategori (`/{locale}/processes/{kategori}`), detail proses (`/{locale}/process/{proses}`) dengan tombol **View diagram** dan bahan yang bisa diklik ([`../../../landing/pages/overview.md`](../../../landing/pages/overview.md)). Perluasan dari A3 ([`../process-categories/overview.md`](../process-categories/overview.md)). Status fase di [`../implementation-plan.md`](../implementation-plan.md).

Istilah: yang semula bernama "material" (bahan di tiap langkah) kini disebut **produk** di dashboard, kode, tabel, dan route. Label "Material" di dialog landing masih teks lama dan diganti "Product" saat A10. Produk di sini bukan kategori proses yang dulu juga bernama "produk" (A3).

## Pembagian pekerjaan

Tiga branch berurutan dari `development`, masing-masing dengan test dan dokumen yang diperbarui.

| Fase | Isi | Branch |
| --- | --- | --- |
| A8 | Master data produk: `/app/products` (daftar, tambah, ubah) | `feat/product-master` |
| A9 | Kategori proses diperluas (slug, urutan, aktif, deskripsi per bahasa), proses, dan langkah (alur diagram dengan produk dari A8). | `feat/processes` |
| A10 | Landing membaca dari database dengan cache. Data statis di config dan `lang` **tidak dihapus dulu**, dipakai sebagai cadangan | `feat/landing-processes` |

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | Hanya admin yang mengelola (middleware dan Policy per model). Staf dan customer mendapat 403 | Keputusan client untuk konten landing |
| 2 | **Nama** kategori, proses, langkah, dan produk satu bahasa saja (Inggris, tampil `lang="en"` di landing seperti sekarang). **Deskripsi** per bahasa di tabel terjemahan (`<tabel>_translations`: kunci asing + `locale` unik), lima bahasa landing dalam tab, **Inggris wajib** sebagai cadangan, bahasa kosong tidak disimpan | Mengikuti pola layanan dan chat. **Tanpa `spatie/laravel-translatable` dan tanpa terjemahan otomatis**: tiap bahasa ditulis admin sendiri supaya kalimatnya natural |
| 3 | `slug` dibuat otomatis dari nama saat ditambahkan dan tidak berubah sesudahnya. Slug kategori dan proses **unik di seluruh tabel** karena URL-nya (`/process/{slug}`) tidak memuat kategori | Alamat landing stabil |
| 4 | Urutan lewat kolom `position` dan tombol Naik/Turun (pola `MoveService`). Nilai kecil lebih dulu. Berlaku untuk kategori, proses dalam satu kategori, dan langkah dalam satu proses | Sama dengan layanan |
| 5 | **Hapus tersedia** untuk kategori, proses, dan produk (tombol Hapus di daftar, hanya admin, dengan konfirmasi; `DeleteProcessCategory`, `DeleteProcess`, `DeleteProduct`). Kategori yang masih punya proses ditolak. Menghapus proses ikut menghapus langkahnya, menghapus produk melepasnya dari semua langkah dan menghapus berkas TDS. Untuk menyembunyikan tanpa menghapus, pakai **aktif/nonaktif** (`is_active`, yang nonaktif tidak tampil di landing). Langkah dihapus lewat form proses | Tidak bisa dibatalkan, jadi ada konfirmasi |
| 6 | **Langkah dikelola di dalam form proses**: tambah, hapus, geser, lalu pilih produk tiap langkah lewat dropdown pencarian `Lookup` (sumber baru `produk`, banyak pilihan). Satu transaksi di `SaveProcess` | Admin tidak perlu halaman terpisah |
| 7 | Satu produk boleh dipakai di banyak langkah (tabel pivot `process_step_product`, pasangan unik, ada `position`). Produk nonaktif disembunyikan dari langkah di landing tetapi tidak dilepas dari pivot | Mudah diaktifkan lagi |
| 8 | **Tanpa impor data awal dan tanpa seeder.** Admin mengisi kategori, proses, langkah, dan produk lewat dashboard. Satu-satunya migrasi data: deskripsi kategori A3 yang sudah ada dipindah ke terjemahan bahasa Inggris. **Data statis di `config/landing.php` dan teks kategori di `lang/*/pages.php` tidak dihapus dulu, dan untuk saat ini landing memakai data statis**: sumbernya diatur `landing.catalog_source` (env `LANDING_CATALOG_SOURCE`, bawaan `static`). Dengan `database`, landing memakai database begitu ada satu kategori aktif dan data statis selama belum ada (semua atau tidak sama sekali, tidak dicampur). Penghapusannya menunggu dashboard dirapikan dan data diisi | Landing tidak kosong selama admin belum mengisi. Aturan proyek: tanpa data contoh |
| 9 | **Gambar kategori wajib, diunggah admin** (wajib sampai kategori punya gambar, lalu boleh dibiarkan saat ubah; kategori lama tanpa gambar harus mengunggah saat diubah berikutnya. JPG, PNG, atau WebP, maks 2 MB, minimal 800x600, dipotong 4:3 dan disimpan WebP di disk `public` dengan nama acak lewat `ProcessCategoryImageStore`, file lama dihapus saat diganti). Dipakai di daftar proses (panel 16:8), halaman kategori, dan kartu katalog beranda. Tanpa gambar unggahan, landing memakai berkas statis `public/images/landing/photos/product-<slug>` dan cadangan yang sudah ada. Pada mode landing `static`, gambar kategori database dipakai untuk kategori statis yang slug atau nama-slug-nya sama, dan hanya dari kategori aktif | Kotak gambar di landing yang tadinya kosong terisi dari dashboard |

## Data

- `products`: `name` (unik, maks 255), `slug` (unik), `brochure_url`, `video_url` (maks 2048, harus `https`), `price` (rupiah bulat, boleh kosong), `tds_path` (PDF maks 5 MB di disk `public`, nama acak), `is_active`. Terjemahan: `description` (penjelasan penggunaan, maks 2000).
- `product_links`: `product_id`, `label` (nama olshop, contoh Tokopedia), `url` (`https`), `position`. Tautan e-commerce bisa lebih dari satu.
- `process_categories` (sudah ada): tambah `slug` (unik), `position`, `is_active`, `image_path`. `description` dipindah ke `process_category_translations` bahasa Inggris oleh migration lalu kolomnya dihapus, dan `slug` diisi dari nama.
- `processes`: `process_category_id` (index, `restrictOnDelete`), `name` (unik), `slug` (unik), `position`, `is_active`. Terjemahan di `process_translations`: `description`.
- `process_steps`: `process_id` (`cascadeOnDelete`), `name`, `position`. Terjemahan di `process_step_translations`: `description` (opsional, maks 1000, tidak disimpan bila kosong).
- `process_step_product`: `process_step_id`, `product_id`, `position`, pasangan unik.
- Index pada kolom yang dicari atau diurutkan: `name`, `slug`, `position`, `locale`, dan kunci asing.

## Halaman

| Halaman | Alamat | Isi |
| --- | --- | --- |
| Produk | `/app/products`, `/create`, `/{product}/edit` | Daftar (nama, slug, lima badge bahasa, status, pencarian nama, 15 per halaman; kolom jumlah langkah yang memakai). Form: data bersama, tautan olshop (tambah/hapus baris, maks 10, baris kosong diabaikan), unggah TDS (bisa dihapus), tab bahasa. Slug dibuat dari nama saat ditambahkan dan tidak berubah |
| Kategori proses | `/app/process-categories` (sudah ada) | Ditambah urutan, status, dan tab bahasa untuk deskripsi |
| Proses | `/app/processes`, `/create`, `/{process}/edit` | Daftar dengan saringan kategori dan pencarian. Form: nama, kategori (dropdown pencarian), status, editor langkah (nama, geser naik/turun, hapus, produk lewat dropdown pencarian yang menambah ke daftar chip), dan tab bahasa. Tiap tab memuat deskripsi proses dan deskripsi tiap langkah dalam bahasa itu. Tombol Naik/Turun di daftar menggeser proses di dalam kategorinya |

Menu ketiganya di grup konten, induk `catalog`, hanya untuk admin.

## Landing (A10)

- `LandingProcesses` membaca kategori, proses, langkah, dan produk aktif dengan `with()` (jumlah query tetap, tidak naik bersama jumlah baris), di-cache per locale selamanya, dan dibersihkan tiap admin menyimpan produk, proses, atau kategori, atau menggeser kategori atau proses. `LandingContent` memakainya hanya bila `landing.catalog_source` bernilai `database` (bawaan `static`), dan jatuh ke data statis (`config/landing.php`) bila tidak ada kategori aktif.
- Teks tanpa terjemahan di bahasa itu memakai bahasa Inggris. Deskripsi proses yang kosong memakai kalimat umum ("`<nama>` process flow."), dan deskripsi kategori yang kosong tidak ditampilkan.
- Route kategori menerima slug apa saja (huruf kecil, angka, tanda hubung). Slug yang tidak ada atau nonaktif menjadi 404. Kategori nonaktif menyembunyikan prosesnya (404 di halaman detail), proses nonaktif hilang dari daftar dan "proses lain", produk nonaktif hilang dari langkah.
- Dialog produk di halaman detail: penjelasan (cadangan: kalimat umum seperti sebelumnya), brosur, video, tautan olshop (satu tautan per toko, `target="_blank"`, `rel="noopener noreferrer"`), TDS, dan harga ("Rp 125.000"). Brosur, video, dan olshop kosong tampil "–". TDS atau harga kosong tetap menautkan ke halaman kontak ("Request to admin") sampai L5. Data dikirim sebagai JSON di atribut tombol (di-escape Blade), dan JavaScript membuat elemen dengan `textContent`, hanya menerima tautan `https` atau berkas situs sendiri.
- Label landing "Materials / Material detail" diganti "Products / Product detail" di lima bahasa (kunci `pages.process.materials` dan `material_detail` tetap), dan ada kunci baru `pages.process.open`.
- Beranda menampilkan empat kategori pertama menurut `position`. Kategori tanpa proses aktif tetap muncul dengan jumlah 0. Gambar kategori dari `image_path` (lihat keputusan 9), dengan berkas statis `product-<slug>` sebagai cadangan. Cache gambar untuk mode statis (`LandingProcesses::images()`) dibersihkan bersama katalog.

## Aturan keamanan dan kasus tepi

- Setiap aksi Livewire memanggil `authorize`. Id di komponen dikunci (`#[Locked]`). Isian dipangkas, teks kosong disimpan `null`.
- URL hanya `https`. Tautan olshop dan video ditampilkan sebagai tautan keluar, bukan di-embed.
- TDS diperiksa jenis (PDF) dan ukuran. Berkas lama dihapus saat diganti.
- Langkah tanpa produk dan proses tanpa langkah tetap valid. Landing menampilkan "belum ada langkah" dan menyembunyikan tombol diagram, seperti sekarang.
- Kategori nonaktif menyembunyikan proses di dalamnya dari landing. Proses nonaktif hilang dari daftar kategori dan "proses lain".
- Memindahkan proses ke kategori lain menempatkannya di akhir kategori tujuan.
- Teks di `lang/{en,id}/`, nama kolom lewat `validationAttributes()` form.

## Test

- **A8:** akses per peran dan tamu, tambah/ubah, validasi (nama unik, URL `https`, ukuran dan jenis PDF), tautan olshop (tambah, hapus, urutan), bahasa Inggris wajib, jumlah query datar saat baris bertambah.
- **A9:** kategori dengan terjemahan, proses dan langkah (tambah, hapus, geser, produk per langkah), pindah kategori, slug unik lintas tabel, migration deskripsi kategori lama ke terjemahan bahasa Inggris, jumlah query datar.
- **A10:** data statis dipakai bila belum ada kategori aktif dan tidak dicampur bila sudah ada, halaman landing per locale dari database, cadangan `en`, nonaktif disembunyikan, 404 untuk slug tidak dikenal, dialog produk memuat tautan dan harga, teks di-escape, cache dibersihkan saat admin menyimpan atau menggeser, jumlah query dingin dan hangat datar.

## Ditunda

- Nama yang diterjemahkan. Seret dan lepas. `<title>` dan meta per proses. Permintaan harga dan unduhan TDS lewat formulir (L5). Gambar atau diagram unggahan per langkah.

## Catatan A9

- Dropdown pencarian `Lookup` punya sumber baru `product`, dan sumber ini (seperti akun) hanya untuk area staf.
- Langkah disimpan dengan cara diganti seluruhnya tiap proses disimpan (hapus lalu buat ulang), jadi id langkah tidak stabil. Tidak ada yang menautkan ke id langkah.
- Deskripsi langkah boleh kosong di semua bahasa (tidak ada cadangan Inggris yang diwajibkan), berbeda dari deskripsi proses.
