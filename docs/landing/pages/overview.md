# Halaman Dalam Landing (L3)

Halaman dalam landing page dalam lima bahasa dengan konten statis: About Us, Processes (daftar dan halaman kategori), Services, Articles dan detailnya, serta Contact. Menu dan footer ikut diperluas. Belum ada database (L4) dan formulir (L5). Fase dan statusnya di [`../implementation-plan.md`](../implementation-plan.md). Kerangka: [`../foundation/overview.md`](../foundation/overview.md). Beranda: [`../home/overview.md`](../home/overview.md). Desain: [`../ui/design.md`](../ui/design.md).

Seperti beranda, **halaman L3 tidak dirilis ke production** sebelum konten contoh diganti data asli (L4) dan kontak dikonfirmasi.

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | Halaman: About, Processes (indeks dan kategori), Services, Articles (daftar dan detail), Contact | Susunan prototipe `homepage-2`. Menu "Products" di prototipe diganti "Processes" (Nov 2026), lihat keputusan 15 |
| 2 | **Halaman detail proses (`/process/...`) tidak dibuat di L3** dan dipindah ke L4. Halaman kategori menampilkan nama proses tanpa tautan | Halaman itu murni data (langkah, diagram, bahan, TDS, harga) dan di prototipe pun masih kosong. Datanya hidup di database |
| 3 | Satu penanda `landing.sample_content` (env `LANDING_SAMPLE_CONTENT`, bawaan aktif di luar production) mengatur konten contoh: anak perusahaan (artikel tidak lagi contoh, lihat catatan A2 di bawah). Bila mati, bagiannya hilang | Satu tombol untuk "konten contoh tidak boleh rilis". Menggantikan pemeriksaan `isProduction()` di beranda |
| 4 | Sertifikasi (ISO 9001 dan ISO 14001) **tidak ditampilkan** | Di prototipe tampak contoh dan belum terverifikasi. Klaim sertifikasi tidak boleh dikarang |
| 5 | Anak perusahaan (enam kota, tautan Google Maps) ditampilkan sebagai konten contoh | Data prototipe belum dikonfirmasi |
| 6 | Contact hanya menampilkan data yang diisi di konfigurasi: alamat, telepon, email, jam kerja, WhatsApp. Tanpa formulir pesan | Formulir dikerjakan di L5. Tidak ada kontrol yang tidak berfungsi |
| 7 | Menu: Home, About Us, Processes, Services, Articles & News (hanya di bahasa yang punya artikel terbit), Contact. Footer memakai daftar yang sama | Semua tujuan sekarang ada |
| 8 | Filter artikel "All / Article / News" lewat parameter `?type=` di URL, tanpa JavaScript | Berfungsi, bisa dibagikan, dan tetap terbaca mesin pencari |
| 9 | Data kategori dengan nama prosesnya di `config/landing.php`. **Artikel sudah dari database** (A2): [`../../roles/admin/articles/overview.md`](../../roles/admin/articles/overview.md). `lang/<locale>/articles.php` hanya memuat label jenis artikel | Satu sumber per jenis data sampai L4. Tiga artikel di `lang/*/home.php` dipindah ke sini |
| 10 | Teks halaman di `lang/<locale>/pages.php`, memakai terjemahan prototipe client untuk `id`, `zh`, `ar`, `ja`. Nama kategori dan proses tetap bahasa Inggris seperti di prototipe | Aturan teks pengguna. Terjemahan baru ditulis sendiri hanya bila prototipe tidak punya, dan ditinjau penutur asli |
| 11 | Dial: ENERGY 2 / RHYTHM 3 / MOTION 2 ([`../ui/motion.md`](../ui/motion.md)), tiap halaman punya komposisi sendiri di bawah kepala halaman yang seragam | Tidak kaku, tetapi tetap dikenali sebagai satu situs |
| 12 | Menu enam item sebaris mulai lebar `lg`, dengan pemilih bahasa berupa dropdown `<details>` yang menampilkan bahasa aktif. Di bawah `lg`, menu dan bahasa masuk ke panel "Menu" | Enam item ditambah lima bahasa tidak muat sebaris di lebar tablet |
| 13 | Akses data statis lewat satu kelas `LandingContent` (kategori, anak perusahaan, menu). Artikel lewat `LandingArticles`. Diganti sumber database di L4 tanpa menyentuh controller dan view | Satu titik pergantian |
| 14 | Catatan "konten contoh" memakai satu kunci `landing.sample_note` yang tidak menyebut jenis konten | Dipakai di anak perusahaan dan halaman tentang |
| 15 | Menu, alamat, dan nama route untuk kategori proses memakai **Processes** (`/{locale}/processes`, `landing.processes.*`), bukan Products. Terjemahan: id Proses, zh 工艺, ar العمليات, ja 工程. Alamat `/products` tidak dialihkan karena situs belum rilis | Isinya kategori proses, bukan produk. Nama "Products" dan alamat `/products` dicadangkan untuk produk sungguhan (nama, deskripsi, TDS, harga) yang dikelola admin di A3 dan L5 |

