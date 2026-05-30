@extends('layouts.app')

@section('title', 'Resep Dokter')

@section('content')
<div class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Resep Dokter</h1>
            <p class="text-sm text-gray-500 mt-1">dr. {{ auth()->user()->name }}</p>
        </div>
        <a href="{{ route('resep.create') }}"
           class="inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-plus"></i>
            <span>Buat Resep</span>
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('resep.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-48">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari no. resep / nama pasien..."
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
                <a href="{{ route('resep.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-times mr-1"></i> Reset
                </a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($resep->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">No. Resep</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama Pasien</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Umur</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Jml. Obat</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Tanggal</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($resep as $r)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <span class="font-mono font-semibold text-blue-600">{{ $r->no_resep }}</span>
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $r->nama_pasien }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $r->umur }} thn</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    {{ $r->obat->count() }} obat
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ \Carbon\Carbon::parse($r->tanggal_resep)->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('resep.pdf', $r->id) }}" target="_blank"
                                       class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors"
                                       title="Cetak PDF">
                                        <i class="fas fa-print"></i>
                                        <span>Cetak</span>
                                    </a>
                                    <a href="{{ route('resep.show', $r->id) }}"
                                       class="inline-flex items-center gap-1 bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors"
                                       title="Detail">
                                        <i class="fas fa-eye"></i>
                                        <span>Detail</span>
                                    </a>
                                    <form action="{{ route('resep.destroy', $r->id) }}" method="POST"
                                          onsubmit="return confirm('Hapus resep {{ $r->no_resep }}?')">
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

            {{-- Pagination Footer --}}
            <div class="flex items-center justify-between px-5 py-3 border-t border-gray-200 bg-gray-50">
                <p class="text-sm text-gray-500">
                    @if($resep->total() > 0)
                        Menampilkan
                        <span class="font-semibold text-gray-700">{{ $resep->firstItem() }}</span>–<span class="font-semibold text-gray-700">{{ $resep->lastItem() }}</span>
                        dari <span class="font-semibold text-gray-700">{{ $resep->total() }}</span> resep
                    @endif
                </p>

                @if($resep->hasPages())
                <nav class="flex items-center gap-1">
                    {{-- Prev --}}
                    @if($resep->onFirstPage())
                        <span class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-sm text-gray-300 cursor-not-allowed select-none">
                            <i class="fas fa-chevron-left text-xs"></i>
                        </span>
                    @else
                        <a href="{{ $resep->previousPageUrl() }}"
                           class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-sm text-gray-600 hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-200 transition-all">
                            <i class="fas fa-chevron-left text-xs"></i>
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @php
                        $current  = $resep->currentPage();
                        $last     = $resep->lastPage();
                        $start    = max(1, $current - 2);
                        $end      = min($last, $current + 2);
                    @endphp

                    @if($start > 1)
                        <a href="{{ $resep->url(1) }}"
                           class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm text-gray-600 hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-200 transition-all">1</a>
                        @if($start > 2)
                            <span class="px-1.5 py-1.5 text-sm text-gray-400 select-none">…</span>
                        @endif
                    @endif

                    @for($p = $start; $p <= $end; $p++)
                        @if($p == $current)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold bg-blue-600 text-white shadow-sm select-none">
                                {{ $p }}
                            </span>
                        @else
                            <a href="{{ $resep->url($p) }}"
                               class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm text-gray-600 hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-200 transition-all">
                                {{ $p }}
                            </a>
                        @endif
                    @endfor

                    @if($end < $last)
                        @if($end < $last - 1)
                            <span class="px-1.5 py-1.5 text-sm text-gray-400 select-none">…</span>
                        @endif
                        <a href="{{ $resep->url($last) }}"
                           class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm text-gray-600 hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-200 transition-all">{{ $last }}</a>
                    @endif

                    {{-- Next --}}
                    @if($resep->hasMorePages())
                        <a href="{{ $resep->nextPageUrl() }}"
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
                <i class="fas fa-file-medical text-5xl mb-3"></i>
                <p class="text-lg font-medium">Belum ada resep</p>
                <p class="text-sm mt-1">Klik tombol "Buat Resep" untuk membuat resep baru</p>
            </div>
        @endif
    </div>
</div>
@endsection
