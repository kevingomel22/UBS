<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{

    public function index()
    {
        return view('customer.index');
    }

    public function getData()
    {
        $customer = Customer::select(['kode_customer', 'nama_customer']);

        return DataTables::of($customer)
            ->addIndexColumn()
            ->editColumn('kode_customer', function ($row) {
                return strtoupper($row->kode_customer); // kapital semua
            })
            ->editColumn('harga_customer', function ($row) {})
            ->addColumn('aksi', function ($row) {
                $editUrl = route('customer.edit', $row->kode_customer);

                return '
                        <div class="btn-group" role="group">                            
                            <a href="' . $editUrl . '" class="btn btn-sm btn-warning mx-1 d-flex align-items-center py-1">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <form id="delete-form-' . $row->kode_customer . '" action="' . route('customer.destroy', $row->kode_customer) . '" method="POST" class="delete-form d-inline">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-danger mx-1 d-flex align-items-center py-1">
                                    <i class="fas fa-trash me-1"></i> Hapus
                                </button>
                            </form>
                        </div>';
            })

            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function edit($kode_customer)
    {

        $customer = Customer::where('kode_customer', $kode_customer)->first();

        if (!$customer) {
            return redirect()->route('customer.index')->with('error', 'Customer tidak ditemukan.');
        }


        return view('customer.edit', compact('customer'));
    }

    public function update(Request $request, $kode_customer)
    {
        // Validasi data yang diterima
        $request->validate([
            'kode_customer' => [
                'required',
                'string',
                'max:4',
                // Pengecualian unique untuk kode_customer yang sedang diperbarui
                Rule::unique('customer', 'kode_customer')->ignore($kode_customer, 'kode_customer'),
            ],
            'nama_customer' => 'required|string|max:40',
        ], [
            'kode_customer.unique' => 'Kode customer sudah terdaftar. Gunakan kode lain.',
        ]);

        try {
            // Mengambil data customer berdasarkan kode_customer
            $customer = Customer::where('kode_customer', $kode_customer)->first();

            if (!$customer) {
                return redirect()->route('customer.index')->with('error', 'Customer tidak ditemukan.');
            }

            // Mengubah format inputan menjadi format yang sesuai
            $kode_customer = strtolower($request->kode_customer);
            $nama_customer = ucwords(strtolower($request->nama_customer));  // "john doe" => "John Doe"

            // Update data customer
            $customer->update([
                'kode_customer' => $kode_customer,
                'nama_customer' => $nama_customer,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Customer berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update customer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function create()
    {
        return view('customer.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'kode_customer' => 'required|string|max:4|unique:customer,kode_customer',
            'nama_customer' => 'required|string|max:40',
        ], [
            'kode_customer.unique' => 'Kode customer sudah terdaftar. Gunakan kode lain.',
        ]);
        $kode_customer = strtolower($request->kode_customer);
        $nama_customer = ucwords(strtolower($request->nama_customer));  // "john doe" => "John Doe"


        // Simpan data
        Customer::create([
            'kode_customer' => $kode_customer,
            'nama_customer' => $nama_customer,

        ]);

        return response()->json([
            'success' => true,
            'message' => 'Customer berhasil ditambahkan'
        ]);
    }


    public function destroy($kode_customer)
    {
        try {
            $customer = Customer::findOrFail($kode_customer);
            $customer->delete(); // Ini akan melakukan soft delete

            return response()->json([
                'success' => true,
                'message' => 'customer berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus customer: ' . $e->getMessage()
            ], 500);
        }
    }
}
