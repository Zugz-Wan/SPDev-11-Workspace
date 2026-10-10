<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Rekapitulasi Fasilitas - FasilitasKita</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css'])

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: #ffffff !important;
                color: #0f172a !important;
                font-size: 11pt;
                line-height: 1.3;
            }
            .page-break-avoid {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            .print-table th, .print-table td {
                border: 1px solid #cbd5e1 !important;
                padding: 6px 8px !important;
            }
            @page {
                size: A4 landscape;
                margin: 12mm 15mm;
            }
        }

        .print-table th {
            background-color: #f1f5f9 !important;
            font-weight: 700;
            color: #1e293b;
        }

        .print-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased p-4 sm:p-8">

    {{-- Floating Print Actions (Hidden on print) --}}
    <div class="no-print max-w-6xl mx-auto mb-6 bg-white p-4 rounded-2xl border border-slate-200 shadow-md flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                🖨️
            </div>
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Mode Cetak / Pratinjau Laporan PDF</h3>
                <p class="text-xs text-slate-500">Gunakan opsi "Save as PDF" di menu cetak browser untuk menyimpan file PDF.</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.rekap.index', request()->query()) }}"
               class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-100 border border-slate-300 transition-colors">
                ← Kembali ke Halaman Rekap
            </a>
            <button onclick="window.print()"
                    class="px-5 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow transition-colors flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    {{-- Printable Document Paper --}}
    <div class="max-w-6xl mx-auto bg-white p-6 sm:p-10 rounded-3xl shadow-sm border border-slate-200 print:border-none print:shadow-none print:p-0 space-y-6">

        {{-- Official Header / Kop Laporan --}}
        <div class="border-b-2 border-slate-900 pb-4">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-slate-900 text-white flex items-center justify-center font-extrabold text-xl">
                        FK
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-slate-900 tracking-tight uppercase">FASILITASKITA</h1>
                        <p class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Sistem Manajemen Fasilitas, Okupansi & Pelaporan Kerusakan</p>
                    </div>
                </div>
                <div class="text-right text-xs text-slate-500">
                    <div class="font-bold text-slate-900">DOKUMEN RESMI REKAPITULASI</div>
                    <div>Dicetak: {{ $printedAt->translatedFormat('d F Y, H:i') }} WIB</div>
                    <div>Oleh: {{ $printedBy->name }} ({{ ucfirst($printedBy->role) }})</div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-300 text-center">
                <h2 class="text-base font-bold text-slate-900 uppercase tracking-wide">
                    LAPORAN REKAPITULASI PENGGUNAAN & KERUSAKAN FASILITAS
                </h2>
                <p class="text-xs text-slate-600 font-medium mt-0.5">
                    Periode Laporan: <strong class="text-slate-900">{{ $periodeLabel }}</strong>
                </p>
            </div>
        </div>

        {{-- Executive Summary KPI --}}
        <div class="grid grid-cols-4 gap-3 bg-slate-50 p-3.5 rounded-2xl border border-slate-200 text-xs">
            <div>
                <span class="text-slate-500 block">Total Fasilitas:</span>
                <span class="text-base font-bold text-slate-900">{{ $summary['total_facilities'] }} Unit</span>
            </div>
            <div>
                <span class="text-slate-500 block">Reservasi Disetujui:</span>
                <span class="text-base font-bold text-emerald-700">{{ $summary['total_approved_reservations'] }} Reservasi</span>
            </div>
            <div>
                <span class="text-slate-500 block">Total Jam Terpakai:</span>
                <span class="text-base font-bold text-indigo-700">{{ $summary['total_hours_used'] }} Jam</span>
            </div>
            <div>
                <span class="text-slate-500 block">Laporan Kerusakan:</span>
                <span class="text-base font-bold text-rose-700">{{ $summary['total_reports'] }} ({{ $summary['total_reports_resolved'] }} Selesai)</span>
            </div>
        </div>

        {{-- TABEL 1: REKAP OKUPANSI FASILITAS --}}
        <div class="page-break-avoid space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 border-l-4 border-indigo-600 pl-2">
                1. Rekapitulasi Okupansi per Fasilitas (Reservasi Disetujui & Jam Terpakai)
            </h3>
            <table class="w-full text-[10.5px] print-table border-collapse border border-slate-300 text-left">
                <thead>
                    <tr>
                        <th class="w-8 text-center">No</th>
                        <th>Nama Fasilitas</th>
                        <th>Tipe</th>
                        <th>Lokasi</th>
                        <th class="text-center">Kapasitas</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Jml Disetujui</th>
                        <th class="text-right">Total Jam</th>
                        <th class="text-right">Rata-rata</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($occupancyData as $index => $row)
                        <tr>
                            <td class="text-center text-slate-500">{{ $index + 1 }}</td>
                            <td class="font-bold text-slate-900">{{ $row['facility']->nama }}</td>
                            <td class="capitalize">{{ $row['facility']->tipe }}</td>
                            <td>{{ $row['facility']->lokasi }}</td>
                            <td class="text-center">{{ $row['facility']->kapasitas }} org</td>
                            <td class="text-center font-medium">{{ $row['facility']->status_label }}</td>
                            <td class="text-center font-bold text-emerald-800">{{ $row['approved_count'] }}</td>
                            <td class="text-right font-bold text-indigo-900">{{ $row['total_hours'] }} Jam</td>
                            <td class="text-right">{{ $row['avg_hours'] > 0 ? $row['avg_hours'].' Jam' : '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-slate-500 py-3">Tidak ada data fasilitas.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="font-bold bg-slate-100">
                        <td colspan="6" class="text-right uppercase">Total:</td>
                        <td class="text-center font-extrabold text-emerald-900">{{ $summary['total_approved_reservations'] }}</td>
                        <td class="text-right font-extrabold text-indigo-900">{{ $summary['total_hours_used'] }} Jam</td>
                        <td class="text-right text-slate-700">
                            @php
                                $overallAvg = $summary['total_approved_reservations'] > 0
                                    ? round($summary['total_hours_used'] / $summary['total_approved_reservations'], 2)
                                    : 0;
                            @endphp
                            {{ $overallAvg > 0 ? $overallAvg.' Jam' : '—' }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- TABEL 2: FREKUENSI KERUSAKAN PER FASILITAS --}}
        <div class="page-break-avoid space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 border-l-4 border-rose-600 pl-2">
                2. Rekapitulasi Frekuensi Kerusakan per Fasilitas
            </h3>
            <table class="w-full text-[10.5px] print-table border-collapse border border-slate-300 text-left">
                <thead>
                    <tr>
                        <th class="w-8 text-center">No</th>
                        <th>Nama Fasilitas</th>
                        <th>Tipe</th>
                        <th>Lokasi</th>
                        <th class="text-center">Total Laporan</th>
                        <th class="text-center">Baru</th>
                        <th class="text-center">Diproses</th>
                        <th class="text-center">Selesai</th>
                        <th class="text-center">Ditolak</th>
                        <th class="text-center">% Selesai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($damageFacilityData as $index => $row)
                        <tr>
                            <td class="text-center text-slate-500">{{ $index + 1 }}</td>
                            <td class="font-bold text-slate-900">{{ $row['facility']->nama }}</td>
                            <td class="capitalize">{{ $row['facility']->tipe }}</td>
                            <td>{{ $row['facility']->lokasi }}</td>
                            <td class="text-center font-bold text-rose-800">{{ $row['total_reports'] }}</td>
                            <td class="text-center">{{ $row['baru_count'] }}</td>
                            <td class="text-center">{{ $row['diproses_count'] }}</td>
                            <td class="text-center font-semibold text-emerald-800">{{ $row['selesai_count'] }}</td>
                            <td class="text-center text-slate-500">{{ $row['ditolak_count'] }}</td>
                            <td class="text-center font-bold">{{ $row['total_reports'] > 0 ? $row['resolution_rate'].'%' : '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-slate-500 py-3">Tidak ada data kerusakan.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="font-bold bg-slate-100">
                        <td colspan="4" class="text-right uppercase">Total:</td>
                        <td class="text-center font-extrabold text-rose-900">{{ $summary['total_reports'] }}</td>
                        <td class="text-center">{{ $damageFacilityData->sum('baru_count') }}</td>
                        <td class="text-center">{{ $damageFacilityData->sum('diproses_count') }}</td>
                        <td class="text-center font-extrabold text-emerald-900">{{ $damageFacilityData->sum('selesai_count') }}</td>
                        <td class="text-center text-slate-500">{{ $damageFacilityData->sum('ditolak_count') }}</td>
                        <td class="text-center font-extrabold">{{ $summary['overall_resolution_rate'] }}%</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- TABEL 3: FREKUENSI KERUSAKAN PER LOKASI --}}
        <div class="page-break-avoid space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 border-l-4 border-amber-600 pl-2">
                3. Rekapitulasi Frekuensi Kerusakan per Lokasi
            </h3>
            <table class="w-full text-[10.5px] print-table border-collapse border border-slate-300 text-left">
                <thead>
                    <tr>
                        <th class="w-8 text-center">No</th>
                        <th>Lokasi / Gedung</th>
                        <th class="text-center">Jml Fasilitas</th>
                        <th>Fasilitas di Lokasi</th>
                        <th class="text-center">Total Laporan</th>
                        <th class="text-center">Baru</th>
                        <th class="text-center">Diproses</th>
                        <th class="text-center">Selesai</th>
                        <th class="text-center">Ditolak</th>
                        <th class="text-center">Belum Selesai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($damageLocationData as $index => $row)
                        <tr>
                            <td class="text-center text-slate-500">{{ $index + 1 }}</td>
                            <td class="font-bold text-slate-900">{{ $row['lokasi'] }}</td>
                            <td class="text-center font-semibold">{{ $row['facilities_count'] }}</td>
                            <td class="text-slate-600 text-[10px]">{{ $row['facility_names'] ?: '—' }}</td>
                            <td class="text-center font-bold text-rose-800">{{ $row['total_reports'] }}</td>
                            <td class="text-center">{{ $row['baru_count'] }}</td>
                            <td class="text-center">{{ $row['diproses_count'] }}</td>
                            <td class="text-center font-semibold text-emerald-800">{{ $row['selesai_count'] }}</td>
                            <td class="text-center text-slate-500">{{ $row['ditolak_count'] }}</td>
                            <td class="text-center font-bold text-amber-900">{{ $row['pending_count'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-slate-500 py-3">Tidak ada data lokasi.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="font-bold bg-slate-100">
                        <td colspan="4" class="text-right uppercase">Total:</td>
                        <td class="text-center font-extrabold text-rose-900">{{ $summary['total_reports'] }}</td>
                        <td class="text-center">{{ $damageLocationData->sum('baru_count') }}</td>
                        <td class="text-center">{{ $damageLocationData->sum('diproses_count') }}</td>
                        <td class="text-center font-extrabold text-emerald-900">{{ $damageLocationData->sum('selesai_count') }}</td>
                        <td class="text-center text-slate-500">{{ $damageLocationData->sum('ditolak_count') }}</td>
                        <td class="text-center font-extrabold text-amber-900">{{ $damageLocationData->sum('pending_count') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Verification Signatures --}}
        <div class="page-break-avoid pt-6 mt-6 border-t border-slate-200 grid grid-cols-2 text-xs">
            <div>
                <p class="text-slate-500">Catatan Sistem:</p>
                <p class="text-slate-600 mt-1 italic text-[11px]">
                    Laporan ini digenerate secara otomatis oleh sistem FasilitasKita berdasarkan data transaksi reservasi dan laporan kerusakan yang telah tervalidasi.
                </p>
            </div>
            <div class="text-center ml-auto w-64 space-y-12">
                <div>
                    <p class="text-slate-600">Dicetak pada {{ $printedAt->translatedFormat('d F Y') }}</p>
                    <p class="font-bold text-slate-900">Administrator FasilitasKita</p>
                </div>
                <div>
                    <p class="font-bold text-slate-900 underline">{{ $printedBy->name }}</p>
                    <p class="text-[11px] text-slate-500">{{ $printedBy->email }}</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
