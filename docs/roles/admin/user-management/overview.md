# Kelola Akun (A1)

Admin mengelola akun **staf** dan **customer** dari dua halaman terpisah di dashboard: daftar dengan pencarian dan penyaring status, membuat akun, mengubah data, mengatur ulang kata sandi, dan menonaktifkan atau mengaktifkan akun. **Aplikasi hanya punya satu admin**: admin tidak dibuat dari dashboard, tidak muncul di daftar, dan tidak bisa dibuat lagi (lihat keputusan 5). Hanya admin yang bisa membuka halaman ini.

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | **Hanya admin** yang mengelola akun, lewat dua halaman: **Staff** di `/app/staff` dan **Customers** di `/app/customers`. Staf dan customer mendapat 403, dan kedua menu (kelompok "User Management") hanya tampil untuk admin | Keputusan client. Dijaga middleware dan Policy, bukan hanya menu |
| 2 | Data akun: nama, email, peran, dan kata sandi awal yang ditetapkan admin. Tidak ada kolom lain. **Bahasa tidak diisi admin**: akun baru berbahasa Inggris (`en`) dan pengguna mengubahnya sendiri lewat pemilih bahasa (D2). Data khusus customer kelak masuk tabel profil terpisah | Sesuai aturan `users` di `CLAUDE.md` |
| 3 | **Akun tidak dihapus**, hanya dinonaktifkan (`is_active`) dan bisa diaktifkan lagi | Riwayat tetap utuh, dan penghapusan tidak bisa dibatalkan |
| 4 | Kata sandi awal dan atur ulang kata sandi ditetapkan admin. Tanpa email, tanpa tautan | Keputusan di D1 |
| 5 | **Hanya ada satu admin.** Admin dibuat sekali lewat `php artisan admin:create`, yang menolak bila admin sudah ada. Dashboard tidak bisa membuat, mengubah, atau menonaktifkan admin: halaman Staff dan Customers hanya menampilkan perannya sendiri, dan halaman ubah untuk admin (atau peran lain) memberi 404 | Keputusan client. Aturan ada di Action `CreateUser` dan `UserPolicy`, bukan hanya di tampilan |
| 6 | Menonaktifkan akun atau mengatur ulang kata sandinya **mengakhiri semua sesi akun itu** saat itu juga | Akses cabut segera, bukan menunggu permintaan berikutnya |
| 7 | **Peran tidak bisa diubah** setelah akun dibuat, dan formulir tidak punya pilihan peran: peran ditentukan halaman tempat akun dibuat (Staff atau Customers). Salah peran diperbaiki dengan membuat akun baru dan menonaktifkan yang lama | Menutup jalan menaikkan akun menjadi admin lewat formulir, dan menyederhanakan formulir |
| 8 | Livewire v4 dipasang. Komponen halaman penuh tipis yang memanggil Action. Komponen tetap memeriksa izin lewat Policy | Aturan arsitektur di `CLAUDE.md` |
| 9 | Middleware `auth`, `role`, dan `SetDashboardLocale` didaftarkan sebagai **persistent middleware** Livewire | Permintaan susulan komponen (`/livewire/update`) tidak lewat route halaman, jadi tanpa ini izin dan bahasa tidak diperiksa lagi |
| 10 | Pencarian dan penyaring (status) berjalan langsung di daftar, dengan penomoran halaman 15 per halaman | Cukup untuk jumlah akun yang wajar |
| 11 | Komponen bersama dibuat di sini karena baru dipakai sekarang: bidang formulir, tombol, lencana status, pesan sukses dan gagal, dan tabel | Sesuai D2: komponen dibuat bersama fitur pertama yang butuh |

## Halaman dan alur

