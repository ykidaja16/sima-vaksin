<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Protokol ABI {{ $protokol->no_protokol }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, sans-serif; font-size: 9pt; color: #111; background: #f0f0f0; }

    .page-wrapper {
        display: flex; flex-direction: column; align-items: center;
        padding: 24px 16px 40px; min-height: 100vh;
    }

    .toolbar {
        display: flex; align-items: center; gap: 10px; margin-bottom: 20px;
        background: #1e40af; color: #fff; padding: 10px 20px; border-radius: 8px; width: 210mm;
    }
    .toolbar span { font-size: 13px; font-weight: 600; flex: 1; }
    .btn-print {
        display: inline-flex; align-items: center; gap: 6px;
        background: #fff; color: #1e40af; border: none; border-radius: 6px;
        padding: 6px 14px; font-size: 12px; font-weight: 700; cursor: pointer;
    }
    .btn-print:hover { background: #dbeafe; }
    .btn-back {
        display: inline-flex; align-items: center; gap: 6px;
        background: transparent; color: #bfdbfe; border: 1px solid #3b82f6;
        border-radius: 6px; padding: 6px 12px; font-size: 12px;
        cursor: pointer; text-decoration: none;
    }
    .btn-back:hover { background: #1e3a8a; }

    .abi-paper {
        width: 210mm; background: #fff;
        box-shadow: 0 4px 24px rgba(0,0,0,.18);
        padding: 14mm 16mm 14mm 16mm;
    }

    .kop-table { width: 100%; border-collapse: collapse; }
    .kop-table td { vertical-align: middle; padding-bottom: 7pt; }
    .td-logo { width: 68pt; border-bottom: 2pt solid #111; }
    .td-logo img { width: 64pt; height: auto; display: block; }
    .td-name { padding-left: 6pt; font-size: 16pt; font-weight: bold; white-space: nowrap; border-bottom: 2pt solid #111; }
    .td-addr { text-align: right; font-size: 7.6pt; line-height: 1.65; color: #333; width: 38%; border-bottom: 2pt solid #111; }

    .judul-abi {
        text-align: center; font-size: 14pt; font-weight: bold;
        letter-spacing: 4pt; padding: 10pt 0 6pt;
    }

    /* Diagram ABI */
    .diagram-wrap {
        position: relative;
        width: 100%;
        height: 380pt;
        margin: 4pt 0 8pt;
    }
    .diagram-body {
        position: absolute;
        left: 50%; top: 50%;
        transform: translate(-50%, -50%);
        width: 160pt;
    }
    .body-img { display: block; width: 100%; height: auto; }

    .bp-block {
        position: absolute; text-align: center;
        line-height: 1.3; font-size: 8pt;
        min-width: 70pt;
    }
    .bp-block .lbl-bp  { font-size: 9pt; font-weight: bold; margin-bottom: 1pt; }
    .bp-block .lbl-unit { font-size: 7pt; color: #666; margin-bottom: 3pt; }
    .bp-block .bp-val  { font-size: 9.5pt; font-weight: bold; white-space: nowrap; line-height: 1.4; }

    .bp-right-arm   { top: 30%;  left: 2%; }
    .bp-left-arm    { top: 30%;  right: 2%; }
    .bp-right-ankle { bottom: 10%; left: 2%; }
    .bp-left-ankle  { bottom: 10%; right: 2%; }

    /* Rumus */
    .rumus-section { font-size: 8pt; line-height: 1.7; margin: 8pt 0; }
    .rumus-title { font-weight: bold; margin-bottom: 4pt; }
    .rumus-formula { margin: 4pt 0; }
    .rumus-calc { margin: 3pt 0 3pt 12pt; }

    /* Tabel interpretasi */
    .table-interpretasi { width: 80%; border-collapse: collapse; margin: 10pt auto; font-size: 7.5pt; }
    .table-interpretasi th, .table-interpretasi td {
        border: 1pt solid #333; padding: 3pt 6pt; text-align: left;
    }
    .table-interpretasi th { background: #f0f0f0; font-weight: bold; }
    .table-interpretasi th:nth-child(1), .table-interpretasi td:nth-child(1) { width: 25%; }
    .table-interpretasi th:nth-child(2), .table-interpretasi td:nth-child(2) { width: 75%; }
    .table-caption { font-size: 7.5pt; font-weight: bold; margin-bottom: 3pt; }

    /* Footer pasien (sama resep) */
    .footer-line { border-top: 1.5pt solid #111; margin-top: 12pt; padding-top: 6pt; }
    .footer-table { width: 100%; border-collapse: collapse; }
    .footer-table td { font-size: 8.5pt; line-height: 1.85; vertical-align: top; padding: 0; }
    .f-lbl { width: 44pt; }
    .f-sep { width: 10pt; }

    @media print {
        @page { size: 210mm auto; margin: 0; }
        body { background: #fff; }
        .page-wrapper { padding: 0; background: #fff; }
        .toolbar { display: none !important; }
        .abi-paper { width: 210mm; box-shadow: none; padding: 14mm 16mm; }
    }
</style>
</head>
<body>
<div class="page-wrapper">
    <div class="toolbar">
        <span>&#128196; Protokol ABI {{ $protokol->no_protokol }} &mdash; {{ $protokol->nama_pasien }}</span>
        <a href="{{ route('protokol-abi.show', $protokol->id) }}" class="btn-back">&#8592; Kembali</a>
        <button class="btn-print" onclick="window.print()">&#128438; Cetak / Simpan PDF</button>
    </div>

    <div class="abi-paper">
        {{-- KOP --}}
        <table class="kop-table">
            <tr>
                <td class="td-logo">
                    <img src="{{ asset('images/logosima.png') }}" alt="SIMA Lab">
                </td>
                <td class="td-name">KLINIK UTAMA SIMA</td>
                <td class="td-addr">
                    Jl. Tangkuban Prahu 14 Malang<br>
                    Telp. 0341-321253,321254,326060<br>
                    Fax. 323846<br>
                    Jl. Ciliwung 51 Malang<br>
                    Telp. 0341-486630<br>
                    Fax. 0341-486627
                </td>
            </tr>
        </table>

        <div class="judul-abi">ABI</div>

        {{-- Diagram tubuh + nilai TD --}}
        <div class="diagram-wrap">
            {{-- Right Arm --}}
            <div class="bp-block bp-right-arm">
                <div class="lbl-bp">BP</div>
                <div class="lbl-unit">mmHg</div>
                <div class="bp-val">
                    <span style="color:#e53935;">{{ $protokol->right_arm_sistolik }}</span>
                    / {{ $protokol->right_arm_diastolik }} ({{ $protokol->right_arm_mean }})
                </div>
            </div>
            {{-- Left Arm --}}
            <div class="bp-block bp-left-arm">
                <div class="lbl-bp">BP</div>
                <div class="lbl-unit">mmHg</div>
                <div class="bp-val">
                    {{ $protokol->left_arm_sistolik }} / {{ $protokol->left_arm_diastolik }} ({{ $protokol->left_arm_mean }})
                </div>
            </div>
            {{-- Right Ankle --}}
            <div class="bp-block bp-right-ankle">
                <div class="lbl-bp">BP</div>
                <div class="lbl-unit">mmHg</div>
                <div class="bp-val">
                    <span style="color:#2e7d32;">{{ $protokol->right_ankle_sistolik }}</span>
                    / {{ $protokol->right_ankle_diastolik }} ({{ $protokol->right_ankle_mean }})
                </div>
            </div>
            {{-- Left Ankle --}}
            <div class="bp-block bp-left-ankle">
                <div class="lbl-bp">BP</div>
                <div class="lbl-unit">mmHg</div>
                <div class="bp-val">
                    <span style="color:#2e7d32;">{{ $protokol->left_ankle_sistolik }}</span>
                    / {{ $protokol->left_ankle_diastolik }} ({{ $protokol->left_ankle_mean }})
                </div>
            </div>

            {{-- Gambar tubuh --}}
            <div class="diagram-body">
                <img src="{{ asset('images/body_silhouette.png') }}" class="body-img" alt="Body">
            </div>
        </div>

        {{-- Rumus & Perhitungan --}}
        <div class="rumus-section">
            <div class="rumus-title">Ankle-Brachial Index (ABI)</div>
            <div class="rumus-formula">
                ABI = Systolic Ankle Pressure / Highest Systolic Brachial Pressure
            </div>
            <div class="rumus-calc">
                Highest Systolic Brachial Pressure : <strong>{{ $protokol->highest_brachial_sistolik }} mmHg</strong>
            </div>
            <div class="rumus-calc">
                ABI<sub>left</sub> = {{ $protokol->abi_left_pembilang }} mmHg / {{ $protokol->abi_left_penyebut }} mmHg
                = <strong>{{ number_format($protokol->abi_left_hasil, 2) }}</strong>
            </div>
            <div class="rumus-calc">
                ABI<sub>right</sub> = {{ $protokol->abi_right_pembilang }} mmHg / {{ $protokol->abi_right_penyebut }} mmHg
                = <strong>{{ number_format($protokol->abi_right_hasil, 2) }}</strong>
            </div>
        </div>

        {{-- Table 2 - Acuan/Informasi --}}
        <div class="table-caption">Table 2. Interpretation of the ankle brachial index</div>
        <table class="table-interpretasi">
            <thead>
                <tr>
                    <th style="width:30%">Value</th>
                    <th>Interpretation</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>&gt; 1.30</td><td>Non-compressible</td></tr>
                <tr><td>1.00 - 1.29</td><td>Normal</td></tr>
                <tr><td>0.91 - 0.99</td><td>Borderline (equivocal)</td></tr>
                <tr><td>0.41 - 0.90</td><td>Mild-to-moderate peripheral arterial disease</td></tr>
                <tr><td>0.00 - 0.40</td><td>Severe peripheral arterial disease</td></tr>
            </tbody>
        </table>

        {{-- TANDA TANGAN DOKTER (format sama Resep) --}}
        <table style="width:100%; border-collapse:collapse; margin-top:12pt;">
        <tr>
            <td style="width:58%; vertical-align:top;"></td>
            <td style="text-align:center; vertical-align:top; font-size:8pt;">
                Malang, {{ \Carbon\Carbon::parse($protokol->tanggal_pemeriksaan)->locale('id')->translatedFormat('d F Y') }}
                <div style="height:48pt;"></div>
                <div style="border-top:1pt solid #111; padding-top:3pt; font-size:8.5pt; font-weight:bold;">
                    dr. {{ $protokol->nama_dokter }}
                </div>
            </td>
        </tr>
        </table>

        {{-- FOOTER PASIEN (format sama Resep) --}}
        <div class="footer-line">
            <table class="footer-table">
            <tr>
                <td class="f-lbl">Pro</td>
                <td class="f-sep">:</td>
                <td>{{ $protokol->nama_pasien }}</td>
            </tr>
            <tr>
                <td class="f-lbl">Umur</td>
                <td class="f-sep">:</td>
                <td>{{ $protokol->umur }} tahun</td>
            </tr>
            <tr>
                <td class="f-lbl">Alamat</td>
                <td class="f-sep">:</td>
                <td>{{ $protokol->alamat }}</td>
            </tr>
            </table>
        </div>
    </div>
</div>

<script>
    @if(session('auto_print'))
        window.addEventListener('load', function() {
            setTimeout(function() { window.print(); }, 400);
        });
    @endif
</script>
</body>
</html>
