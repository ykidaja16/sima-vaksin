@extends('layouts.app')

@section('title', 'Buat Protokol ABI')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('protokol-abi.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <i class="fas fa-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Buat Protokol ABI</h1>
            <p class="text-sm text-gray-500 mt-0.5">dr. {{ $namaDokter }}</p>
        </div>
    </div>

    <form action="{{ route('protokol-abi.store') }}" method="POST" id="formProtokolAbi">
        @csrf

        {{-- Data Pasien --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-5">
            <h2 class="text-base font-semibold text-gray-700 mb-4 flex items-center gap-2">
                <i class="fas fa-user text-blue-500"></i> Data Pasien
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pasien <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_pasien" value="{{ old('nama_pasien') }}"
                           class="w-full border @error('nama_pasien') border-red-400 @else border-gray-300 @enderror rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Masukkan nama pasien" required>
                    @error('nama_pasien')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Umur <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="umur" value="{{ old('umur') }}" min="0" max="150"
                               class="w-full border @error('umur') border-red-400 @else border-gray-300 @enderror rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="0" required>
                        <span class="text-sm text-gray-500 whitespace-nowrap">tahun</span>
                    </div>
                    @error('umur')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pemeriksaan <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_pemeriksaan" value="{{ old('tanggal_pemeriksaan', date('Y-m-d')) }}"
                           class="w-full border @error('tanggal_pemeriksaan') border-red-400 @else border-gray-300 @enderror rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                    @error('tanggal_pemeriksaan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat <span class="text-red-500">*</span></label>
                    <textarea name="alamat" rows="2"
                              class="w-full border @error('alamat') border-red-400 @else border-gray-300 @enderror rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                              placeholder="Masukkan alamat pasien" required>{{ old('alamat') }}</textarea>
                    @error('alamat')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Tekanan Darah --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-5">
            <h2 class="text-base font-semibold text-gray-700 mb-4 flex items-center gap-2">
                <i class="fas fa-heartbeat text-red-500"></i> Tekanan Darah
            </h2>
            <p class="text-xs text-gray-500 mb-4">Format: Sistolik / Diastolik (Mean) — contoh: 122 / 83 (92)</p>

            @php
                $lokasi = [
                    ['key' => 'right_arm', 'label' => 'Right Arm (Lengan Kanan)', 'color' => 'red'],
                    ['key' => 'left_arm', 'label' => 'Left Arm (Lengan Kiri)', 'color' => 'yellow'],
                    ['key' => 'right_ankle', 'label' => 'Right Ankle (Pergelangan Kaki Kanan)', 'color' => 'gray'],
                    ['key' => 'left_ankle', 'label' => 'Left Ankle (Pergelangan Kaki Kiri)', 'color' => 'green'],
                ];
            @endphp

            <div class="space-y-4">
                @foreach($lokasi as $loc)
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">{{ $loc['label'] }}</h3>
                    <div class="flex flex-wrap items-end gap-3">
                        <div class="flex-1 min-w-24">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Sistolik *</label>
                            <input type="number" name="{{ $loc['key'] }}_sistolik" value="{{ old($loc['key'].'_sistolik') }}"
                                   min="1" max="300" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="122">
                        </div>
                        <span class="text-gray-400 font-bold pb-2">/</span>
                        <div class="flex-1 min-w-24">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Diastolik *</label>
                            <input type="number" name="{{ $loc['key'] }}_diastolik" value="{{ old($loc['key'].'_diastolik') }}"
                                   min="1" max="300" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="83">
                        </div>
                        <span class="text-gray-400 font-bold pb-2">(</span>
                        <div class="flex-1 min-w-24">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Mean *</label>
                            <input type="number" name="{{ $loc['key'] }}_mean" value="{{ old($loc['key'].'_mean') }}"
                                   min="1" max="300" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="92">
                        </div>
                        <span class="text-gray-400 font-bold pb-2">)</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Perhitungan ABI --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-5">
            <h2 class="text-base font-semibold text-gray-700 mb-4 flex items-center gap-2">
                <i class="fas fa-calculator text-purple-500"></i> Perhitungan ABI
            </h2>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Highest Systolic Brachial Pressure (mmHg) <span class="text-red-500">*</span></label>
                <input type="number" name="highest_brachial_sistolik" value="{{ old('highest_brachial_sistolik') }}"
                       min="1" max="300" required
                       class="w-full max-w-xs border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="122">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- ABI Left --}}
                <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                    <h3 class="text-sm font-semibold text-purple-800 mb-3">ABI Left</h3>
                    <div class="flex flex-wrap items-end gap-2 mb-3">
                        <div class="flex-1 min-w-20">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Pembilang *</label>
                            <input type="number" name="abi_left_pembilang" id="abi_left_pembilang"
                                   value="{{ old('abi_left_pembilang') }}" min="1" max="300" required
                                   class="abi-calc w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                                   placeholder="98">
                        </div>
                        <span class="text-gray-500 font-bold pb-2">/</span>
                        <div class="flex-1 min-w-20">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Penyebut *</label>
                            <input type="number" name="abi_left_penyebut" id="abi_left_penyebut"
                                   value="{{ old('abi_left_penyebut') }}" min="1" max="300" required
                                   class="abi-calc w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                                   placeholder="122">
                        </div>
                    </div>
                    <div class="text-sm text-purple-700 bg-white rounded-lg px-3 py-2 border border-purple-100">
                        Hasil: <strong id="abi_left_hasil_preview">-</strong>
                    </div>
                </div>

                {{-- ABI Right --}}
                <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-200">
                    <h3 class="text-sm font-semibold text-indigo-800 mb-3">ABI Right</h3>
                    <div class="flex flex-wrap items-end gap-2 mb-3">
                        <div class="flex-1 min-w-20">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Pembilang *</label>
                            <input type="number" name="abi_right_pembilang" id="abi_right_pembilang"
                                   value="{{ old('abi_right_pembilang') }}" min="1" max="300" required
                                   class="abi-calc w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                   placeholder="127">
                        </div>
                        <span class="text-gray-500 font-bold pb-2">/</span>
                        <div class="flex-1 min-w-20">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Penyebut *</label>
                            <input type="number" name="abi_right_penyebut" id="abi_right_penyebut"
                                   value="{{ old('abi_right_penyebut') }}" min="1" max="300" required
                                   class="abi-calc w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                   placeholder="122">
                        </div>
                    </div>
                    <div class="text-sm text-indigo-700 bg-white rounded-lg px-3 py-2 border border-indigo-100">
                        Hasil: <strong id="abi_right_hasil_preview">-</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('protokol-abi.index') }}"
               class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium text-sm transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium text-sm transition-colors">
                <i class="fas fa-save"></i> Simpan & Cetak PDF
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function hitungAbi(pembilangId, penyebutId, previewId) {
    const p = parseFloat(document.getElementById(pembilangId).value);
    const d = parseFloat(document.getElementById(penyebutId).value);
    const el = document.getElementById(previewId);
    if (p > 0 && d > 0) {
        el.textContent = (p / d).toFixed(2);
    } else {
        el.textContent = '-';
    }
}

function updateAllAbi() {
    hitungAbi('abi_left_pembilang', 'abi_left_penyebut', 'abi_left_hasil_preview');
    hitungAbi('abi_right_pembilang', 'abi_right_penyebut', 'abi_right_hasil_preview');
}

document.querySelectorAll('.abi-calc').forEach(el => {
    el.addEventListener('input', updateAllAbi);
});

updateAllAbi();
</script>
@endpush
