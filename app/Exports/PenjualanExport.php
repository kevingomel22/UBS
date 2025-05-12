<?php

namespace App\Exports;

use App\Models\Penjualan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PenjualanExport implements FromCollection, WithHeadings
{
    protected $penjualan;

    public function __construct(Penjualan $penjualan)
    {
        $this->penjualan = $penjualan;
    }

    public function collection()
    {
        $data = collect();

        foreach ($this->penjualan->detailPenjualan as $detail) {
            $namaBarang = $detail->barang->nama_barang ?? ''; // Jika tidak ada, kosongkan


            $data->push([
                $this->penjualan->no_faktur,
                $this->penjualan->tgl_faktur,
                $this->penjualan->customer->nama_customer ?? '',
                $this->penjualan->jenisTransaksi->nama_jenis_transaksi ?? '',
                $detail->kode_barang,
                $namaBarang,
                $detail->harga,
                $detail->qty,
                $detail->diskon,
                $detail->bruto,
                $detail->jumlah,
                $this->penjualan->total_bruto,
                $this->penjualan->total_diskon,
                $this->penjualan->total_jumlah,
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'No Faktur',
            'Tanggal',
            'Customer',
            'Jenis Transaksi',
            'Kode Barang',
            'Nama Barang',
            'Harga',
            'Qty',
            'Diskon',
            'Bruto',
            'Jumlah',
            'Total Bruto',
            'Total Diskon',
            'Total Jumlah'
        ];
    }
}
