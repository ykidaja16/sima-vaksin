@extends('layouts.app')

@section('title', 'Buat Resep Baru')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('resep.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <i class="fas fa-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Buat Resep Baru</h1>
            <p class="text-sm text-gray-500 mt-0.5">dr. {{ $namaDokter }}</p>
        </div>
    </div>

    <form action="{{ route('resep.store') }}" method="POST" id="formResep">
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
                    @error('nama_pasien')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Umur <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="umur" value="{{ old('umur') }}" min="0" max="150"
                               class="w-full border @error('umur') border-red-400 @else border-gray-300 @enderror rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="0" required>
                        <span class="text-sm text-gray-500 whitespace-nowrap">tahun</span>
                    </div>
                    @error('umur')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Resep <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_resep" value="{{ old('tanggal_resep', date('Y-m-d')) }}"
                           class="w-full border @error('tanggal_resep') border-red-400 @else border-gray-300 @enderror rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                    @error('tanggal_resep')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat <span class="text-red-500">*</span></label>
                    <textarea name="alamat" rows="2"
                              class="w-full border @error('alamat') border-red-400 @else border-gray-300 @enderror rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                              placeholder="Masukkan alamat pasien" required>{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Daftar Obat --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-gray-700 flex items-center gap-2">
                    <i class="fas fa-pills text-green-500"></i> Daftar Obat
                </h2>
                <button type="button" onclick="tambahObat()"
                        class="inline-flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-plus text-xs"></i> Tambah Obat
                </button>
            </div>

            @error('obat')
                <div class="bg-red-50 border border-red-200 text-red-600 rounded-lg px-4 py-2 text-sm mb-3">
                    {{ $message }}
                </div>
            @enderror

            <div id="obatContainer" class="space-y-3">
                {{-- Populated by JS or old() --}}
            </div>

            <p id="emptyObat" class="text-center text-gray-400 text-sm py-6 hidden">
                <i class="fas fa-pills text-2xl mb-2 block"></i>
                Belum ada obat. Klik "Tambah Obat".
            </p>
        </div>

        {{-- Action --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('resep.index') }}"
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
const satuanKekuatanList = ['-', 'mg', 'ml', '%'];
const waktuList  = ['Sesuai Dosis', 'Pagi', 'Siang', 'Sore', 'Malam'];
const makanList  = ['-', 'Sebelum Makan', 'Sesudah Makan'];
const satuanList = ['-','tablet','kaplet','kapsul','strip','tube','botol'];
const oldObat = @json(old('obat', []));

let counter = 0;

function tambahObat(data = {}) {
    counter++;
    const idx = counter - 1;
    const container = document.getElementById('obatContainer');
    document.getElementById('emptyObat').classList.add('hidden');

    // Parse dosis lama jika ada (format "3x1")
    let dosisKali = 1, dosisJumlah = 1;
    if (data.dosis_kali) dosisKali = data.dosis_kali;
    if (data.dosis_jumlah) dosisJumlah = data.dosis_jumlah;

    const waktuOptions = waktuList.map(w =>
        `<option value="${w}" ${(data.waktu_minum || 'Sesuai Dosis') === w ? 'selected' : ''}>${w}</option>`
    ).join('');

    const satuanKekuatanOptions = satuanKekuatanList.map(s =>
        `<option value="${s}" ${(data.satuan_kekuatan || 'mg') === s ? 'selected' : ''}>${s}</option>`
    ).join('');

    const makanOptions = makanList.map(m =>
        `<option value="${m}" ${(data.makan || '-') === m ? 'selected' : ''}>${m}</option>`
    ).join('');

    const satuanOptions = satuanList.map(s =>
        `<option value="${s}" ${(data.satuan || '-') === s ? 'selected' : ''}>${s}</option>`
    ).join('');

    const html = `
        <div class="obat-row flex items-end gap-2 bg-gray-50 rounded-lg p-3 border border-gray-200" data-idx="${idx}">
            <div class="flex-1 min-w-0">
                <label class="block text-xs font-medium text-gray-600 mb-1">Nama Obat *</label>
                <input type="text" name="obat[${idx}][nama_obat]" value="${data.nama_obat || ''}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Nama obat" required>
            </div>
            <div class="shrink-0 w-16">
                <label class="block text-xs font-medium text-gray-600 mb-1">Kadar</label>
                <input type="number" name="obat[${idx}][kekuatan]" value="${data.kekuatan || ''}" min="0" step="any"
                       class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm text-center focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="500">
            </div>
            <div class="shrink-0 w-16">
                <label class="block text-xs font-medium text-gray-600 mb-1">Satuan</label>
                <select name="obat[${idx}][satuan_kekuatan]"
                        class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    ${satuanKekuatanOptions}
                </select>
            </div>
            <div class="shrink-0">
                <label class="block text-xs font-medium text-gray-600 mb-1">Dosis *</label>
                <div class="flex items-center gap-1">
                    <input type="number" name="obat[${idx}][dosis_kali]" value="${dosisKali}" min="1" max="99"
                           class="w-12 border border-gray-300 rounded-lg px-1 py-2 text-sm text-center focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                    <span class="text-gray-500 font-bold text-sm select-none">x</span>
                    <input type="number" name="obat[${idx}][dosis_jumlah]" value="${dosisJumlah}" min="1" max="99"
                           class="w-12 border border-gray-300 rounded-lg px-1 py-2 text-sm text-center focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                </div>
            </div>
            <div class="shrink-0 w-28">
                <label class="block text-xs font-medium text-gray-600 mb-1">Waktu Minum</label>
                <select name="obat[${idx}][waktu_minum]"
                        class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    ${waktuOptions}
                </select>
            </div>
            <div class="shrink-0 w-32">
                <label class="block text-xs font-medium text-gray-600 mb-1">Makan</label>
                <select name="obat[${idx}][makan]"
                        class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    ${makanOptions}
                </select>
            </div>
            <div class="shrink-0 w-14">
                <label class="block text-xs font-medium text-gray-600 mb-1">Jumlah</label>
                <input type="number" name="obat[${idx}][jumlah]" value="${data.jumlah !== undefined ? data.jumlah : 0}" min="0" max="9999"
                       class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm text-center focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>
            <div class="shrink-0 w-24">
                <label class="block text-xs font-medium text-gray-600 mb-1">Satuan</label>
                <select name="obat[${idx}][satuan]"
                        class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    ${satuanOptions}
                </select>
            </div>
            <div class="flex-1 min-w-0">
                <label class="block text-xs font-medium text-gray-600 mb-1">Keterangan</label>
                <input type="text" name="obat[${idx}][keterangan]" value="${data.keterangan || ''}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Keterangan (Opsional)">
            </div>
            <div class="shrink-0">
                <button type="button" onclick="hapusObat(this)"
                        class="p-2 rounded-lg text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors mb-0.5" title="Hapus">
                    <i class="fas fa-trash text-sm"></i>
                </button>
            </div>
        </div>`;

    container.insertAdjacentHTML('beforeend', html);
    checkEmpty();
}

function hapusObat(btn) {
    btn.closest('.obat-row').remove();
    checkEmpty();
}

function checkEmpty() {
    const rows = document.querySelectorAll('.obat-row');
    document.getElementById('emptyObat').classList.toggle('hidden', rows.length > 0);
}

if (oldObat.length > 0) {
    oldObat.forEach(o => tambahObat(o));
} else {
    tambahObat();
}

document.getElementById('formResep').addEventListener('submit', function(e) {
    const rows = document.querySelectorAll('.obat-row');
    if (rows.length === 0) {
        e.preventDefault();
        alert('Minimal satu obat harus ditambahkan!');
    }
});
</script>
@endpush
