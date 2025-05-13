<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Customer;
use App\Models\DetailPenjualan;
use App\Models\JenisTransaksi;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Exists;
use App\Exports\PenjualanExport;
use Maatwebsite\Excel\Facades\Excel;

class PenjualanController extends Controller
{
    public function preview(Request $request)
    {
        $data = $request->input('data'); // Mengambil data langsung dari request
        $formMode = $request->input('form_mode');
        // dd($data);
        return view('penjualan.preview', compact('data', 'formMode'));
    }

    public function printFaktur(Request $request)
    {
        $data = $request->input('data'); // Ambil data dari request
        return view('penjualan.print', compact('data'));  // Render view print.blade.php
    }

    public function exportCSV($noFaktur)
    {
        $penjualan = Penjualan::with('customer', 'detailPenjualan', 'jenisTransaksi')
            ->where('no_faktur', $noFaktur)
            ->firstOrFail(); // Mengambil penjualan berdasarkan no_faktur

        return Excel::download(new PenjualanExport($penjualan), 'data-penjualan.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function handleSave(Request $request)
    {
        if ($request->form_mode === 'create') {
            return $this->store($request);
        } else {
            return $this->update($request, $request->no_faktur);
        }
    }
    public function create()
    {
        $customer = Customer::all();
        $jenisTransaksi = JenisTransaksi::all();
        $barang = Barang::all();
        return view('penjualan.create', compact('jenisTransaksi', 'customer', 'barang'));
    }
    public function store(Request $request)
    {
        try {
            // dd($request);
            $no_faktur = strtolower($request->no_faktur);

            // Cek apakah no_faktur sudah ada
            $exists = Penjualan::whereRaw('LOWER(no_faktur) = ?', [$no_faktur])->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor faktur sudah ada.'
                ], 422);
            }

            // Log::info($request->all());
            DB::beginTransaction();
            $request->validate([
                'no_faktur' => ['required', 'string', 'size:6', 'unique:penjualan,no_faktur'],
                'kode_customer' => ['required', 'string', 'size:4', 'exists:customer,kode_customer'], // asumsi tabel customer ada
                'kode_jenis_transaksi' => ['required', 'string', 'size:1'],
                'tgl_faktur' => ['required', 'date'],
                
            ], [
                'no_faktur.required' => 'Nomor faktur wajib diisi.',
                'no_faktur.size' => 'Nomor faktur harus terdiri dari 6 karakter.',
                'no_faktur.unique' => 'Nomor faktur sudah digunakan.',
                'kode_customer.required' => 'Kode customer wajib diisi.',
                'kode_customer.size' => 'Kode customer harus terdiri dari 4 karakter.',
                'kode_customer.exists' => 'Kode customer tidak ditemukan.',
                'kode_jenis_transaksi.required' => 'Jenis transaksi wajib diisi.',
                'kode_jenis_transaksi.size' => 'Kode jenis transaksi harus 1 karakter.',
                'tgl_faktur.required' => 'Tanggal faktur wajib diisi.',
                'tgl_faktur.date' => 'Format tanggal faktur tidak valid.',
            ]);

            // Simpan data penjualan ke tabel penjualan
            $penjualan = Penjualan::create([
                'no_faktur' => $request->no_faktur,
                'kode_customer' => $request->kode_customer,
                'kode_jenis_transaksi' => $request->kode_jenis_transaksi,
                'tgl_faktur' => $request->tgl_faktur,
                'total_bruto' => 0,
                'total_diskon' => 0,
                'total_jumlah' => 0,
            ]);
            
            



            DB::commit();
            // Menampilkan pesan atau redirect setelah berhasil
            return response()->json(['success' => true, 'message' => 'Data penjualan berhasil disimpan!']);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['msg' => 'Gagal simpan: ' . $e->getMessage()]);
        }
    }
    public function hapusDetail($kode_barang, Request $request)
    {
        dd($request);
        try {
            // Temukan detail penjualan berdasarkan kode_barang dan no_faktur
            $detailPenjualan = DetailPenjualan::where('kode_barang', $kode_barang)
                                            ->where('no_faktur', $request->no_faktur) // pastikan no_faktur sesuai
                                            ->firstOrFail();

            // Hapus detail penjualan
            $detailPenjualan->delete();

            // Update total penjualan setelah penghapusan detail
            $penjualan = Penjualan::where('no_faktur', $request->no_faktur)->firstOrFail();
            $penjualan->update([
                'total_bruto' => $penjualan->detailPenjualan->sum('bruto'),
                'total_diskon' => $penjualan->detailPenjualan->sum('diskon'),
                'total_jumlah' => $penjualan->detailPenjualan->sum('jumlah')
            ]);

            // Kembalikan response sukses
            return response()->json(['success' => true, 'message' => 'Detail penjualan berhasil dihapus!']);
        } catch (\Exception $e) {
            // Jika terjadi error
            return response()->json(['success' => false, 'message' => 'Gagal menghapus detail penjualan: ' . $e->getMessage()], 500);
        }
    }



   public function savePenjualanDetail(Request $request)
{
    try {
        $penjualan = Penjualan::where('no_faktur', $request->no_faktur)->firstOrFail();

        foreach ($request->detail as $item) {
            $harga = (float) preg_replace('/[^\d]/', '', $item['harga']);
            $qty = (float) $item['qty'];
            $diskon = (float) str_replace(['%', ','], ['', '.'], $item['diskon']);

            $bruto = $harga * $qty;
            $jumlah = $bruto - ($diskon / 100 * $bruto);

            $penjualan->detailPenjualan()->updateOrCreate(
                ['no_faktur' => $penjualan->no_faktur, 'kode_barang' => $item['kode_barang']],
                [
                    'harga' => $harga,
                    'qty' => $qty,
                    'diskon' => $diskon,
                    'bruto' => $bruto,
                    'jumlah' => $jumlah,
                ]
            );
        }

        return response()->json(['success' => true, 'message' => 'Detail berhasil disimpan']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}




    public function searchTransaksi(Request $request)
    {
        $term = $request->get('term');

        $results = Penjualan::with('customer') // asumsikan relasi `customer`
            ->where('no_faktur', 'like', "%{$term}%")
            ->limit(10)
            ->get()
            ->map(function ($p) {
                return [
                    'no_faktur' => $p->no_faktur,
                    'nama_customer' => $p->customer->nama_customer,
                    'tanggal' => date('d-m-Y', strtotime($p->tgl_faktur)),
                ];
            });

        return response()->json($results);
    }
    public function search($no_faktur)
    {
        $penjualan = Penjualan::with('detailPenjualan')->where('no_faktur', $no_faktur)->first();

        if (!$penjualan) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'no_faktur' => $penjualan->no_faktur,
            'tgl_faktur' => $penjualan->tgl_faktur,
            'kode_customer' => $penjualan->kode_customer,
            'kode_jenis_transaksi' => $penjualan->kode_jenis_transaksi,
            'total_bruto' => $penjualan->total_bruto,
            'total_diskon' => $penjualan->total_diskon,
            'total_jumlah' => $penjualan->total_jumlah,
            'detail_penjualan' => $penjualan->detailPenjualan->map(function ($item) {
                return [
                    'no_faktur' => $item->no_faktur,
                    'kode_barang' => $item->kode_barang,
                    'nama_barang' => $item->barang->nama_barang ?? '-',
                    'harga' => $item->harga,
                    'qty' => $item->qty,
                    'diskon' => $item->diskon,
                ];
            }),
        ]);
    }
    public function destroy($no_faktur)
    {
        try {
            // Memulai transaksi
            DB::beginTransaction();

            // Mencari transaksi berdasarkan no_faktur
            $penjualan = Penjualan::where('no_faktur', $no_faktur)->firstOrFail();

            // Soft delete detail terlebih dahulu
            $penjualan->detailPenjualan()->delete();

            // Soft delete header
            $penjualan->delete();

            // Commit transaksi jika semua berhasil
            DB::commit();

            return response()->json(['message' => 'Transaksi penjualan berhasil dihapus.']);
        } catch (\Throwable $th) {
            // Rollback jika terjadi error
            DB::rollBack();

            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus transaksi.',
                'error' => $th->getMessage(), // Pesan error
                'trace' => $th->getTraceAsString() // Stack trace
            ], 500);
        }
    }
