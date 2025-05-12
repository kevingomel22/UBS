<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Faktur Penjualan - {{ $data['customer']['nama_customer'] }}</title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #000000;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            margin: 2cm auto;
            max-width: 21cm;
            background: #f8f9fa;
        }

        .invoice-box {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
        }

        .company-info {
            max-width: 300px;
        }

        .company-name {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .invoice-title {
            font-size: 2rem;
            color: var(--secondary-color);
            text-align: center;
            margin: 1.5rem 0;
        }

        .client-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .info-box {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
            table-layout: auto;
        }

        th {
            background: var(--primary-color);
            color: white;
            padding: 1rem;
            text-align: left;
        }

        th:nth-child(3),
        th:nth-child(5),
        th:nth-child(6) {
            /* Kolom Harga, Diskon, dan Subtotal */
            min-width: 120px;
        }

        td {
            white-space: nowrap;
            padding: 8px 12px !important;
        }

        td:nth-child(2) {
            /* Kolom Nama Barang */
            max-width: 250px;
            white-space: normal;
            word-break: break-word;
        }

        .text-right {
            text-align: right;
        }

        .total-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 6px;
            margin-top: 2rem;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .grand-total {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--accent-color);
        }

        @media print {
            body {
                margin: 0 !important;
            }

            .invoice-box {
                page-break-inside: avoid;
            }

            img {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                filter: brightness(100%) !important;
            }

            /* Hapus page-break yang memaksa pemisahan halaman */
            .header-section,
            .client-info,
            .total-section {
                /* page-break-before: always; */
                /* HAPUS atau komentar */
                page-break-inside: avoid;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <div class="header-section">
            <div class="company-header" style="display: flex; align-items: center; gap: 20px;">
                <div style="background-color: #282748; padding: 10px; border-radius: 8px;">
                    <img src="{{ asset('images/logo-ubs-gold.png') }}" alt="Logo UBS" style="height: 80px;">
                </div>
                <div class="company-info" style="color: #000;">
                    <div class="company-name" style="font-weight: bold; font-size: 18px;">PT. Untung Bersama Sejahtera
                    </div>
                    <div>
                        Jl. Kenjeran 395 - 399, Gading Kec. Tambaksari. <br>
                        Surabaya, 60134<br>
                        Telp: (+62) 082230308000<br>
                        info@ubsgold.com <br>
                    </div>
                </div>
            </div>

            <div class="info-box">
                <div style="font-size: 1.2rem; margin-bottom: 0.5rem;">FAKTUR PENJUALAN</div>
                <div>No Faktur: {{ $data['detail'][0]['no_faktur'] }}</div>
                <div>Tanggal: {{ \Carbon\Carbon::parse($data['tanggal'])->format('d-m-Y') }}</div>
                Jenis Transaksi: {{ $data['jenis_transaksi']['nama'] }}
            </div>
        </div>

        <div class="client-info">
            <div class="info-box">
                <strong>Customer:</strong><br>
                {{ $data['customer']['nama_customer'] }}<br>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th class="text-right">Harga</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Diskon</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['detail'] as $item)
                    <tr>
                        <td>{{ $item['kode_barang'] }}</td>
                        <td>{{ $item['nama_barang'] }}</td>
                        <td class="text-right">{{ formatRupiah($item['harga']) }}</td>
                        <td class="text-right">{{ $item['qty'] }}</td>
                        <td class="text-right">{{ number_format($item['diskon'], 2, ',', '.') }}%</td>
                        <td class="text-right">{{ formatRupiah($item['harga'] * $item['qty'] - $item['diskon']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">
                <span>Total Bruto:</span>
                <span>{{ formatRupiah($data['total_bruto']) }}</span>
            </div>
            <div class="total-row">
                <span>Total Diskon:</span>
                <span>{{ formatRupiah($data['total_diskon']) }}</span>
            </div>
            <div class="total-row grand-total">
                <span>TOTAL PEMBAYARAN:</span>
                <span>{{ formatRupiah($data['total_jumlah']) }}</span>
            </div>
        </div>
    </div>
</body>

</html>
@php
    function formatRupiah($angka)
    {
        return 'Rp ' . number_format($angka, 2, ',', '.');
    }
@endphp
