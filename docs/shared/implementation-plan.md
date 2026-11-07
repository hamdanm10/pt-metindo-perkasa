# Fondasi Dashboard (Lintas Peran)

Fondasi yang dipakai admin, staf, dan customer: infrastruktur pemeriksaan kode, akun dan login, serta kerangka dashboard. Dikerjakan berurutan, satu fase satu branch dari `development`, mengikuti `CLAUDE.md`. Fitur khusus peran ada di indeks perannya.

## Fase

| Fase | Fitur | Dokumen | Branch | Status |
| --- | --- | --- | --- | --- |
| D0 | Infrastruktur: test di PostgreSQL, pengaman database, Larastan level 8, dan `composer check` | bagian Testing dan Tooling di [`../../CLAUDE.md`](../../CLAUDE.md) dan [`../setup.md`](../setup.md) | `build/postgres-testing` | Selesai, ter-merge ke `development` |
| D1 | Fondasi akun: peran, login `/app/login` dan `/customer/login`, middleware per peran, perintah admin pertama | [`auth/overview.md`](auth/overview.md) | `feat/auth-foundation` | Selesai, ter-merge ke `development` |
| D2 | Kerangka dashboard: tata letak, menu per peran, pemilih bahasa (`en` dan `id`), halaman beranda, dan desain dasar | [`dashboard/overview.md`](dashboard/overview.md) | `feat/dashboard-shell` | Rancangan ditulis, belum ada kode |

## Keputusan

| # | Keputusan | Nilai |
| --- | --- | --- |
| 1 | Akun | Dibuat oleh admin, tanpa pendaftaran mandiri |
| 2 | Antarmuka admin dan staf | Livewire, dipasang saat fitur interaktif pertama (A1). Kerangka D2 memakai Blade biasa dan siap dipakai halaman Livewire. Halaman login tetap Blade |
| 3 | Peran | `admin`, `staff`, `customer` dalam satu tabel `users` |
| 4 | Desain dashboard | Dibahas di D2, setelah desain landing dianggap cukup |

## Belum diputuskan

- **Fitur tiap peran.** Apa yang dikerjakan admin, staf, dan customer di dashboard menyusul dan dirancang per fitur. Indeks tiap peran dibuat saat fitur pertamanya dirancang.
- **Kata sandi:** apakah pengguna kelak mengganti kata sandinya sendiri atau mengaturnya ulang lewat email.
