# Peta Dokumentasi

Dokumen proyek dikelompokkan per **area** (landing, bersama, dan peran `admin`, `staff`, `customer`) dan per **fitur**, supaya mudah dibaca dan dirawat. Isi dokumen berbahasa Indonesia, nama folder dan berkas berbahasa Inggris.

## Susunan

```
docs/
├── README.md                     peta ini
├── setup.md                      prasyarat lingkungan
├── references/                   aset acuan: logo, favicon, palet warna
├── landing/                      landing page publik (5 bahasa)
│   ├── ui/                       desain landing dan prompt aset vektor
│   └── implementation-plan.md    indeks
├── shared/                       dipakai lebih dari satu peran
│   ├── design-system/            fondasi desain: warna, tipografi, RTL
│   └── implementation-plan.md    indeks (dibuat saat fitur pertama)
└── roles/
    ├── admin/                    dashboard admin
    ├── staff/                    dashboard staf
    └── customer/                 area customer
```

Folder fitur dibuat saat fitur pertamanya ditulis, bukan lebih awal. Setiap area punya `implementation-plan.md` sebagai **indeks**: status, urutan fase, dan tautan ke dokumen fitur. Rancangan rinci ada di folder fitur di sampingnya (contoh: `docs/roles/admin/landing-content/overview.md`).

## Daftar indeks

| Area | Indeks | Isi |
| --- | --- | --- |
| Landing | [`landing/implementation-plan.md`](landing/implementation-plan.md) | Fase L1 sampai L7 landing page |
| Bersama | `shared/implementation-plan.md` | Belum ada |
| Admin | `roles/admin/implementation-plan.md` | Belum ada |
| Staf | `roles/staff/implementation-plan.md` | Belum ada |
| Customer | `roles/customer/implementation-plan.md` | Belum ada |

Baris diubah menjadi tautan saat indeksnya dibuat.

## Aturan menulis dokumen

- Fitur baru: buat folder fitur di bawah area yang memilikinya, dan tambahkan baris di indeks area itu. Jika dipakai lebih dari satu peran, taruh di `shared/`.
- Satu dokumen satu topik. Dokumen yang melewati sekitar 250 baris dipecah.
- Status fase dicatat di indeks. Dokumen fitur memuat rancangan, aturan, dan langkah pengerjaannya.
- Tautan antar dokumen memakai jalur relatif.
- **Fitur boleh berubah di tengah jalan.** Ubah dokumen yang sudah ada di branch yang sama dengan perubahan kodenya. Jangan membuat dokumen baru untuk topik yang sudah punya dokumen, dan jangan membiarkan dokumen tertinggal dari perilaku sebenarnya.
- Sebelum membuka PR, cari dengan `grep -rn` nama route atau istilah terkait di seluruh `docs/`, lalu perbarui semua yang menyebutnya, termasuk indeks.

## Kapan menulis dokumen dan seberapa panjang

### Kapan

- **Tulis** untuk fitur yang punya keputusan atau alur lintas berkas (beberapa route, tabel, atau peran). Rancangan ditulis **sebelum** kode, supaya keputusan bisa ditinjau lebih dulu.
- **Jangan tulis** untuk perbaikan bug, penyesuaian tampilan, refactor kecil, atau penambahan field. Cukup pesan commit dan test.
- Jika topiknya sudah punya dokumen, **ubah dokumen itu**, jangan membuat yang baru.

### Isi

Masukkan hal yang tidak bisa dibaca dari kode:

- **Keputusan** dan alasannya, termasuk pilihan yang ditolak.
- **Alur** langkah demi langkah (diagram atau daftar bernomor).
- **Aturan keamanan dan kasus tepi** (throttle, akses per peran, akun belum terverifikasi, bahasa yang belum diterjemahkan, dan sebagainya).
- **Struktur kode** secara ringkas: Action, Service, controller, dan route yang terlibat.
- **Langkah pengerjaan** dan daftar test yang harus ada.
- **Ditunda**: hal yang sengaja belum dikerjakan.

Jangan masukkan:

- Salinan kode atau daftar berkas yang bisa dilihat di repo.
- Status pengerjaan di dokumen fitur. Status hanya di indeks.

### Panjang

- Target **60 sampai 150 baris**, maksimal sekitar 250. Lebih dari itu, pecah per topik.
- Setelah fitur selesai, **pangkas** dokumennya menjadi keputusan, alur, dan aturan keamanan. Bagian "langkah pengerjaan" boleh dibuang.

### Kerangka

```
# Judul Fitur

Satu paragraf: apa dan untuk siapa.

## Keputusan
## Alur
## Aturan keamanan dan kasus tepi
## Struktur kode
## Langkah dan test
## Ditunda
```
