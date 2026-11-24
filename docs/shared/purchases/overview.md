# Catatan Pembelian (D4)

Arsip pembelian yang dilakukan customer atau distributor: siapa pembelinya, produk apa saja yang dibeli beserta jumlahnya, dan berkas pendukung (misalnya faktur atau surat jalan). **Admin dan staf** mencatatnya di dashboard `/app`, dan **customer atau distributor hanya membaca dan mengunduh berkas pembeliannya sendiri** di areanya. Di antarmuka namanya "Purchase Records" (Indonesia: "Catatan Pembelian"); di kode `purchases`. Status fase di [`../implementation-plan.md`](../implementation-plan.md). Fitur ini polanya sama dengan [Tanya Jawab](../inquiries/overview.md).

Rancangan awal karena detail dari client belum lengkap. Harga dan total sengaja belum ada (lihat "Ditunda").

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | Nama: `Purchase` (tabel `purchases`), label "Purchase Records" / "Catatan Pembelian" | Mudah dipahami admin, staf, dan nanti pembeli |
| 2 | **Halaman customer dan distributor dipisah**, seperti Tanya Jawab. Pembeli (`buyer_id`) adalah akun berperan `customer` atau `distributor`. Catatan customer dikelola di `/app/customer-purchases` dan catatan distributor di `/app/distributor-purchases`, masing-masing dengan menu, judul, daftar, dan pilihan pembeli sendiri. Membuka catatan lewat halaman kind lain memberi 404, dan pembeli di formulir harus berperan sesuai halaman | Keputusan client: dua kelompok ditangani terpisah. Satu set komponen dipakai dua kali lewat default route `kind` |
| 2a | **Customer dan distributor hanya baca.** Di `/customer/purchases` dan `/distributor/purchases` pembeli hanya melihat catatan dengan `buyer_id` miliknya dan mengunduh berkasnya. Catatan pembeli lain dijawab **404** (bukan 403) supaya keberadaannya tidak bocor. Nama penambah dan tombol ubah atau hapus tidak tampil | Keputusan client: pembeli menemukan riwayat dan dokumennya sendiri |
| 3 | Admin dan staf **melihat semua** catatan. **Admin mengubah dan menghapus semua. Staf hanya yang ia buat sendiri** (`created_by`); catatan staf lain, atau yang `created_by`-nya kosong (pembuatnya dihapus), tidak bisa diubah staf | Keputusan client |
| 4 | Satu catatan punya **banyak produk**, tiap baris berisi produk (dari master data produk) dan jumlah (bilangan bulat ≥ 1, tanpa satuan). Satu produk hanya boleh sekali per catatan, minimal 1 dan maksimal 50 baris | Cukup untuk mencatat apa yang dibeli; satuan dan harga menunggu kejelasan client |
| 5 | Nama produk **disalin** ke baris (`product_name`) saat disimpan. Bila produk dihapus, `product_id` jadi kosong dan catatan tetap menampilkan nama yang tersimpan | Riwayat pembelian tidak boleh hilang atau memblokir penghapusan produk |
| 6 | Berkas: maksimal **10 per catatan**, **PDF, JPG, PNG**, **5 MB per berkas**. Disimpan di disk **privat** (`local`, `storage/app/private/purchases`) dengan nama acak. Nama asli (dibersihkan) disimpan hanya untuk tampilan dan nama unduhan. Berkas dilayani lewat route yang memeriksa login dan Policy, sebagai unduhan | Faktur bisa memuat data harga dan identitas, jadi tidak boleh lewat URL publik |
| 7 | Isi: `purchased_at` (tanggal, wajib, tidak boleh di masa depan), `invoice_number` (opsional, maks 100 karakter), `notes` (opsional, teks biasa maks 2000 karakter) | Permintaan: tanggal, nomor invoice, dan catatan boleh ditambahkan |

## Data

- `purchases`: `buyer_id` (wajib, relasi ke `users`, tanpa hapus berantai), `purchased_at` (date), `invoice_number` (nullable), `notes` (text, nullable), `created_by` (`nullOnDelete`), cap waktu. Index: gabungan `buyer_id, purchased_at`, `purchased_at`, `invoice_number`, `created_by`.
- `purchase_items`: `purchase_id` (`cascadeOnDelete`, index), `product_id` (nullable, `nullOnDelete`, index), `product_name`, `quantity` (unsigned integer). Unik `purchase_id, product_id`.
- `purchase_files`: `purchase_id` (`cascadeOnDelete`, index), `path`, `name` (nama asli), `mime_type`, `size` (byte), cap waktu. Urutan mengikuti id.

## Halaman

