<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Frekuensi Kerusakan Fasilitas</title>
    <style>
        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #333;
            margin: 15px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0;
            text-transform: uppercase;
            color: #9f1239;
        }
        .header p {
            font-size: 10px;
            margin: 3px 0 0 0;
            color: #666;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .meta-table td {
            font-size: 10px;
            padding: 2px 0;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        table.data-table th {
            background-color: #E11D48;
            color: #fff;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            padding: 5px;
            border: 1px solid #BE123C;
        }
        table.data-table td {
            padding: 5px;
            border: 1px solid #cbd5e1;
            font-size: 9px;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            margin: 12px 0 6px 0;
            color: #1e293b;
            text-transform: uppercase;
            border-left: 3px solid #E11D48;
            padding-left: 6px;
        }
        .footer {
            margin-top: 25px;
            width: 100%;
        }
        .signature {
            float: right;
            width: 200px;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Rekapitulasi Frekuensi Kerusakan Fasilitas</h1>
        <p>Sistem Pengelolaan Pemeliharaan & Kerusakan Fasilitas &bull; Modul Admin (FR-ADM-02)</p>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 15%;"><strong>Periode Laporan</strong></td>
            <td style="width: 35%;">: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</td>
            <td style="width: 20%;"><strong>Total Kerusakan Dilaporkan</strong></td>
            <td style="width: 30%;">: {{ $totalReports }} Kasus</td>
        </tr>
        <tr>
            <td><strong>Tanggal Cetak</strong></td>
            <td>: {{ date('d F Y - H:i') }} WIB</td>
            <td><strong>Status Penanganan</strong></td>
            <td>: Menunggu ({{ $totalWaiting }}), Diproses ({{ $totalInProgress }}), Selesai ({{ $totalDone }})</td>
        </tr>
    </table>

    <div class="section-title">1. Frekuensi Kerusakan Per Fasilitas</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;" class="center">No</th>
                <th>Nama Fasilitas</th>
                <th style="width: 70px;" class="center">Tipe</th>
                <th>Lokasi</th>
                <th style="width: 80px;" class="center">Total Kasus</th>
                <th style="width: 60px;" class="center">Menunggu</th>
                <th style="width: 60px;" class="center">Diproses</th>
                <th style="width: 60px;" class="center">Selesai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($damageByFacility as $index => $item)
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td><strong>{{ $item['nama'] }}</strong></td>
                <td class="center">{{ ucfirst($item['tipe']) }}</td>
                <td>{{ $item['lokasi'] }}</td>
                <td class="center"><strong>{{ $item['total'] }}</strong></td>
                <td class="center">{{ $item['menunggu'] }}</td>
                <td class="center">{{ $item['diproses'] }}</td>
                <td class="center">{{ $item['selesai'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="center">Tidak ada laporan kerusakan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">2. Frekuensi Kerusakan Per Lokasi / Gedung</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;" class="center">No</th>
                <th colspan="3">Nama Lokasi / Gedung</th>
                <th style="width: 80px;" class="center">Total Kasus</th>
                <th style="width: 60px;" class="center">Menunggu</th>
                <th style="width: 60px;" class="center">Diproses</th>
                <th style="width: 60px;" class="center">Selesai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($damageByLocation as $index => $item)
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td colspan="3"><strong>{{ $item['lokasi'] }}</strong></td>
                <td class="center"><strong>{{ $item['total'] }}</strong></td>
                <td class="center">{{ $item['menunggu'] }}</td>
                <td class="center">{{ $item['diproses'] }}</td>
                <td class="center">{{ $item['selesai'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="center">Tidak ada data lokasi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">3. Log Detail Laporan Kerusakan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;" class="center">No</th>
                <th style="width: 75px;" class="center">Tgl Lapor</th>
                <th>Fasilitas</th>
                <th>Lokasi</th>
                <th>Pelapor</th>
                <th>Deskripsi Kerusakan</th>
                <th style="width: 60px;" class="center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $index => $rep)
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td class="center">{{ $rep->created_at->format('d/m/Y') }}</td>
                <td><strong>{{ $rep->facility->nama ?? '-' }}</strong></td>
                <td>{{ $rep->facility->lokasi ?? '-' }}</td>
                <td>{{ $rep->user->name ?? '-' }}</td>
                <td>{{ $rep->deskripsi }}</td>
                <td class="center"><strong>{{ ucfirst($rep->status) }}</strong></td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="center">Tidak ada log laporan kerusakan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <p>Mengetahui,</p>
            <p><strong>Kepala Tim Pemeliharaan Fasilitas</strong></p>
            <br><br><br>
            <p><u>( ..................................... )</u></p>
            <p>NIP. .....................................</p>
        </div>
    </div>

</body>
</html>
