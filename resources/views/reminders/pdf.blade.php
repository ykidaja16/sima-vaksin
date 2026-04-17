<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reminder H-7 - Sistem Reminder Vaksin</title>
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
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .completed {
            background-color: #d4edda;
            color: #155724;
        }
        .urgent {
            color: #dc3545;
            font-weight: bold;
        }
        .warning {
            color: #fd7e14;
            font-weight: bold;
        }
        .normal {
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REMINDER H-7</h1>
        <p>Sistem Reminder Vaksin</p>
        <p>Periode: {{ now()->format('d-m-Y') }} - {{ now()->addDays(7)->format('d-m-Y') }}</p>
        <p>Exported: {{ $exported_at }}</p>
    </div>

    @if($filters['status'] !== 'pending' || $filters['date_from'] || $filters['date_to'])
    <div class="filters">
        <strong>Filter Aktif:</strong><br>
        @if($filters['status'] !== 'pending')
            Status: {{ ucfirst($filters['status']) }}<br>
        @endif
        @if($filters['date_from'])
            Dari Tanggal: {{ $filters['date_from'] }}<br>
        @endif
        @if($filters['date_to'])
            Sampai Tanggal: {{ $filters['date_to'] }}
        @endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">No</th>
                <th style="width: 10%;">PID</th>
                <th style="width: 18%;">Nama Pasien</th>
                <th style="width: 12%;">No HP</th>
                <th style="width: 12%;">Jenis Vaksin</th>
                <th class="text-center" style="width: 8%;">Dosis</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 12%;">Countdown</th>
                <th style="width: 11%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($schedules as $index => $schedule)
                @php
                    $daysUntil = floor(now()->diffInDays($schedule->tanggal_vaksin, false));
                    $isUrgent = $daysUntil <= 2 && $daysUntil >= 0;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $schedule->patient->pid }}</td>
                    <td>{{ $schedule->patient->nama_pasien }}</td>
                    <td>{{ $schedule->patient->no_hp ?? '-' }}</td>
                    <td>
                        <span class="vaccine-badge {{ strtolower($schedule->vaccine->jenis_vaksin) }}">
                            {{ $schedule->vaccine->jenis_vaksin }}
                        </span>
                    </td>
                    <td class="text-center">{{ $schedule->dosis_ke }}</td>
                    <td>{{ $schedule->tanggal_vaksin->format('d-m-Y') }}</td>
                    <td class="{{ $isUrgent ? 'urgent' : ($daysUntil < 0 ? 'urgent' : 'normal') }}">
                        @if($daysUntil == 0)
                            HARI INI
                        @elseif($daysUntil == 1)
                            Besok
                        @elseif($daysUntil < 0)
                            Overdue
                        @else
                            {{ $daysUntil }} hari
                        @endif
                    </td>
                    <td>
                        <span class="status-badge {{ $schedule->status }}">
                            {{ ucfirst($schedule->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada data reminder H-7</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Total Data: {{ $schedules->count() }} jadwal<br>
        Sistem Reminder Vaksin - SIMA
    </div>
</body>
</html>
