# TODO - Revisi Import Data & Status Vaksin

## Revisi 1: Notifikasi Duplicate ✅
- [x] Update `app/Imports/PatientsImport.php` - tambah property `$duplicateCount` dan logic detection
- [x] Update `app/Imports/PatientsImport.php` - tambah method `getDuplicateCount()`
- [x] Update `app/Http/Controllers/ImportController.php` - update notifikasi dengan info duplicate

## Revisi 2: Status Vaksin Pertama ✅
- [x] Update `app/Services/VaccineScheduleService.php` - ubah status dosis pertama jadi 'completed'

## Summary Perubahan

### File 1: `app/Services/VaccineScheduleService.php`
- Dosis pertama (dosis_ke = 1) sekarang langsung status 'completed'
- Dosis berikutnya tetap status 'pending'

### File 2: `app/Imports/PatientsImport.php`
- Tambah property `$duplicateCount` untuk menghitung data duplicate di database
- Modifikasi logic di `collection()` method: jika vaccine sudah ada dengan tanggal sama, increment `$duplicateCount` dan skip
- Tambah method `getDuplicateCount()` untuk mengembalikan jumlah duplicate

### File 3: `app/Http/Controllers/ImportController.php`
- Update notifikasi dengan 4 skenario:
  1. Jika ada data baru + ada duplicate: "Import berhasil! X data baru diimport. Y data sudah ada di sistem (tidak diimport ulang)."
  2. Jika tidak ada data baru + ada duplicate: "Tidak ada data baru yang diimport. X data sudah ada di sistem."
  3. Jika tidak ada data baru + tidak ada duplicate: "Tidak ada data yang diimport."
  4. Jika ada data baru + tidak ada duplicate: "Import berhasil! X data berhasil diimport."
