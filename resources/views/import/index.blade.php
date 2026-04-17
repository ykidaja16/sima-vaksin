@extends('layouts.app')

@section('title', 'Import Data Pasien - Sistem Reminder Vaksin')

@section('content')
<div class="max-w-6xl mx-auto space-y-6 px-4 sm:px-0">
    <!-- Header -->
    <div class="text-center">
        <h1 class="text-2xl font-bold text-gray-900">Import Data Pasien</h1>
        <p class="text-gray-600">Upload file Excel untuk mengimport data pasien dan generate jadwal vaksinasi</p>
    </div>

    <!-- Template Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-start space-x-4">
            <div class="bg-blue-100 p-3 rounded-full">
                <i class="fas fa-info-circle text-blue-600 text-xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-blue-900 mb-2">Format File Excel yang Diperlukan</h3>
                <p class="text-blue-800 text-sm mb-4">File Excel harus memiliki header kolom berikut:</p>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border border-blue-200 rounded">
                        <thead class="bg-blue-100">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium text-blue-900">pid</th>
                                <th class="px-3 py-2 text-left font-medium text-blue-900">nama_pasien</th>
                                <th class="px-3 py-2 text-left font-medium text-blue-900">no_hp</th>
                                <th class="px-3 py-2 text-left font-medium text-blue-900">alamat</th>
                                <th class="px-3 py-2 text-left font-medium text-blue-900">dob</th>
                                <th class="px-3 py-2 text-left font-medium text-blue-900">jenis_vaksin</th>
                                <th class="px-3 py-2 text-left font-medium text-blue-900">tanggal_vaksin_pertama</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <tr>
                                <td class="px-3 py-2 text-gray-600">LXB001 (prefix sesuai cabang)</td>
                                <td class="px-3 py-2 text-gray-600">Budi Santoso</td>
                                <td class="px-3 py-2 text-gray-600">08123456789</td>
                                <td class="px-3 py-2 text-gray-600">Jl. Mawar No. 1</td>
                                <td class="px-3 py-2 text-gray-600">1990-05-15</td>
                                <td class="px-3 py-2 text-gray-600">HPV/Hepatitis/Influenza</td>
                                <td class="px-3 py-2 text-gray-600">2026-01-15</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- <div class="mt-4 text-sm text-blue-800">
                    <p class="font-medium mb-2">Contoh PID berdasarkan Cabang:</p>
                    <ul class="list-disc list-inside space-y-1 ml-2">
                        <li>Ciliwung: LXB0049356 (diawali dengan LX)</li>
                        <li>Tangkuban Perahu: LZD0010534 (diawali dengan LZ)</li>
                    </ul>
                </div> --}}
            </div>
        </div>
    </div>

    <!-- Download Template -->
    <div class="bg-white rounded-lg shadow p-6 text-center">
        <p class="text-gray-600 mb-4">Belum punya template? Download template Excel di bawah ini:</p>
        <a href="{{ route('import.template') }}" class="inline-flex items-center space-x-2 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition">
            <i class="fas fa-download"></i>
            <span>Download Template CSV</span>
        </a>
    </div>

    <!-- Upload Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <!-- Branch Selection -->
            <div class="max-w-md mx-auto">
                <label for="branch_id" class="block text-sm font-medium text-gray-700 mb-2 text-center">
                    <i class="fas fa-hospital mr-2"></i>Pilih Cabang
                </label>
                <select name="branch_id" id="branch_id" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('branch_id') border-red-500 @enderror">
                    <option value="">-- Pilih Cabang --</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->nama_cabang }} (Prefix: {{ $branch->kode_prefix }})
                        </option>
                    @endforeach
                </select>
                @error('branch_id')
                    <div class="text-red-600 text-sm text-center mt-1">{{ $message }}</div>
                @enderror
                <p class="text-xs text-gray-500 text-center mt-2">
                    PID di file Excel harus diawali dengan kode prefix cabang yang dipilih
                </p>
            </div>

            <!-- File Upload - Centered -->
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-500 transition max-w-2xl mx-auto">
                <div class="space-y-4">
                    <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto">
                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400"></i>
                    </div>
                    <div class="text-center">
                        <label for="file" class="block text-sm font-medium text-gray-700 mb-4">
                            Pilih File Excel (.xlsx, .xls, .csv)
                        </label>
                        <div class="flex justify-center">
                            <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv" required
                                   class="block w-full max-w-xs text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 file:cursor-pointer">
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">Maksimal ukuran file: 10MB</p>
                </div>
            </div>

            @error('file')
                <div class="text-red-600 text-sm text-center">{{ $message }}</div>
            @enderror

            <!-- Import Errors Display -->
            {{-- @if(session('import_errors'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 max-w-2xl mx-auto">
                    <h4 class="font-semibold text-red-900 mb-2 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Error Import
                    </h4>
                    <ul class="text-sm text-red-800 space-y-1 list-disc list-inside max-h-40 overflow-y-auto">
                        @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif --}}

            <div class="flex justify-center space-x-4">
                <a href="{{ route('patients.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-3 rounded-lg transition">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center space-x-2 transition">
                    <i class="fas fa-file-import"></i>
                    <span>Import Data</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Tips -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <h4 class="font-semibold text-yellow-900 mb-2 flex items-center">
            <i class="fas fa-lightbulb mr-2"></i>
            Tips Import
        </h4>
        <ul class="text-sm text-yellow-800 space-y-1 list-disc list-inside">
            <li><strong>Pilih cabang terlebih dahulu</strong> sebelum mengupload file</li>
            <li>PID harus diawali dengan kode prefix cabang (contoh: LX untuk Ciliwung, LZ untuk Tangkuban Perahu)</li>
            <li>PID yang tidak sesuai dengan prefix cabang akan ditolak</li>
            <li>PID yang sudah ada dengan nama berbeda akan ditolak</li>
            <li>Data duplikat (PID, Nama, DOB, Jenis Vaksin, Tanggal Vaksin Pertama sama) akan ditolak</li>
            <li>Format tanggal yang didukung: YYYY-MM-DD, DD-MM-YYYY, atau DD/MM/YYYY</li>
            <li>Jenis vaksin harus sesuai dengan data di master vaksin</li>
            <li>Jadwal vaksinasi akan otomatis digenerate setelah import</li>
        </ul>
    </div>
</div>
@endsection
