@extends('layouts.app')

@section('title', 'Detail Resep ' . $resep->no_resep)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('resep.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <i class="fas fa-arrow-left text-lg"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-800">Detail Resep</h1>
            <p class="text-sm text-blue-600 font-mono font-semibold">{{ $resep->no_resep }}</p>
        </div>
        <a id="btn-cetak-pdf"
           href="{{ route('resep.pdf', $resep->id) }}"
           target="_blank"
           class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="fas fa-print"></i> Cetak PDF
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-5">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Informasi Pasien</h2>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">Dokter</dt>
                <dd class="font-semibold text-gray-800 mt-0.5">dr. {{ $resep->nama_dokter }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Tanggal Resep</dt>
                <dd class="font-semibold text-gray-800 mt-0.5">
                    {{ \Carbon\Carbon::parse($resep->tanggal_resep)->locale('id')->translatedFormat('d F Y') }}
                </dd>
            </div>
            <div>
                <dt class="text-gray-500">Nama Pasien</dt>
                <dd class="font-semibold text-gray-800 mt-0.5">{{ $resep->nama_pasien }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Umur</dt>
                <dd class="font-semibold text-gray-800 mt-0.5">{{ $resep->umur }} tahun</dd>
            </div>
            <div class="col-span-2">
                <dt class="text-gray-500">Alamat</dt>
                <dd class="font-semibold text-gray-800 mt-0.5">{{ $resep->alamat }}</dd>
            </div>
        </dl>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Daftar Obat</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama Obat</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Dosis</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Makan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($resep->obat as $i => $obat)
                <tr>
                    <td class="px-4 py-3 text-gray-400">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $obat->nama_obat }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $obat->dosis }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $obat->waktu_minum === 'Sesuai Dosis' ? 'bg-gray-100 text-gray-500' : 'bg-blue-100 text-blue-700' }}">
                            {{ $obat->waktu_minum }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        @if($obat->makan !== '-')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                                {{ $obat->makan }}
                            </span>
                        @else
                            <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @if($obat->jumlah > 0 || $obat->satuan !== '-')
                            <span class="font-medium text-gray-700">
                                {{ $obat->jumlah > 0 ? $obat->jumlah : '' }}
                                {{ $obat->satuan !== '-' ? $obat->satuan : '' }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex justify-end">
        <a href="{{ route('resep.index') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <i class="fas fa-list"></i> Kembali ke Daftar Resep
        </a>
    </div>
</div>
@endsection

@push('scripts')
@if(session('auto_print'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('btn-cetak-pdf').click();
    });
</script>
@endif
@endpush
