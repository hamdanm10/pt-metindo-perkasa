# Halaman Dalam Landing (L3)

Halaman dalam landing page dalam lima bahasa dengan konten statis: About Us, Processes (daftar dan halaman kategori), Services, Articles dan detailnya, serta Contact. Menu dan footer ikut diperluas. Belum ada database (L4) dan formulir (L5). Fase dan statusnya di [`../implementation-plan.md`](../implementation-plan.md). Kerangka: [`../foundation/overview.md`](../foundation/overview.md). Beranda: [`../home/overview.md`](../home/overview.md). Desain: [`../ui/design.md`](../ui/design.md).

Seperti beranda, **halaman L3 tidak dirilis ke production** sebelum data anak perusahaan dari prototipe diganti data asli dan kontak dikonfirmasi.

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | Halaman: About, Processes (indeks dan kategori), Services, Articles (daftar dan detail), Contact | Susunan prototipe `homepage-2`. Menu "Products" di prototipe diganti "Processes" (Nov 2026), lihat keputusan 15 |
| 2 | **Halaman detail proses (`/{locale}/process/{slug}`) dibuat di branch `feat/process-detail`**, mengikuti alur prototipe (kategori, daftar proses, detail proses). Datanya statis di `config/landing.php` (`process_details`, dengan kunci slug proses) sampai pindah ke database di L4. Hanya **Alkaline Zinc** yang punya langkah dan bahan (data asli prototipe, 4 langkah dan 8 bahan); proses lain menampilkan "No steps defined for this process yet." dan tanpa tombol diagram. Kategori **Chemical Alkaline Type** (6 proses) ditambahkan karena memuat satu-satunya data itu, sehingga kini ada **14 kategori** | Tampilan dirancang ulang, alurnya sama dengan prototipe. Tidak mengarang langkah atau bahan |
| 3 | **Mode konten contoh dihapus** (`landing.sample_content`, `LANDING_SAMPLE_CONTENT`, `SampleArticles`, kotak placeholder foto, dan catatan "sample"). Landing hanya menampilkan data asli: artikel, layanan, dan nomor WhatsApp dari dashboard, serta logo dan foto dari berkas. Bagian tanpa data tidak tampil | Konten contoh tidak boleh ikut rilis, jadi tidak perlu penanda |
| 4 | Sertifikasi (ISO 9001 dan ISO 14001) **tidak ditampilkan** | Di prototipe tampak contoh dan belum terverifikasi. Klaim sertifikasi tidak boleh dikarang |
| 5 | Anak perusahaan (enam kota, tautan Google Maps) dibaca dari `config/landing.php` (`subsidiaries`) dan tampil di About, Contact, dan footer. **Datanya masih dari prototipe dan belum dikonfirmasi**; ganti atau kosongkan sebelum rilis | Pengelolaan dari admin belum ada |
| 6 | Contact hanya menampilkan data yang diisi di konfigurasi: alamat, email, dan jam kerja dari konfigurasi, serta telepon dan WhatsApp dari nomor aktif di dashboard admin (semuanya tampil). Tanpa formulir pesan | Formulir dikerjakan di L5. Tidak ada kontrol yang tidak berfungsi |
| 7 | Menu: Home, About Us, Processes, Services, Articles & News (hanya di bahasa yang punya artikel terbit), Contact. Footer memakai daftar yang sama | Semua tujuan sekarang ada |
| 8 | Filter artikel "All / Article / News" lewat parameter `?type=` di URL, tanpa JavaScript | Berfungsi, bisa dibagikan, dan tetap terbaca mesin pencari |
| 8a | **Pencarian artikel** lewat parameter `?q=` (form GET di bagian pembuka daftar, tanpa JavaScript): mencari judul dan ringkasan di bahasa aktif, tanpa membedakan huruf besar kecil (`ILIKE`), dan semua kata harus ada. Digabung dengan `?type=` dan pagination; jumlah di tiap filter ikut menghitung pencarian. Tanpa hasil, halaman tetap 200 dengan pesan kosong (bukan 404). Hasil pencarian `noindex, follow` dan canonical-nya ke daftar polos | Mudah dipakai dan dibagikan, tidak membuat halaman hasil yang terindeks |
| 9 | Data kategori dengan nama prosesnya di `config/landing.php`. **Artikel sudah dari database** (A2): [`../../roles/admin/articles/overview.md`](../../roles/admin/articles/overview.md). `lang/<locale>/articles.php` hanya memuat label jenis artikel | Satu sumber per jenis data sampai L4. Tiga artikel di `lang/*/home.php` dipindah ke sini |
| 10 | Teks halaman di `lang/<locale>/pages.php`, memakai terjemahan prototipe client untuk `id`, `zh`, `ar`, `ja`. Nama kategori dan proses tetap bahasa Inggris seperti di prototipe | Aturan teks pengguna. Terjemahan baru ditulis sendiri hanya bila prototipe tidak punya, dan ditinjau penutur asli |
| 11 | Dial: ENERGY 2 / RHYTHM 3 / MOTION 2 ([`../ui/motion.md`](../ui/motion.md)), tiap halaman punya komposisi sendiri di bawah kepala halaman yang seragam | Tidak kaku, tetapi tetap dikenali sebagai satu situs |
| 12 | Menu enam item sebaris mulai lebar `lg`, dengan pemilih bahasa berupa dropdown `<details>` yang menampilkan bahasa aktif. Di bawah `lg`, menu dan bahasa masuk ke panel "Menu" | Enam item ditambah lima bahasa tidak muat sebaris di lebar tablet |
| 13 | Akses data lewat satu kelas `LandingContent` (kategori dan proses, anak perusahaan, menu). Kategori, proses, langkah, dan produk kini dari database lewat `LandingProcesses` (A10, [`../../roles/admin/process-content/overview.md`](../../roles/admin/process-content/overview.md)), tetapi untuk saat ini landing memakai data statis di `config/landing.php`: sumbernya diatur `landing.catalog_source` (env `LANDING_CATALOG_SOURCE`, bawaan `static`, nilai `database` untuk memakai database). Artikel lewat `LandingArticles`. Diganti sumber database di L4 tanpa menyentuh controller dan view | Satu titik pergantian |
| 14 | Catatan "konten contoh" (`landing.sample_note`) dihapus bersama mode contoh | Tidak ada lagi konten contoh |
| 15 | Menu, alamat, dan nama route untuk kategori proses memakai **Processes** (`/{locale}/processes`, `landing.processes.*`), bukan Products. Terjemahan: id Proses, zh 工艺, ar العمليات, ja 工程. Alamat `/products` tidak dialihkan karena situs belum rilis | Isinya kategori proses, bukan produk. Nama "Products" dan alamat `/products` dicadangkan untuk produk sungguhan (nama, deskripsi, TDS, harga) yang dikelola admin di A3 dan L5 |