| Halaman | Alamat | Isi |
| --- | --- | --- |
| Daftar | `/app/staff`, `/app/customers` | Tabel nama, email, status, dan tombol ubah untuk peran halaman itu. Pencarian nama atau email, penyaring status, dan tombol "Tambah staf" atau "Tambah customer" |
| Tambah | `/app/staff/create`, `/app/customers/create` | Nama, email, dan kata sandi awal. Peran mengikuti halaman. **Kolom kata sandi sudah terisi `MetindoPerkasa2026`** (`config('auth.default_account_password')`, bisa diubah lewat `ACCOUNT_DEFAULT_PASSWORD` di `.env`), dan admin boleh menggantinya sebelum menyimpan |
| Ubah | `/app/staff/{user}/edit`, `/app/customers/{user}/edit` | Nama dan email. Bahasa dan peran tidak diubah dari sini. Bagian terpisah untuk atur ulang kata sandi, dan tombol nonaktifkan atau aktifkan. Akun dengan peran lain (termasuk admin) memberi 404 |

1. Admin membuka daftar, mencari atau menyaring, lalu memilih akun.
2. Tambah: mengisi formulir, simpan, kembali ke daftar dengan pesan sukses. Email disimpan huruf kecil.
3. Ubah: perubahan data disimpan dengan validasi di tempat. Atur ulang kata sandi meminta kata sandi baru dan mengakhiri sesi akun itu.
4. Nonaktifkan: butuh konfirmasi. Akun tidak bisa masuk lagi dan sesinya berakhir. Aktifkan mengembalikan akses.

## Aturan keamanan dan kasus tepi

- Semua halaman dan aksi dijaga `role:admin` dan `UserPolicy` (`viewAny`, `create`, `update`, `setActive`, `resetPassword`); `update`, `resetPassword`, dan `setActive` menolak akun admin. Setiap aksi Livewire memanggil `authorize`, termasuk setelah halaman terbuka lama.
- Id akun di komponen dikunci (`#[Locked]`), dan `role` serta kolom lain divalidasi di sisi server (`Rule::enum`). `locale` tidak ada di formulir sehingga tidak bisa diubah dari sini. Nilai dari browser tidak dipercaya.
- Aturan satu admin diperiksa di Action `CreateUser` (juga dipakai `admin:create`), bukan di tampilan, dan menghasilkan pesan galat yang jelas. `SetUserActive` tetap menolak menonaktifkan diri sendiri dan admin aktif terakhir. Peran halaman (`kind`) dikunci (`#[Locked]`) dan hanya `staff` atau `customer` yang diterima; nilai lain 404.
- Email unik tanpa membedakan huruf besar-kecil, kecuali akun yang sedang diubah. Kata sandi: minimal 8 karakter dengan huruf besar, huruf kecil, dan angka (aturan sama dengan `admin:create`).
- Kata sandi awal bawaan itu sama untuk semua akun baru dan diketahui admin, jadi akun yang memakainya harus mengganti kata sandinya. Pengguna belum bisa mengganti kata sandinya sendiri (lihat "Ditunda"), sehingga untuk sementara admin yang mengatur ulang kata sandi lewat halaman ubah bila perlu. Server tetap memvalidasi apa pun yang ada di kolom.
- Kata sandi tidak ditampilkan, tidak dicatat di log, dan kolom kata sandi dikosongkan setelah dikirim.
- Pengakhiran sesi memakai tabel `sessions` (driver `database`). Bila driver sesi lain, akun nonaktif tetap ditolak pada permintaan berikutnya oleh middleware (perilaku D1).
- Pesan validasi dan antarmuka lewat `lang/{en,id}`, dan kolom baru ditambahkan di `attributes`.

## Struktur kode

