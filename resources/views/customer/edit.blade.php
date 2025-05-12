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
                        <h5 class="mb-0" style="text-align: center">Edit Customer</h5>
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
                        <form id="edit-form-{{ $customer->kode_customer }}"
                            action="{{ route('customer.update', $customer->kode_customer) }}" method="POST"
                            class="edit-form">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label for="kode_customer" class="form-label fw-bold">Kode Customer</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-upc-scan"></i>
                                    </span>
                                    <input id="kode_customer" type="text" class="form-control form-control-lg"
                                        name="kode_customer" style="text-transform: uppercase"
                                        value="{{ old('kode_customer', $customer->kode_customer) }}" required maxlength="4"
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
                                        value="{{ old('nama_customer', $customer->nama_customer) }}" required maxlength="40"
                                        placeholder="Masukkan nama customer" oninput="autoCapitalize(this)">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-arrow text-light">Update</button>
                            <a href="{{ route('customer.index') }}" class="btn btn-arrow--grey text-light">Batal</a>
                        </form>
                    </div>
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
                text: "Anda yakin ingin memperbarui data customer?",
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
                                    window.location.href = '/customer';
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
