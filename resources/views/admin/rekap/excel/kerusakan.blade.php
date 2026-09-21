<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        table { border-collapse: collapse; width: 100%; font-family: Calibri, sans-serif; font-size: 11pt; }
        th { background-color: #E11D48; color: #FFFFFF; font-weight: bold; border: 1px solid #000; text-align: center; padding: 6px; }
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
            <td colspan="7" class="title">LAPORAN REKAP FREKUENSI KERUSAKAN FASILITAS</td>
        </tr>
        <tr>
            <td colspan="7" class="subtitle">Dicetak pada: {{ date('d F Y H:i') }} | Total Kasus: {{ $totalReports }} (Menunggu: {{ $totalWaiting }}, Diproses: {{ $totalInProgress }}, Selesai: {{ $totalDone }})</td>
        </tr>
        <tr><td colspan="7"></td></tr>
        <tr>
            <td colspan="7" class="section-header">FREKUENSI KERUSAKAN PER FASILITAS</td>
        </tr>
        <tr>
            <th style="width: 40px;">No</th>
            <th>Nama Fasilitas</th>
            <th>Tipe</th>
            <th>Lokasi</th>
            <th>Total Kerusakan</th>
            <th>Status Menunggu</th>
            <th>Status Diproses</th>
            <th>Status Selesai</th>
        </tr>
        @foreach($damageByFacility as $index => $item)
        <tr>
            <td class="center">{{ $loop->iteration }}</td>
            <td>{{ $item['nama'] }}</td>
            <td class="center">{{ ucfirst($item['tipe']) }}</td>
            <td>{{ $item['lokasi'] }}</td>
            <td class="center" style="font-weight: bold;">{{ $item['total'] }}</td>
            <td class="center">{{ $item['menunggu'] }}</td>
            <td class="center">{{ $item['diproses'] }}</td>
            <td class="center">{{ $item['selesai'] }}</td>
        </tr>
        @endforeach

        <tr><td colspan="7"></td></tr>
        <tr>
            <td colspan="7" class="section-header">FREKUENSI KERUSAKAN PER LOKASI / GEDUNG</td>
        </tr>
        <tr>
            <th style="background-color: #475569; width: 40px;">No</th>
            <th colspan="2" style="background-color: #475569;">Lokasi Gedung</th>
            <th style="background-color: #475569;">Total Kerusakan</th>
            <th style="background-color: #475569;">Menunggu</th>
            <th style="background-color: #475569;">Diproses</th>
            <th style="background-color: #475569;">Selesai</th>
        </tr>
        @foreach($damageByLocation as $item)
        <tr>
            <td class="center">{{ $loop->iteration }}</td>
            <td colspan="2">{{ $item['lokasi'] }}</td>
            <td class="center" style="font-weight: bold;">{{ $item['total'] }}</td>
            <td class="center">{{ $item['menunggu'] }}</td>
            <td class="center">{{ $item['diproses'] }}</td>
            <td class="center">{{ $item['selesai'] }}</td>
        </tr>
        @endforeach

        <tr><td colspan="7"></td></tr>
        <tr>
            <td colspan="7" class="section-header">LOG DETAIL LAPORAN KERUSAKAN</td>
        </tr>
        <tr>
            <th style="background-color: #1E293B; width: 40px;">No</th>
            <th style="background-color: #1E293B;">Tanggal Lapor</th>
            <th style="background-color: #1E293B;">Fasilitas</th>
            <th style="background-color: #1E293B;">Lokasi</th>
            <th style="background-color: #1E293B;">Pelapor</th>
            <th style="background-color: #1E293B;">Deskripsi Masalah</th>
            <th style="background-color: #1E293B;">Status Penanganan</th>
        </tr>
        @foreach($reports as $rep)
        <tr>
            <td class="center">{{ $loop->iteration }}</td>
            <td class="center">{{ $rep->created_at->format('Y-m-d H:i') }}</td>
            <td>{{ $rep->facility->nama ?? '-' }}</td>
            <td>{{ $rep->facility->lokasi ?? '-' }}</td>
            <td>{{ $rep->user->name ?? '-' }}</td>
            <td>{{ $rep->deskripsi }}</td>
            <td class="center">{{ ucfirst($rep->status) }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>
