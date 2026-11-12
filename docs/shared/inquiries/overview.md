# Tanya Jawab (D3)

Arsip pertanyaan yang pernah diajukan customer beserta jawabannya. **Admin dan staf** mencatatnya, **customer hanya membaca** supaya tidak menanyakan hal yang sama berulang kali ke PT Metindo Perkasa. Di antarmuka namanya "Questions & Answers" (Indonesia: "Tanya Jawab"); di kode `inquiries`. Status fase di [`../implementation-plan.md`](../implementation-plan.md). Peran dan kebijakan akses umum: [`../auth/overview.md`](../auth/overview.md).

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | Nama: `Inquiry` (tabel `inquiries`), label "Questions & Answers" / "Tanya Jawab" | Bisa dipahami customer, admin, dan staf. "Problem record" kurang pas karena yang dicatat adalah pertanyaan dan jawabannya |
| 2 | Admin dan staf boleh menambah. **Admin mengubah dan menghapus semua catatan. Staf hanya yang ia buat sendiri** (`created_by`). Catatan milik staf lain atau yang `created_by`-nya kosong (penulisnya dihapus) tidak bisa diubah staf | Keputusan client |
| 3 | Semua peran yang login boleh membaca semua catatan. Customer di `/customer/inquiries`, hanya baca | Tujuan fitur: customer menemukan jawabannya sendiri |
| 4 | Isi satu bahasa, tidak dibuat lima bahasa seperti artikel | Arsip internal untuk pengguna dashboard, bukan konten landing |
| 5 | Tidak tampil di landing dan tidak menyimpan identitas penanya (nama, perusahaan, kontak) | Keputusan client: data kontak sensitif |
| 6 | Tautan ke layanan dan produk **opsional**. Bila layanan atau produknya dihapus, tautan jadi kosong dan catatan tetap ada | Tidak kehilangan riwayat |
| 7 | Gambar: maksimal **10 per catatan**, JPG/PNG/WebP, **2 MB per gambar** (sama dengan thumbnail artikel) | Keputusan client |
| 8 | Gambar disimpan di disk **privat** (`local`, `storage/app/private/inquiries`), dikodekan ulang ke WebP dengan nama acak, diperkecil (sisi terpanjang 1600 px, tidak dipotong), lalu dilayani lewat route yang memeriksa login dan peran | Gambar bisa memuat hal yang tidak untuk umum, jadi tidak boleh lewat URL publik |

## Data

- `inquiries`: `question` (teks), `answer` (teks), `asked_at` (tanggal pertanyaan masuk, boleh mundur, tidak boleh di masa depan), `service_id` dan `product_id` (opsional, `nullOnDelete`), `created_by` (`nullOnDelete`), cap waktu. Index: `asked_at`, `service_id`, `product_id`, `created_by`.
- `inquiry_images`: `inquiry_id` (`cascadeOnDelete`, index), `path`, cap waktu. Urutan gambar mengikuti id.
- `question` maks 5000 karakter, `answer` maks 10000. Keduanya dipangkas spasi.

## Halaman

| Halaman | Alamat | Isi |
| --- | --- | --- |
| Daftar | `/app/inquiries`, `/customer/inquiries` | Pertanyaan (cuplikan), layanan atau produk terkait, tanggal, jumlah gambar. Pencarian di pertanyaan dan jawaban (tanpa membedakan huruf), saringan layanan dan produk, urutan terbaru dulu, 15 per halaman. Staf melihat kolom "Added by"; customer tidak. Tombol Edit dan Delete muncul sesuai Policy |
| Baca | `/app/inquiries/{id}`, `/customer/inquiries/{id}` | Pertanyaan, jawaban (baris baru dipertahankan), tautan layanan dan produk, galeri gambar (klik membuka gambar penuh di tab baru). Nama penulis hanya untuk staf |
| Tambah | `/app/inquiries/create` | Pertanyaan\*, jawaban\*, tanggal\*, layanan, produk, unggah banyak gambar |
| Ubah | `/app/inquiries/{id}/edit` | Sama dengan Tambah. Gambar tersimpan bisa ditandai untuk dihapus (bisa dibatalkan sebelum simpan), gambar baru bisa ditarik sebelum simpan |
| Gambar | `/app/inquiry-images/{id}`, `/customer/inquiry-images/{id}` | Mengirim gambar dari disk privat. Guest diarahkan ke login, peran yang salah mendapat 403 |

- Hapus lewat dialog konfirmasi situs. Menghapus catatan ikut menghapus berkas gambarnya.
- Menu: grup "Support" / "Bantuan", item "Questions & Answers" / "Tanya Jawab" (ikon `chat`) untuk admin, staf, dan customer.
- Teks di `lang/{en,id}/inquiries.php`, `dashboard.nav.inquiries`, dan `dashboard.groups.support`. Contoh isian (placeholder) ada pada pertanyaan dan jawaban.

## Struktur kode

- Model `Inquiry`, `InquiryImage`. `InquiryPolicy`. `Role::area()` (`app` atau `customer`) dipakai komponen yang dibagi dua area.
- Action di `app/Actions/Inquiries/`: `ListInquiries`, `SaveInquiry` (menyimpan catatan, menambah dan menghapus gambar dalam satu transaksi; berkas baru dibersihkan bila gagal, berkas lama dihapus setelah berhasil), `DeleteInquiry`. Service `InquiryImageStore`.
- Livewire di `app/Livewire/Inquiries/`: `Index` dan `Show` dipakai kedua area, `Editor` hanya area staf. Form `app/Livewire/Forms/InquiryForm.php`.
- `InquiryImageController` (satu, dipakai kedua area; di luar folder per area karena dibagi). Route: `routes/app/inquiries.php` (`role:admin,staff`) dan `routes/customer/inquiries.php` (`role:customer`).
- Daftar memuat relasi (`service`, `product`, `creator`) dan `withCount('images')` di Action, jadi jumlah query tidak naik bersama jumlah baris.

## Aturan keamanan dan kasus tepi

- Setiap aksi Livewire memanggil `authorize`. Id catatan di komponen dikunci (`#[Locked]`). Id gambar yang dikirim untuk dihapus hanya berlaku bila milik catatan itu.
- Batas 10 gambar dihitung dari gambar tersimpan yang tidak ditandai hapus ditambah gambar baru, baik saat memilih berkas maupun saat menyimpan.
- Nama berkas dan metadata asli tidak disimpan. Gambar tidak pernah ada di disk publik.

## Ditunda

- Urutan gambar yang bisa diatur, keterangan per gambar, kategori atau tag, lampiran selain gambar, pencarian teks penuh, penandaan "dibaca customer", dan tampil di landing (FAQ publik).

## Test

`tests/Feature/Inquiries/InquiryTest.php`: akses per peran dan area, matriks Policy, membuat dan mengubah (termasuk batas staf), validasi, gambar (tersimpan privat sebagai WebP, tipe dan ukuran, batas 10, menghapus dan membatalkan), pelayanan gambar per peran, pencarian dan saringan, `nullOnDelete`, jumlah query tetap, menu, dan teks dua bahasa.
