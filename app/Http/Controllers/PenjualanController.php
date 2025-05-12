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
                'total_bruto' => ['required', 'numeric', 'min:0'],
                'total_diskon' => ['required', 'numeric', 'min:0'],
                'total_jumlah' => ['required', 'numeric', 'min:0'],
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
                'total_bruto.required' => 'Total bruto wajib diisi.',
                'total_bruto.numeric' => 'Total bruto harus berupa angka.',
                'total_diskon.required' => 'Total diskon wajib diisi.',
                'total_diskon.numeric' => 'Total diskon harus berupa angka.',
                'total_jumlah.required' => 'Total jumlah wajib diisi.',
                'total_jumlah.numeric' => 'Total jumlah harus berupa angka.',
            ]);

            // Simpan data penjualan ke tabel penjualan
            $penjualan = Penjualan::create([
                'no_faktur' => $request->no_faktur,
                'kode_customer' => $request->kode_customer,
                'kode_jenis_transaksi' => $request->kode_jenis_transaksi,
                'tgl_faktur' => $request->tgl_faktur,
                'total_bruto' => $request->total_bruto,
                'total_diskon' => $request->total_diskon,
                'total_jumlah' => $request->total_jumlah,
            ]);

            if ($request->has('detail')) {
                foreach ($request->detail as $item) {
                    $penjualan->detailPenjualan()->create([
                        'no_faktur' => $penjualan->no_faktur,
                        'kode_barang' => $item['kode_barang'],
                        'harga' => $item['harga'],
                        'qty' => $item['qty'],
                        'diskon' => $item['diskon'],
                        'bruto' => $item['harga'] * $item['qty'],
                        'jumlah' => ($item['harga'] * $item['qty']) - ($item['diskon'] / 100 * $item['harga'] * $item['qty']),
                    ]);
                }
            }



            DB::commit();
            // Menampilkan pesan atau redirect setelah berhasil
            return response()->json(['success' => true, 'message' => 'Data penjualan berhasil disimpan!']);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['msg' => 'Gagal simpan: ' . $e->getMessage()]);
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

    public function update(Request $request, $no_faktur)
    {
        DB::beginTransaction();

        $no_faktur_lama = strtolower($request->no_faktur_lama);

        $penjualan = Penjualan::whereRaw('LOWER(no_faktur) = ?', [$no_faktur_lama])->first();

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
            'total_bruto' => ['required', 'numeric', 'min:0'],
            'total_diskon' => ['required', 'numeric', 'min:0'],
            'total_jumlah' => ['required', 'numeric', 'min:0'],
            'detail' => ['required', 'array', 'min:1'],
            'detail.*.kode_barang' => ['required', 'string', 'exists:barang,kode_barang'],
            'detail.*.harga' => ['required', 'numeric', 'min:0'],
            'detail.*.qty' => ['required', 'numeric', 'min:1'],
            'detail.*.diskon' => ['required', 'numeric', 'min:0'],
        ], [
            'detail.required' => 'Detail penjualan wajib diisi.',
            'detail.array' => 'Format detail penjualan tidak valid.',
            'detail.min' => 'Minimal harus ada satu barang di detail.',
            'detail.*.kode_barang.required' => 'Kode barang wajib diisi di setiap baris detail.',
            'detail.*.kode_barang.exists' => 'Kode barang tidak ditemukan.',
            'detail.*.harga.required' => 'Harga wajib diisi di setiap baris detail.',
            'detail.*.harga.numeric' => 'Harga harus berupa angka.',
            'detail.*.qty.required' => 'Qty wajib diisi di setiap baris detail.',
            'detail.*.qty.numeric' => 'Qty harus berupa angka.',
            'detail.*.qty.min' => 'Qty minimal 1.',
            'detail.*.diskon.required' => 'Diskon wajib diisi di setiap baris detail.',
            'detail.*.diskon.numeric' => 'Diskon harus berupa angka.',
        ]);
        $penjualan->detailPenjualan()->where('no_faktur', $no_faktur_lama)->forceDelete();

        try {
            // Update header
            $penjualan->update([
                'no_faktur' => $request->no_faktur,  // Pastikan no_faktur diupdate
                'kode_customer' => $request->kode_customer,
                'kode_jenis_transaksi' => $request->kode_jenis_transaksi,
                'tgl_faktur' => $request->tgl_faktur,
                'total_bruto' => $request->total_bruto,
                'total_diskon' => $request->total_diskon,
                'total_jumlah' => $request->total_jumlah,
            ]);


            $detailData = $request->detail;

            // Tambahkan ulang detail baru dengan no_faktur yang baru
            foreach ($detailData as $item) {
                $harga = (float)$item['harga'];
                $qty = (float)$item['qty'];
                $diskon = (float)$item['diskon'];

                $bruto = $harga * $qty;
                $jumlah = $bruto - ($diskon / 100 * $bruto);

                // Cek apakah detail dengan kode_barang sudah ada
                $existingDetail = $penjualan->detailPenjualan()->where('kode_barang', $item['kode_barang'])->first();

                if ($existingDetail) {
                    // Update detail jika ada
                    $existingDetail->update([
                        'harga' => $harga,
                        'qty' => $qty,
                        'diskon' => $diskon,
                        'bruto' => $bruto,
                        'jumlah' => $jumlah,
                    ]);
                } else {
                    // Jika tidak ada, buat data baru
                    $penjualan->detailPenjualan()->create([
                        'no_faktur' => $request->no_faktur,
                        'kode_barang' => $item['kode_barang'],
                        'harga' => $harga,
                        'qty' => $qty,
                        'diskon' => $diskon,
                        'bruto' => $bruto,
                        'jumlah' => $jumlah,
                    ]);
                }
            }


            DB::commit();  // Commit transaksi setelah selesai semua proses
            return response()->json([
                'success' => true,
                'message' => 'Data penjualan berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal update: ' . $e->getMessage()
            ], 500);
        }
    }
}
