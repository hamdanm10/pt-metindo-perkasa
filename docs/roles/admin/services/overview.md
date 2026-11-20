# Kelola Layanan (A4)

Admin menambah dan mengubah layanan perusahaan dari dashboard. Layanan punya **data bersama** (nama internal, alamat, gambar, urutan, aktif) dan **teks per bahasa** (judul, ringkasan, deskripsi, daftar yang termasuk, cocok untuk). Field ini disiapkan agar landing bisa membaca layanan dari database. **Landing membaca data ini**: beranda, daftar, dan detail layanan ([`../../landing/pages/overview.md`](../../landing/pages/overview.md)). Hanya layanan aktif dengan slug dan teks `en` yang tampil, urut `position`, dan bahasa tanpa teks memakai teks `en`. Cache landing dibersihkan tiap admin menyimpan. Status fase di [`../implementation-plan.md`](../implementation-plan.md).

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | Hanya admin yang mengelola, di `/app/services`. Staf dan customer mendapat 403, dijaga middleware dan `ServicePolicy` | Keputusan client untuk pengelolaan konten |
| 2 | Data bersama di tabel `services`: `name` (wajib, maks 255, dilihat staf di daftar dan dropdown pencarian, tidak tampil di landing), `slug`, `image_path`, `position` (angka kecil lebih dulu), `is_active`. **Form hanya menampilkan `name` (wajib, maks 255, unik), gambar, aktif, dan tab bahasa**: `slug` dibuat otomatis dari `name` (`Str::slug`) saat layanan ditambahkan dan tidak berubah saat nama diubah (supaya tautan lama tetap hidup), kecuali masih kosong (layanan lama), dan `position` otomatis diletakkan paling akhir, lalu diatur lewat tombol Naik dan Turun di daftar | Alamat dan gambar tidak perlu beda per bahasa. Nama yang unik mencegah alamat kembar, dan nama yang alamatnya sudah dipakai layanan lain atau kosong (misal hanya simbol) ditolak dengan pesan di kolom nama. `name` dipertahankan karena dipakai Tanya Jawab dan `Lookup` |
| 3 | Teks per bahasa di `service_translations` (`service_id` + `locale` unik): `title` (wajib bila bahasa terisi, maks 255), `summary` (maks 500), `description` (maks 5000), `included` (daftar JSON, satu butir per baris di form, maks 255 karakter per butir), `fit` (maks 1000) | Mengikuti pola chat dan artikel: tabel terjemahan, bukan kolom JSON |
| 4 | Lima bahasa landing (`en`, `id`, `zh`, `ar`, `ja`) dalam tab. Bahasa tanpa teks tidak disimpan, dan bahasa yang dikosongkan saat ubah dihapus. **Bahasa Inggris wajib** karena jadi cadangan bahasa lain (aturan teks dinamis landing di `CLAUDE.md`) | Berbeda dari artikel: layanan tidak diterbitkan per bahasa |
| 5 | Satu gambar bersama, **wajib** (wajib sampai layanan punya gambar, lalu boleh dibiarkan saat ubah; layanan lama tanpa gambar harus mengunggah saat diubah berikutnya), unggahan JPG, PNG, atau WebP maks 2 MB, minimal 800x600, dipotong 4:3 dan disimpan WebP di disk `public` dengan nama acak (`ServiceImageStore`). Gambar lama dihapus saat diganti | Rasio 4:3 sama dengan kotak gambar layanan di landing |
| 6 | Layanan lama dipindah otomatis oleh migration: `name` menjadi `title` bahasa Inggris, `description` menjadi `description` bahasa Inggris, lalu kolom `description` dihapus. `slug` kosong sampai layanan disimpan ulang dari form (dibuat dari nama) | Tidak kehilangan data |
| 7 | Tanpa seeder dan tanpa perintah impor: teks tetap lama di `lang` (`home.services.items`, `pages.services.detail.items`) dihapus dan admin mengisi layanan lewat dashboard. Landing tidak menampilkan layanan (menu, bagian beranda, halaman) sampai ada layanan aktif | Aturan proyek: tanpa data contoh dan tanpa seeder |
| 8 | Livewire `Services\Index` dan `Services\Editor`, logic di `SaveService` (satu transaksi), `ListServices`, dan `MoveService` (geser satu tempat di seluruh daftar: posisi dinomori ulang dulu sesuai urutan tampil, lalu dua layanan bertukar, cache landing dibersihkan; tombol dinonaktifkan pada layanan pertama dan terakhir) | Pola yang sama dengan produk dan artikel |

## Halaman

| Halaman | Alamat | Isi |
| --- | --- | --- |
| Daftar | `/app/services` | Nama, alamat, bahasa (lima badge seperti di artikel: terang bila terisi, redup bila belum), status, dan tombol Ubah, Naik, Turun. Tidak ada kolom urutan. Pencarian nama, urut berdasarkan `position` lalu nama, 15 per halaman |
| Tambah | `/app/services/create` | Data bersama dan tab lima bahasa |
| Ubah | `/app/services/{service}/edit` | Sama dengan Tambah |

## Aturan keamanan dan kasus tepi

- Setiap aksi Livewire memanggil `authorize`, dan persistent middleware dari A1 berlaku.
- Id layanan di komponen dikunci. Isian dipangkas spasi, slug diubah ke huruf kecil, teks kosong disimpan `null`, dan baris kosong di daftar "yang termasuk" dibuang.
- Berkas unggahan diperiksa jenis dan ukurannya, lalu disandikan ulang ke WebP sehingga nama dan metadata asli tidak sampai ke disk publik.
- Daftar memuat `translations` lewat `with()`, jadi jumlah query tidak naik bersama jumlah baris. Kolom `name`, `position`, `slug`, dan `locale` punya index.
- Teks di `lang/{en,id}/services.php`, nama kolom di pesan validasi lewat `validationAttributes()` form.
- Nama `app.services.*` tidak bentrok dengan `landing.services` karena prefix route berbeda.

## Test

`tests/Feature/Services/ServiceAdminTest.php`: akses per peran dan tamu, menu hanya untuk admin, tambah dengan bahasa dan gambar, bahasa Inggris wajib, judul wajib di bahasa yang terisi, validasi nama (wajib, maks, unik, alamat kembar atau kosong ditolak), slug otomatis dari nama dan urutan di akhir, slug dan urutan tetap saat ubah, batas panjang teks, validasi gambar, ubah dan hapus bahasa yang dikosongkan, slug sendiri bukan duplikat, penggantian gambar menghapus berkas lama, daftar (pencarian, urutan, badge bahasa, status, geser naik dan turun, penomoran, jumlah query tetap), dan kunci terjemahan identik. Unit test `SaveServiceTest`. Sisi landing di `tests/Feature/Landing/DynamicServicesTest.php`. Migration yang memindahkan data lama belum diuji otomatis karena rollback tidak dipakai di test (lihat `CLAUDE.md`).

## Ditunda

- Hapus layanan, urutan lewat seret dan lepas, judul `<title>` dan deskripsi meta per layanan, alt gambar per bahasa.
