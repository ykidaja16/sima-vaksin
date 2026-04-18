# TODO: Implementasi Input Data Manual

## Task Description
1. ✅ Menu "Import Excel" diganti menjadi "Input Data"
2. ✅ Ada submenu: "Import Excel" dan "Input Manual"
3. ✅ Buat menu "Input Manual" dengan form pengisian manual:
   - User mengisi form
   - Klik button "Tambah" 
   - Data disimpan di tabel bawahnya (temporary/buffer)
   - User bisa mengisi banyak data
   - Setelah selesai, klik "Save" dan data tersimpan di database

## Implementation Progress

### 1. ✅ Update Routes (routes/web.php)
- [x] Tambahkan import ManualInputController
- [x] Route GET /input-data/manual - Menampilkan form
- [x] Route POST /input-data/manual/add - Menambahkan ke session
- [x] Route POST /input-data/manual/save - Menyimpan ke database
- [x] Route POST /input-data/manual/clear - Mengosongkan session
- [x] Route DELETE /input-data/manual/remove/{id} - Menghapus satu item

### 2. ✅ Buat ManualInputController (app/Http/Controllers/ManualInputController.php)
- [x] Method index() - Menampilkan form dan data temporary
- [x] Method addToSession() - Validasi dan tambah ke session
- [x] Method save() - Simpan semua data ke database dengan transaction
- [x] Method remove() - Hapus satu item dari session
- [x] Method clear() - Hapus semua data dari session
- [x] Validasi PID prefix sesuai cabang
- [x] Cek duplikat PID di database dan session
- [x] Generate jadwal vaksin otomatis menggunakan VaccineScheduleService

### 3. ✅ Buat View Input Manual (resources/views/manual-input/index.blade.php)
- [x] Form input dengan field lengkap: pid, nama_pasien, no_hp, alamat, dob, jenis_vaksin, tanggal_vaksin_pertama, branch_id
- [x] Tabel menampilkan data temporary dengan informasi lengkap
- [x] Button "Tambah ke Daftar" untuk menambahkan ke temporary
- [x] Button hapus di setiap row untuk menghapus data temporary
- [x] Button "Kosongkan Daftar" untuk mengosongkan semua data
- [x] Button "Simpan Semua Data" untuk menyimpan ke database
- [x] Empty state ketika belum ada data
- [x] Tips dan informasi penggunaan

### 4. ✅ Update Layout Menu (resources/views/layouts/app.blade.php)
- [x] Ganti menu "Import Excel" menjadi "Input Data" dengan icon keyboard
- [x] Tambahkan Alpine.js untuk dropdown functionality
- [x] Submenu "Import Excel" dengan icon file-excel
- [x] Submenu "Input Manual" dengan icon edit
- [x] Highlight active menu berdasarkan route
- [x] Animasi transition untuk dropdown

## Testing Checklist

### Critical Path Testing
- [ ] Akses menu Input Data > Input Manual
- [ ] Isi form dengan data valid
- [ ] Klik "Tambah ke Daftar" - data muncul di tabel
- [ ] Tambah beberapa data lagi
- [ ] Hapus satu data dari tabel
- [ ] Klik "Simpan Semua Data" - data tersimpan di database
- [ ] Cek data pasien di halaman Data Pasien
- [ ] Cek jadwal vaksin di database

### Error Handling Testing
- [ ] Validasi PID prefix tidak sesuai cabang
- [ ] Validasi PID duplikat di database
- [ ] Validasi PID duplikat di session
- [ ] Validasi field wajib diisi
- [ ] Simpan tanpa data di session

### Edge Cases
- [ ] Input data dengan tanggal lahir berbeda format
- [ ] Input data dengan jenis vaksin berbeda
- [ ] Banyak data sekaligus (10+ data)

## Notes
- Controller terpisah (ManualInputController) sesuai permintaan
- Menggunakan session untuk penyimpanan temporary
- Validasi lengkap sebelum data masuk ke session
- Transaction database untuk keamanan data
- Generate jadwal vaksin otomatis
- Tidak mengubah design UI yang sudah ada
- Tidak mengubah flow bisnis yang sudah ada
