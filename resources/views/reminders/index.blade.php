@extends('layouts.app')

@section('title', 'Reminder H-7 - Sistem Reminder Vaksin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reminder H-7</h1>
            <p class="text-gray-600">Pasien yang perlu diingatkan vaksinasi dalam 7 hari ke depan</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-blue-50 rounded-lg shadow p-4 border border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-600 font-medium">Reminder H-7</p>
                    <p class="text-3xl font-bold text-blue-800">{{ $stats['h7_count'] }}</p>
                </div>
                <div class="bg-blue-200 p-3 rounded-full">
                    <i class="fas fa-bell text-blue-700 text-xl"></i>
                </div>
            </div>
        </div>      
    </div>

    <!-- Filters -->
    {{-- <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('reminders.index') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="sm:w-40">
                <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="pending" {{ request('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                </select>
            </div>
            <div>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                    placeholder="Dari tanggal" 
                    class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                    placeholder="Sampai tanggal" 
                    class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('reminders.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div> --}}

    <!-- Reminder Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Reminder H-7</h3>
            <span class="text-sm text-gray-500">{{ now()->format('d-m-Y') }} - {{ now()->addDays(7)->format('d-m-Y') }}</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pasien</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No HP</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Vaksin</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dosis</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Countdown</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($schedules as $schedule)
                        @php
                            $daysUntil = floor(now()->diffInDays($schedule->tanggal_vaksin, false));
                            $isUrgent = $daysUntil <= 2 && $daysUntil >= 0;
                        @endphp
                        <tr class="hover:bg-gray-50 {{ $isUrgent ? 'bg-red-50' : '' }}">
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $schedule->patient->pid }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $schedule->patient->nama_pasien }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $schedule->patient->no_hp) }}" 
                                   target="_blank" 
                                   class="text-green-600 hover:text-green-800 flex items-center space-x-1">
                                    <i class="fab fa-whatsapp"></i>
                                    <span>{{ $schedule->patient->no_hp ?? '-' }}</span>
                                </a>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                    {{ $schedule->vaccine->jenis_vaksin === 'HPV' ? 'bg-pink-100 text-pink-800' : 
                                       ($schedule->vaccine->jenis_vaksin === 'Hepatitis' ? 'bg-yellow-100 text-yellow-800' : 
                                        'bg-green-100 text-green-800') }}">
                                    {{ $schedule->vaccine->jenis_vaksin }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                {{ $schedule->dosis_ke }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $schedule->tanggal_vaksin->format('d-m-Y') }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm">
                                @if($daysUntil == 0)
                                    <span class="text-red-600 font-bold">HARI INI</span>
                                @elseif($daysUntil == 1)
                                    <span class="text-orange-600 font-bold">Besok</span>
                                @elseif($daysUntil < 0)
                                    <span class="text-red-600 font-bold">Overdue</span>
                                @else
                                    <span class="{{ $isUrgent ? 'text-orange-600' : 'text-blue-600' }}">
                                        {{ $daysUntil }} hari
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                    {{ $schedule->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($schedule->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                        'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($schedule->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-1">
                                    @if($schedule->status === 'pending')
                                        @php
                                            $isLastDose = $schedule->dosis_ke == $schedule->vaccine->vaccineType->total_dosis;
                                        @endphp
                                        
                                        @if($isLastDose)
                                            {{-- Dosis terakhir: hanya tampilkan button Selesai --}}
                                            <button onclick="showCompleteModal({{ $schedule->id }})" 
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs transition">
                                                <i class="fas fa-check"></i> Dosis Akhir
                                            </button>
                                        @else
                                            {{-- Bukan dosis terakhir: tampilkan button Kirim dengan popup --}}
                                            <button onclick="showSendModal({{ $schedule->id }})" 
                                                    class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-xs transition">
                                                <i class="fas fa-paper-plane"></i> Kirim
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-check-circle text-4xl text-green-400 mb-2"></i>
                                    <p>Tidak ada reminder H-7 saat ini</p>
                                    <p class="text-sm text-gray-400">Semua jadwal vaksinasi sudah up to date</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-gray-50 border-t border-gray-200">
            @include('components.pagination', ['paginator' => $schedules])
        </div>
    </div>
</div>

<!-- Complete Modal -->
<div id="completeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Konfirmasi Vaksinasi Selesai</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 mb-4">Tandai vaksinasi ini sebagai selesai?</p>
                <form id="completeForm" method="POST">
                    @csrf
                    <textarea name="keterangan" placeholder="Keterangan (opsional)" 
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </form>
            </div>
            <div class="flex justify-center space-x-4 mt-4">
                <button onclick="closeCompleteModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Batal
                </button>
                <button onclick="submitComplete()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Ya, Selesai
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Send Modal -->
<div id="sendModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Konfirmasi Reminder Terkirim</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 mb-4">Tandai reminder sudah dikirim?</p>
                <form id="sendForm" method="POST">
                    @csrf
                    <textarea name="keterangan" placeholder="Keterangan (opsional)" 
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </form>
            </div>
            <div class="flex justify-center space-x-4 mt-4">
                <button onclick="closeSendModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Batal
                </button>
                <button onclick="submitSend()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Ya, Kirim
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentScheduleId = null;

function showCompleteModal(scheduleId) {
    currentScheduleId = scheduleId;
    document.getElementById('completeForm').action = `/reminders/${scheduleId}/complete`;
    document.getElementById('completeModal').classList.remove('hidden');
}

function closeCompleteModal() {
    document.getElementById('completeModal').classList.add('hidden');
    currentScheduleId = null;
}

function submitComplete() {
    document.getElementById('completeForm').submit();
}

function showSendModal(scheduleId) {
    currentScheduleId = scheduleId;
    document.getElementById('sendForm').action = `/reminders/${scheduleId}/sent`;
    document.getElementById('sendModal').classList.remove('hidden');
}

function closeSendModal() {
    document.getElementById('sendModal').classList.add('hidden');
    currentScheduleId = null;
}

function submitSend() {
    const form = document.getElementById('sendForm');
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Reminder berhasil ditandai terkirim dan status diupdate ke selesai');
            location.reload();
        } else {
            alert('Gagal: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

// Close modal when clicking outside
window.onclick = function(event) {
    const completeModal = document.getElementById('completeModal');
    const sendModal = document.getElementById('sendModal');
    
    if (event.target == completeModal) {
        closeCompleteModal();
    }
    if (event.target == sendModal) {
        closeSendModal();
    }
}
</script>
@endpush
@endsection
