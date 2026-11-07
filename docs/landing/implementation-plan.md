# Landing Page

Landing page publik PT Metindo Perkasa (electroplating, metal finishing, dan pemasok bahan kimia plating) dalam lima bahasa: `en`, `id`, `zh`, `ar`, `ja`. Dikerjakan berurutan, satu fase satu branch dari `development`, mengikuti `CLAUDE.md`. Status keseluruhan: **rencana, belum ada kode**.

## Acuan

- **Bagian dan fitur** mengikuti prototipe `http://103.125.180.65/~wwwmetin/en/homepage-2`. Tampilan dan UX **dirancang ulang**: prototipe hanya acuan isi dan fungsi, bukan visual. Desain ada di [`ui/design.md`](ui/design.md) dan [`../shared/design-system/overview.md`](../shared/design-system/overview.md).
- Urutan fase sengaja menaruh **tampilan lebih dulu** (L1 sampai L3, konten statis), supaya hasilnya bisa dilihat dan dinilai sebelum konten dipindah ke database (L4).

## Susunan halaman (dari prototipe)

Beranda: hero, video pengantar ("See how we work"), pendekatan, katalog 13 kategori bahan kimia (produk dengan TDS dan harga "Request to admin", serta proses), layanan (Electroplating Service, Chemical Supply, Technical Consultation), klien dan mitra, artikel, ajakan menghubungi, dan footer.

Halaman lain: About Us, Products, Services, Articles (dan detail artikel), Contact, dan halaman detail per proses (`/process/<kategori>-<proses>`, sekitar 50 halaman).

## Fase

| Fase | Fitur | Dokumen | Branch | Status |
| --- | --- | --- | --- | --- |
| L1 | Kerangka landing: layout, navigasi, footer, locale (awalan URL, 5 bahasa, RTL), token tema dan font | belum ditulis | `feat/landing-foundation` | Rencana |
| L2 | Beranda statis lengkap: semua bagian, aset vektor Recraft, layar intro | belum ditulis | `feat/landing-home` | Rencana |
| L3 | Halaman dalam statis: About Us, Products, Services, Articles, Contact, templat detail proses | belum ditulis | `feat/landing-pages` | Rencana |
| L4 | Konten dinamis di database dan pengelolaannya oleh admin di dashboard (kategori, produk, proses, layanan, klien, mitra, artikel, video) | belum ditulis | `feat/landing-content` | Rencana. Butuh login dan dashboard admin lebih dulu |
| L5 | Permintaan harga, unduhan TDS, dan formulir kontak | belum ditulis | belum ditentukan | Belum diputuskan |
| L6 | SEO dan performa: `hreflang`, sitemap, meta per bahasa, ukur Lighthouse (termasuk dampak layar intro) | belum ditulis | `feat/landing-seo` | Rencana |
| L7 | Widget chat | belum ditulis | belum ditentukan | Belum diputuskan |

Dokumen fitur ditulis **sebelum** kodenya tiap fase, lalu baris tabel ini diubah menjadi tautan.

## Keputusan

| # | Keputusan | Nilai |
| --- | --- | --- |
| 1 | Acuan bagian dan fitur | Prototipe `homepage-2`. Tampilan dirancang ulang |
| 2 | Isi awal | Teks statis di `lang/<locale>` dalam lima bahasa (L1 sampai L3), lalu dipindah ke database di L4 |
| 3 | Penanganan locale | Awalan URL, locale bawaan `en`, tanpa deteksi bahasa browser |
| 4 | Pengelola konten | Hanya `admin` |

## Belum diputuskan

- **Harga "Request to admin":** apakah harga memang tidak ditampilkan, dan lewat mana pengunjung mengirim permintaan (formulir, WhatsApp, atau email). Apakah permintaan disimpan di database dan dilihat admin.
- **TDS:** berkas PDF yang diunduh langsung atau lewat permintaan.
- **Widget chat:** chat sungguhan, tombol WhatsApp, atau tidak dipakai. Prototipe hanya punya balasan otomatis di sisi browser.

Ketiganya menentukan isi L5 dan L7. L1 sampai L4 tidak bergantung padanya.

## Di luar lingkup

Dashboard dan area customer ada di indeks perannya masing-masing. Fase L4 bergantung pada fase login dan dashboard admin di `docs/roles/admin/` (indeksnya dibuat saat fitur admin pertama dirancang).
