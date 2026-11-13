# Kelola Artikel (A2)

Admin menulis dan menerbitkan artikel dan berita landing dalam lima bahasa, satu bahasa satu tulisan. Admin mengelola semua artikel; staf melihat semua artikel dan boleh menulis, tetapi hanya mengubah dan menghapus artikel yang ia buat sendiri (`created_by`). Fitur ini berdiri sendiri: kelola konten landing lainnya (A5: kalimat beranda, layanan, dan seterusnya) **belum dikerjakan** dan dirancang terpisah nanti. Status fase di [`../implementation-plan.md`](../implementation-plan.md). Latar keputusan bahasa: [`../../../landing/pages/overview.md`](../../../landing/pages/overview.md).

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | Satu artikel punya satu terjemahan per bahasa (`en`, `id`, `zh`, `ar`, `ja`). Judul, slug, ringkasan, isi, judul dan deskripsi meta, serta teks alt gambar ditulis per bahasa | SEO dan tata bahasa lebih baik bila ditulis manusia per bahasa |
| 2 | Tiap bahasa **diterbitkan sendiri**. Artikel boleh hanya ada di `id` dulu. Tidak ada jumlah bahasa minimum | Keputusan client, bahasa lain menyusul |
| 3 | **Tanpa fallback bahasa Inggris** untuk artikel. URL bahasa yang belum terbit mendapat 404 | Teks bahasa lain di URL berbeda merusak SEO. Aturan fallback `en` di `CLAUDE.md` hanya untuk teks antarmuka tetap |
| 4 | Slug per bahasa, unik per bahasa, hanya huruf kecil, angka, dan tanda hubung | Alamat tetap bersih dan aman di semua bahasa |
| 5 | Tabel terpisah `articles` dan `article_translations`, bukan kolom JSON `spatie/laravel-translatable` | Status, slug, dan tanggal terbit berbeda per bahasa dan harus bisa dicari lewat index |
| 6 | Jenis artikel: `article` atau `news`, satu nilai per artikel (bukan per bahasa) | Sudah dipakai penyaring di landing |
| 7 | **Thumbnail satu gambar**, dipakai bersama semua bahasa, wajib untuk menerbitkan (draf boleh tanpa). Teks alt per bahasa. Gambar sampul halaman dan gambar Open Graph diturunkan dari berkas ini | Keputusan client: cukup satu gambar |
| 8 | Isi ditulis di editor **TipTap**, disimpan sebagai JSON, dirender dan dibersihkan di server (`ueberdosis/tiptap-php`). Tanpa gambar di dalam isi | Isi tidak bisa menyisipkan HTML mentah atau skrip |
| 9 | Halaman tulis satu layar penuh dengan tab bahasa. Livewire, komponen tipis yang memanggil Action | Aturan arsitektur `CLAUDE.md` |
| 10 | Admin boleh **menghapus** artikel (dengan konfirmasi), berbeda dengan akun. Alamat yang dihapus menjadi 404 | Salah tulis harus bisa dibuang. Belum perlu arsip |
| 11 | Terjemahan mesin **tidak** dibuat sekarang | Bila nanti ada, hanya sebagai draf noindex |

## Data

- `articles`: `type`, `thumbnail_path`, `created_by` (relasi ke `users`), cap waktu.
- `article_translations`: `article_id`, `locale`, `slug`, `title`, `excerpt`, `body` (JSON), `body_html`, `meta_title`, `meta_description`, `image_alt`, `status` (`draft` atau `published`), `published_at`, cap waktu.
- `article_translations.views_count` (total, index) dan tabel `article_view_days` (`article_translation_id`, `day`, `views`, unik per pasangan pertama dan kedua) untuk hitungan view per bahasa dan per hari. `views_count` tidak boleh diisi massal.
- Unik `(article_id, locale)` dan `(locale, slug)`. Index `(locale, status, published_at)` untuk daftar publik. `created_by` dan `article_id` diberi index.
- Status dan locale memakai backed enum. Locale hanya dari `config('locales.landing')`.

## Alur admin

