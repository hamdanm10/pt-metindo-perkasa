# Kelola Akun (A1)

Admin mengelola akun staf, customer, dan admin lain dari dashboard: daftar dengan pencarian dan penyaring, membuat akun, mengubah data, mengatur ulang kata sandi, dan menonaktifkan atau mengaktifkan akun. Hanya admin yang bisa melakukannya. Ini fitur interaktif pertama, jadi Livewire dipasang di fase ini. Fase dan statusnya di [`implementation-plan.md`](implementation-plan.md). Akun dan login: [`../../shared/auth/overview.md`](../../shared/auth/overview.md). Kerangka dashboard: [`../../shared/dashboard/overview.md`](../../shared/dashboard/overview.md).

## Keputusan

| # | Keputusan | Alasan |
| --- | --- | --- |
| 1 | **Hanya admin** yang mengelola akun, di `/app/users`. Staf dan customer mendapat 403, dan menu "Akun" hanya tampil untuk admin | Keputusan client. Dijaga middleware dan Policy, bukan hanya menu |
| 2 | Data akun: nama, email, peran, dan kata sandi awal yang ditetapkan admin. Tidak ada kolom lain. **Bahasa tidak diisi admin**: akun baru berbahasa Inggris (`en`) dan pengguna mengubahnya sendiri lewat pemilih bahasa (D2). Data khusus customer kelak masuk tabel profil terpisah | Sesuai aturan `users` di `CLAUDE.md` |
| 3 | **Akun tidak dihapus**, hanya dinonaktifkan (`is_active`) dan bisa diaktifkan lagi | Riwayat tetap utuh, dan penghapusan tidak bisa dibatalkan |
| 4 | Kata sandi awal dan atur ulang kata sandi ditetapkan admin. Tanpa email, tanpa tautan | Keputusan di D1 |
| 5 | Admin **tidak bisa** menonaktifkan atau menurunkan peran dirinya sendiri, dan tidak bisa menonaktifkan atau menurunkan admin aktif terakhir | Mencegah sistem tanpa admin |
| 6 | Menonaktifkan akun atau mengatur ulang kata sandinya **mengakhiri semua sesi akun itu** saat itu juga | Akses cabut segera, bukan menunggu permintaan berikutnya |
| 7 | Mengubah peran akun yang sudah ada diperbolehkan oleh admin, dengan aturan nomor 5 | Salah memilih peran saat membuat akun harus bisa diperbaiki |
| 8 | Livewire v4 dipasang. Komponen halaman penuh tipis yang memanggil Action. Komponen tetap memeriksa izin lewat Policy | Aturan arsitektur di `CLAUDE.md` |
| 9 | Middleware `auth`, `role`, dan `SetDashboardLocale` didaftarkan sebagai **persistent middleware** Livewire | Permintaan susulan komponen (`/livewire/update`) tidak lewat route halaman, jadi tanpa ini izin dan bahasa tidak diperiksa lagi |
| 10 | Pencarian dan penyaring (peran, status) berjalan langsung di daftar, dengan penomoran halaman 15 per halaman | Cukup untuk jumlah akun yang wajar |
| 11 | Komponen bersama dibuat di sini karena baru dipakai sekarang: bidang formulir, tombol, lencana status, pesan sukses dan gagal, dan tabel | Sesuai D2: komponen dibuat bersama fitur pertama yang butuh |

## Halaman dan alur

| Halaman | Alamat | Isi |
| --- | --- | --- |
| Daftar | `/app/users` | Tabel nama, email, peran, status, dan tanggal dibuat. Pencarian nama atau email, penyaring peran dan status, dan tombol "Tambah akun" |
| Tambah | `/app/users/create` | Nama, email, peran, dan kata sandi awal |
| Ubah | `/app/users/{user}/edit` | Nama, email, dan peran. Bahasa akun tidak diubah dari sini. Bagian terpisah untuk atur ulang kata sandi, dan tombol nonaktifkan atau aktifkan |

1. Admin membuka daftar, mencari atau menyaring, lalu memilih akun.
2. Tambah: mengisi formulir, simpan, kembali ke daftar dengan pesan sukses. Email disimpan huruf kecil.
3. Ubah: perubahan data disimpan dengan validasi di tempat. Atur ulang kata sandi meminta kata sandi baru dan mengakhiri sesi akun itu.
4. Nonaktifkan: butuh konfirmasi. Akun tidak bisa masuk lagi dan sesinya berakhir. Aktifkan mengembalikan akses.

## Aturan keamanan dan kasus tepi

- Semua halaman dan aksi dijaga `role:admin` dan `UserPolicy` (`viewAny`, `create`, `update`, `deactivate`, `resetPassword`). Setiap aksi Livewire memanggil `authorize`, termasuk setelah halaman terbuka lama.
- Id akun di komponen dikunci (`#[Locked]`), dan `role` serta kolom lain divalidasi di sisi server (`Rule::enum`). `locale` tidak ada di formulir sehingga tidak bisa diubah dari sini. Nilai dari browser tidak dipercaya.
- Aturan admin terakhir dan diri sendiri diperiksa di Action, bukan di tampilan, dan menghasilkan pesan galat yang jelas.
- Email unik tanpa membedakan huruf besar-kecil, kecuali akun yang sedang diubah. Kata sandi: minimal 8 karakter dengan huruf besar, huruf kecil, dan angka (aturan sama dengan `admin:create`).
- Kata sandi tidak ditampilkan, tidak dicatat di log, dan kolom kata sandi dikosongkan setelah dikirim.
- Pengakhiran sesi memakai tabel `sessions` (driver `database`). Bila driver sesi lain, akun nonaktif tetap ditolak pada permintaan berikutnya oleh middleware (perilaku D1).
- Pesan validasi dan antarmuka lewat `lang/{en,id}`, dan kolom baru ditambahkan di `attributes`.

## Struktur kode

- Pasang `livewire/livewire` (v4). Daftarkan persistent middleware di `AppServiceProvider`. `dashboard.css` memindai tampilan penomoran Livewire supaya kelas Tailwind-nya ikut dibuat.
- Livewire: `app/Livewire/Users/Index.php`, `Create.php`, dan `Edit.php`, dengan objek formulir `app/Livewire/Forms/UserForm.php`. View di `resources/views/livewire/users/`.
- Action di `app/Actions/Users/`: `CreateUser` (sudah ada, bahasa selalu `en`), `UpdateUser`, `ResetUserPassword`, `SetUserActive`, dan `ListUsers` (pencarian, penyaring, penomoran). Tanpa `request()`, dan aturan admin terakhir di sini. `app/Services/Auth/SessionRevoker.php` mengakhiri sesi akun dan dipakai dua Action.
- `app/Policies/UserPolicy.php` (`setActive` menolak diri sendiri). Route di `routes/app/users.php` dengan `role:admin`. Scope `User::activeAdmins()` dipakai aturan admin terakhir, dan trait `ResolvesActor` menyediakan pengguna yang sedang masuk di komponen.
- `DashboardMenu` ditambah item "Akun" untuk admin.
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
