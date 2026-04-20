@extends('layouts.app')

@section('title', 'Data Pasien - Sistem Reminder Vaksin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Data Pasien</h1>
            <p class="text-gray-600">Kelola data pasien dan jadwal vaksinasi</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <!-- Export Dropdown -->
            <div class="relative group">
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
                    <i class="fas fa-download"></i>
                    <span>Export Data</span>
                    <i class="fas fa-chevron-down ml-1"></i>
                </button>
                <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                    <a href="{{ route('patients.export.excel', request()->all()) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 first:rounded-t-lg">
                        <i class="fas fa-file-excel text-green-600 mr-2"></i> Export Excel
                    </a>
                    <a href="{{ route('patients.export.pdf', request()->all()) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 last:rounded-b-lg">
                        <i class="fas fa-file-pdf text-red-600 mr-2"></i> Export PDF
                    </a>
                </div>
            </div>
            <a href="{{ route('import.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
                <i class="fas fa-file-import"></i>
                <span>Import Excel</span>
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('patients.index') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Cari PID, nama, atau no HP..." 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="sm:w-48">
                <select name="jenis_vaksin" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Vaksin</option>
                    <option value="HPV" {{ request('jenis_vaksin') == 'HPV' ? 'selected' : '' }}>HPV</option>
                    <option value="Hepatitis" {{ request('jenis_vaksin') == 'Hepatitis' ? 'selected' : '' }}>Hepatitis</option>
                    <option value="Influenza" {{ request('jenis_vaksin') == 'Influenza' ? 'selected' : '' }}>Influenza</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route('patients.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Pasien</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $patients->total() }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-pink-50 rounded-lg shadow p-4 border border-pink-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-pink-600 font-medium">Total Pasien HPV</p>
                    <p class="text-2xl font-bold text-pink-700">{{ $stats['total_hpv'] }}</p>
                </div>
                <div class="bg-pink-200 p-3 rounded-full">
                    <i class="fas fa-syringe text-pink-700 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-green-50 rounded-lg shadow p-4 border border-green-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600 font-medium">Total Pasien Influenza</p>
                    <p class="text-2xl font-bold text-green-700">{{ $stats['total_influenza'] }}</p>
                </div>
                <div class="bg-green-200 p-3 rounded-full">
                    <i class="fas fa-virus text-green-700 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-yellow-50 rounded-lg shadow p-4 border border-yellow-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-yellow-600 font-medium">Total Pasien Hepatitis</p>
                    <p class="text-2xl font-bold text-yellow-700">{{ $stats['total_hepatitis'] }}</p>
                </div>
                <div class="bg-yellow-200 p-3 rounded-full">
                    <i class="fas fa-shield-virus text-yellow-700 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            <a href="{{ route('patients.index', array_merge(request()->all(), ['sort' => 'pid', 'direction' => ($sortField == 'pid' && $sortDirection == 'asc') ? 'desc' : 'asc'])) }}" class="flex items-center">
                                PID
                                @if($sortField == 'pid')
                                    <i class="fas fa-sort-{{ $sortDirection == 'asc' ? 'up' : 'down' }} ml-1 text-blue-500"></i>
                                @else
                                    <i class="fas fa-sort ml-1 text-gray-300"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            <a href="{{ route('patients.index', array_merge(request()->all(), ['sort' => 'nama_pasien', 'direction' => ($sortField == 'nama_pasien' && $sortDirection == 'asc') ? 'desc' : 'asc'])) }}" class="flex items-center">
                                Nama Pasien
                                @if($sortField == 'nama_pasien')
                                    <i class="fas fa-sort-{{ $sortDirection == 'asc' ? 'up' : 'down' }} ml-1 text-blue-500"></i>
                                @else
                                    <i class="fas fa-sort ml-1 text-gray-300"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            <a href="{{ route('patients.index', array_merge(request()->all(), ['sort' => 'no_hp', 'direction' => ($sortField == 'no_hp' && $sortDirection == 'asc') ? 'desc' : 'asc'])) }}" class="flex items-center">
                                No HP
                                @if($sortField == 'no_hp')
                                    <i class="fas fa-sort-{{ $sortDirection == 'asc' ? 'up' : 'down' }} ml-1 text-blue-500"></i>
                                @else
                                    <i class="fas fa-sort ml-1 text-gray-300"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            <a href="{{ route('patients.index', array_merge(request()->all(), ['sort' => 'alamat', 'direction' => ($sortField == 'alamat' && $sortDirection == 'asc') ? 'desc' : 'asc'])) }}" class="flex items-center">
                                Alamat
                                @if($sortField == 'alamat')
                                    <i class="fas fa-sort-{{ $sortDirection == 'asc' ? 'up' : 'down' }} ml-1 text-blue-500"></i>
                                @else
                                    <i class="fas fa-sort ml-1 text-gray-300"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            <a href="{{ route('patients.index', array_merge(request()->all(), ['sort' => 'dob', 'direction' => ($sortField == 'dob' && $sortDirection == 'asc') ? 'desc' : 'asc'])) }}" class="flex items-center">
                                Tanggal Lahir
                                @if($sortField == 'dob')
                                    <i class="fas fa-sort-{{ $sortDirection == 'asc' ? 'up' : 'down' }} ml-1 text-blue-500"></i>
                                @else
                                    <i class="fas fa-sort ml-1 text-gray-300"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Vaksin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($patients as $patient)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $patient->pid }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $patient->nama_pasien }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @php
                                    $cleanNumber = preg_replace('/[^0-9]/', '', $patient->no_hp);
                                    $waNumber = $cleanNumber;
                                    if (substr($cleanNumber, 0, 1) === '0') {
                                        $waNumber = '62' . substr($cleanNumber, 1);
                                    } elseif (substr($cleanNumber, 0, 2) !== '62' && !empty($cleanNumber)) {
                                        $waNumber = '62' . $cleanNumber;
                                    }
                                @endphp
                                @if($patient->no_hp)
                                    <a href="https://wa.me/{{ $waNumber }}" 
                                       target="_blank" 
                                       class="text-green-600 hover:text-green-800 flex items-center space-x-1">
                                        <i class="fab fa-whatsapp"></i>
                                        <span>{{ $patient->no_hp }}</span>
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $patient->alamat }}">
                                {{ $patient->alamat ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $patient->dob ? $patient->dob->format('d-m-Y') : '-' }}
                                @if($patient->dob)
                                    <span class="text-xs text-gray-400">({{ $patient->age }} th)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @foreach($patient->vaccines as $vaccine)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $vaccine->jenis_vaksin === 'HPV' ? 'bg-pink-100 text-pink-800' : 
                                           ($vaccine->jenis_vaksin === 'Hepatitis' ? 'bg-yellow-100 text-yellow-800' : 
                                            'bg-green-100 text-green-800') }}">
                                        {{ $vaccine->jenis_vaksin }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('patients.show', $patient->id) }}" class="text-blue-600 hover:text-blue-900 mr-2">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                                <button type="button" onclick="showEditModal({{ $patient->id }}, '{{ $patient->pid }}', '{{ $patient->nama_pasien }}', '{{ $patient->no_hp }}', '{{ $patient->alamat }}', '{{ $patient->dob ? $patient->dob->format('Y-m-d') : '' }}', {{ $patient->branch_id }})" class="text-yellow-600 hover:text-yellow-900 mr-2 bg-yellow-100 hover:bg-yellow-200 px-2 py-1 rounded text-xs transition">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <form action="{{ route('patients.destroy', $patient->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada data pasien ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-gray-50 border-t border-gray-200">
            @include('components.pagination', ['paginator' => $patients])
        </div>
    </div>
</div>

<!-- Edit Patient Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Edit Data Pasien</h3>
            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Branch Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cabang <span class="text-red-500">*</span></label>
                        <select name="branch_id" id="editBranchId" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">-- Pilih Cabang --</option>
                            @foreach(\App\Models\Branch::where('is_active', true)->orderBy('nama_cabang')->get() as $branch)
                                <option value="{{ $branch->id }}">
                                    {{ $branch->nama_cabang }} (Prefix: {{ $branch->kode_prefix }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- PID -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PID <span class="text-red-500">*</span></label>
                        <input type="text" name="pid" id="editPid" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Contoh: LXB001">
                        <p class="text-xs text-gray-500 mt-1">PID harus diawali dengan kode prefix cabang</p>
                    </div>

                    <!-- Nama Pasien -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pasien <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_pasien" id="editNamaPasien" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Nama lengkap pasien">
                    </div>

                    <!-- No HP -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No HP <span class="text-red-500">*</span></label>
                        <input type="text" name="no_hp" id="editNoHp" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="08123456789">
                    </div>

                    <!-- Alamat -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat <span class="text-red-500">*</span></label>
                        <textarea name="alamat" id="editAlamat" rows="2" required
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Alamat lengkap pasien"></textarea>
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
                        <input type="date" name="dob" id="editDob" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="closeEditModal()" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg text-sm transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showEditModal(id, pid, namaPasien, noHp, alamat, dob, branchId) {
        document.getElementById('editForm').action = `/patients/${id}`;
        document.getElementById('editPid').value = pid;
        document.getElementById('editNamaPasien').value = namaPasien;
        document.getElementById('editNoHp').value = noHp;
        document.getElementById('editAlamat').value = alamat || '';
        document.getElementById('editDob').value = dob || '';
        document.getElementById('editBranchId').value = branchId || '';
        
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('editModal');
        if (event.target == modal) {
            closeEditModal();
        }
    }
</script>
@endpush
@endsection
