<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;

class BarangController extends Controller
{
    public function index()
    {
        return view('barang.index');
    }

    public function getData()
    {
        $barang = Barang::select(['kode_barang', 'nama_barang', 'harga_barang']);

        return DataTables::of($barang)
            ->addIndexColumn()
            ->editColumn('kode_barang', function ($row) {
                return strtoupper($row->kode_barang); // kapital semua
            })
            ->editColumn('harga_barang', function ($row) {
                return '<span class="currency">' . $row->harga_barang . '</span>';
            })
            ->addColumn('aksi', function ($row) {
                $editUrl = route('barang.edit', $row->kode_barang);
                return '
                        <div class="btn-group" role="group">
                            
                            <a href="' . $editUrl . '" class="btn btn-sm btn-warning mx-1 d-flex align-items-center py-1">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <form id="delete-form-' . $row->kode_barang . '" action="' . route('barang.destroy', $row->kode_barang) . '" method="POST" class="delete-form d-inline">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-danger mx-1 d-flex align-items-center py-1">
                                    <i class="fas fa-trash me-1"></i> Hapus
                                </button>
                            </form>
                        </div>';
            })

            ->rawColumns(['aksi', 'harga_barang'])
            ->make(true);
    }
    public function create()
    {
        return view('barang.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|string|max:10|unique:barang,kode_barang',
            'nama_barang' => 'required|string|max:20',
            'harga_barang' => 'required|regex:/^\d{1,13}(\.\d{1,2})?$/',
        ], [
            'kode_barang.unique' => 'Kode barang sudah terdaftar. Gunakan kode lain.',
            'harga_barang.regex' => 'Harga harus dalam format angka dengan maksimal 13 digit sebelum koma dan maksimal 2 digit setelah koma.',
        ]);

        $kode_barang = strtolower($request->kode_barang);
        $nama_barang = ucwords(strtolower($request->nama_barang));
        $harga = str_replace(',', '.', $request->harga_barang);
        $harga = floatval($harga);

        // Simpan data
        Barang::create([
            'kode_barang' => $kode_barang,
            'nama_barang' => $nama_barang,
            'harga_barang' => $harga
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil ditambahkan'
        ]);
    }



    public function search(Request $request)
    {
        $keyword = $request->get('search');
        $barang = Barang::where('kode_barang', 'like', "%$keyword%")
            ->orWhere('nama_barang', 'like', "%$keyword%")
            ->get();

        return response()->json($barang);
    }

    public function edit($kode_barang)
    {

        $barang = Barang::where('kode_barang', $kode_barang)->first();

        if (!$barang) {
            return redirect()->route('barang.index')->with('error', 'Barang tidak ditemukan.');
        }

        // Mengirim data barang ke view untuk diedit
        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, $kode_barang)
    {
        // Validasi data yang diterima
        $request->validate([
            'kode_barang' => [
                'required',
                'string',
                'max:10',
                // Pengecualian unique untuk kode_barang yang sedang diperbarui
                Rule::unique('barang', 'kode_barang')->ignore($kode_barang, 'kode_barang'),
            ],
            'nama_barang' => 'required|string|max:20',
            'harga_barang' => 'required|regex:/^\d{1,13}(\.\d{1,2})?$/',
        ], [
            'kode_barang.unique' => 'Kode barang sudah terdaftar. Gunakan kode lain.',
            'harga_barang.regex' => 'Harga harus dalam format angka dengan maksimal 13 digit sebelum koma dan maksimal 2 digit setelah koma.',
        ]);

        try {
            // Mengambil data barang berdasarkan kode_barang
            $barang = Barang::where('kode_barang', $kode_barang)->first();

            if (!$barang) {
                return redirect()->route('barang.index')->with('error', 'Barang tidak ditemukan.');
            }

            // Update data barang
            $barang->update([
                'kode_barang' => $request->kode_barang,
                'nama_barang' => $request->nama_barang,
                'harga_barang' => (float) $request->harga_barang,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update barang: ' . $e->getMessage()
            ], 500);
        }
    }


    public function destroy($kode_barang)
    {
        try {
            $barang = Barang::findOrFail($kode_barang);
            $barang->delete(); // Ini akan melakukan soft delete

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus barang: ' . $e->getMessage()
            ], 500);
        }
    }
}