| Halaman | Alamat | Isi |
| --- | --- | --- |
| Daftar | `/app/articles` | Judul (bahasa pertama yang ada), jenis, status tiap bahasa, views, pembuat ("Created by", diisi saat artikel dibuat; "Unknown" bila akunnya sudah dihapus). Penyaring jenis dan status, pencarian judul, 15 per halaman |
| Tulis | `/app/articles/create` | Jenis dan thumbnail (bersama, **thumbnail di bawah jenis**, satu kolom selebar penuh), lalu tab per bahasa. Kolom biasa (judul, alamat/slug, ringkasan, judul meta, deskripsi meta, deskripsi gambar) punya **placeholder contoh** (`article_manager.placeholders.*`, mengikuti bahasa dashboard); kolom isi (editor) tanpa placeholder |
| Ubah | `/app/articles/{article}/edit` | Sama dengan Tulis |

1. Admin memilih jenis dan mengunggah thumbnail.
2. Per tab bahasa: judul, slug (diusulkan dari judul untuk `en` dan `id`, diisi sendiri untuk `zh`, `ar`, `ja`), ringkasan, isi, meta, dan alt gambar.
3. Simpan sebagai draf, atau terbitkan satu bahasa. Menerbitkan menolak bila thumbnail, judul, slug, isi, dan deskripsi meta bahasa itu belum lengkap. Terbit atau draf diatur dengan kotak centang per tab, dan tanggal terbit tidak berubah saat artikel disimpan ulang.
4. Tab bahasa kosong dibiarkan. Artikel tanpa terjemahan terbit tidak tampil di mana pun.

## Tampilan publik

- `/{locale}/articles` hanya memuat terjemahan **terbit** bahasa itu. `/{locale}/articles/{slug}` mencari slug di bahasa itu saja.
- Menu "Artikel", bagian artikel di beranda, dan tautan terkait **disembunyikan** pada bahasa yang belum punya artikel terbit.
- `hreflang` di halaman artikel hanya untuk bahasa yang terbit. `canonical` ke URL bahasa itu sendiri. Sitemap memuat terjemahan terbit saja.
- Judul dan deskripsi meta dari kolom meta (cadangan: judul dan ringkasan), serta gambar Open Graph dari thumbnail.
- Artikel contoh (`config('landing.articles')` dan teks `items` di `lang/*/articles.php`) dihapus. Label jenis tetap di `lang/*/articles.php`. Artikel terkait: tiga terbaru lain di bahasa yang sama.
- **View artikel.** Dihitung per terjemahan lewat JavaScript (`resources/js/landing/article-views.js`): setelah halaman terbuka dan terlihat 3 detik, mengirim `POST /{locale}/articles/{slug}/views` (`ArticleViewController@store`, `throttle:30,1`, token CSRF dari meta di halaman artikel). Respons selalu `204`. Server (`RecordArticleView`, `ArticleViews`) tidak menghitung: artikel yang bukan terbit atau artikel contoh, user agent kosong atau bot, admin dan staf yang login, serta pengunjung yang sudah menghitung artikel itu dalam 24 jam (cookie `viewed_articles` berisi id terjemahan dan batas waktunya, maksimal 100 entri; IP tidak disimpan). Penambahan atomik (`upsert` per hari dan `increment` pada total).
- Landing menampilkan hitungan ("1,2K views") di daftar, artikel terbaru, kartu terkait, dan halaman artikel, **hanya dari 10 view** (`ArticleViews::MINIMUM_SHOWN`) supaya artikel baru tidak terlihat sepi. Angka tidak ikut cache `LandingArticles`: ditempel pada tiap permintaan dengan satu kueri per bahasa. Teks di `lang/*/articles.php` (`views`).
- Dashboard: kolom "Views" sendiri di daftar artikel (terpisah dari kolom Languages): satu baris berisi total semua bahasa terbit (huruf tebal, didahului ikon mata di kiri angka), garis pemisah tipis, lalu rincian per bahasa terbit (mis. "EN 1.2K  ID 34"). Artikel tanpa bahasa terbit menampilkan tanda "—".
- Daftar dan beranda di-cache per bahasa, dan cache dibersihkan saat artikel disimpan, diterbitkan, atau dihapus.

## Aturan keamanan dan kasus tepi

