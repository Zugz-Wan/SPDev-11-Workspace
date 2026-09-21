<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        table { border-collapse: collapse; width: 100%; font-family: Calibri, sans-serif; font-size: 11pt; }
        th { background-color: #4F46E5; color: #FFFFFF; font-weight: bold; border: 1px solid #000; text-align: center; padding: 6px; }
        td { border: 1px solid #D1D5DB; padding: 5px; }
        .title { font-size: 16pt; font-weight: bold; color: #1E293B; text-align: left; }
        .subtitle { font-size: 11pt; color: #64748B; }
        .section-header { background-color: #E2E8F0; font-weight: bold; padding: 6px; }
        .center { text-align: center; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="7" class="title">LAPORAN REKAP OKUPANSI PENGGUNAAN FASILITAS</td>
        </tr>
        <tr>
            <td colspan="7" class="subtitle">Dicetak pada: {{ date('d F Y H:i') }} | Total Reservasi: {{ $totalApprovedReservations }} | Total Durasi: {{ $totalHoursAll }} Jam</td>
        </tr>
        <tr><td colspan="7"></td></tr>
        <tr>
            <th style="width: 40px;">No</th>
            <th>Nama Fasilitas</th>
            <th>Tipe</th>
            <th>Lokasi</th>
            <th>Kapasitas</th>
            <th>Jml Reservasi Disetujui</th>
            <th>Total Jam Pemakaian</th>
            <th>Estimasi Okupansi (%)</th>
        </tr>
        @foreach($occupancySummary as $index => $item)
        <tr>
            <td class="center">{{ $index + 1 }}</td>
            <td>{{ $item['facility']->nama }}</td>
            <td class="center">{{ ucfirst($item['facility']->tipe) }}</td>
            <td>{{ $item['facility']->lokasi }}</td>
            <td class="center">{{ $item['facility']->kapasitas }} Orang</td>
            <td class="center">{{ $item['total_reservations'] }}</td>
            <td class="center">{{ $item['total_hours'] }} Jam</td>
            <td class="center">{{ $item['occupancy_rate'] }}%</td>
        </tr>
        @endforeach
        <tr><td colspan="7"></td></tr>
        <tr>
            <td colspan="7" class="section-header">LOG RINCIAN RESERVASI</td>
        </tr>
        <tr>
            <th style="background-color: #334155;">No</th>
            <th style="background-color: #334155;">Tanggal</th>
            <th style="background-color: #334155;">Jam Mulai</th>
            <th style="background-color: #334155;">Jam Selesai</th>
            <th style="background-color: #334155;">Fasilitas</th>
            <th style="background-color: #334155;">Peminjam</th>
            <th style="background-color: #334155;">Keperluan</th>
        </tr>
        @foreach($reservations as $index => $res)
        <tr>
            <td class="center">{{ $index + 1 }}</td>
            <td class="center">{{ $res->tanggal }}</td>
            <td class="center">{{ substr($res->start_time, 0, 5) }}</td>
            <td class="center">{{ substr($res->end_time, 0, 5) }}</td>
            <td>{{ $res->facility->nama ?? '-' }}</td>
            <td>{{ $res->user->name ?? '-' }}</td>
            <td>{{ $res->keperluan }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>
