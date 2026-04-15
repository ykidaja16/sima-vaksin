@extends('layouts.app')

@section('title', 'Manajemen Vaksin - Sistem Reminder Vaksin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Vaksin</h1>
            <p class="text-gray-600">Kelola jenis vaksin dan interval dosis</p>
        </div>
        <button onclick="showAddModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
            <i class="fas fa-plus"></i>
            <span>Tambah Vaksin</span>
        </button>
    </div>

    <!-- Vaccine Types Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Vaksin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Dosis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Interval (Bulan)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($vaccineTypes as $vaccineType)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $vaccineType->nama_vaksin }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $vaccineType->deskripsi }}">
                                {{ $vaccineType->deskripsi ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                {{ $vaccineType->total_dosis }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ implode(', ', $vaccineType->interval_bulan) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $vaccineType->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $vaccineType->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="showEditModal({{ $vaccineType->id }}, '{{ $vaccineType->nama_vaksin }}', '{{ $vaccineType->deskripsi }}', {{ $vaccineType->total_dosis }}, '{{ implode(', ', $vaccineType->interval_bulan) }}', {{ $vaccineType->is_active ? 'true' : 'false' }})" 
                                            class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-2 py-1 rounded text-xs transition">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    
                                    <form action="{{ route('vaccine-types.toggle-active', $vaccineType->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="{{ $vaccineType->is_active ? 'text-orange-600 hover:text-orange-900 bg-orange-100 hover:bg-orange-200' : 'text-green-600 hover:text-green-900 bg-green-100 hover:bg-green-200' }} px-2 py-1 rounded text-xs transition"
                                                onclick="return confirm('Yakin ingin {{ $vaccineType->is_active ? 'menonaktifkan' : 'mengaktifkan' }} vaksin ini?')">
                                            <i class="fas {{ $vaccineType->is_active ? 'fa-ban' : 'fa-check' }}"></i> 
                                            {{ $vaccineType->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <form action="{{ route('vaccine-types.destroy', $vaccineType->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus vaksin ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-2 py-1 rounded text-xs transition">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                Tidak ada jenis vaksin ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-gray-50 border-t border-gray-200">
            @include('components.pagination', ['paginator' => $vaccineTypes])
        </div>
    </div>
</div>

<!-- Add Vaccine Type Modal -->
<div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Tambah Jenis Vaksin</h3>
            <form id="addForm" method="POST" action="{{ route('vaccine-types.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Vaksin</label>
                    <input type="text" name="nama_vaksin" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Dosis</label>
                    <input type="number" name="total_dosis" required min="1" max="10" value="1" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Interval Bulan (pisahkan dengan koma, contoh: 0, 2, 6)</label>
                    <input type="text" name="interval_bulan" required placeholder="0, 2, 6" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <p class="text-xs text-gray-500 mt-1">Jumlah interval harus sama dengan total dosis</p>
                </div>
                <div class="flex justify-center space-x-4 mt-4">
                    <button type="button" onclick="closeAddModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Vaccine Type Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Edit Jenis Vaksin</h3>
            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Vaksin</label>
                    <input type="text" name="nama_vaksin" id="editNamaVaksin" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" id="editDeskripsi" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Dosis</label>
                    <input type="number" name="total_dosis" id="editTotalDosis" required min="1" max="10" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Interval Bulan (pisahkan dengan koma)</label>
                    <input type="text" name="interval_bulan" id="editIntervalBulan" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <p class="text-xs text-gray-500 mt-1">Jumlah interval harus sama dengan total dosis</p>
                </div>
                <div>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="is_active" id="editIsActive" value="1" class="rounded border-gray-300">
                        <span class="text-sm text-gray-700">Aktif</span>
                    </label>
                </div>
                <div class="flex justify-center space-x-4 mt-4">
                    <button type="button" onclick="closeEditModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showAddModal() {
    document.getElementById('addModal').classList.remove('hidden');
}

function closeAddModal() {
    document.getElementById('addModal').classList.add('hidden');
}

function showEditModal(id, namaVaksin, deskripsi, totalDosis, intervalBulan, isActive) {
    document.getElementById('editForm').action = `/vaccine-types/${id}`;
    document.getElementById('editNamaVaksin').value = namaVaksin;
    document.getElementById('editDeskripsi').value = deskripsi || '';
    document.getElementById('editTotalDosis').value = totalDosis;
    document.getElementById('editIntervalBulan').value = intervalBulan;
    document.getElementById('editIsActive').checked = isActive;
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const addModal = document.getElementById('addModal');
    const editModal = document.getElementById('editModal');
    if (event.target == addModal) {
        closeAddModal();
    }
    if (event.target == editModal) {
        closeEditModal();
    }
}
</script>
@endpush
@endsection