public function updateHeader(Request $request, $no_faktur)
{
$penjualan = Penjualan::whereRaw('LOWER(no_faktur) = ?', [strtolower($no_faktur)])->first();

    if (!$penjualan) {
        return response()->json([
            'success' => false,
            'message' => 'Data penjualan tidak ditemukan.'
        ], 404);
    }

    $request->validate([
        'kode_customer' => ['required', 'string', 'size:4', 'exists:customer,kode_customer'],
        'kode_jenis_transaksi' => ['required', 'string', 'size:1'],
        'tgl_faktur' => ['required', 'date'],
    ]);

    $penjualan->update([
        'kode_customer' => $request->kode_customer,
        'kode_jenis_transaksi' => $request->kode_jenis_transaksi,
        'tgl_faktur' => $request->tgl_faktur,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Header penjualan berhasil diperbarui.'
    ]);
}
public function updateDetail(Request $request, $no_faktur)
{
    $penjualan = Penjualan::where('no_faktur', $no_faktur)->first();

    if (!$penjualan) {
        return response()->json([
            'success' => false,
            'message' => 'Data penjualan tidak ditemukan.'
        ], 404);
    }

    $request->validate([
        'detail' => ['required', 'array', 'min:1'],
        'detail.*.kode_barang' => ['required', 'string'],
        'detail.*.harga' => ['required', 'numeric'],
        'detail.*.qty' => ['required', 'numeric'],
        'detail.*.diskon' => ['required', 'numeric'],
    ]);

    DB::beginTransaction();
    try {
        $penjualan->detailPenjualan()->forceDelete(); // atau hapus per barang jika perlu

        foreach ($request->detail as $item) {
            $harga = (float)$item['harga'];
            $qty = (float)$item['qty'];
            $diskon = (float)$item['diskon'];

            $bruto = $harga * $qty;
            $jumlah = $bruto - ($diskon / 100 * $bruto);

            $penjualan->detailPenjualan()->create([
                'no_faktur' => $no_faktur,
                'kode_barang' => $item['kode_barang'],
                'harga' => $harga,
                'qty' => $qty,
                'diskon' => $diskon,
                'bruto' => $bruto,
                'jumlah' => $jumlah,
            ]);
        }

        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Detail penjualan berhasil diperbarui.'
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Gagal update detail: ' . $e->getMessage()
        ], 500);
    }
}

}