- Pasang `livewire/livewire` (v4). Daftarkan persistent middleware di `AppServiceProvider`. `dashboard.css` memindai tampilan penomoran Livewire supaya kelas Tailwind-nya ikut dibuat.
- Livewire: `app/Livewire/Users/Index.php`, `Create.php`, dan `Edit.php`, dengan objek formulir `app/Livewire/Forms/UserForm.php`. View di `resources/views/livewire/users/`.
- Action di `app/Actions/Users/`: `CreateUser` (sudah ada, bahasa selalu `en`), `UpdateUser`, `ResetUserPassword`, `SetUserActive`, dan `ListUsers` (pencarian, penyaring, penomoran). Tanpa `request()`, dan aturan admin terakhir di sini. `app/Services/Auth/SessionRevoker.php` mengakhiri sesi akun dan dipakai dua Action.
- `app/Policies/UserPolicy.php` (`setActive` menolak diri sendiri). Route di `routes/app/accounts.php` dengan `role:admin`: satu set halaman (`Users\Index`, `Create`, `Edit`) didaftarkan dua kali dengan default `kind` (`staff` atau `customer`), dan trait `ManagesAccountKind` memetakan `kind` ke `Role` serta awalan route (`Role::accountRoutePrefix()`). Scope `User::activeAdmins()` dipakai aturan admin terakhir, dan trait `ResolvesActor` menyediakan pengguna yang sedang masuk di komponen.
- `DashboardMenu` ditambah item "Staff" dan "Customers" untuk admin, di kelompok "User Management".
- Komponen Blade di `resources/views/components/dashboard/` dan `form/`: bidang formulir, pilihan, tombol, lencana, pesan, dan tabel.
- `lang/{en,id}/users.php`, ditambah kolom di `lang/id/validation.php`.

## Langkah dan test

Langkah: pasang Livewire dan persistent middleware, Policy dan Action, daftar, tambah, ubah dan atur ulang kata sandi, nonaktifkan, komponen bersama dan teks, test.

Test yang harus ada:

- Admin membuka daftar, tambah, dan ubah. Staf dan customer mendapat 403 di semua alamat dan semua aksi Livewire. Tamu diarahkan ke login. Menu "Akun" hanya untuk admin.
- Daftar: pencarian nama dan email (tanpa membedakan huruf), penyaring peran dan status, penomoran 15 per halaman, dan jumlah query tetap saat baris bertambah.
- Tambah: membuat akun dengan kata sandi ter-hash dan email huruf kecil. Menolak email ganda (beda huruf), peran di luar daftar, dan kata sandi lemah.
- Ubah: menyimpan data, menjaga email unik, dan menolak mengubah akun lain menjadi admin terakhir yang hilang.
- Atur ulang kata sandi: kata sandi lama tidak berlaku, yang baru berlaku, dan sesi akun itu berakhir.
- Nonaktifkan dan aktifkan: akun nonaktif tidak bisa masuk, sesinya berakhir saat itu juga, dan aktifkan mengembalikan akses.
- Admin tidak bisa menonaktifkan atau menurunkan dirinya, dan tidak bisa menonaktifkan atau menurunkan admin aktif terakhir.
- Persistent middleware: test otomatis memastikan `EnsureRole` dan `SetDashboardLocale` terdaftar. Perilakunya (aksi Livewire dari admin yang sudah diturunkan perannya ditolak dengan 403) diperiksa di browser, karena test Livewire tidak melewati titik akhir pembaruan.
- Teks tampil dalam `en` dan `id`, dan kunci `lang/*/users.php` identik.
- Unit: setiap Action.

Pemeriksaan manual di browser: alur penuh di desktop dan ponsel, pencarian langsung, konfirmasi nonaktif, dan keyboard.

## Ditunda

- Menghapus akun, pembuatan kata sandi acak otomatis, dan undangan atau atur ulang lewat email.
- Pengguna mengganti kata sandi atau profilnya sendiri.
- Profil khusus customer (tabel terpisah), aksi massal, ekspor, dan log aktivitas (siapa mengubah apa).
- Hak staf yang lebih rinci. Saat ini staf tidak punya akses ke fitur ini sama sekali.
- Pengaman tingkat basis data untuk satu admin (indeks unik parsial pada `role = 'admin'`). Ditunda karena basis data lokal yang sudah ada punya lebih dari satu admin, sehingga migrasinya gagal sampai data dirapikan.
- Halaman untuk admin mengganti kata sandi atau profilnya sendiri (admin tidak lagi bisa mengubah akunnya lewat halaman akun).
