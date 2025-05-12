@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-header bg-custom text-white">
                        <a href="{{ route('customer.index') }}" class="btn bg-custom text-light btn-sm hover-effect">
                            <i class="bi bi-arrow-left-circle me-1"></i>
                            <span class="d-none d-md-inline">Kembali ke List Customer</span>
                            <span class="d-inline d-md-none">Kembali</span>
                        </a>
                        <h5 class="mb-0" style="text-align: center">Tambah Customer Baru</h5>
                    </div>
                    @if ($errors->any())
                        <script>
                            Swal.fire({
                                title: 'Error Validasi',
                                html: `@foreach ($errors->all() as $error)
                                    <p class="text-danger">• {{ $error }}</p>
                                @endforeach`,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        </script>
                    @endif
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('customer.store') }}" id="form-customer">
                            @csrf

                            <div class="mb-4">
                                <label for="kode_customer" class="form-label fw-bold">Kode Customer</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-upc-scan"></i>
                                    </span>
                                    <input id="kode_customer" type="text" class="form-control form-control-lg"
                                        name="kode_customer" style="text-transform: uppercase"
                                        value="{{ old('kode_customer') }}" required maxlength="4"
                                        placeholder="Masukkan kode customer">
                                </div>
                                <small class="text-muted">Maksimal 4 karakter</small>
                            </div>

                            <div class="mb-4">
                                <label for="nama_customer" class="form-label fw-bold">Nama Customer</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-box-seam"></i>
                                    </span>
                                    <input id="nama_customer" type="text"
                                        class="form-control form-control-lg text-capitalize" name="nama_customer"
                                        value="{{ old('nama_customer') }}" required maxlength="20"
                                        placeholder="Masukkan nama barang" oninput="autoCapitalize(this)">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="reset" class="btn btn-slide px-4">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-arrow text-light px-4">
                                    <i class="bi bi-save me-2"></i>Simpan Barang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function() {
            function autoCapitalize(input) {
                input.value = input.value.toLowerCase().replace(/\b\w/g, function(char) {
                    return char.toUpperCase();
                });
            }
            $('#form-customer').submit(function(e) {
                e.preventDefault();
                // Ambil data dari form
                let formData = $(this).serialize();

                $.ajax({
                    url: '/customer/store',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        // Tampilkan SweetAlert sukses jika berhasil
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses',
                                text: response.message,
                            }).then(() => {
                                window.location.href =
                                    '/customer/create';
                            });
                        }
                    },
                    error: function(xhr) {
                        // Menangani error jika ada masalah
                        const errorMessage = xhr.responseJSON.message;
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi kesalahan',
                            text: errorMessage,
                        });
                    }
                });
            });
        });
    </script>
@endpush
