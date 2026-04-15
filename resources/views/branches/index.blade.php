@extends('layouts.app')

@section('title', 'Manajemen Cabang - Sistem Reminder Vaksin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Cabang</h1>
            <p class="text-gray-600">Kelola cabang dan kode prefix PID</p>
        </div>
        <button onclick="showAddModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
            <i class="fas fa-plus"></i>
            <span>Tambah Cabang</span>
        </button>
    </div>

    <!-- Info Card -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start space-x-3">
            <i class="fas fa-info-circle text-blue-600 mt-1"></i>
            <div class="text-sm text-blue-800">
                <p class="font-medium">Informasi Kode Prefix:</p>
                <ul class="list-disc list-inside mt-1 space-y-1">
                    <li>Kode prefix adalah 2 huruf kapital di awal PID (contoh: LX, LZ)</li>
                    <li>Ciliwung menggunakan prefix LX (contoh: LXB0049356)</li>
                    <li>Tangkuban Perahu menggunakan prefix LZ (contoh: LZD0010534)</li>
                    <li>Kode prefix harus unik untuk setiap cabang</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Branches Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Cabang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Prefix</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Telepon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($branches as $branch)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $branch->nama_cabang }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono font-bold">
                                {{ $branch->kode_prefix }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $branch->alamat }}">
                                {{ $branch->alamat ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $branch->no_telp ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $branch->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $branch->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="showEditModal({{ $branch->id }}, '{{ $branch->nama_cabang }}', '{{ $branch->kode_prefix }}', '{{ $branch->alamat }}', '{{ $branch->no_telp }}', {{ $branch->is_active ? 'true' : 'false' }})" 
                                            class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-2 py-1 rounded text-xs transition">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    
                                    <form action="{{ route('branches.toggle-active', $branch->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="{{ $branch->is_active ? 'text-orange-600 hover:text-orange-900 bg-orange-100 hover:bg-orange-200' : 'text-green-600 hover:text-green-900 bg-green-100 hover:bg-green-200' }} px-2 py-1 rounded text-xs transition"
                                                onclick="return confirm('Yakin ingin {{ $branch->is_active ? 'menonaktifkan' : 'mengaktifkan' }} cabang ini?')">
                                            <i class="fas {{ $branch->is_active ? 'fa-ban' : 'fa-check' }}"></i> 
                                            {{ $branch->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <form action="{{ route('branches.destroy', $branch->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus cabang ini?')">
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
                                Tidak ada cabang ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-gray-50 border-t border-gray-200">
            @include('components.pagination', ['paginator' => $branches])
        </div>
    </div>
</div>

<!-- Add Branch Modal -->
<div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Tambah Cabang Baru</h3>
            <form id="addForm" method="POST" action="{{ route('branches.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Cabang</label>
                    <input type="text" name="nama_cabang" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Prefix (2 huruf kapital)</label>
                    <input type="text" name="kode_prefix" required maxlength="2" pattern="[A-Z]{2}" placeholder="LX" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono uppercase">
                    <p class="text-xs text-gray-500 mt-1">Contoh: LX untuk Ciliwung, LZ untuk Tangkuban Perahu</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea name="alamat" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                    <input type="text" name="no_telp" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
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

<!-- Edit Branch Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Edit Cabang</h3>
            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Cabang</label>
                    <input type="text" name="nama_cabang" id="editNamaCabang" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Prefix (2 huruf kapital)</label>
                    <input type="text" name="kode_prefix" id="editKodePrefix" required maxlength="2" pattern="[A-Z]{2}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono uppercase">
                    <p class="text-xs text-gray-500 mt-1">Tidak bisa diubah jika sudah ada data pasien</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea name="alamat" id="editAlamat" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                    <input type="text" name="no_telp" id="editNoTelp" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
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

function showEditModal(id, namaCabang, kodePrefix, alamat, noTelp, isActive) {
    document.getElementById('editForm').action = `/branches/${id}`;
    document.getElementById('editNamaCabang').value = namaCabang;
    document.getElementById('editKodePrefix').value = kodePrefix;
    document.getElementById('editAlamat').value = alamat || '';
    document.getElementById('editNoTelp').value = noTelp || '';
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
