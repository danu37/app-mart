<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Data Pelanggan</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
        }

        .header p {
            margin: 5px 0 0;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
            text-transform: uppercase;
            font-size: 11px;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>TechStore - Laporan Data Pelanggan</h1>
        <p>Dicetak pada: {{ date('d M Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Nama Pelanggan</th>
                <th>Email / Kontak</th>
                <th>Kota</th>
                <th class="text-center">Total Belanja</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pelanggan as $index => $p)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $p->nama_pelanggan }}</td>
                <td>{{ $p->user->email ?? '-' }}</td>
                <td>{{ $p->kota ?? '-' }}</td>
                <td class="text-center">{{ $p->transaksi_count ?? 0 }} Transaksi</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Tidak ada data pelanggan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>