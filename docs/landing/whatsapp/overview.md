# Nomor WhatsApp yang dinamis (L8)

Tombol WhatsApp di pojok kanan bawah landing, dan pilihan penerima di halaman Contact, memakai nomor customer service. Nomor itu sekarang **dikelola admin di dashboard**, bukan lagi dari `.env`. Tampilan tombol dan panelnya ada di [`../ui/design.md`](../ui/design.md) (bagian "Tombol WhatsApp dan chat"). Aturan umum ada di `CLAUDE.md`.

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | Nomor disimpan di tabel `whatsapp_agents`: `name`, `number` (angka saja, unik), `position`, `is_active` | Admin mengubah nomor tanpa menyentuh server |
| 2 | **Database saja** (keputusan client). `LANDING_WHATSAPP_AGENTS` dan `LANDING_CONTACT_WHATSAPP` dihapus dari `config/landing.php` dan `.env.example`. **Tanpa nomor aktif, tombol WhatsApp tidak tampil** | Satu sumber kebenaran. Tanpa seeder dan tanpa nomor contoh |
| 3 | Admin mengisi **nama** (satu teks untuk semua bahasa, wajib, maks 100 karakter) dan **nomor** | Nama orang atau divisi sama di semua bahasa. Nama bawaan "Customer service" ikut dihapus karena nama sekarang wajib |
| 4 | Nomor ditulis bebas (`+62 812-0000-0001`); spasi, strip, dan tanda plus dibuang dan yang disimpan hanya angka. Harus diawali kode negara tanpa angka nol di depan, 8 sampai 15 angka, dan tidak boleh kembar | Format `wa.me` meminta angka saja; batas panjang mengikuti nomor internasional |
| 5 | **Urutan** diatur admin dengan tombol Naik dan Turun, dan **sakelar aktif** mematikan nomor sementara tanpa menghapusnya. Hanya nomor aktif yang tampil, sesuai urutan. Nomor baru ada di urutan terakhir dan langsung aktif | Keputusan client |
| 6 | **Hanya admin** yang mengelola (`WhatsappAgentPolicy`), di grup Content. Staf, customer, dan distributor mendapat 403 | Konten landing hanya untuk admin |
| 7 | Daftar nomor aktif disimpan di cache (`landing.whatsapp`) dan dibersihkan otomatis lewat event model (`saved` dan `deleted`) setiap nomor berubah | Sesuai aturan cache konten landing |

## Halaman admin

Menu "WhatsApp numbers" / "Nomor WhatsApp" (ikon `phone`) di grup Content.

| Halaman | Alamat | Isi |
| --- | --- | --- |
| Daftar | `/app/whatsapp` | Nama, nomor, status (On/Off), dan aksi: Ubah, Nyalakan/Matikan, Naik, Turun, Hapus (dengan dialog konfirmasi). Tanpa paginasi karena jumlahnya sedikit |
| Tambah | `/app/whatsapp/create` | Nama dan nomor |
| Ubah | `/app/whatsapp/{id}/edit` | Sama. Nomor sendiri tidak dianggap kembar |

## Di landing

- `App\Services\Landing\LandingContact::agents()` mengembalikan `{name, number, display}` untuk nomor aktif. Dipakai tombol WhatsApp, panel, halaman Contact (pilihan penerima bila lebih dari satu nomor, dan tautan `wa.me` dengan nomor pertama), dan `ContactController`.
- Tombol WhatsApp tampil bila ada minimal satu nomor aktif; tombol chat tampil bila bahasa halaman punya pertanyaan ([`../chat/overview.md`](../chat/overview.md)). Tanpa keduanya, `data-widgets` tidak dirender.

## Pengujian

`tests/Feature/Whatsapp/WhatsappAgentTest.php`: akses per peran, menyimpan angka saja di urutan terakhir, validasi (nama wajib, format, kembar), mengubah dengan nomor sendiri, naik-turun dan nyala-mati, hapus, staf ditolak di komponen, tombol di landing mengikuti aksi admin (tampil, mati, hapus), jumlah query daftar tetap, dan teks tersedia di en dan id. `tests/Feature/Landing/ContactWidgetsTest.php` dan `ContactPageTest.php` untuk urutan, cache, dan halaman Contact.

## Ditunda

- Nama per bahasa dan keterangan jam layanan per nomor.
- Pesan sapaan per nomor (sekarang satu sapaan untuk semua: `chat.whatsapp.message`).
- Memigrasikan nomor dari `.env` lama: tidak ada otomatisasi, admin memasukkannya sekali lewat dashboard.
