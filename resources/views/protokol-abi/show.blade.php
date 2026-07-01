@extends('layouts.app')

@section('title', 'Detail Pemeriksaan ABI ' . $protokol->no_protokol)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('protokol-abi.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <i class="fas fa-arrow-left text-lg"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-800">Detail Pemeriksaan ABI</h1>
            <p class="text-sm text-blue-600 font-mono font-semibold">{{ $protokol->no_protokol }}</p>
        </div>
        <a id="btn-cetak-pdf"
           href="{{ route('protokol-abi.pdf', $protokol->id) }}"
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
                <dd class="font-semibold text-gray-800 mt-0.5">dr. {{ $protokol->nama_dokter }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Tanggal Pemeriksaan</dt>
                <dd class="font-semibold text-gray-800 mt-0.5">
                    {{ \Carbon\Carbon::parse($protokol->tanggal_pemeriksaan)->locale('id')->translatedFormat('d F Y') }}
                </dd>
            </div>
            <div>
                <dt class="text-gray-500">Nama Pasien</dt>
                <dd class="font-semibold text-gray-800 mt-0.5">{{ $protokol->nama_pasien }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Umur</dt>
                <dd class="font-semibold text-gray-800 mt-0.5">{{ $protokol->umur }} tahun</dd>
            </div>
            <div class="col-span-2">
                <dt class="text-gray-500">Alamat</dt>
                <dd class="font-semibold text-gray-800 mt-0.5">{{ $protokol->alamat }}</dd>
            </div>
        </dl>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-5">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Tekanan Darah</h2>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between py-1 border-b border-gray-100">
                <span class="text-gray-600">Right Arm</span>
                <span class="font-semibold">{{ $protokol->right_arm_sistolik }}/{{ $protokol->right_arm_diastolik }} ({{ $protokol->right_arm_mean }})</span>
            </div>
            <div class="flex justify-between py-1 border-b border-gray-100">
                <span class="text-gray-600">Left Arm</span>
                <span class="font-semibold">{{ $protokol->left_arm_sistolik }}/{{ $protokol->left_arm_diastolik }} ({{ $protokol->left_arm_mean }})</span>
            </div>
            <div class="flex justify-between py-1 border-b border-gray-100">
                <span class="text-gray-600">Right Ankle</span>
                <span class="font-semibold">{{ $protokol->right_ankle_sistolik }}/{{ $protokol->right_ankle_diastolik }} ({{ $protokol->right_ankle_mean }})</span>
            </div>
            <div class="flex justify-between py-1">
                <span class="text-gray-600">Left Ankle</span>
                <span class="font-semibold">{{ $protokol->left_ankle_sistolik }}/{{ $protokol->left_ankle_diastolik }} ({{ $protokol->left_ankle_mean }})</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-5">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Hasil ABI</h2>
        <dl class="space-y-3 text-sm">
            <div>
                <dt class="text-gray-500">Highest Systolic Brachial Pressure</dt>
                <dd class="font-semibold text-gray-800 mt-0.5">{{ $protokol->highest_brachial_sistolik }} mmHg</dd>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-purple-50 rounded-lg p-3">
                    <dt class="text-purple-600 text-xs font-medium">ABI Left</dt>
                    <dd class="font-bold text-purple-800 mt-1">
                        {{ $protokol->abi_left_pembilang }}/{{ $protokol->abi_left_penyebut }} = {{ number_format($protokol->abi_left_hasil, 2) }}
                    </dd>
                </div>
                <div class="bg-indigo-50 rounded-lg p-3">
                    <dt class="text-indigo-600 text-xs font-medium">ABI Right</dt>
                    <dd class="font-bold text-indigo-800 mt-1">
                        {{ $protokol->abi_right_pembilang }}/{{ $protokol->abi_right_penyebut }} = {{ number_format($protokol->abi_right_hasil, 2) }}
                    </dd>
                </div>
            </div>
        </dl>
    </div>

    <div class="mt-4 flex justify-end">
        <a href="{{ route('protokol-abi.index') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <i class="fas fa-list"></i> Kembali ke Daftar Pemeriksaan ABI
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
