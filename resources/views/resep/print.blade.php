@php
    // Ukuran kertas dari URL param ?p= (default: A5)
    $paperMap = [
        'A5' => ['css' => 'A5 portrait',      'label' => 'A5'],
        'A4' => ['css' => 'A4 portrait',      'label' => 'A4'],
        'L'  => ['css' => 'letter portrait',  'label' => 'Letter'],
        'LG' => ['css' => 'legal portrait',   'label' => 'Legal'],
    ];
    $pKey     = array_key_exists(request('p', 'A5'), $paperMap) ? request('p', 'A5') : 'A5';
    $pageSize = $paperMap[$pKey]['css'];
    $baseUrl  = url()->current();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Resep {{ $resep->no_resep }}</title>
<style>
    /* ── Reset ── */
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: Arial, sans-serif;
        font-size: 9pt;
        color: #111;
        background: #f0f0f0;
    }

    /* ── Screen: tengah dengan shadow ── */
    .page-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 24px 16px 40px;
        min-height: 100vh;
    }

    /* ── Toolbar (tidak ikut print) ── */
    .toolbar {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        background: #1e40af;
        color: #fff;
        padding: 10px 20px;
        border-radius: 8px;
        width: 148mm;
    }
    .toolbar span { font-size: 13px; font-weight: 600; flex: 1; }
    /* Tombol pilihan ukuran kertas di toolbar */
    .paper-btns { display: flex; gap: 4px; }
    .btn-sz {
        display: inline-flex; align-items: center;
        background: #1e3a8a; color: #93c5fd;
        border: 1px solid #3b82f6; border-radius: 5px;
        padding: 4px 10px; font-size: 11px; font-weight: 600;
        cursor: pointer; text-decoration: none; transition: background .15s;
    }
    .btn-sz:hover { background: #1e40af; color: #fff; }
    .btn-sz.active { background: #fff; color: #1e40af; border-color: #fff; }
    .btn-print {
        display: inline-flex; align-items: center; gap: 6px;
        background: #fff; color: #1e40af;
        border: none; border-radius: 6px;
        padding: 6px 14px; font-size: 12px; font-weight: 700;
        cursor: pointer; transition: background .15s;
    }
    .btn-print:hover { background: #dbeafe; }
    .btn-back {
        display: inline-flex; align-items: center; gap: 6px;
        background: transparent; color: #bfdbfe;
        border: 1px solid #3b82f6; border-radius: 6px;
        padding: 6px 12px; font-size: 12px;
        cursor: pointer; text-decoration: none; transition: background .15s;
    }
    .btn-back:hover { background: #1e3a8a; }

    /* ── Kertas resep ── */
    .resep-paper {
        width: 148mm;
        background: #fff;
        box-shadow: 0 4px 24px rgba(0,0,0,.18);
        padding: 12mm 13mm 12mm 13mm;
        position: relative;
    }

    /* ── KOP ── */
    .kop-table { width: 100%; border-collapse: collapse; }
    .kop-table td { vertical-align: middle; padding-bottom: 7pt; }
    .td-logo  { width: 68pt; }
    .td-logo img { width: 64pt; height: auto; display: block; }
    .td-name  { padding-left: 6pt; }
    .clinic-name {
        font-size: 16pt; font-weight: bold; color: #111;
        white-space: nowrap; letter-spacing: .2pt;
    }
    .td-addr {
        text-align: right; font-size: 7.6pt;
        line-height: 1.25; color: #333; width: 45%;
    }
    .kop-line { border-top: 2pt solid #111; }

    /* ── JADWAL ── */
    .jadwal-table { width: 100%; border-collapse: collapse; }
    .jadwal-table td { padding: 5pt 0; vertical-align: top; }
    .td-jadwal { width: 60%; font-size: 7.8pt; line-height: 1; }
    .td-jadwal-right {
        text-align: right; font-size: 8pt;
        line-height: 1.7; white-space: nowrap;
    }
    .jadwal-inner { border-collapse: collapse; margin-top: 1pt; }
    .jadwal-inner td { font-size: 7.8pt; line-height: 1.1; padding: 1pt 4pt 1pt 0; white-space: nowrap; }
    .jadwal-line { border-top: 1.5pt solid #111; }

    /* ── Judul ── */
    .judul {
        text-align: center; font-size: 10.5pt; font-weight: bold;
        text-decoration: underline; letter-spacing: 2.5pt;
        text-transform: uppercase; padding: 8pt 0 7pt;
    }

    /* ── R/ Tanggal ── */
    .r-table { width: 100%; border-collapse: collapse; margin-bottom: 9pt; }
    .r-table td { vertical-align: top; }
    .td-r { width: 28pt; font-size: 12pt; font-weight: bold; }
    .td-tgl { text-align: right; font-size: 8.5pt; padding-top: 1.5pt; }

    /* ── Obat ── */
    .obat-area { padding: 0 0 0 18pt; }
    .obat-item { margin-bottom: 10pt; }
    .obat-nama { font-size: 10pt; font-weight: bold; }
    .obat-dosis { font-size: 8.8pt; color: #333; padding-left: 8pt; margin-top: 1.5pt; }
    .obat-sig   { font-size: 8.5pt; font-style: italic; color: #555; padding-left: 8pt; }
    .obat-ket   { font-size: 8.5pt; color: #444; padding-left: 8pt; margin-top: 1.5pt; }

    /* ── Footer ── */
    .footer-line { border-top: 1.5pt solid #111; margin-top: 12pt; padding-top: 6pt; }
    .footer-table { width: 100%; border-collapse: collapse; }
    .footer-table td { font-size: 8.5pt; line-height: 1.85; vertical-align: top; padding: 0; }
    .f-lbl { width: 44pt; }
    .f-sep { width: 10pt; }
    .note {
        font-size: 7.5pt; font-style: italic; color: #666;
        text-align: center; margin-top: 6pt;
        border-top: 1pt dashed #bbb; padding-top: 5pt;
    }

    /* ══ PRINT STYLES ══ */
    @media print {
        body { background: #fff; }
        .page-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 0;
            background: #fff;
            min-height: unset;
        }
        .toolbar { display: none !important; }
        .resep-paper {
            width: 148mm;
            max-width: 148mm;
            margin: 0 auto;
            box-shadow: none;
            padding: 0;
            overflow: hidden;
        }
        /* Paksa teks nowrap bisa wrap — cegah overflow kanan */
        .clinic-name,
        .td-jadwal-right,
        .jadwal-inner td,
        .td-addr {
            white-space: normal !important;
            word-break: break-word;
        }
        table { max-width: 100%; }
    }
</style>
{{-- @page size: ditulis sebagai string PHP agar IDE tidak parse sebagai CSS --}}
{!! '<style>@page{size:' . $pageSize . ';margin:12mm 13mm;}</style>' !!}
</head>
<body>

<div class="page-wrapper">

    {{-- Toolbar (hanya tampil di layar) --}}
    <div class="toolbar">
        <span>&#128196; Resep {{ $resep->no_resep }} &mdash; {{ $resep->nama_pasien }}</span>
        <div class="paper-btns">
            @foreach($paperMap as $key => $info)
            <a href="{{ $baseUrl }}?p={{ $key }}"
               class="btn-sz {{ $pKey === $key ? 'active' : '' }}">{{ $info['label'] }}</a>
            @endforeach
        </div>
        <a href="{{ route('resep.show', $resep->id) }}" class="btn-back">&#8592; Kembali</a>
        <button class="btn-print" onclick="window.print()">&#128438; Cetak / Simpan PDF</button>
    </div>

    {{-- Kertas Resep --}}
    <div class="resep-paper">

        {{-- KOP --}}
        <table class="kop-table">
        <tr>
            <td class="td-logo">
                <img src="{{ asset('images/logosima.png') }}" alt="SIMA Lab">
            </td>
            <td class="td-name">
                <div class="clinic-name">KLINIK UTAMA SIMA</div>
            </td>
            <td class="td-addr">
                Jl. Tangkuban Prahu 14 Malang<br>
                Telp. 0341-321253,321254,326060<br>
                Fax. 323846<br>
                Jl. Ciliwung 51 Malang<br>
                Telp. 0341-486630<br>
                Fax. 0341-486627
            </td>
        </tr>
        <tr><td colspan="3" class="kop-line" style="padding:0;height:0;"></td></tr>
        </table>

        {{-- JADWAL --}}
        <table class="jadwal-table">
        <tr>
            <td class="td-jadwal">
                <strong>BUKA SETIAP HARI KERJA</strong>
                <table class="jadwal-inner">
                    <tr>
                        <td style="width:80pt;">Senin s.d Jumat</td>
                        <td>: 06.00 - 21.00</td>
                    </tr>
                    <tr>
                        <td>Sabtu</td>
                        <td>: 06.00 - 20.00</td>
                    </tr>
                    <tr>
                        <td>Minggu/Hari Besar</td>
                        <td>: TUTUP</td>
                    </tr>
                </table>
            </td>
            <td class="td-jadwal-right">
                No. Resep &nbsp;: <strong>{{ $resep->no_resep }}</strong><br>
                Dokter &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <strong>dr. {{ $resep->nama_dokter }}</strong>
            </td>
        </tr>
        <tr><td colspan="2" class="jadwal-line" style="padding:0;height:0;"></td></tr>
        </table>

        {{-- JUDUL --}}
        <div class="judul">Resep Dokter</div>

        {{-- R/ & TANGGAL --}}
        <table class="r-table">
        <tr>
            <td class="td-r">R/</td>
            <td class="td-tgl"></td>
        </tr>
        </table>

        {{-- DAFTAR OBAT --}}
        <div class="obat-area">
        @foreach($resep->obat as $i => $obat)
        <div class="obat-item">
            <div class="obat-nama">
                {{ $i + 1 }}. {{ $obat->nama_obat }}@if($obat->kekuatan) <span style="font-size:9pt; font-weight:normal;">{{ $obat->kekuatan }}{{ $obat->satuan_kekuatan !== '-' ? ' '.$obat->satuan_kekuatan : '' }}</span>@endif
            </div>
            <div class="obat-dosis">Dosis &nbsp;&nbsp;&nbsp;: {{ $obat->dosis }}</div>
            @if($obat->jumlah > 0 || $obat->satuan !== '-')
            <div class="obat-dosis">Jumlah &nbsp;: {{ $obat->jumlah > 0 ? $obat->jumlah : '' }} {{ $obat->satuan !== '-' ? $obat->satuan : '' }}</div>
            @endif
            <div class="obat-sig">
                Sig. {{ $obat->waktu_minum === 'Sesuai Dosis' ? 'Sesuai dosis' : $obat->waktu_minum }}
                @if($obat->makan !== '-') &mdash; {{ $obat->makan }} @endif
            </div>
            @if(!empty($obat->keterangan))
            <div class="obat-ket">Ket: {{ $obat->keterangan }}</div>
            @endif
        </div>
        @endforeach
        </div>

        {{-- TANDA TANGAN DOKTER --}}
        <table style="width:100%; border-collapse:collapse; margin-top:12pt;">
        <tr>
            <td style="width:58%; vertical-align:top;"></td>
            <td style="text-align:center; vertical-align:top; font-size:8pt;">
                Malang, {{ \Carbon\Carbon::parse($resep->tanggal_resep)->locale('id')->translatedFormat('d F Y') }}
                <div style="height:48pt;"></div>
                <div style="border-top:1pt solid #111; padding-top:3pt; font-size:8.5pt; font-weight:bold;">
                    dr. {{ $resep->nama_dokter }}
                </div>
            </td>
        </tr>
        </table>

        {{-- FOOTER PASIEN --}}
        <div class="footer-line">
            <table class="footer-table">
            <tr>
                <td class="f-lbl">Pro</td>
                <td class="f-sep">:</td>
                <td>{{ $resep->nama_pasien }}</td>
            </tr>
            <tr>
                <td class="f-lbl">Umur</td>
                <td class="f-sep">:</td>
                <td>{{ $resep->umur }} tahun</td>
            </tr>
            <tr>
                <td class="f-lbl">Alamat</td>
                <td class="f-sep">:</td>
                <td>{{ $resep->alamat }}</td>
            </tr>
            </table>
            <div class="note">Dilarang mengganti obat tanpa seijin dokter</div>
        </div>

    </div>{{-- end .resep-paper --}}
</div>{{-- end .page-wrapper --}}

<input type="hidden" id="autoPrintFlag" value="{{ session('auto_print') ? '1' : '0' }}">

<script>
    // Auto print saat halaman dimuat (dari redirect store dengan auto_print)
    if (document.getElementById('autoPrintFlag').value === '1') {
        window.addEventListener('load', function() {
            setTimeout(function() { window.print(); }, 400);
        });
    }
</script>

</body>
</html>
