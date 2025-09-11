# Sistem Manajemen E-Invoicing

Aplikasi API backend komprehensif yang dirancang untuk mengelola faktur, karyawan, klien, dan penempatan. Sistem ini dibangun dengan backend API Laravel.

## 🚀 Fitur

- **Autentikasi:** Autentikasi pengguna berbasis JWT (registrasi, login, logout, perpanjangan token).
- **Manajemen Karyawan:** Operasi CRUD untuk profil karyawan.
- **Manajemen Klien:** Melacak informasi klien, detail perusahaan, dan kontak.
- **Manajemen Penempatan:** Mengelola penempatan karyawan, hubungan klien-karyawan, dan status penempatan.
- **Manajemen Faktur:** Membuat dan mengelola faktur, melacak status (Lunas, Belum Lunas, Jatuh Tempo).

## 📁 Struktur Proyek

Proyek ini adalah aplikasi API backend Laravel.

```
e-invoicing/
├── app/                  # Kode aplikasi Laravel
├── routes/               # Rute API
├── database/             # Migrasi dan seeder database
├── config/               # Konfigurasi Laravel
├── .env                  # Variabel lingkungan backend
└── docker-compose.yml    # Pengaturan Docker untuk seluruh aplikasi
```

## 🛠️ Teknologi yang Digunakan

### Backend (API Laravel)
- **Framework:** Laravel 8.x
- **Bahasa:** PHP 8.x
- **Database:** MySQL
- **Autentikasi:** JWT (JSON Web Tokens)
- **API:** RESTful API

## 🐳 Pengaturan Docker

Proyek ini dapat dengan mudah diatur dan dijalankan menggunakan Docker dan Docker Compose. Pengaturan ini mencakup aplikasi Laravel (PHP-FPM), Nginx (server web), dan database MySQL.

### Prasyarat
- Docker
- Docker Compose

### Instalasi dan Menjalankan

1.  **Build dan jalankan kontainer Docker:**
    ```bash
    docker-compose up --build -d
    ```
    Perintah ini akan:
    -   Membangun image aplikasi Laravel.
    -   Memulai server web Nginx.
    -   Memulai database MySQL.

2.  **Akses Aplikasi:**
    -   Aplikasi Laravel berfungsi sebagai API backend. Akses langsung ke URL root (`http://localhost:8080`) akan menghasilkan error `404 Not Found` karena tidak ada rute web yang didefinisikan.
    -   Endpoint API dapat diakses di bawah prefiks `/api`. Misalnya, Anda dapat mengakses endpoint login autentikasi melalui `http://localhost:8080/api/auth/login` (permintaan POST).
    -   Database MySQL dapat diakses pada port `3032` dari mesin host Anda (`localhost:3032`).

3.  **Jalankan Migrasi dan Seeder Laravel:**
    Setelah kontainer berjalan, jalankan perintah berikut untuk mengatur skema database Anda dan mengisi data awal:
    ```bash
    docker-compose exec app php artisan migrate --seed
    ```

4.  **Buat Kunci Aplikasi:**
    Buat kunci aplikasi unik untuk Laravel:
    ```bash
    docker-compose exec app php artisan key:generate
    ```
5. **Pengaturan Symlink:**
     ```bash
    docker-compose exec app php artisan storage:link
    ```

## ⚙️ Konfigurasi

### Variabel Lingkungan (`.env`)

Setelah menyalin `.env.example` ke `.env`, konfigurasikan variabel lingkungan aplikasi Anda. Pengaturan database utama adalah:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=e_invoicing
DB_USERNAME=root
DB_PASSWORD=rootpass
```

*Catatan: `DB_HOST` diatur ke `mysql` karena itu adalah nama layanan dari kontainer database di dalam jaringan Docker.* `DB_PASSWORD` harus cocok dengan `MYSQL_ROOT_PASSWORD` yang diatur di `docker-compose.yml`.

## 🚀 Pengembangan

### Menjalankan Aplikasi (tanpa Docker untuk backend)

Jika Anda lebih suka menjalankan backend Laravel langsung di mesin host Anda:

1.  **Mulai backend Laravel:**
    ```bash
    php artisan serve
    ```
    API akan tersedia di `http://localhost:8000`.

### Pengisian Database (Seeding)

Untuk menjalankan pengisian database, jalankan perintah berikut di dalam kontainer Docker `app`:
```bash
docker-compose exec app php artisan db:seed
```

### Pengujian

Untuk menjalankan pengujian backend, jalankan perintah berikut di dalam kontainer Docker `app`:
```bash
docker-compose exec app php artisan test
```

## 🤝 Berkontribusi

Kontribusi sangat diterima! Silakan ikuti langkah-langkah berikut:

1.  Fork repositori ini.
2.  Buat branch fitur baru (`git checkout -b feature/NamaFiturAnda`).
3.  Buat perubahan Anda.
4.  Tambahkan tes untuk perubahan Anda jika berlaku.
5.  Commit perubahan Anda (`git commit -m 'Menambahkan fitur baru'`).
6.  Push ke branch (`git push origin feature/NamaFiturAnda`).
7.  Buka Pull Request.

## 📄 Lisensi

Proyek ini adalah perangkat lunak sumber terbuka yang dilisensikan di bawah [lisensi MIT](https://opensource.org/licenses/MIT).

### Menjalankan Migrasi Satu per Satu

Proyek ini menyediakan skrip untuk menjalankan file migrasi satu per satu secara berurutan. Ini bisa berguna untuk proses *debugging* atau jika Anda memerlukan kontrol lebih saat proses migrasi.

Untuk menggunakan skrip ini, jalankan perintah berikut dari terminal Anda:

```bash
./run_migrations.sh
```

**Catatan:** Ini bukanlah cara standar untuk menjalankan migrasi di Laravel. Perintah standar adalah `php artisan migrate`. Gunakan skrip ini hanya jika Anda memiliki kebutuhan khusus untuk menjalankan migrasi secara individual.