## Halaman

| Halaman | Alamat | Isi |
| --- | --- | --- |
| About | `/{locale}/about` | Profil perusahaan, pendekatan, visi dan misi, fasilitas (teks), dan anak perusahaan sebagai **peta Indonesia** dengan pin (contoh). Isinya mengikuti halaman About prototipe `.../en/about`; tampilannya dirancang ulang ([`../ui/design.md`](../ui/design.md), bagian About) |
| Processes | `/{locale}/processes` | Jumlah kategori, **daftar tab 14 kategori dengan panel rinci** (tiap proses bertautan ke halamannya, dan tautan "View all N processes" ke halaman kategori; mengikuti `homepage-5`), dan penjelasan empat tingkat katalog. Tanpa label "Process catalogue"; lihat [`../ui/design.md`](../ui/design.md), bagian Processes |
| Kategori | `/{locale}/processes/{category}` | Pembuka dengan posisi kategori ("Category 03 of 14"), nama, deskripsi, dan jumlah proses; foto kategori yang menempel di samping kisi kartu proses bernomor (nama, deskripsi singkat, "View process"); kategori lain. Lihat [`../ui/design.md`](../ui/design.md), bagian Halaman Kategori Proses |
| Detail proses | `/{locale}/process/{slug}` | Jejak remah (Processes / kategori), ringkasan (kategori, jumlah langkah, jumlah bahan), alur langkah bernomor dengan chip bahan, tombol **View diagram** (dialog berisi alur beranimasi), dan proses lain di kategori yang sama. Chip bahan membuka dialog detail bahan: Usage, Brochure, Product video, E-commerce (belum ada data, tampil "–"), serta TDS dan Price ("Request to admin", tautan ke Contact). Slug tidak dikenal menghasilkan 404 |
| Services | `/{locale}/services` | Layanan dari database ([`../../roles/admin/services/overview.md`](../../roles/admin/services/overview.md)), masing-masing dengan **gambar** unggahan (sama dengan daftar di beranda). Tanpa layanan aktif halaman ini 404 dan menu Services disembunyikan. Tiap layanan punya dan satu tombol "Learn more" ke halaman detailnya (tombol "Talk to our team" dipindah ke halaman detail), tautan loncat di pembuka, dan "How we work" empat langkah berurutan (Enquiry, Spec review, Process and supply, Delivery). Tanpa label "What we do"; lihat [`../ui/design.md`](../ui/design.md), bagian Services |
| Detail layanan | `/{locale}/services/{slug}` | Slug dari database (diisi admin di dashboard). Kepala dengan jejak remah "Services / Service detail", penanda urutan, ringkasan, tombol, dan gambar layanan, lalu deskripsi, kartu "What's included", "Good fit for" (bagian yang kosong tidak tampil), kartu "Talk to our team" (tombol WhatsApp dari nomor aktif pertama yang dikelola admin, tersembunyi bila tidak ada nomor, dan tombol Contact us), "How we work", kartu layanan lain (gambar, judul, ringkasan, Learn more), dan ajakan. Slug tidak dikenal atau layanan nonaktif menghasilkan 404, dan bagian "layanan lain" tidak tampil bila hanya ada satu layanan. Teks layanan dari database per bahasa dengan cadangan `en`. Label tetap (judul bagian, tombol) di `lang/<locale>/pages.php` (`services.detail`), lima bahasa |
| Articles | `/{locale}/articles` | Daftar artikel terbit di bahasa itu: satu artikel terbaru besar, kartu untuk sisanya, dan filter jenis dengan jumlah, serta kolom pencarian (`?q=`). 404 bila bahasa itu belum punya artikel terbit |
| Artikel | `/{locale}/articles/{slug}` | **Halaman baca ala Medium** (kolom 680 px): judul, jenis, tanggal, waktu baca, gambar, isi, tombol bagikan, jumlah view (mulai 10), dan artikel terkait |
| Contact | `/{locale}/contact` | Kartu data kontak (satu kartu telepon per nomor admin, email, alamat, jam) dengan tombol salin, tombol WhatsApp, **formulir pesan yang membuka WhatsApp atau email dengan pesan sudah tertulis** (situs tidak menyimpan apa pun), dan peta lokasi (dari config). Lihat [`../ui/design.md`](../ui/design.md), bagian Contact |

