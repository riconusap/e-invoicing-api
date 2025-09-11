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

Proyek ini sepenuhnya dikemas dalam kontainer dengan Docker. Skrip instalasi sederhana disediakan untuk mengotomatiskan seluruh proses penyiapan.

1.  **Clone repositori (jika Anda belum melakukannya):**
    ```bash
    git clone https://github.com/riconusap/e-invoicing-api.git
    cd e-invoicing-api
    ```

2.  **Jalankan skrip instalasi:**
    Pertama, buat skrip agar dapat dieksekusi:
    ```bash
    chmod +x install.sh
    ```
    Kemudian, jalankan skripnya:
    ```bash
    ./install.sh
    ```

Skrip ini akan memandu Anda melalui proses instalasi dan mengotomatiskan langkah-langkah berikut:
- Membangun dan memulai kontainer Docker.
- Menginstal dependensi Composer.
- Membuat file `.env` dari `.env.example`.
- Menghasilkan kunci aplikasi (`APP_KEY`).
- Menghasilkan kunci rahasia JWT (`JWT_SECRET`).
- Menunggu hingga kontainer database siap.
- Membuat database.
- Menjalankan migrasi dan seeder database.
- Membuat symlink penyimpanan.

### Mengakses Aplikasi

-   **API:** API tersedia di `http://localhost:8080`. Endpoint diawali dengan `/api`. Sebagai contoh, endpoint login adalah permintaan POST ke `http://localhost:8080/api/auth/login`.
-   **Database:** Database MySQL dapat diakses dari mesin host Anda di `localhost:3032`.

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
