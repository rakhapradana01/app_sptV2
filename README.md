# Aplikasi Bidang Anggaran (SPT & SPJ)

Aplikasi berbasis Laravel untuk pengelolaan Surat Perintah Tugas (SPT) dan Surat Pertanggungjawaban (SPJ) pada Bidang Anggaran.

## Prasyarat (Requirements)

- PHP >= 8.2
- Composer
- Node.js & NPM
- Database (MySQL/MariaDB/SQLite)

## Panduan Installasi Lokal (Local Development)

1. **Clone Repository**
   ```bash
   git clone <repository-url>
   cd app-spt
   ```

2. **Install Dependensi PHP**
   ```bash
   composer install
   ```

3. **Install Dependensi Frontend**
   ```bash
   npm install
   ```

4. **Konfigurasi Environment**
   Salin file `.env.example` menjadi `.env` dan sesuaikan konfigurasi database Anda.
   ```bash
   cp .env.example .env
   ```

5. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

6. **Migrasi Database & Seeder**
   ```bash
   php artisan migrate --seed
   ```

7. **Jalankan Aplikasi**
   Jalankan server Laravel dan Vite secara bersamaan:
   ```bash
   # Terminal 1
   php artisan serve

   # Terminal 2
   npm run dev
   ```

---

## Panduan Installasi Server (Production Deployment)

Pastikan server Anda sudah terinstall PHP, Web Server (Nginx/Apache), dan MySQL.

1. **Persiapan di Server**
   - Clone repository ke direktori web server (misal: `/var/www/html/app-spt`).
   - Jalankan `composer install --optimize-autoloader --no-dev`.
   - Jalankan `npm install && npm run build`.

2. **Konfigurasi Environment Production**
   - Edit `.env` dan set:
     ```env
     APP_ENV=production
     APP_DEBUG=false
     APP_URL=https://domain-anda.com
     ```
   - Lakukan migrasi database: `php artisan migrate --force`.

3. **Izin Direktori (Permissions)**
   Pastikan folder storage dan bootstrap/cache dapat ditulis oleh web server:
   ```bash
   chown -R www-data:www-data storage bootstrap/cache
   chmod -R 775 storage bootstrap/cache
   ```

4. **Optimasi Laravel**
   Jalankan perintah berikut untuk mempercepat performa:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

5. **Konfigurasi Nginx (Contoh)**
   ```nginx
   server {
       listen 80;
       server_name domain-anda.com;
       root /var/www/html/app-spt/public;

       add_header X-Frame-Options "SAMEORIGIN";
       add_header X-Content-Type-Options "nosniff";

       index index.php;

       charset utf-8;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }

       location ~ /\.(?!well-known).* {
           deny all;
       }
   }
   ```

## Fitur Utama
- **Dashboard**: Statistik ringkasan data.
- **Monitoring & Evaluasi (Monev)**: Rekap progres sub-kegiatan PPTK.
- **Perjalanan Dinas**: Pembuatan Nota Dinas dan SPT.
- **SPJ**: Pengelolaan Surat Pertanggungjawaban dengan fitur Rincian Biaya, Kuitansi, dan Tanda Tangan.
- **Master Data**: Manajemen Pegawai dan Sub Kegiatan.

## Kontributor
- Rakha Pradana
