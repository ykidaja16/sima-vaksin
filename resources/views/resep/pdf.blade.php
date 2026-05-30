<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body {
    font-family: Arial, sans-serif;
    font-size: 9pt;
    color: #111;
    background: #fff;
    padding: 16pt 20pt 14pt 20pt;
}
</style>
</head>
<body>

{{-- ════════════════ KOP SURAT ════════════════ --}}
<table style="width:100%; border-collapse:collapse;">
<tr>
    <td style="width:68pt; vertical-align:middle; padding-bottom:8pt;
               border-bottom:2pt solid #111;">
        <img src="{{ public_path('images/logosima.png') }}"
             style="width:64pt; height:auto; display:block;" alt="SIMA Lab">
    </td>
    <td style="vertical-align:middle; padding-left:6pt; padding-bottom:8pt;
               border-bottom:2pt solid #111;">
        <span style="font-size:16pt; font-weight:bold; color:#111; white-space:nowrap;">KLINIK UTAMA SIMA</span>
    </td>
    <td style="vertical-align:middle; text-align:right; padding-bottom:8pt;
               font-size:7.8pt; line-height:1.65; color:#222;
               border-bottom:2pt solid #111; width:38%;">
        Jl. Tangkuban Prahu 14 Malang<br>
        Telp. 0341-321253,321254,326060<br>
        Fax. 323846<br>
        Jl. Ciliwung 51 Malang<br>
        Telp. 0341-486630<br>
        Fax. 0341-486627
    </td>
</tr>
</table>

{{-- ════════════════ JADWAL + INFO RESEP ════════════════ --}}
<table style="width:100%; border-collapse:collapse; margin-top:0;">
<colgroup>
    <col style="width:61%;">
    <col style="width:39%;">
</colgroup>
<tr>
    <td style="vertical-align:top; padding:5pt 0;
               border-bottom:1.5pt solid #111;">
        <strong style="font-size:8.2pt;">BUKA SETIAP HARI KERJA</strong>
        <table style="border-collapse:collapse; margin-top:2pt;">
            <tr>
                <td style="font-size:8pt; line-height:1.75; white-space:nowrap;
                           padding-right:4pt; width:88pt;">Senin s.d Jumat</td>
                <td style="font-size:8pt; line-height:1.75; white-space:nowrap;">: 06.00 - 21.00</td>
            </tr>
            <tr>
                <td style="font-size:8pt; line-height:1.75; white-space:nowrap;
                           padding-right:4pt;">Sabtu</td>
                <td style="font-size:8pt; line-height:1.75; white-space:nowrap;">: 06.00 - 20.00</td>
            </tr>
            <tr>
                <td style="font-size:8pt; line-height:1.75; white-space:nowrap;
                           padding-right:4pt;">Minggu/Hari Besar</td>
                <td style="font-size:8pt; line-height:1.75; white-space:nowrap;">: TUTUP</td>
            </tr>
        </table>
    </td>
    <td style="vertical-align:top; text-align:right; padding:5pt 0;
               font-size:8.2pt; line-height:1.9;
               border-bottom:1.5pt solid #111;">
        No. Resep &nbsp;: <strong>{{ $resep->no_resep }}</strong><br>
        Dokter &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <strong>dr. {{ $resep->nama_dokter }}</strong>
    </td>
</tr>
</table>

{{-- ════════════════ JUDUL RESEP DOKTER ════════════════ --}}
<div style="text-align:center; font-size:10.5pt; font-weight:bold;
            text-decoration:underline; letter-spacing:2.5pt;
            text-transform:uppercase; padding:9pt 0 8pt 0;">
    Resep Dokter
</div>

{{-- ════════════════ R/ & TANGGAL ════════════════ --}}
<table style="width:100%; border-collapse:collapse; margin-bottom:9pt;">
<tr>
    <td style="font-size:12pt; font-weight:bold; vertical-align:top;
               width:28pt; white-space:nowrap;">R/</td>
    <td style="text-align:right; font-size:8.5pt; vertical-align:top;
               padding-top:1.5pt;">
        Malang, {{ \Carbon\Carbon::parse($resep->tanggal_resep)->locale('id')->translatedFormat('d F Y') }}
    </td>
</tr>
</table>

{{-- ════════════════ DAFTAR OBAT ════════════════ --}}
<div style="padding:0 0 0 20pt; min-height:140pt;">
@foreach($resep->obat as $i => $obat)
<div style="margin-bottom:11pt;">
    <div style="font-size:10pt; font-weight:bold;">
        {{ $i + 1 }}. {{ $obat->nama_obat }}
    </div>
    <div style="font-size:8.8pt; color:#333; padding-left:8pt; margin-top:1.5pt;">
        Dosis &nbsp;&nbsp;&nbsp;: {{ $obat->dosis }}
    </div>
    <div style="font-size:8.5pt; font-style:italic; color:#555; padding-left:8pt;">
        Sig. {{ $obat->waktu_minum === 'Sesuai Dosis' ? 'Sesuai dosis' : $obat->waktu_minum }}
        @if($obat->makan !== '-')
            &mdash; {{ $obat->makan }}
        @endif
    </div>
</div>
@endforeach
</div>

{{-- ════════════════ FOOTER PASIEN ════════════════ --}}
<table style="width:100%; border-collapse:collapse; margin-top:12pt;">
<tr>
    <td colspan="3" style="border-top:1.5pt solid #111; padding:0; height:0;"></td>
</tr>
<tr>
    <td style="width:44pt; font-size:8.5pt; line-height:1.85; padding-top:6pt;
               vertical-align:top;">Pro</td>
    <td style="width:10pt; font-size:8.5pt; line-height:1.85; padding-top:6pt;
               vertical-align:top;">:</td>
    <td style="font-size:8.5pt; line-height:1.85; padding-top:6pt;
               vertical-align:top;">{{ $resep->nama_pasien }}</td>
</tr>
<tr>
    <td style="font-size:8.5pt; line-height:1.85; vertical-align:top;">Umur</td>
    <td style="font-size:8.5pt; line-height:1.85; vertical-align:top;">:</td>
    <td style="font-size:8.5pt; line-height:1.85; vertical-align:top;">{{ $resep->umur }} tahun</td>
</tr>
<tr>
    <td style="font-size:8.5pt; line-height:1.85; vertical-align:top;">Alamat</td>
    <td style="font-size:8.5pt; line-height:1.85; vertical-align:top;">:</td>
    <td style="font-size:8.5pt; line-height:1.85; vertical-align:top;">{{ $resep->alamat }}</td>
</tr>
<tr>
    <td colspan="3" style="border-top:1pt dashed #bbb; padding-top:5pt;
                            font-size:7.5pt; font-style:italic; color:#666;
                            text-align:center;">
        Dilarang mengganti obat tanpa seijin dokter
    </td>
</tr>
</table>

</body>
</html>