Setiap halaman memakai komponen kepala halaman (judul, ringkasan, jejak remah bila perlu) dan diakhiri ajakan menghubungi yang sama seperti beranda. Tautan kategori, layanan, dan artikel di beranda diaktifkan di fase ini.

## Aturan keamanan dan kasus tepi

- `{category}` dan `{slug}` dibatasi pada daftar di config. Nilai lain menghasilkan 404, dan nilainya tidak dipakai untuk membentuk jalur berkas atau query.
- Judul artikel di bagian Articles beranda dipotong dengan "…" setelah dua baris (`line-clamp-2`). Kartu artikel (daftar dan artikel terkait) memotong judul setelah dua baris dan ringkasan setelah tiga baris. Elemen dengan `line-clamp-*` tidak boleh diberi kelas `block` karena menimpa `display: -webkit-box` yang dibutuhkan pemotongan.
- Judul, ringkasan, dan isi artikel tidak boleh melebarkan halaman: kata yang sangat panjang dipatahkan (`overflow-wrap: anywhere` pada judul, ringkasan, kartu, dan `.article-body`).
- Parameter `?type=` hanya menerima `article` atau `news`, nilai lain diabaikan (menampilkan semua).
- Parameter `?q=`: spasi di ujung dan berulang dirapikan, dipotong 100 karakter, nilai kosong atau bukan teks (misalnya `q[]=`) diabaikan. Karakter `%` dan `_` dicari apa adanya. Satu query hitung per jenis (tidak di-cache karena berbeda tiap kata) dan satu query daftar, tetap sama berapa pun jumlah hasilnya. Pencarian hanya memakai kolom `title` dan `excerpt` di bahasa yang dibuka, tanpa index khusus karena `ILIKE '%kata%'` tidak bisa memakai index biasa; penyaringan bahasa dan status tetap lewat index yang ada. Bila artikel sudah ribuan, pertimbangkan `pg_trgm`. Request yang membawa `?q=` dibatasi **60 per menit per IP** (limiter `article-search` di `AppServiceProvider`, balasan 429); daftar tanpa pencarian dan halaman baca tidak dibatasi.
- Anak perusahaan: pin dan kartu dirender dari config di About dan Contact.
- Bahasa tanpa artikel terbit: route artikel 404, serta tautan menu dan bagian beranda hilang (A2).
- Tautan Google Maps dibuat dari data anak perusahaan, dibuka dengan `rel="noopener noreferrer"` dan jelas ditandai sebagai tautan luar.
- Tombol WhatsApp dan Call memakai semua nomor aktif dari dashboard admin (`wa.me/<nomor>` dan `tel:+<nomor>`) lewat pemilih nomor ([`../whatsapp/overview.md`](../whatsapp/overview.md)), dan hanya dirender bila ada nomor aktif.
- Setiap halaman menyetel judul `<title>` per halaman dan `hreflang` serta `canonical` seperti kerangka. Deskripsi meta menyusul di L6.
- Properti logis dan uji RTL berlaku seperti sebelumnya. Teks panjang (Indonesia, Arab) diuji di tiap komposisi.

## Struktur kode