## Halaman

| Halaman | Alamat | Isi |
| --- | --- | --- |
| About | `/{locale}/about` | Profil perusahaan, pendekatan, visi dan misi, fasilitas (teks), dan anak perusahaan sebagai **peta Indonesia** dengan pin (contoh). Isinya mengikuti halaman About prototipe `.../en/about`; tampilannya dirancang ulang ([`../ui/design.md`](../ui/design.md), bagian About) |
| Processes | `/{locale}/processes` | Jumlah kategori, **daftar tab 13 kategori dengan panel rinci** (proses lengkap dan tautan "View all N processes" ke halaman kategori; mengikuti `homepage-5`), dan penjelasan empat tingkat katalog. Tanpa label "Process catalogue"; lihat [`../ui/design.md`](../ui/design.md), bagian Processes |
| Kategori | `/{locale}/processes/{category}` | Nama kategori, daftar proses (tanpa tautan), kategori lain |
| Services | `/{locale}/services` | Tiga layanan masing-masing dengan **ilustrasi** (sama dengan daftar layanan di beranda) dan tombol ke Contact, tautan loncat di pembuka, dan "How we work" empat langkah berurutan (Enquiry, Spec review, Process and supply, Delivery). Tanpa label "What we do"; lihat [`../ui/design.md`](../ui/design.md), bagian Services |
| Articles | `/{locale}/articles` | Daftar artikel terbit di bahasa itu dengan filter jenis. 404 bila bahasa itu belum punya artikel terbit |
| Artikel | `/{locale}/articles/{slug}` | Judul, jenis, tanggal, isi, artikel terkait |
| Contact | `/{locale}/contact` | Data kontak yang dikonfigurasi dan tombol WhatsApp bila ada |

Setiap halaman memakai komponen kepala halaman (judul, ringkasan, jejak remah bila perlu) dan diakhiri ajakan menghubungi yang sama seperti beranda. Tautan kategori, layanan, dan artikel di beranda diaktifkan di fase ini.

## Aturan keamanan dan kasus tepi

- `{category}` dan `{slug}` dibatasi pada daftar di config. Nilai lain menghasilkan 404, dan nilainya tidak dipakai untuk membentuk jalur berkas atau query.
- Parameter `?type=` hanya menerima `article` atau `news`, nilai lain diabaikan (menampilkan semua).
- Konten contoh nonaktif: bagian anak perusahaan tidak dirender.
- Bahasa tanpa artikel terbit: route artikel 404, serta tautan menu dan bagian beranda hilang (A2).
- Tautan Google Maps dibuat dari data anak perusahaan, dibuka dengan `rel="noopener noreferrer"` dan jelas ditandai sebagai tautan luar.
- Tombol WhatsApp memakai `https://wa.me/<nomor>` dari konfigurasi dan hanya dirender bila nomor diisi.
- Setiap halaman menyetel judul `<title>` per halaman dan `hreflang` serta `canonical` seperti kerangka. Deskripsi meta menyusul di L6.
- Properti logis dan uji RTL berlaku seperti sebelumnya. Teks panjang (Indonesia, Arab) diuji di tiap komposisi.

