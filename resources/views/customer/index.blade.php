@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center bg-custom text-white">
                    <h5>Data Customer</h5>
                    <a href="{{ route('customer.create') }}" class="btn btn-sm btn-light">Tambah Customer</a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle" id="customer-table">
                            <thead class="thead-light" style="background-color: #f8f9fa;">
                                <tr>
                                    <th style="font-size: 15px">No</th>
                                    <th>Kode Customer</th>
                                    <th>Nama Customer</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function() {
            $('#customer-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('customer.data') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kode_customer',
                        name: 'kode_customer'
                    },
                    {
                        data: 'nama_customer',
                        name: 'nama_customer'
                    },

                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false
                    }
                ],
            });
        });

        $(document).on('submit', '.delete-form', function(e) {
            e.preventDefault();

            const form = this;

            Swal.fire({
                title: 'Yakin Hapus Data?',
                text: "Data akan dihapus !!!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form via AJAX
                    $.ajax({
                        url: form.action,
                        type: 'POST',
                        data: {
                            _token: form.querySelector('input[name="_token"]').value,
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                // Reload DataTables
                                window.location.href =
                                    '/customer';
                                $('#dataTable').DataTable().ajax.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                            });
                        }
                    });
                }
            });
        });
    </script>
@endpush
