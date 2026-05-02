@extends('layouts.app')

@php
    $vaccineTypesList = $vaccineTypes ?? [];
    $canDelete = $patient->vaccines->count() > 1;
@endphp

@section('title', 'Detail Pasien - ' . $patient->nama_pasien)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Pasien</h1>
            <p class="text-gray-600">Informasi lengkap pasien dan jadwal vaksinasi</p>
        </div>
        <a href="{{ route('patients.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Patient Info Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pasien</h3>
                <div class="space-y-3">
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">PID</span>
                        <span class="font-medium">{{ $patient->pid }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Nama</span>
                        <span class="font-medium">{{ $patient->nama_pasien }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">No HP</span>
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
                               class="font-medium text-green-600 hover:text-green-800 flex items-center space-x-1">
                                <i class="fab fa-whatsapp"></i>
                                <span>{{ $patient->no_hp }}</span>
                            </a>
                        @else
                            <span class="font-medium">-</span>
                        @endif
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Alamat</span>
                        <span class="font-medium">{{ $patient->alamat ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Tanggal Lahir</span>
                        <span class="font-medium">
                            {{ $patient->dob ? $patient->dob->format('d-m-Y') : '-' }}
                            @if($patient->dob)
                                ({{ $patient->age }} tahun)
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Vaksin</h3>
                @foreach($patient->vaccines as $vaccine)
                    <div class="bg-gray-50 rounded-lg p-4 mb-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-gray-900">{{ $vaccine->jenis_vaksin }}</span>
                            <div class="flex items-center space-x-2">
                                <button type="button" 
                                        onclick="showEditDateModal({{ $vaccine->id }}, '{{ $vaccine->jenis_vaksin }}', {{ $vaccine->vaccine_type_id ?? 'null' }}, '{{ $vaccine->tanggal_vaksin_pertama->format('Y-m-d') }}')"
                                        class="text-blue-600 hover:text-blue-800 text-sm flex items-center space-x-1">
                                    <i class="fas fa-edit"></i>
                                    <span>Edit</span>
                                </button>
                                @if($canDelete)
                                <button type="button" 
                                        onclick="showDeleteModal({{ $vaccine->id }}, '{{ $vaccine->jenis_vaksin }}')"
                                        class="text-red-600 hover:text-red-800 text-sm flex items-center space-x-1">
                                    <i class="fas fa-trash"></i>
                                    <span>Hapus</span>
                                </button>
                                @endif
                            </div>
                        </div>
                        <div class="text-sm text-gray-600">
                            Pertama: {{ $vaccine->tanggal_vaksin_pertama->format('d-m-Y') }} | Total Dosis: {{ $vaccine->total_dosis }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Vaccine Schedules -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Jadwal Vaksinasi</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis Vaksin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dosis Ke</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Vaksin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($patient->vaccineSchedules as $schedule)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $schedule->vaccine->jenis_vaksin }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $schedule->dosis_ke }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $schedule->tanggal_vaksin->format('d-m-Y') }}
                                @if($schedule->tanggal_vaksin->isPast() && $schedule->status === 'pending')
                                    <span class="text-red-600 text-xs ml-2">(Done)</span>
                                @elseif($schedule->tanggal_vaksin->diffInDays(now()) <= 7 && $schedule->status === 'pending')
                                    <span class="text-yellow-600 text-xs ml-2">(H-{{ floor(now()->diffInDays($schedule->tanggal_vaksin, false)) }} Hari)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @php
                                    $displayStatus = $schedule->status;
                                    $badgeClass = 'bg-gray-100 text-gray-800';
                                    
                                    if ($schedule->dosis_ke == 1) {
                                        $displayStatus = 'completed';
                                        $badgeClass = 'bg-green-100 text-green-800';
                                    } elseif ($schedule->status === 'completed') {
                                        $displayStatus = 'completed';
                                        $badgeClass = 'bg-green-100 text-green-800';
                                    } elseif ($schedule->tanggal_vaksin->isPast() && is_null($schedule->reminder_sent_at)) {
                                        $displayStatus = 'tidak reminder';
                                        $badgeClass = 'bg-red-100 text-red-800';
                                    } elseif ($schedule->status === 'pending') {
                                        $displayStatus = 'pending';
                                        $badgeClass = 'bg-yellow-100 text-yellow-800';
                                    } else {
                                        $displayStatus = $schedule->status;
                                        $badgeClass = 'bg-red-100 text-red-800';
                                    }
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeClass }}">
                                    {{ ucfirst($displayStatus) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $schedule->keterangan ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada jadwal vaksinasi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Date Modal -->
<div id="editDateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="text-lg font-semibold text-gray-900 mb-4">Edit Jadwal Vaksin</div>
        <form id="editDateForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="modalVaccineId" name="vaccine_id" value="">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Vaksin</label>
                <select id="modalVaccineType" 
                        name="vaccine_type_id" 
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach($vaccineTypesList as $vt)
                        <option value="{{ $vt->id }}">{{ $vt->nama_vaksin }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Dosis Pertama</label>
                <input type="date" 
                       id="modalFirstDate" 
                       name="tanggal_vaksin_pertama" 
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" 
                        onclick="closeEditDateModal()" 
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="text-lg font-semibold text-gray-900 mb-4">Hapus Vaccine</div>
        <p class="text-gray-600 mb-4">Apakah Anda yakin ingin menghapus vaccine <span id="deleteVaccineName" class="font-medium"></span>? Semua jadwal juga akan dihapus.</p>
        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
            <input type="hidden" id="deleteVaccineId" name="vaccine_id" value="">
            <div class="flex justify-end space-x-2">
                <button type="button" 
                        onclick="closeDeleteModal()" 
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded">
                    Hapus
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showEditDateModal(vaccineId, vaccineName, vaccineTypeId, currentDate) {
    document.getElementById('modalVaccineId').value = vaccineId;
    document.getElementById('modalVaccineType').value = vaccineTypeId;
    document.getElementById('modalFirstDate').value = currentDate;
    document.getElementById('editDateForm').action = '/patients/{{ $patient->id }}/vaccine-first-date';
    document.getElementById('editDateModal').classList.remove('hidden');
}
function closeEditDateModal() {
    document.getElementById('editDateModal').classList.add('hidden');
}
function showDeleteModal(vaccineId, vaccineName) {
    document.getElementById('deleteVaccineId').value = vaccineId;
    document.getElementById('deleteVaccineName').textContent = vaccineName;
    document.getElementById('deleteForm').action = '/patients/{{ $patient->id }}/vaccine';
    document.getElementById('deleteModal').classList.remove('hidden');
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}
window.onclick = function(event) {
    const editModal = document.getElementById('editDateModal');
    const deleteModal = document.getElementById('deleteModal');
    if (event.target == editModal) {
        closeEditDateModal();
    }
    if (event.target == deleteModal) {
        closeDeleteModal();
    }
}
</script>
@endsection
