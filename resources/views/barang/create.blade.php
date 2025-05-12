@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-header bg-custom text-white">
                        <a href="{{ route('barang.index') }}" class="btn bg-custom text-light btn-sm hover-effect">
                            <i class="bi bi-arrow-left-circle me-1"></i>
                            <span class="d-none d-md-inline">Kembali ke List Barang</span>
                            <span class="d-inline d-md-none">Kembali</span>
                        </a>
                        <h5 class="mb-0" style="text-align: center">Tambah Barang Baru</h5>
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
                        <form method="POST" action="{{ route('barang.store') }}" id="form-barang">
                            @csrf

                            <div class="mb-4">
                                <label for="kode_barang" class="form-label fw-bold">Kode Barang</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-upc-scan"></i>
                                    </span>
                                    <input id="kode_barang" type="text" style="text-transform: uppercase"
                                        class="form-control form-control-lg" name="kode_barang"
                                        value="{{ old('kode_barang') }}" required maxlength="10"
                                        placeholder="Masukkan kode barang">
                                </div>
                                <small class="text-muted">Maksimal 10 karakter</small>
                            </div>

                            <div class="mb-4">
                                <label for="nama_barang" class="form-label fw-bold">Nama Barang</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-box-seam"></i>
                                    </span>
                                    <input id="nama_barang" type="text"
                                        class="form-control form-control-lg text-capitalize" name="nama_barang"
                                        value="{{ old('nama_barang') }}" required maxlength="20"
                                        placeholder="Masukkan nama barang" oninput="autoCapitalize(this)">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="harga_barang" class="form-label fw-bold">Harga Barang</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-cash"></i></span>

                                    <input id="harga_barang" type="text" class="form-control form-control-lg currency"
                                        name="harga_barang" value="{{ old('harga_barang') }}" required min="0"
                                        placeholder="Masukkan harga barang">
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

    <style>
        .card {
            border: none;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .form-control {
            border-left: 0;
            border-radius: 0 0.375rem 0.375rem 0 !important;
        }

        .input-group-text {
            border-right: 0;
            border-radius: 0.375rem 0 0 0.375rem !important;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
            border-color: #86b7fe;
        }

        .invalid-feedback {
            display: block;
        }
    </style>
@endsection
@push('js')
    <script>
        $(document).ready(function() {
            function autoCapitalize(input) {
                input.value = input.value.toLowerCase().replace(/\b\w/g, function(char) {
                    return char.toUpperCase();
                });
            }


            initAutoNumericAll();
            $('#form-barang').submit(function(e) {
                e.preventDefault();
                let harga = AutoNumeric.getAutoNumericElement("#harga_barang")?.get();

                // Menghapus "Rp" dan pemisah ribuan
                let hargaTanpaRpDanPemisah = harga.replace(/[Rp\s.]/g,
                    ''); // Menghapus "Rp", spasi, dan titik

                if (!/^\d{1,13}(\.\d{1,2})?$/.test(hargaTanpaRpDanPemisah)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Harga',
                        text: 'Harga harus memiliki maksimal 13 digit sebelum koma dan maksimal 2 digit setelah koma.',
                    });

                    // Reset harga
                    AutoNumeric.getAutoNumericElement("#harga_barang")?.set('');
                    return;
                }


                // Ambil data dari form
                let formData = $(this).serialize();

                $.ajax({
                    url: '/barang/store', // Ganti dengan URL yang sesuai
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
                                // Redirect ke halaman lain setelah SweetAlert ditutup
                                window.location.href =
                                    '/barang/create';
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
