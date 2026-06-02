@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-5xl mx-auto">
    {{-- Greeting --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            Selamat datang, {{ $user->isDokter() ? 'dr. ' : '' }}{{ $user->name }}
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            {{ now()->translatedFormat('l, d F Y') }} &mdash;
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                @if($user->isIT()) bg-purple-100 text-purple-700
                @elseif($user->isAdmin()) bg-blue-100 text-blue-700
                @elseif($user->isDokter()) bg-green-100 text-green-700
                @endif">
                {{ $user->role_name }}
            </span>
        </p>
    </div>

    {{-- ===== DOKTER ===== --}}
    @if($user->isDokter())
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center gap-4">
                <div class="bg-green-100 p-3 rounded-xl">
                    <i class="fas fa-file-medical text-2xl text-green-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Resep Hari Ini</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $data['resepHariIni'] }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center gap-4">
                <div class="bg-blue-100 p-3 rounded-xl">
                    <i class="fas fa-calendar-alt text-2xl text-blue-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Bulan Ini</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $data['resepBulanIni'] }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center gap-4">
                <div class="bg-indigo-100 p-3 rounded-xl">
                    <i class="fas fa-notes-medical text-2xl text-indigo-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Resep</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $data['totalResep'] }}</p>
                </div>
            </div>
        </div>

        {{-- Shortcut --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
            <h2 class="text-sm font-semibold text-gray-600 mb-3">Aksi Cepat</h2>
            <a href="{{ route('resep.create') }}"
               class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg font-medium text-sm transition-colors">
                <i class="fas fa-plus"></i> Buat Resep Baru
            </a>
            <a href="{{ route('resep.index') }}"
               class="inline-flex items-center gap-2 bg-blue-50 hover:bg-blue-100 text-blue-700 px-5 py-2.5 rounded-lg font-medium text-sm transition-colors ml-2">
                <i class="fas fa-list"></i> Lihat Semua Resep
            </a>
        </div>

        {{-- Resep terbaru --}}
        @if($data['resepTerbaru']->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-700">Resep Terbaru</h2>
                <span class="text-xs text-gray-400">{{ $data['resepTerbaru']->total() }} resep</span>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">No. Resep</th>
                        <th class="px-4 py-3 text-left">Nama Pasien</th>
                        <th class="px-4 py-3 text-left">Obat</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($data['resepTerbaru'] as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-blue-600 font-semibold">{{ $r->no_resep }}</td>
                        <td class="px-4 py-3 font-medium">{{ $r->nama_pasien }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $r->obat->count() }} obat</td>
                        <td class="px-4 py-3 text-gray-500">{{ \Carbon\Carbon::parse($r->tanggal_resep)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('resep.pdf', $r->id) }}" target="_blank"
                               class="inline-flex items-center gap-1 text-xs text-red-600 hover:underline">
                                <i class="fas fa-print"></i> Cetak
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination Footer --}}
            <div class="flex items-center justify-between px-5 py-3 border-t border-gray-200 bg-gray-50">
                <p class="text-sm text-gray-500">
                    Menampilkan
                    <span class="font-semibold text-gray-700">{{ $data['resepTerbaru']->firstItem() }}</span>–<span class="font-semibold text-gray-700">{{ $data['resepTerbaru']->lastItem() }}</span>
                    dari <span class="font-semibold text-gray-700">{{ $data['resepTerbaru']->total() }}</span> resep
                </p>

                @if($data['resepTerbaru']->hasPages())
                <nav class="flex items-center gap-1">
                    @if($data['resepTerbaru']->onFirstPage())
                        <span class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-sm text-gray-300 cursor-not-allowed select-none">
                            <i class="fas fa-chevron-left text-xs"></i>
                        </span>
                    @else
                        <a href="{{ $data['resepTerbaru']->previousPageUrl() }}"
                           class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-sm text-gray-600 hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-200 transition-all">
                            <i class="fas fa-chevron-left text-xs"></i>
                        </a>
                    @endif

                    @php
                        $current = $data['resepTerbaru']->currentPage();
                        $last    = $data['resepTerbaru']->lastPage();
                        $start   = max(1, $current - 2);
                        $end     = min($last, $current + 2);
                    @endphp

                    @if($start > 1)
                        <a href="{{ $data['resepTerbaru']->url(1) }}"
                           class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm text-gray-600 hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-200 transition-all">1</a>
                        @if($start > 2)
                            <span class="px-1 py-1.5 text-sm text-gray-400 select-none">…</span>
                        @endif
                    @endif

                    @for($p = $start; $p <= $end; $p++)
                        @if($p == $current)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold bg-blue-600 text-white shadow-sm select-none">{{ $p }}</span>
                        @else
                            <a href="{{ $data['resepTerbaru']->url($p) }}"
                               class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm text-gray-600 hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-200 transition-all">{{ $p }}</a>
                        @endif
                    @endfor

                    @if($end < $last)
                        @if($end < $last - 1)
                            <span class="px-1 py-1.5 text-sm text-gray-400 select-none">…</span>
                        @endif
                        <a href="{{ $data['resepTerbaru']->url($last) }}"
                           class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm text-gray-600 hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-200 transition-all">{{ $last }}</a>
                    @endif

                    @if($data['resepTerbaru']->hasMorePages())
                        <a href="{{ $data['resepTerbaru']->nextPageUrl() }}"
                           class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-sm text-gray-600 hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-200 transition-all">
                            <i class="fas fa-chevron-right text-xs"></i>
                        </a>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-sm text-gray-300 cursor-not-allowed select-none">
                            <i class="fas fa-chevron-right text-xs"></i>
                        </span>
                    @endif
                </nav>
                @endif
            </div>
        </div>
        @endif
    @endif

    {{-- ===== ADMIN ===== --}}
    @if($user->isAdmin())
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="bg-blue-100 p-3 rounded-xl inline-flex mb-3">
                    <i class="fas fa-users text-2xl text-blue-600"></i>
                </div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Pasien</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $data['totalPasien'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="bg-yellow-100 p-3 rounded-xl inline-flex mb-3">
                    <i class="fas fa-bell text-2xl text-yellow-600"></i>
                </div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Reminder Hari Ini</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $data['reminderHariIni'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="bg-orange-100 p-3 rounded-xl inline-flex mb-3">
                    <i class="fas fa-clock text-2xl text-orange-600"></i>
                </div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Reminder H-7</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $data['reminderH7'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="bg-green-100 p-3 rounded-xl inline-flex mb-3">
                    <i class="fas fa-calendar-check text-2xl text-green-600"></i>
                </div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Jadwal Bulan Ini</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $data['jadwalBulanIni'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h2 class="text-sm font-semibold text-gray-600 mb-3">Aksi Cepat</h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('patients.index') }}"
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-users"></i> Data Pasien
                </a>
                <a href="{{ route('reminders.index') }}"
                   class="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-bell"></i> Reminder H-7
                </a>
                <a href="{{ route('manual-input.index') }}"
                   class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-edit"></i> Input Manual
                </a>
            </div>
        </div>
    @endif

    {{-- ===== IT ===== --}}
    @if($user->isIT())
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="bg-purple-100 p-3 rounded-xl inline-flex mb-3">
                    <i class="fas fa-user-cog text-2xl text-purple-600"></i>
                </div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total User</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $data['totalUser'] }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $data['userAktif'] }} aktif</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="bg-blue-100 p-3 rounded-xl inline-flex mb-3">
                    <i class="fas fa-hospital text-2xl text-blue-600"></i>
                </div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Cabang</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $data['totalCabang'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="bg-green-100 p-3 rounded-xl inline-flex mb-3">
                    <i class="fas fa-syringe text-2xl text-green-600"></i>
                </div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Jenis Vaksin</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $data['totalVaksin'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h2 class="text-sm font-semibold text-gray-600 mb-3">Aksi Cepat</h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('users.index') }}"
                   class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-user-cog"></i> Manajemen User
                </a>
                <a href="{{ route('vaccine-types.index') }}"
                   class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-syringe"></i> Manajemen Vaksin
                </a>
                <a href="{{ route('branches.index') }}"
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-hospital"></i> Manajemen Cabang
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
