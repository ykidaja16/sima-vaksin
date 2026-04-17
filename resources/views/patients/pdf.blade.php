<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Pasien - Sistem Reminder Vaksin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            font-size: 10px;
            color: #666;
        }
        .filters {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
            font-size: 10px;
        }
        .filters strong {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        th {
            background-color: #4a5568;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        .vaccine-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .hpv {
            background-color: #fce4ec;
            color: #c2185b;
        }
        .hepatitis {
            background-color: #fff8e1;
            color: #f57f17;
        }
        .influenza {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DATA PASIEN</h1>
        <p>Sistem Reminder Vaksin</p>
        <p>Exported: {{ $exported_at }}</p>
    </div>

    @if($filters['search'] || $filters['jenis_vaksin'])
    <div class="filters">
        <strong>Filter Aktif:</strong><br>
        @if($filters['search'])
            Pencarian: {{ $filters['search'] }}<br>
        @endif
        @if($filters['jenis_vaksin'])
            Jenis Vaksin: {{ $filters['jenis_vaksin'] }}
        @endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 8%;">No</th>
                <th style="width: 12%;">PID</th>
                <th style="width: 20%;">Nama Pasien</th>
                <th style="width: 12%;">No HP</th>
                <th style="width: 20%;">Alamat</th>
                <th style="width: 12%;">Tanggal Lahir</th>
                <th style="width: 8%;">Umur</th>
                <th style="width: 8%;">Jenis Vaksin</th>
            </tr>
        </thead>
        <tbody>
            @forelse($patients as $index => $patient)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $patient->pid }}</td>
                    <td>{{ $patient->nama_pasien }}</td>
                    <td>{{ $patient->no_hp ?? '-' }}</td>
                    <td>{{ $patient->alamat ?? '-' }}</td>
                    <td>{{ $patient->dob ? $patient->dob->format('d-m-Y') : '-' }}</td>
                    <td class="text-center">{{ $patient->age ? $patient->age . ' th' : '-' }}</td>
                    <td>
                        @foreach($patient->vaccines as $vaccine)
                            <span class="vaccine-badge {{ strtolower($vaccine->jenis_vaksin) }}">
                                {{ $vaccine->jenis_vaksin }}
                            </span>
                            @if(!$loop->last)<br>@endif
                        @endforeach
                        @if($patient->vaccines->isEmpty())
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data pasien</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Total Data: {{ $patients->count() }} pasien<br>
        Sistem Reminder Vaksin - SIMA
    </div>
</body>
</html>