- Semua halaman dijaga `role:admin,staff` dan `ArticlePolicy`. Admin dan staf melihat semua artikel dan boleh menulis. **Admin mengubah dan menghapus semua artikel; staf hanya yang `created_by`-nya dirinya** (artikel staf lain atau yang pembuatnya sudah dihapus: tombol Edit dan Delete tidak tampil, halaman edit dan aksi hapus 403). Persistent middleware Livewire sudah berlaku (A1). Customer dan distributor mendapat 403.
- Unggahan thumbnail: JPG, PNG, atau WebP, maksimal 2 MB, minimal 1200x675. Server memeriksa isi berkas (bukan hanya ekstensi), mengubah ukuran dan format ke WebP, dan memberi nama acak. Berkas lama dihapus saat diganti atau artikel dihapus.
- Isi dari editor tidak dipercaya: server merender dari JSON dengan daftar node yang diizinkan (paragraf, judul 2 dan 3, tebal, miring, daftar, kutipan, tautan). Tautan luar memakai `rel="noopener nofollow"`.
- Slug yang bentrok ditolak dengan pesan jelas. Mengubah slug artikel terbit menghapus alamat lama (dicatat sebagai risiko, pengalihan ditunda).
- Menghapus tab terakhir yang terbit membuat artikel tak tampil. Id artikel dan locale di komponen dikunci (`#[Locked]`) dan divalidasi di server.
- Hindari N+1: daftar admin dan publik memuat terjemahan lewat `with()`. Test jumlah query tetap saat baris bertambah.
- Teks dashboard di `lang/{en,id}/article_manager.php`. Nama kolom pada pesan validasi disusun dari label formulir dan nama bahasa.

## Struktur kode

- Model `Article`, `ArticleTranslation`. Enum `ArticleType`, `PublicationStatus`. `ArticlePolicy`.
- Action di `app/Actions/Articles/`: `SaveArticle` (menyimpan semua tab sekaligus, termasuk menerbitkan), `DeleteArticle`, `ListArticles`. Service: `ThumbnailStore` (validasi, ubah ukuran, hapus), `ArticleBody` (render dan bersihkan JSON), `LandingArticles` (kueri publik dan cache). Form Livewire: `app/Livewire/Forms/ArticleForm.php`.
- Livewire: `app/Livewire/Articles/{Index,Editor}.php`. Route `routes/app/articles.php` (`Route::resource`). `DashboardMenu` ditambah "Artikel" untuk admin dan staf.
- Landing: `ArticleController` dan `LandingContent` membaca database. Penyaring route slug dari config diganti pencarian per bahasa.
- Paket baru: `ueberdosis/tiptap-php`, `intervention/image`, dan `@tiptap/*` (npm) untuk dashboard saja, tidak memengaruhi bundel landing.

## Langkah dan test

Langkah: migrasi dan model, Action dan Service, daftar admin, editor dan thumbnail, halaman publik dan cache, hapus data contoh, `hreflang` dan sitemap bila L6 sudah ada, teks, test.

Test yang harus ada:

- Admin membuka semua halaman. Staf membuka daftar dan editor tulis, serta edit dan hapus hanya artikelnya sendiri (lainnya 403, termasuk aksi Livewire). Customer dan distributor 403, tamu ke login.
- Simpan draf tanpa thumbnail berhasil, terbitkan tanpa thumbnail atau tanpa kolom wajib ditolak.
- Slug unik per bahasa (sama di bahasa berbeda boleh), format slug, dan peran locale di luar daftar ditolak.
- Terbitkan satu bahasa saja: tampil di bahasa itu, 404 di bahasa lain, tanpa fallback `en`.
- Menu, beranda, dan `hreflang` mengikuti bahasa yang terbit. Daftar kosong menyembunyikan menu.
- Unggahan: tipe dan ukuran salah ditolak, berkas diubah ke WebP, berkas lama terhapus saat diganti dan saat artikel dihapus.
- Pembersih isi membuang skrip, atribut berbahaya, dan node terlarang.
- Cache dibersihkan saat simpan, terbitkan, dan hapus. Jumlah query tetap saat artikel bertambah.
- Unit: setiap Action dan Service.

Pemeriksaan manual di browser: menulis di lima bahasa termasuk Arab (RTL di editor), unggah gambar, terbit satu bahasa, dan tampilan publik di ponsel.

## Ditunda

- Grafik view per hari, "artikel terpopuler" di beranda dashboard, urutan berdasarkan view, dan rincian view per bahasa di halaman ubah (datanya sudah tersimpan di `article_view_days`).
- Terjemahan mesin sebagai draf, gambar di dalam isi, kategori dan tag, penjadwalan terbit, pengalihan slug lama, arsip, riwayat revisi, dan hak staf.
- Kelola konten landing lainnya (kalimat beranda, produk, layanan, klien, video). Dirancang terpisah.
