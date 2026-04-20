@extends('layouts.app')

@section('title', 'Input Data Manual - Sistem Reminder Vaksin')

@section('content')
<div class="max-w-6xl mx-auto space-y-6 px-4 sm:px-0">
    <!-- Header -->
    <div class="text-center">
        <h1 class="text-2xl font-bold text-gray-900">Input Data Manual</h1>
        <p class="text-gray-600">Isi form di bawah untuk menambahkan data pasien dan jadwal vaksinasi</p>
    </div>

    <!-- Form Input -->
    <div class="bg-white rounded-lg shadow p-6">
        <form id="manualInputForm" class="space-y-6">
            @csrf
            
            <!-- Error Alert Container -->
            <div id="errorAlert" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <strong class="font-bold">Error:</strong>
                <ul id="errorList" class="mt-1 list-disc list-inside text-sm"></ul>
                <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="hideError()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Branch Selection -->
                <div>
                    <label for="branch_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-hospital mr-2"></i>Cabang <span class="text-red-500">*</span>
                    </label>
                    <select name="branch_id" id="branch_id" required
                            class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Pilih Cabang --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">
                                {{ $branch->nama_cabang }} (Prefix: {{ $branch->kode_prefix }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- PID -->
                <div>
                    <label for="pid" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-id-card mr-2"></i>PID <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="pid" id="pid" required
                           class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Contoh: LXB001">
                    <p class="text-xs text-gray-500 mt-1">PID harus diawali dengan kode prefix cabang</p>
                </div>

                <!-- Nama Pasien -->
                <div>
                    <label for="nama_pasien" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2"></i>Nama Pasien <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_pasien" id="nama_pasien" required
                           class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Nama lengkap pasien">
                </div>

                <!-- No HP -->
                <div>
                    <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-phone mr-2"></i>No HP <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="no_hp" id="no_hp" required
                           class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="08123456789">
                </div>

                <!-- Alamat -->
                <div class="md:col-span-2">
                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt mr-2"></i>Alamat <span class="text-red-500">*</span>
                    </label>
                    <textarea name="alamat" id="alamat" rows="2" required
                              class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Alamat lengkap pasien"></textarea>
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label for="dob" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-2"></i>Tanggal Lahir <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="dob" id="dob" required
                           class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Jenis Vaksin -->
                <div>
                    <label for="jenis_vaksin" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-syringe mr-2"></i>Jenis Vaksin <span class="text-red-500">*</span>
                    </label>
                    <select name="jenis_vaksin" id="jenis_vaksin" required
                            class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Pilih Jenis Vaksin --</option>
                        @foreach($vaccineTypes as $vaccineType)
                            <option value="{{ $vaccineType->nama_vaksin }}">
                                {{ $vaccineType->nama_vaksin }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tanggal Vaksin Pertama -->
                <div>
                    <label for="tanggal_vaksin_pertama" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-check mr-2"></i>Tanggal Vaksin Pertama <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_vaksin_pertama" id="tanggal_vaksin_pertama" required
                           class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-center space-x-4 pt-4">
                <a href="{{ route('patients.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-3 rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
                <button type="button" id="btnBatalEdit" onclick="resetFormToAddMode()" class="hidden bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg items-center space-x-2 transition">
                    <i class="fas fa-times"></i>
                    <span>Batal Edit</span>
                </button>
                <button type="submit" id="btnTambah" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition">
                    <i class="fas fa-plus"></i>
                    <span>Tambah ke Daftar</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Temporary Data Table -->
    @if(!empty($temporaryData))
    <div id="dataTableContainer" class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-list mr-2"></i>Daftar Data ({{ count($temporaryData) }} data)
            </h2>
            <button type="button" onclick="clearAll()" class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-trash mr-1"></i>Kosongkan Daftar
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pasien</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No HP</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Vaksin</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Vaksin</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cabang</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($temporaryData as $data)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $data['pid'] }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $data['nama_pasien'] }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $data['no_hp'] }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $data['jenis_vaksin'] }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ \Carbon\Carbon::parse($data['tanggal_vaksin_pertama'])->format('d-m-Y') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $data['branch_name'] }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                            <button type="button" onclick="editItem('{{ $data['id'] }}')" class="text-yellow-600 hover:text-yellow-900 mr-2 bg-yellow-100 hover:bg-yellow-200 px-2 py-1 rounded text-xs transition">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button type="button" onclick="deleteItem('{{ $data['id'] }}', this)" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Save Button -->
        <div class="mt-6 text-center">
            <form action="{{ route('manual-input.save') }}" method="POST" onsubmit="return confirm('Yakin ingin menyimpan {{ count($temporaryData) }} data ke database?')">
                @csrf
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg flex items-center space-x-2 transition mx-auto">
                    <i class="fas fa-save"></i>
                    <span>Simpan Semua Data ({{ count($temporaryData) }})</span>
                </button>
            </form>
        </div>
    </div>
    @else
    <!-- Empty State -->
    <div class="bg-gray-50 rounded-lg p-8 text-center">
        <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-inbox text-2xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data</h3>
        <p class="text-gray-600">Isi form di atas dan klik "Tambah ke Daftar" untuk menambahkan data</p>
    </div>
    @endif

    <!-- Tips -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <h4 class="font-semibold text-yellow-900 mb-2 flex items-center">
            <i class="fas fa-lightbulb mr-2"></i>
            Tips Input Manual
        </h4>
        <ul class="text-sm text-yellow-800 space-y-1 list-disc list-inside">
            <li><strong>Pilih cabang terlebih dahulu</strong> sebelum mengisi data</li>
            <li>PID harus diawali dengan kode prefix cabang (contoh: LX untuk Ciliwung, LZ untuk Tangkuban Perahu)</li>
            <li>PID yang sudah ada di database akan ditolak</li>
            <li>Data yang sudah ditambahkan ke daftar bisa dihapus sebelum disimpan</li>
            <li>Klik "Simpan Semua Data" untuk menyimpan ke database dan generate jadwal vaksinasi otomatis</li>
        </ul>
    </div>
