@extends('layouts.app')

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
                        <span class="font-medium">{{ $patient->no_hp ?? '-' }}</span>
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
                            <span class="text-sm text-gray-500">
                                Pertama: {{ $vaccine->tanggal_vaksin_pertama->format('d-m-Y') }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-600">
                            Total Dosis: {{ $vaccine->total_dosis }}
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
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $schedule->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($schedule->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                        'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($schedule->status) }}
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
@endsection
