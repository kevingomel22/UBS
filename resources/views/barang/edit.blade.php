@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">

                <div class="card-header bg-custom text-white">
                    <a href="{{ route('barang.index') }}" class="btn bg-custom text-light btn-sm hover-effect">
                        <i class="bi bi-arrow-left-circle me-1"></i>
                        <span class="d-none d-md-inline">Kembali ke List Barang</span>
                        <span class="d-inline d-md-none">Kembali</span>
                    </a>
                    <h5 style="text-align: center">Edit Barang</h5>
                </div>

                <div class="card-body">
                    <!-- Form untuk mengedit barang -->
                    <form id="edit-form-{{ $barang->kode_barang }}"
                        action="{{ route('barang.update', $barang->kode_barang) }}" method="POST" class="edit-form">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="kode_barang" class="form-label fw-bold">Kode Barang</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-upc-scan"></i>
                                </span>
                                <input type="text" class="form-control form-control-lg"style="text-transform: uppercase"
                                    id="kode_barang" name="kode_barang"
                                    value="{{ old('kode_barang', $barang->kode_barang) }}" required maxlength="10">
                            </div>
                            <small class="text-muted">Maksimal 10 karakter</small>
                        </div>

                        <div class="mb-4">
                            <label for="nama_barang" class="form-label fw-bold">Nama Barang</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-box-seam"></i>
                                </span>

                                <input type="text" class="form-control form-control-lg text-capitalize" id="nama_barang"
                                    name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" required
                                    maxlength="20" oninput="autoCapitalize(this)">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="harga_barang" class="form-label fw-bold">Harga Barang</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-cash"></i></span>
                                <input type="text" class="form-control form-control-lg currency" id="harga_barang"
                                    name="harga_barang" value="{{ old('harga_barang', $barang->harga_barang) }}" required
                                    min="0">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-arrow text-light">Update</button>
                        <a href="{{ route('barang.index') }}" class="btn btn-arrow--grey text-light">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).on('submit', '.edit-form', function(e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);

            Swal.fire({
                title: 'Konfirmasi Update Data',
                text: "Anda yakin ingin memperbarui data barang?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Update!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                    submitBtn.disabled = true;

                    $.ajax({
                        url: form.action,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                // Hanya reload jika di halaman index
                                if ($.fn.DataTable.isDataTable('#dataTable')) {
                                    $('#dataTable').DataTable().ajax.reload(null,
                                        false);
                                } else {
                                    window.location.href = '/barang';
                                }
                            });
                        },
                        error: function(xhr) {
                            let errorMessage = xhr.responseJSON?.message ||
                                'Terjadi kesalahan saat update';

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: errorMessage
                            });

                            // Kembalikan tombol ke state semula
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }
                    });
                }
            });
        });
    </script>
@endpush