| Halaman | Alamat | Isi |
| --- | --- | --- |
| Daftar | `/app/customer-purchases`, `/app/distributor-purchases` | Pembeli, nomor invoice, tanggal, jumlah produk dan berkas, ditambah oleh. Pencarian di nomor invoice dan nama pembeli (tanpa membedakan huruf), saringan pembeli, terbaru dulu, 15 per halaman. Tombol Edit dan Delete muncul sesuai Policy |
| Baca | `/app/customer-purchases/{id}`, `/app/distributor-purchases/{id}` | Pembeli, tanggal, invoice, catatan, tabel produk dan jumlah, daftar berkas dengan tombol unduh, penambahnya |
| Tambah | `/app/customer-purchases/create`, `/app/distributor-purchases/create` | Customer atau Distributor\* (pilihan akun sesuai halaman), tanggal\*, nomor invoice, catatan, baris produk (dropdown pencarian produk dan jumlah, bisa tambah dan hapus baris), unggah banyak berkas |
| Ubah | `/app/customer-purchases/{id}/edit`, `/app/distributor-purchases/{id}/edit` | Sama dengan Tambah. Berkas tersimpan bisa ditandai untuk dihapus (bisa dibatalkan sebelum simpan), berkas baru bisa ditarik sebelum simpan |
| Daftar dan baca pembeli | `/customer/purchases`, `/customer/purchases/{id}`, `/distributor/purchases`, `/distributor/purchases/{id}` | Sama seperti daftar dan baca staf, tanpa kolom pembeli, saringan pembeli, penambah, dan tombol ubah atau hapus. Pencarian di nomor invoice. Catatan pembeli lain: 404 |
| Berkas | `/app/purchase-files/{id}`, `/customer/purchase-files/{id}`, `/distributor/purchase-files/{id}` | Mengirim berkas sebagai unduhan. Guest diarahkan ke login, peran yang salah mendapat 403, berkas milik pembeli lain 404 |

- Hapus lewat dialog konfirmasi situs. Menghapus catatan ikut menghapus berkasnya dari disk.
- Menu staf: grup "Sales" / "Penjualan", dropdown "Purchases" / "Pembelian" berisi "Customer purchases" / "Pembelian Customer" dan "Distributor purchases" / "Pembelian Distributor" (ikon `document`) untuk admin dan staf, seperti dropdown Q&A. Customer dan distributor mendapat satu item "Purchases" / "Pembelian" di grup yang sama.
- Teks di `lang/{en,id}/purchases.php` (judul dan label per kind di `kinds`), `dashboard.nav.purchases` (area pembeli), `dashboard.nav.customer_purchases`, `dashboard.nav.distributor_purchases`, dan `dashboard.groups.sales`.

## Struktur kode

- Model `Purchase`, `PurchaseItem`, `PurchaseFile`. `PurchasePolicy`.
- Action di `app/Actions/Purchases/`: `ListPurchases`, `SavePurchase` (menyimpan catatan, mengganti baris produk, dan menambah dan menghapus berkas dalam satu transaksi; berkas baru dibersihkan bila gagal, berkas lama dihapus setelah berhasil), `DeletePurchase`. Service `PurchaseFileStore`.
- Livewire di `app/Livewire/Purchases/` (`Index`, `Show`, `Editor`; trait `ScopesPurchaseKind` mengambil `kind` dari route) dan form `app/Livewire/Forms/PurchaseForm.php`. `Index` dan `Show` dipakai kedua area (tanpa `kind` untuk pembeli), `Editor` hanya area staf. `PurchaseFileController` melayani unduhan untuk ketiga area. Route staf di `routes/app/purchases.php` (`role:admin,staff`; satu set komponen didaftarkan dua kali dengan default `kind`, seperti halaman Tanya Jawab) dan route pembeli di `routes/client/purchases.php` (dimuat untuk area `customer` dan `distributor`).
- Daftar memuat relasi (`buyer`, `creator`) dan `withCount('items', 'files')` di Action, jadi jumlah query tidak naik bersama jumlah baris.

## Aturan keamanan dan kasus tepi

- Pembatasan per pembeli ada di tiga tempat yang sama-sama memakai `PurchasePolicy::view`: kueri daftar (`ListPurchases` menambahkan `buyer_id` untuk pembeli dan mengabaikan saringan pembeli yang dikirimnya), halaman baca, dan route berkas.
- Setiap aksi Livewire memanggil `authorize`. Id catatan di komponen dikunci (`#[Locked]`). Id berkas yang dikirim untuk dihapus hanya berlaku bila milik catatan itu.
- `buyer_id` harus akun berperan sesuai halaman (`customer` di halaman customer, `distributor` di halaman distributor). Admin dan staf pemilik boleh memindahkan catatan ke pembeli lain saat mengubah.
- Batas 10 berkas dihitung dari berkas tersimpan yang tidak ditandai hapus ditambah berkas baru.
- Bila produk pada sebuah baris sudah dihapus, formulir ubah menampilkan nama yang tersimpan dan meminta produk lain (atau baris dihapus) sebelum bisa disimpan.
- Berkas tidak pernah ada di disk publik, dan nama asli tidak dipakai sebagai jalur penyimpanan.

## Ditunda

- Harga, total, dan satuan produk (menunggu kejelasan client), status pembelian, dan ekspor.

## Test

`tests/Feature/Purchases/PurchaseTest.php`: akses per peran, matriks Policy (staf hanya miliknya, admin semua, pembeli membaca miliknya), halaman dan berkas pembeli (hanya miliknya, 404 untuk pembeli lain, tanpa data staf), pemisahan halaman customer dan distributor (daftar, baca, ubah, dan peran pembeli), membuat dan mengubah (termasuk penggantian baris produk), validasi (pembeli harus berperan sesuai halaman, produk ganda, jumlah, tanggal), berkas (tersimpan privat dengan nama acak, tipe dan ukuran, batas 10, menghapus dan membatalkan), unduhan per peran, pencarian dan saringan, `nullOnDelete` produk, jumlah query tetap, menu, dan teks dua bahasa.