## Struktur kode

- Controller invokable atau resource di `app/Http/Controllers/Landing`: `AboutController`, `ProductController` (`index`, `show`), `ServiceController`, `ArticleController` (`index`, `show`), `ContactController`.
- Route di `routes/landing/{about,processes,services,articles,contact}.php`. Resource dipakai untuk processes dan articles. Controller kategori: `ProcessController`.
- `config/landing.php` ditambah: `sample_content`, `categories[].processes`, `subsidiaries`, `contact.address`, `contact.hours`, `contact.whatsapp` (env `LANDING_SAMPLE_CONTENT`, `LANDING_CONTACT_ADDRESS`, `LANDING_CONTACT_HOURS`, `LANDING_CONTACT_WHATSAPP`).
- `app/Services/Landing/LandingContent.php`: akses kategori, artikel, anak perusahaan, dan daftar menu. `SetLandingLocale` kini melepas parameter `locale` dari route setelah dipakai, supaya controller hanya menerima parameternya sendiri.
- `lang/{en,id,zh,ar,ja}/pages.php` dan `articles.php`. Kunci `lang/*/landing.php` untuk menu ditambah.
- View di `resources/views/landing/pages/` (satu berkas per halaman) dan komponen `x-landing.page-header` serta `x-landing.cta` (ajakan, dipakai juga beranda).
- Header dan footer membaca satu daftar menu yang sama dari `LandingContent::menu()`.

## Langkah dan test

Langkah: config dan teks lima bahasa, komponen bersama, halaman satu per satu, menu dan footer, aktifkan tautan di beranda, uji RTL dan ponsel.

Test (feature) yang harus ada:

- Setiap halaman mengembalikan 200 di kelima locale dengan `lang` dan `dir` yang benar, dan judulnya dalam bahasa aktif.
- Kunci `lang/*/pages.php` dan `articles.php` identik di kelima bahasa.
- Kategori yang tidak dikenal menghasilkan 404, semua 13 kategori menghasilkan 200 dan menampilkan prosesnya.
- Artikel: filter `?type=` menyaring, slug tidak dikenal 404, dan dengan konten contoh nonaktif route artikel 404 serta menu tidak memuatnya.
- Contact: elemen hanya muncul bila konfigurasinya diisi, dan tautan WhatsApp berbentuk `wa.me`.
- Sertifikasi tidak muncul di About, dan anak perusahaan hanya muncul dengan konten contoh aktif.
- Menu memuat tautan ke semua halaman yang ada dan tanda halaman aktif (`aria-current`).

Pemeriksaan manual di browser: semua halaman di ponsel dan desktop, Arab dalam RTL, filter artikel, dan tautan beranda ke halaman dalam.

## Ditunda

- Halaman detail proses, langkah, diagram, bahan, TDS, dan harga (L4 dan L5).
- Empat grup di prototipe (Chemical Alkaline Type, Acid Type, Basic, Ferrous Metals Pre Treatment): isinya belum dikenal, jadi belum dimodelkan.
- Formulir pesan di Contact (L5), widget chat (L7).
- Sertifikasi, foto perusahaan, dan data resmi anak perusahaan menunggu konfirmasi client.
- Artikel asli dan isi lengkapnya (L4).
- Deskripsi meta, sitemap, dan pengukuran performa (L6).
- Penjelasan empat tingkat di halaman Processes menyebut Diagram dan Materials seperti di prototipe, tetapi halaman kategori belum punya diagram alur maupun daftar bahan; keduanya ditunda sampai isinya ada.
- Chat berbasis aturan dijawab dengan mengetuk bubble pilihan (tanpa kolom ketik) dan hanya menjawab dari isi situs; belum ada AI. Mode AI di prototipe ("Rule-based / AI") belum dikerjakan.
- Nomor customer service untuk tombol WhatsApp harus diisi di `LANDING_WHATSAPP_AGENTS` sebelum rilis; tanpa itu tombolnya tidak tampil.
