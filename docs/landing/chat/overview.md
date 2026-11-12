# Chat berbasis aturan yang dinamis (L7)

Chat di pojok kanan bawah landing menjawab pertanyaan lewat bubble yang diketuk, tanpa kolom ketik dan tanpa AI. Isinya sekarang **ditulis admin di dashboard**, per bahasa, berupa pohon pertanyaan: satu pertanyaan punya satu jawaban, dan jawaban itu bisa mengarah ke pertanyaan lanjutan, sedalam yang admin mau. Tampilan chat dan tombol WhatsApp ada di [`../ui/design.md`](../ui/design.md) (bagian "Tombol WhatsApp dan chat"). Aturan umum ada di `CLAUDE.md`.

```
Pertanyaan 1
  Jawaban 1
  Pertanyaan 1.1
    Jawaban 1.1
  Pertanyaan 1.2
    Jawaban 1.2
Pertanyaan 2
```

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | Pertanyaan disimpan di database: `chat_entries` (`parent_id` ke dirinya sendiri, `position`) dan `chat_entry_translations` (`locale`, `question`, `answer`, `link_label`, `link_url`; unik per entri dan bahasa) | Admin mengubah isi chat tanpa mengubah kode |
| 2 | **Kedalaman tanpa batas** (keputusan client). Panjang alur jadi urusan admin | Fleksibel |
| 3 | **Per bahasa, seperti artikel**: admin mengisi tiap bahasa sendiri (en, id, zh, ar, ja). Bahasa yang kosong tidak disimpan dan **tidak ada cadangan ke Inggris**. Pertanyaan tanpa teks di suatu bahasa tidak tampil di bahasa itu, **berikut semua pertanyaan lanjutannya** | Sama dengan aturan artikel; pengunjung tidak melihat jawaban berbahasa lain |
| 4 | Satu jawaban = teks biasa (baris baru dipertahankan) dan **satu tautan opsional** (teks tautan dan alamat, harus diisi berpasangan). Alamat hanya boleh halaman situs yang diawali satu `/` atau alamat `http://` / `https://`; `javascript:`, `//host`, dan lainnya ditolak | Mencegah tautan berbahaya; di browser teks juga dimasukkan dengan `textContent` |
| 5 | **Hanya admin** yang mengelola (Policy `ChatEntryPolicy`). Staf, customer, dan distributor mendapat 403 | Konten landing hanya untuk admin |
| 6 | **Topik bawaan lama dihapus** (Processes, Services, Contact, dan seterusnya, beserta teksnya di `lang/<locale>/chat.php`). Chat kosong dulu: **tombol chat tidak tampil** di bahasa yang belum punya pertanyaan, tombol WhatsApp tidak terpengaruh. Tanpa seeder | Keputusan client; aturan proyek tanpa data contoh |
| 7 | Urutan diatur admin dengan tombol Naik dan Turun di antara pertanyaan yang satu induk. **Memindahkan pertanyaan ke induk lain belum ada**: salah induk diperbaiki dengan membuat ulang | Sederhana dulu |
| 8 | Menghapus pertanyaan **menghapus semua pertanyaan lanjutannya** dan teks semua bahasa (cascade di database), dengan dialog konfirmasi | Pohon tidak boleh punya anak yatim |
| 9 | Hasil per bahasa di-cache (`landing.chat.<locale>`) dan dibersihkan tiap admin menambah, mengubah, memindah, atau menghapus | Sesuai aturan cache konten landing |

## Halaman admin

Menu "Chat assistant" / "Asisten chat" (ikon `question`) di grup Content, hanya admin.

| Halaman | Alamat | Isi |
| --- | --- | --- |
| Daftar | `/app/chat` | Semua pertanyaan dalam urutan pohon, **menjorok** sesuai kedalaman (batas indentasi tampilan 8 tingkat) dan bernomor `1`, `1.1`, `1.1.1`, `2`. Kolom bahasa yang terisi, dan aksi: Tambah lanjutan, Ubah, Naik, Turun, Hapus. Tombol "Tambah pertanyaan" untuk tingkat teratas |
| Tambah | `/app/chat/create`, `/app/chat/create?parent={id}` | Bahasa berupa **tab** seperti form artikel (satu panel terbuka, tab bertanda titik bila sudah diisi dan tanda seru bila ada galat; isi tab yang tersembunyi tetap tersimpan di halaman): pertanyaan (maks 200 karakter), jawaban (maks 2000), teks tautan (maks 100), alamat tautan (maks 500). Dengan `?parent=` pertanyaan menjadi lanjutan dari induk itu (induk yang tidak ada: 404). Minimal satu bahasa harus diisi; bahasa yang diisi wajib punya pertanyaan dan jawaban |
| Ubah | `/app/chat/{id}/edit` | Sama. Induk tidak berubah. Bahasa yang dikosongkan dihapus |

## Di landing

- `App\Services\Landing\LandingChat::topics()` mengembalikan pohon bahasa halaman: `{id, label, answer, link, children[]}`. Dimasukkan sebagai JSON ke `data-chat-rules` oleh `x-landing.floating-contact` bersama `welcome`, `again`, `talk`, dan `menu`.
- `resources/js/landing/contact-widgets.js`: asisten menyapa dan menampilkan pertanyaan tingkat teratas. Mengetuk satu pertanyaan menaruhnya sebagai pesan pengunjung, lalu asisten menjawab (dengan tautan bila ada). **Bila punya lanjutan**, bubble pilihan di bawahnya adalah pertanyaan lanjutan itu, ditambah "Back to the main menu" yang menampilkan tingkat teratas lagi. **Bila tidak**, asisten bertanya "Anything else I can help with?" dan tingkat teratas muncul lagi. "Talk to customer service" tetap ada bila ada nomor WhatsApp.
- Teks tetap (sapaan, "menu", dan seterusnya) ada di `lang/<locale>/chat.php` untuk lima bahasa; teks dashboard di `lang/{en,id}/chat_manager.php`.

## Pengujian

`tests/Feature/Chat/ChatEntryTest.php`: akses (tamu, staf, customer, admin), menyimpan hanya bahasa yang diisi, validasi (minimal satu bahasa, pertanyaan dan jawaban wajib, tautan berpasangan dan aman), lanjutan sedalam apa pun dengan nomor outline, induk yang tidak ada, mengubah dan mengosongkan bahasa, naik dan turun hanya di antara saudara, hapus berantai, staf ditolak di komponen, pohon per bahasa (bahasa tanpa teks dan yatim disembunyikan), urutan dan cache ikut berubah, tombol chat tersembunyi bila kosong, jumlah query daftar tetap, dan teks tersedia di semua bahasa. `tests/Feature/Landing/ContactWidgetsTest.php` untuk tombol dan panel di landing.

## Ditunda

- Memindahkan pertanyaan ke induk lain, dan menyeret untuk mengurutkan.
- Beberapa tautan per jawaban, gambar di jawaban, dan format teks (tebal, daftar).
- Statistik pertanyaan yang paling sering diketuk.
- Terjemahan zh, ar, dan ja untuk teks tetap chat belum ditinjau penutur asli.
