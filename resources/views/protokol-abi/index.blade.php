@extends('layouts.app')

@section('title', 'Pemeriksaan ABI')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pemeriksaan ABI</h1>
        </div>
        <a href="{{ route('protokol-abi.create') }}"
           class="inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-plus"></i>
            <span>Buat Pemeriksaan ABI</span>
        </a>
    </div>

    <form method="GET" action="{{ route('protokol-abi.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-48">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari no. Pemeriksaan / nama pasien..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-search mr-1"></i> Cari
            </button>
            @if(request('search') || request('tanggal'))
                <a href="{{ route('protokol-abi.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-times mr-1"></i> Reset
                </a>
            @endif
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($protokol->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">No. Pemeriksaan</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama Pasien</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Umur</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">ABI Left</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">ABI Right</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Tanggal</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($protokol as $p)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <span class="font-mono font-semibold text-blue-600">{{ $p->no_protokol }}</span>
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $p->nama_pasien }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $p->umur }} thn</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                    {{ number_format($p->abi_left_hasil, 2) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                                    {{ number_format($p->abi_right_hasil, 2) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ \Carbon\Carbon::parse($p->tanggal_pemeriksaan)->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('protokol-abi.pdf', $p->id) }}" target="_blank"
                                       class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors"
                                       title="Cetak PDF">
                                        <i class="fas fa-print"></i>
                                        <span>Cetak</span>
                                    </a>
                                    <a href="{{ route('protokol-abi.show', $p->id) }}"
                                       class="inline-flex items-center gap-1 bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors"
                                       title="Detail">
                                        <i class="fas fa-eye"></i>
                                        <span>Detail</span>
                                    </a>
                                    <form action="{{ route('protokol-abi.destroy', $p->id) }}" method="POST"
                                          onsubmit="return confirm('Hapus protokol {{ $p->no_protokol }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 bg-gray-50 hover:bg-red-50 text-gray-500 hover:text-red-600 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between px-5 py-3 border-t border-gray-200 bg-gray-50">
                <p class="text-sm text-gray-500">
                    @if($protokol->total() > 0)
                        Menampilkan
                        <span class="font-semibold text-gray-700">{{ $protokol->firstItem() }}</span>–<span class="font-semibold text-gray-700">{{ $protokol->lastItem() }}</span>
                        dari <span class="font-semibold text-gray-700">{{ $protokol->total() }}</span> Pemeriksaan
                    @endif
                </p>

                @if($protokol->hasPages())
                <nav class="flex items-center gap-1">
                    @if($protokol->onFirstPage())
                        <span class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-sm text-gray-300 cursor-not-allowed select-none">
                            <i class="fas fa-chevron-left text-xs"></i>
                        </span>
                    @else
                        <a href="{{ $protokol->previousPageUrl() }}"
                           class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-sm text-gray-600 hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-200 transition-all">
                            <i class="fas fa-chevron-left text-xs"></i>
                        </a>
                    @endif

                    @php
                        $current = $protokol->currentPage();
                        $last    = $protokol->lastPage();
                        $start   = max(1, $current - 2);
                        $end     = min($last, $current + 2);
                    @endphp

                    @for($p = $start; $p <= $end; $p++)
                        @if($p == $current)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold bg-blue-600 text-white shadow-sm select-none">{{ $p }}</span>
                        @else
                            <a href="{{ $protokol->url($p) }}"
                               class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm text-gray-600 hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-200 transition-all">{{ $p }}</a>
                        @endif
                    @endfor

                    @if($protokol->hasMorePages())
                        <a href="{{ $protokol->nextPageUrl() }}"
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
        @else
            <div class="text-center py-16 text-gray-400">
                <i class="fas fa-heartbeat text-5xl mb-3"></i>
                <p class="text-lg font-medium">Belum ada Pemeriksaan ABI</p>
                <p class="text-sm mt-1">Klik tombol "Buat Pemeriksaan ABI" untuk membuat Pemeriksaan baru</p>
            </div>
        @endif
    </div>
</div>
@endsection