- Controller invokable atau resource di `app/Http/Controllers/Landing`: `AboutController`, `ProcessController` (`index`, `show`), `ProcessDetailController` (`show`), `ServiceController`, `ArticleController` (`index`, `show`), `ContactController`.
- Route di `routes/landing/{about,processes,services,articles,contact}.php`. Resource dipakai untuk processes, services (`landing.services.index` dan `landing.services.show`), dan articles. Controller kategori: `ProcessController`.
- `config/landing.php` ditambah: `categories[].processes`, `subsidiaries`, `contact.address`, `contact.hours` (env `LANDING_CONTACT_ADDRESS`, `LANDING_CONTACT_HOURS`). Nomor WhatsApp tidak lagi di konfigurasi: dikelola admin ([`../whatsapp/overview.md`](../whatsapp/overview.md)).
- `app/Services/Landing/LandingServices.php`: layanan aktif dengan alamat dan teks `en` untuk satu bahasa, cache per bahasa yang dibersihkan `SaveService`.
- `app/Services/Landing/LandingContent.php`: akses kategori, anak perusahaan, dan daftar menu (item Services hanya bila ada layanan aktif). `SetLandingLocale` kini melepas parameter `locale` dari route setelah dipakai, supaya controller hanya menerima parameternya sendiri.
- `lang/{en,id,zh,ar,ja}/pages.php` dan `articles.php`. Kunci `lang/*/landing.php` untuk menu ditambah.
- View di `resources/views/landing/pages/` (satu berkas per halaman) dan komponen `x-landing.page-header` serta `x-landing.cta` (ajakan, dipakai juga beranda).
- Header dan footer membaca satu daftar menu yang sama dari `LandingContent::menu()`.

## Langkah dan test

Langkah: config dan teks lima bahasa, komponen bersama, halaman satu per satu, menu dan footer, aktifkan tautan di beranda, uji RTL dan ponsel.

Test (feature) yang harus ada:

- Setiap halaman mengembalikan 200 di kelima locale dengan `lang` dan `dir` yang benar, dan judulnya dalam bahasa aktif.
- Kunci `lang/*/pages.php` dan `articles.php` identik di kelima bahasa.
- Kategori yang tidak dikenal menghasilkan 404, semua 14 kategori dan semua prosesnya menghasilkan 200 dan menampilkan prosesnya.
- Artikel: pencarian `?q=` (judul dan ringkasan, besar kecil huruf, semua kata, `%` dan `_` apa adanya, per bahasa, digabung filter dan pagination, tanpa hasil tetap 200, `noindex`, jumlah query tetap, batas 60 pencarian per menit) dan filter `?type=` menyaring, slug tidak dikenal 404, dan tanpa artikel terbit route artikel 404 serta menu tidak memuatnya.
- Contact: elemen hanya muncul bila konfigurasinya diisi, dan tautan WhatsApp berbentuk `wa.me`.
- Sertifikasi tidak muncul di About, dan anak perusahaan muncul dari config.
- Menu memuat tautan ke semua halaman yang ada dan tanda halaman aktif (`aria-current`).

Pemeriksaan manual di browser: semua halaman di ponsel dan desktop, Arab dalam RTL, filter artikel, dan tautan beranda ke halaman dalam.

## Ditunda

- Langkah dan bahan untuk proses selain Alkaline Zinc, data e-commerce, brosur, video, serta TDS dan harga yang sebenarnya (L4 dan L5). Permintaan TDS dan harga kini hanya mengarah ke Contact.
- Empat grup di prototipe (Chemical Alkaline Type, Acid Type, Basic, Ferrous Metals Pre Treatment): isinya belum dikenal, jadi belum dimodelkan.
- Formulir pesan di Contact (L5), widget chat (L7).
- Sertifikasi, foto perusahaan, dan data resmi anak perusahaan menunggu konfirmasi client.
- Artikel asli dan isi lengkapnya (L4).
- Deskripsi meta, sitemap, dan pengukuran performa (L6).
- Penjelasan empat tingkat di halaman Processes menyebut Diagram dan Materials seperti di prototipe, tetapi halaman kategori belum punya diagram alur maupun daftar bahan; keduanya ditunda sampai isinya ada.
- Chat berbasis aturan dijawab dengan mengetuk bubble pilihan (tanpa kolom ketik) dan hanya menjawab dari isi situs; belum ada AI. Mode AI di prototipe ("Rule-based / AI") belum dikerjakan.
- Nomor customer service untuk tombol WhatsApp harus ditambahkan admin di dashboard (`/app/whatsapp`) sebelum rilis; tanpa nomor aktif tombolnya tidak tampil.
- Formulir Contact belum menyimpan pesan di server: ia hanya menyusun pesan untuk WhatsApp atau email. Bila nanti perlu riwayat pesan, notifikasi, atau perlindungan spam, itu fitur terpisah (tabel pesan, halaman admin, batas laju, dan jebakan spam).
