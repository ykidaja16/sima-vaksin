# Sistem Reminder Vaksin

Aplikasi Laravel untuk manajemen reminder vaksinasi dengan fitur import Excel, manajemen jadwal, dan reminder H-7.

## Fitur Utama

### 1. Master Data Management (Role IT)
- **Manajemen User**: CRUD user dengan role IT dan Admin
- **Manajemen Vaksin**: CRUD jenis vaksin dengan interval dosis (JSON array)
- **Manajemen Cabang**: CRUD cabang dengan kode prefix PID (2 huruf)

### 2. Operasional (Role Admin)
- **Data Pasien**: View dan hapus data pasien dengan filter cabang
- **Reminder H-7**: Daftar pasien yang perlu diingatkan dalam 7 hari
- **Import Excel**: Upload data pasien dengan validasi PID prefix

### 3. Sistem Role & Autentikasi
- **Master Table Role**: Role IT dan Admin tersimpan di database
- **Login dengan Username**: Autentikasi menggunakan username (bukan email)
- **Middleware Role**: Akses menu berdasarkan role user
- **Status User**: User dapat diaktifkan/dinonaktifkan

## Struktur Database

### Tabel Roles
- `id`, `nama_role` (IT/Admin), `deskripsi`, `is_active`, timestamps

### Tabel Branches (Cabang)
- `id`, `nama_cabang`, `kode_prefix` (2 huruf, contoh: LX, LZ), `alamat`, `no_telp`, `is_active`, timestamps

### Tabel Vaccine Types (Master Vaksin)
- `id`, `nama_vaksin`, `deskripsi`, `interval_bulan` (JSON), `total_dosis`, `is_active`, timestamps

### Tabel Users
- `id`, `name`, `username` (unique), `password`, `role_id` (FK), `is_active`, timestamps

### Tabel Patients
- `id`, `branch_id` (FK), `pid` (unique per cabang), `nama_pasien`, `no_hp`, `alamat`, `dob`, timestamps

### Tabel Vaccines
- `id`, `patient_id` (FK), `vaccine_type_id` (FK), `tanggal_vaksin_pertama`, timestamps

### Tabel Vaccine Schedules
- `id`, `patient_id` (FK), `vaccine_id` (FK), `dosis_ke`, `tanggal_vaksin`, `status` (pending/completed), `completed_at`, `keterangan`, timestamps

## Kode Prefix Cabang

| Cabang | Prefix | Contoh PID |
|--------|--------|------------|
| Ciliwung | LX | LXB0049356 |
| Tangkuban Perahu | LZ | LZD0010534 |

## Login Credentials (Default)

| Role | Username | Password |
|------|----------|----------|
| IT | it | password |
| Admin | admin | password |

## Validasi Import Excel

1. **Pilih Cabang**: User harus memilih cabang sebelum import
2. **Validasi Prefix**: PID harus diawali dengan kode prefix cabang (LX/LZ)
3. **Validasi Nama**: Jika PID sudah ada dengan nama berbeda, import ditolak
4. **Validasi Duplikat**: Data dianggap duplikat jika PID, Nama, DOB, Jenis Vaksin, dan Tanggal Vaksin Pertama sama

## Interval Vaksin (Default)

| Jenis Vaksin | Interval (Bulan) | Total Dosis |
|--------------|------------------|-------------|
| HPV | 0, 2, 6 | 3 |
| Hepatitis | 0, 1, 6 | 3 |
| Influenza | 0, 12 | 2 |

## Instalasi

```bash
# 1. Install dependencies
composer install

# 2. Jalankan migration
php artisan migrate

# 3. Jalankan seeder untuk data awal
php artisan db:seed

# 4. Jalankan server
php artisan serve
```

## Teknologi

- Laravel 11.x
- Tailwind CSS
- Font Awesome
- Maatwebsite/Excel untuk import
- Carbon untuk manajemen tanggal

## Catatan Penting

- User IT hanya bisa akses menu Master Data (User, Vaksin, Cabang)
- User Admin hanya bisa akses menu Operasional (Pasien, Reminder, Import)
- 1 Pasien bisa memiliki data di 2 cabang (2 PID berbeda)
- PID unik per cabang (composite unique: branch_id + pid)
- Reminder H-7 menampilkan pasien dengan vaksinasi dalam 7 hari ke depan