</div>

@push('scripts')
<script>
    // Hide error alert
    function hideError() {
        document.getElementById('errorAlert').classList.add('hidden');
    }

    // Show error alert with messages
    function showError(errors) {
        const errorList = document.getElementById('errorList');
        errorList.innerHTML = '';
        errors.forEach(error => {
            const li = document.createElement('li');
            li.textContent = error;
            errorList.appendChild(li);
        });
        document.getElementById('errorAlert').classList.remove('hidden');
    }

    // Format date for display
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }

    // Single event listener for form submission (handles both add and update)
    document.getElementById('manualInputForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });

        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Check if in edit mode
        const editId = this.getAttribute('data-edit-id');
        
        if (editId) {
            // Update mode
            fetch(`/input-data/manual/update/${editId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    // Success - update row in table
                    hideError();
                    
                    // Find and update the row
                    const rows = document.querySelectorAll('#tableBody tr');
                    rows.forEach(row => {
                        if (row.getAttribute('data-id') === editId) {
                            row.innerHTML = `
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">${result.data.pid}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">${result.data.nama_pasien}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">${result.data.no_hp}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">${result.data.jenis_vaksin}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">${formatDate(result.data.tanggal_vaksin_pertama)}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">${result.data.branch_name}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                    <button type="button" onclick="editItem('${result.data.id}')" class="text-yellow-600 hover:text-yellow-900 mr-2 bg-yellow-100 hover:bg-yellow-200 px-2 py-1 rounded text-xs transition">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="button" onclick="deleteItem('${result.data.id}', this)" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            `;
                        }
                    });
                    
                    // Reset form to add mode
                    resetFormToAddMode();
                    
                    // Show success message
                    alert('Data berhasil diupdate!');
                    
                } else {
                    // Error from server
                    showError(result.errors || ['Terjadi kesalahan']);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError(['Terjadi kesalahan saat mengupdate data']);
            });
            
        } else {
            // Add mode - original behavior
            fetch('{{ route("manual-input.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    // Success - add row to table dynamically
                    hideError();
                    
                    // Check if table exists, if not create it
                    let tableContainer = document.querySelector('.bg-white.rounded-lg.shadow.p-6:not(#manualInputForm)');
                    const emptyState = document.querySelector('.bg-gray-50.rounded-lg.p-8');
                    
                    if (emptyState) {
                        // Remove empty state and create table
                        emptyState.remove();
                        createTableStructure(result.data, result.count);
                    } else {
                        // Add row to existing table
                        addRowToTable(result.data, result.count);
                    }
                    
                    // Clear form fields except branch
                    const branchId = document.getElementById('branch_id').value;
                    document.getElementById('manualInputForm').reset();
                    document.getElementById('branch_id').value = branchId;
                    
                    // Focus on PID field for next input
                    document.getElementById('pid').focus();
                    
                } else {
                    // Error from server
                    showError(result.errors || ['Terjadi kesalahan']);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError(['Terjadi kesalahan saat mengirim data']);
            });
        }
    });

    // Create table structure when first data is added
    function createTableStructure(data, count) {
        const container = document.createElement('div');
        container.id = 'dataTableContainer';
        container.className = 'bg-white rounded-lg shadow p-6';
        container.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-list mr-2"></i>Daftar Data (${count} data)
                </h2>
                <button type="button" onclick="clearAll()" class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg text-sm transition">
                    <i class="fas fa-trash mr-1"></i>Kosongkan Daftar
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="dataTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pasien</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No HP</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Vaksin</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Vaksin</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cabang</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="tableBody">
                    </tbody>
                </table>
            </div>
            <div class="mt-6 text-center">
                <form action="{{ route('manual-input.save') }}" method="POST" onsubmit="return confirm('Yakin ingin menyimpan ${count} data ke database?')">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg flex items-center space-x-2 transition mx-auto">
                        <i class="fas fa-save"></i>
                        <span>Simpan Semua Data (${count})</span>
                    </button>
                </form>
            </div>
        `;
        
        // Insert after form
        const formContainer = document.getElementById('manualInputForm').closest('.bg-white');
        formContainer.parentNode.insertBefore(container, formContainer.nextSibling);
        
        // Add the first row
        addRowToTable(data, count);
    }

    // Add row to existing table
    function addRowToTable(data, count) {
        const tableBody = document.getElementById('tableBody');
        const row = document.createElement('tr');
        row.setAttribute('data-id', data.id);
        row.innerHTML = `
            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">${data.pid}</td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">${data.nama_pasien}</td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">${data.no_hp}</td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">${data.jenis_vaksin}</td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">${formatDate(data.tanggal_vaksin_pertama)}</td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">${data.branch_name}</td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                <button type="button" onclick="editItem('${data.id}')" class="text-yellow-600 hover:text-yellow-900 mr-2 bg-yellow-100 hover:bg-yellow-200 px-2 py-1 rounded text-xs transition">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button type="button" onclick="deleteItem('${data.id}', this)" class="text-red-600 hover:text-red-900">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(row);
        
        // Update count in header and save button
        updateCountDisplay(count);
    }

    // Edit item - load data into form
    function editItem(id) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/input-data/manual/edit/${id}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                // Fill form with data
                document.getElementById('branch_id').value = result.data.branch_id;
                document.getElementById('pid').value = result.data.pid;
                document.getElementById('nama_pasien').value = result.data.nama_pasien;
                document.getElementById('no_hp').value = result.data.no_hp;
                document.getElementById('alamat').value = result.data.alamat;
                document.getElementById('dob').value = result.data.dob;
                document.getElementById('jenis_vaksin').value = result.data.jenis_vaksin;
                document.getElementById('tanggal_vaksin_pertama').value = result.data.tanggal_vaksin_pertama;
                
                // Change form behavior to update mode
                const form = document.getElementById('manualInputForm');
                const submitBtn = document.getElementById('btnTambah');
                const cancelBtn = document.getElementById('btnBatalEdit');
                
                // Store the ID being edited
                form.setAttribute('data-edit-id', id);
                
                // Change button text and color
                submitBtn.innerHTML = '<i class="fas fa-save"></i><span>Update Data</span>';
                submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                submitBtn.classList.add('bg-yellow-600', 'hover:bg-yellow-700');
                
                // Show cancel button
                cancelBtn.classList.remove('hidden');
                cancelBtn.classList.add('flex');
                
                // Scroll to form
                form.scrollIntoView({ behavior: 'smooth' });
                
                // Focus on first field
                document.getElementById('branch_id').focus();
                
            } else {
                showError([result.message || 'Gagal mengambil data']);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError(['Terjadi kesalahan saat mengambil data']);
        });
    }

    // Reset form to add mode
    function resetFormToAddMode() {
        const form = document.getElementById('manualInputForm');
        const submitBtn = document.getElementById('btnTambah');
        const cancelBtn = document.getElementById('btnBatalEdit');
        
        // Remove edit ID
        form.removeAttribute('data-edit-id');
        
        // Reset button
        submitBtn.innerHTML = '<i class="fas fa-plus"></i><span>Tambah ke Daftar</span>';
        submitBtn.classList.remove('bg-yellow-600', 'hover:bg-yellow-700');
        submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
        
        // Hide cancel button
        cancelBtn.classList.add('hidden');
        cancelBtn.classList.remove('flex');
        
        // Clear form
        form.reset();
    }

    // Delete single item
    function deleteItem(id, button) {
        if (!confirm('Yakin ingin menghapus data ini?')) {
            return;
        }
        
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/input-data/manual/remove/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                // Remove row from table
                const row = button.closest('tr');
                row.remove();
                
                // Update count display
                updateCountDisplay(result.count);
                
                // If no more data, show empty state
                if (result.count === 0) {
                    const tableContainer = document.getElementById('dataTableContainer');
                    if (tableContainer) {
                        tableContainer.remove();
                        
                        // Create empty state
                        const emptyState = document.createElement('div');
                        emptyState.className = 'bg-gray-50 rounded-lg p-8 text-center';
                        emptyState.innerHTML = `
                            <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-inbox text-2xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data</h3>
                            <p class="text-gray-600">Isi form di atas dan klik "Tambah ke Daftar" untuk menambahkan data</p>
                        `;
                        
                        const formContainer = document.getElementById('manualInputForm').closest('.bg-white');
                        formContainer.parentNode.insertBefore(emptyState, formContainer.nextSibling);
                    }
                }
            } else {
                showError(['Gagal menghapus data']);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError(['Terjadi kesalahan saat menghapus data']);
        });
    }

    // Clear all data
    function clearAll() {
        if (!confirm('Yakin ingin mengosongkan daftar?')) {
            return;
        }
        
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch('{{ route("manual-input.clear") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                // Remove table
                const tableContainer = document.getElementById('dataTableContainer');
                if (tableContainer) {
                    tableContainer.remove();
                }
                
                // Create empty state
                const emptyState = document.createElement('div');
                emptyState.className = 'bg-gray-50 rounded-lg p-8 text-center';
                emptyState.innerHTML = `
                    <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-inbox text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data</h3>
                    <p class="text-gray-600">Isi form di atas dan klik "Tambah ke Daftar" untuk menambahkan data</p>
                `;
                
                const formContainer = document.getElementById('manualInputForm').closest('.bg-white');
                formContainer.parentNode.insertBefore(emptyState, formContainer.nextSibling);
            } else {
                showError(['Gagal mengosongkan daftar']);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError(['Terjadi kesalahan saat mengosongkan daftar']);
        });
    }

    // Update count display in header and save button
    function updateCountDisplay(count) {
        const header = document.querySelector('h2');
        if (header) {
            header.innerHTML = `<i class="fas fa-list mr-2"></i>Daftar Data (${count} data)`;
        }
        
        const saveButton = document.querySelector('button[type="submit"].bg-green-600');
        if (saveButton) {
            const span = saveButton.querySelector('span');
            if (span) {
                span.textContent = `Simpan Semua Data (${count})`;
            }
        }
        
        // Update form onsubmit (not button onclick) to avoid double confirm
        const saveForm = document.querySelector('#dataTableContainer form');
        if (saveForm) {
            saveForm.setAttribute('onsubmit', `return confirm('Yakin ingin menyimpan ${count} data ke database?')`);
        }
    }
</script>
@endpush
@endsection
