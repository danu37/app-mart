<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $transaksi;

    public function __construct($transaksi)
    {
        $this->transaksi = $transaksi;
    }

    public function collection()
    {
        return $this->transaksi;
    }

    public function headings(): array
    {
        return [
            'ID Transaksi',
            'Tanggal',
            'ID Pelanggan',
            'Nama Pelanggan',
            'Total Belanja (Rp)'
        ];
    }

    public function map($row): array
    {
        return [
            $row->id_transaksi,
            $row->tanggal,
            $row->id_pelanggan,
            $row->pelanggan->nama_pelanggan ?? '-',
            $row->total_belanja
        ];
    }
}
