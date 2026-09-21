<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Okupansi Fasilitas</title>
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
            color: #1a365d;
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
            margin-bottom: 20px;
        }
        table.data-table th {
            background-color: #4F46E5;
            color: #fff;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            padding: 6px 5px;
            border: 1px solid #3730A3;
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
            border-left: 3px solid #4F46E5;
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
        <h1>Laporan Rekapitulasi Okupansi Fasilitas</h1>
        <p>Sistem Pengelolaan Reservasi Fasilitas Terpadu &bull; Modul Admin (FR-ADM-01)</p>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 15%;"><strong>Periode Data</strong></td>
            <td style="width: 35%;">: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }} ({{ $daysDiff }} Hari)</td>
            <td style="width: 20%;"><strong>Total Reservasi Disetujui</strong></td>
            <td style="width: 30%;">: {{ $totalApprovedReservations }} Kali Pemakaian</td>
        </tr>
        <tr>
            <td><strong>Tanggal Cetak</strong></td>
            <td>: {{ date('d F Y - H:i') }} WIB</td>
            <td><strong>Total Jam Pemakaian</strong></td>
            <td>: {{ $totalHoursAll }} Jam</td>
        </tr>
    </table>

    <div class="section-title">1. Ringkasan Okupansi Per Fasilitas</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;" class="center">No</th>
                <th>Nama Fasilitas</th>
                <th style="width: 70px;" class="center">Tipe</th>
                <th>Lokasi</th>
                <th style="width: 60px;" class="center">Kapasitas</th>
                <th style="width: 80px;" class="center">Jml Reservasi</th>
                <th style="width: 70px;" class="center">Durasi (Jam)</th>
                <th style="width: 80px;" class="center">Okupansi (%)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($occupancySummary as $index => $item)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td><strong>{{ $item['facility']->nama }}</strong></td>
                <td class="center">{{ ucfirst($item['facility']->tipe) }}</td>
                <td>{{ $item['facility']->lokasi }}</td>
                <td class="center">{{ $item['facility']->kapasitas }} Orang</td>
                <td class="center">{{ $item['total_reservations'] }}x</td>
                <td class="center">{{ $item['total_hours'] }} Jam</td>
                <td class="center"><strong>{{ $item['occupancy_rate'] }}%</strong></td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="center">Tidak ada data fasilitas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">2. Log Rincian Reservasi Disetujui</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;" class="center">No</th>
                <th style="width: 75px;" class="center">Tanggal</th>
                <th style="width: 80px;" class="center">Waktu</th>
                <th>Fasilitas</th>
                <th>Peminjam</th>
                <th>Keperluan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reservations as $index => $res)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td class="center">{{ \Carbon\Carbon::parse($res->tanggal)->format('d/m/Y') }}</td>
                <td class="center">{{ substr($res->start_time, 0, 5) }} - {{ substr($res->end_time, 0, 5) }}</td>
                <td><strong>{{ $res->facility->nama ?? '-' }}</strong></td>
                <td>{{ $res->user->name ?? '-' }}</td>
                <td>{{ $res->keperluan }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="center">Tidak ada log reservasi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <p>Mengetahui,</p>
            <p><strong>Kepala Bagian Fasilitas</strong></p>
            <br><br><br>
            <p><u>( ..................................... )</u></p>
            <p>NIP. .....................................</p>
        </div>
    </div>

</body>
</html>
