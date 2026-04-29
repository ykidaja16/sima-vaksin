# TODO: Perbaikan Validasi PID Input Data Manual

## Status: 🚀 Sedang dikerjakan

### Plan Breakdown:
- [ ] **Step 1**: Update method `addToSession()` di ManualInputController.php
  - Ubah validasi PID untuk cek nama juga
- [ ] **Step 2**: Update method `save()` di ManualInputController.php  
  - Handle PID duplikat dengan nama sama (tambah vaksin baru)
- [ ] **Step 3**: Test implementasi
  - Test case 1: PID sama + nama sama → allow
  - Test case 2: PID sama + nama berbeda → block  
  - Test case 3: Batch save duplikat
- [ ] **Step 4**: Complete & cleanup

**File target**: `app/Http/Controllers/ManualInputController.php`
**Estimasi**: 2 file edits, no UI changes
