<meta name="csrf-token" content="{{ csrf_token() }}">
@extends('layouts.app') @section('title', 'Transaksi Baru') @section('content')
<div class="modal fade" id="modalCari" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formCari" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cari Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <select id="cariNoFaktur" class="form-control select2"></select>
            </div>
            <div class="modal-footer">
                <button type="button" id="modal-submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>
<form id="form-penjualan" action="{{ route('penjualan.store') }}" method="POST">
    @csrf
    <input type="hidden" id="form_mode" name="form_mode" value="create">
    <div class="container">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <a href="{{ url('/barang') }}" class="btn btn-sm btn-primary ms-3">Master Barang</a>
        <a href="{{ url('/customer') }}" class="btn btn-sm btn-primary ms-3">Master Customer</a>

        {{-- H: Header --}}
        <div class="card mb-4" id="header">
            <div class="card-body">
                <div>
                    <button class="btn btn-light btn-sm" type="button" id="btnInput" disabled>
                        Input
                    </button>
                    <button class="btn btn-light btn-sm" type="button" id="btnHapus" disabled>
                        Hapus
                    </button>
                    <button class="btn btn-light btn-sm" type="button" id="btnEdit" disabled>
                        Edit
                    </button>
                    <button class="btn btn-light btn-sm" type="submit" id="btnSimpan" disabled>
                        Simpan
                    </button>
                    <button class="btn btn-light btn-sm" type="button" id="btnCari">
                        Cari
                    </button>
                    <button class="btn btn-light btn-sm" type="button" id="btnBatal" disabled>
                        Batal
                    </button>
                    <button class="btn btn-light btn-sm" type="button" id="btnPrint" disabled>
                        Print
                    </button>
                    <button type="button" class="btn btn-light btn-sm" id="btnPreview" data-bs-toggle="modal"
                        data-bs-target="#modalPreview" disabled>
                        <i class="bi bi-eye"></i> Preview
                    </button>
                    <button class="btn btn-light btn-sm" type="button" id="btnCSV" disabled>
                        CSV
                    </button>
                </div>
                <div class="form-row mb-3">
                    <div class="col-md-6">
                        <label>No Faktur</label>
                        <input type="text" class="form-control" id="no-faktur" name="no_faktur"
                            style="text-transform: uppercase" maxlength="6" autofocus />
                        <input type="hidden" name="no_faktur_lama">
                    </div>
                    <div class="col-md-6">
                        <label>Tanggal</label>
                        <input type="date" class="select-readonly form-control" id="tgl-faktur" name="tgl_faktur" />
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-6">
                        <label>Kode Customer</label>
                        <select class="form-control select-readonly" id="kode-customer" name="kode_customer">
                            <option value="">Pilih customer</option>
                            @foreach ($customer as $customers)
                                <option value="{{ $customers->kode_customer }}"
                                    data-nama="{{ $customers->nama_customer }}">
                                    {{ strtoupper($customers->kode_customer . ' - ' . $customers->nama_customer) }}
                                </option>
                            @endforeach
                        </select>

                    </div>

                    <div class="col-md-6">
                        <label>Jenis Transaksi</label>
                        <select class="form-control select-readonly" name="kode_jenis_transaksi" id="jenis-transaksi">
                            <option value="">Pilih jenis transaksi</option>
                            @foreach ($jenisTransaksi as $jenis)
                                <option value="{{ $jenis->kode_jenis_transaksi }}">
                                    {{ strtoupper($jenis->nama_jenis_transaksi) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- D: Detail Input Barang --}}
        <div class="card mb-4" id="detail" style="display: none">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                    <div>
                        <button id="btn-input" type="button" class="btn btn-light btn-sm" disabled>
                            Input
                        </button>
                        <button id="btn-hapus" type="button" class="btn btn-light btn-sm" disabled>
                            Hapus
                        </button>
                        <button id="btn-simpan" type="button" class="btn btn-light btn-sm" disabled>
                            Simpan
                        </button>
                        <button id="btn-batal" type="button" class="btn btn-light btn-sm" disabled>
                            Batal
                        </button>
                        <button id="btn-header" type="button" class="btn btn-light btn-sm">
                            Header
                        </button>
                    </div>
                </div>
                <div class="form-row d-flex flex-wrap mb-3 align-items-end">
                    <div class="col-auto mb-3" style="min-width: 160px; max-width: 180px;">
                        <label for="kode-select">Kode Barang</label>
                        <select name="kode-barang" id="kode-select" class="form-control select2" readonly>
                            <option value=""></option>
                        </select>
                    </div>
                    <div class="col-auto mb-3" style="min-width: 160px; max-width: 180px;">
                        <label for="nama-barang">Nama Barang</label>
                        <input type="text" class="form-control" name="nama_barang" id="nama-barang" readonly />
                    </div>
                    <div class="col-auto mb-3" style="min-width: 140px; max-width: 160px;">
                        <label for="harga-barang">Harga Barang</label>
                        <input type="text" class="form-control text-right currency harga-barang"
                            name="harga_barang" id="harga-barang" readonly />
                    </div>

                    <!-- QTY -->
                    <div class="col-auto mb-3" style="min-width: 100px; max-width: 120px;">
                        <label for="qty">QTY</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <button class="btn btn-outline-secondary btn-minus px-2" type="button">-</button>
                            </div>
                            <input type="text" class="form-control text-right px-1 qty select-readonly"
                                name="qty" id="qty" value="0" />
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary btn-plus px-2" type="button">+</button>
                            </div>
                        </div>
                    </div>

                    <!-- Diskon -->
                    <div class="col-auto mb-3" style="min-width: 100px; max-width: 120px;">
                        <label for="diskon">Diskon %</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <button class="btn btn-outline-secondary btn-minus-diskon px-2"
                                    type="button">-</button>
                            </div>
                            <input type="text"
                                class="form-control text-right px-1 qtyPercent diskon select-readonly" name="diskon"
                                id="diskon" value="0" />
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary btn-plus-diskon px-2"
                                    type="button">+</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-auto mb-3" style="min-width: 120px; max-width: 140px;">
                        <label for="bruto">Bruto</label>
                        <input type="text" class="form-control text-right currency" name="bruto" id="bruto"
                            readonly />
                    </div>
                    <div class="col-auto mb-3" style="min-width: 120px; max-width: 140px;">
                        <label for="jumlah">Jumlah</label>
                        <input type="text" class="form-control text-right currency" name="jumlah" id="jumlah"
                            readonly />
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4" id="tabel-transaksi-barang" style="display: none">
            <div class="card-body">
                <div class="form-row mb-3">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="thead-light">
                            <tr>
                                <th>N0 FAKTUR</th>
                                <th>KODE BARANG</th>
                                <th>NAMA BARANG</th>
                                <th>HARGA</th>
                                <th>QTY</th>
                                <th>DISKON</th>
                                <th>BRUTO</th>
                                <th>JUMLAH</th>
                            </tr>
                        </thead>
                        <tbody id="detail-table-body"></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="hidden-input-container">
        </div>
        {{-- H - TOT: Footer Total --}}
        <div class="card" id="footer" style="display: none">
            <div class="card-body">
                <div class="row text-right">
                    <div class="col-md-9"><strong>TOTAL BRUTO</strong></div>
                    <div class="col-md-3">
                        <input type="hidden" name="total_bruto" id="hidden-total-bruto" />
                        <input type="text" class="form-control text-right currency" id="total-bruto" readonly />
                    </div>

                    <div class="col-md-9"><strong>TOTAL DISKON</strong></div>
                    <div class="col-md-3">
                        <input type="hidden" name="total_diskon" id="hidden-total-diskon" />
                        <input type="text" class="form-control text-right currency" id="total-diskon" readonly />
                    </div>
                    <div class="col-md-9"><strong>TOTAL JUMLAH</strong></div>
                    <div class="col-md-3">
                        <input type="hidden" name="total_jumlah" id="hidden-total-jumlah" />
                        <input type="text" class="form-control text-right currency" id="total-jumlah" readonly />
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- Modal -->
<div class="modal fade" id="modalPreview" tabindex="-1" role="dialog" aria-labelledby="modalPreviewLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPreviewLabel">Preview Faktur Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="preview-content">
                <!-- Konten preview akan dimasukkan di sini -->
            </div>
        </div>
    </div>
</div>
<iframe id="print-frame" style="display: none;"></iframe>



@endsection
@push('js')
<script>
    $(document).ready(function() {

        document.getElementById('kode-customer').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const kode = selectedOption.value.toUpperCase();
            selectedOption.text = kode;
        });



        $(document).on('click', '.btn-plus', function() {
            const input = $(this).closest('.input-group').find('.qty')[0];
            if ($(input).hasClass('select-readonly')) {
                return; // Batalkan klik jika input readonly
            }
            const an = AutoNumeric.getAutoNumericElement(input);
            an.set(an.getNumber() + 1);
            calculate();
        });

        $(document).on('click', '.btn-minus', function() {
            const input = $(this).closest('.input-group').find('.qty')[0];
            if ($(input).hasClass('select-readonly')) {
                return; // Batalkan klik jika input readonly
            }
            const an = AutoNumeric.getAutoNumericElement(input);
            const newVal = Math.max(1, an.getNumber() - 1); // min 1
            an.set(newVal);
            calculate();
        });

        $(document).on('click', '.btn-plus-diskon', function() {
            const input = $(this).closest('.input-group').find('#diskon')[0];
            if ($(input).hasClass('select-readonly')) {
                return; // Batalkan klik jika input readonly
            }
            const an = AutoNumeric.getAutoNumericElement(input);
            an.set(an.getNumber() + 1);
            calculate();
        });

        $(document).on('click', '.btn-minus-diskon', function() {
            const input = $(this).closest('.input-group').find('#diskon')[0];
            if ($(input).hasClass('select-readonly')) {
                return; // Batalkan klik jika input readonly
            }
            const an = AutoNumeric.getAutoNumericElement(input);
            const newVal = Math.max(0, an.getNumber() - 1); // Batas bawah 0%
            an.set(newVal);
            calculate();
        });
        initAutoNumeric("#bruto", "currency");
        initAutoNumeric("#jumlah", "currency");
        initAutoNumeric("#total-bruto", "currency");
        initAutoNumeric("#total-jumlah", "currency");
        initAutoNumeric("#total-diskon", "currency");
        $('#kode-select').select2({
            placeholder: 'Ketik untuk mencari kode barang...',
            allowClear: true,
            ajax: {
                url: '{{ route('barang.search') }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                        _token: '{{ csrf_token() }}' // Tambahkan token CSRF
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            const kode = item.kode_barang.toUpperCase();
                            return {
                                id: item.kode_barang,
                                text: kode,
                                data: item
                            };
                        })
                    };
                },
                cache: true
            }
        }).on('select2:select', function(e) {
            unsetSelectReadonly("qty");
            unsetSelectReadonly("diskon");
            toggleButtons(['#btn-batal'], true); // enable
            toggleButtons(['#btn-input'], true); // enable
            const data = e.params.data.data;

            // Isi input nama barang dan harga
            $('#nama-barang').val(toTitleCase(data.nama_barang));
            initAutoNumeric("#harga-barang", "currency").set(data.harga_barang);
        });
        $('#qty, #diskon').on('change', function() {
            calculate();
        });

        const $btnInput = $('#btn-input');
        const $btnHapus = $('#btn-hapus');
        const $btnSimpan = $('#btn-simpan');
        const $btnBatal = $('#btn-batal');
        const $btnHeader = $('#btn-header');



        // Saat tombol BATAL ditekan
        $btnBatal.on('click', function() {
            resetFormDetail();
            toggleButtons(['#btn-batal'], false);

        });

        // Saat tombol SIMPAN ditekan
        $btnInput.on('click', function() {
            const qty = AutoNumeric.getNumber('#qty');
            const diskon = AutoNumeric.getNumber('#diskon');
            const kodeSelect = $('#kode-select').val();
            // Tambahkan validasi isian form

            if (kodeSelect && qty > 0 && diskon >= 0) {
                simpanDetailBarang();
                $('#kode-select').val(null).trigger('change');
                AutoNumeric.getAutoNumericElement("#qty")?.set('');
                AutoNumeric.getAutoNumericElement("#diskon")?.set('');
                $('#nama-barang').val('');
                $('#harga-barang').val('');
                $('#bruto').val('');
                $('#jumlah').val('');
                AutoNumeric.getAutoNumericElement("#harga-barang")?.set('');
                AutoNumeric.getAutoNumericElement("#bruto")?.set('');
                AutoNumeric.getAutoNumericElement("#jumlah")?.set('');
                toggleButtons(['#btnSimpan'], true);
                toggleButtons(['#btn-input', 'btn-batal'], false);
                setSelectReadonly("qty");
                setSelectReadonly("diskon");

            } else if (!$('#qty').val() || parseFloat($('#qty').val()) <= 0 || isNaN(parseFloat($(
                    '#qty').val()))) {
                Swal.fire({
                    title: 'Error Validasi',
                    html: 'Quantity harus diisi dan lebih besar dari 0',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } else if (!$('#diskon').val() || parseFloat($('#diskon').val()) < 0 || isNaN(parseFloat($(
                    '#diskon').val()))) {
                Swal.fire({
                    title: 'Error Validasi',
                    html: 'Diskon tidak boleh kurang dari 0',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
            $('#footer').show();

        });

        // Saat tombol HEADER ditekan
        $btnHeader.on('click', function() {
            $('#header').toggle();
            toggleButtons(['#btnEdit', '#btnBatal', '#btnCari', '#btnPreview', "#btnPrint"], true);
            $btnEditHeader.on('click', function() {
                $('#form_mode').val('create');
                toggleButtons(['#btnSimpan'], false);
                $('#no-faktur, #tgl-faktur, #kode-customer, #jenis-transaksi').on('change',
                    function() {
                        const filled = $('#no-faktur').val() && $('#tgl-faktur').val() && $(
                            '#kode-customer').val() && $('#jenis-transaksi').val();
                        toggleButtons(['#btnSimpan'], filled);

                    });
            })

        });

        const $btnInputHeader = $('#btnInput');
        const $no_faktur = $('#no-faktur');
        const $btnEditHeader = $('#btnEdit');
        const $btnSimpanHeader = $('#btnSimpan');
        const $btnHapusHeader = $('#btnHapus');

        const $btnCariHeader = $('#btnCari');
        const $btnBatalHeader = $('#btnBatal');
        const $btnPrintHeader = $('#btnPrint');
        const $btnPreviewHeader = $('#btnPreview');
        const $btnCSVHeader = $('#btnCSV');

        $('#btnBatal').on('click', function() {
            const mode = $(this).data('mode');

            if (mode === 'edit') {
                // Kembalikan semua input ke nilai awal
                $('#no-faktur').val(originalData.header.no_faktur);
                $('#tgl-faktur').val(originalData.header.tgl_faktur);
                $('#kode-customer').val(originalData.header.kode_customer).trigger('change');
                $('#jenis-transaksi').val(originalData.header.kode_jenis_transaksi).trigger('change');
                renderDetailBarang(originalData.detail_penjualan);


                // Reset tampilan dan readonly jika perlu
                setSelectReadonly("tgl-faktur");
                document.getElementById('no-faktur').setAttribute('readonly', true);
                setSelectReadonly("kode-customer");
                setSelectReadonly("jenis-transaksi");

                // Kembalikan tombol ke kondisi awal
                toggleButtons(["#btnInput", "#btnHapus", "#btnSimpan", "#btnBatal"], false);
            } else {
                resetHeader();
                resetFormDetail();
                toggleButtons(['#btnInput', '#btnEdit'], false); // enable
                setSelectReadonly("tgl-faktur");
                document.getElementById('no-faktur').removeAttribute('readonly', 'readonly');

                setSelectReadonly("kode-customer");
                $('#detail').css('display', 'none');
                $('#footer').css('display', 'none');
                $('#tabel-transaksi-barang').css('display', 'none');
                setSelectReadonly("jenis-transaksi");
            }

            // Setelah selesai, reset mode
            $(this).data('mode', null);
        });





        function setSelectReadonly(selectId) {
            const el = document.getElementById(selectId);
            el.classList.add("select-readonly");
        }

        function unsetSelectReadonly(selectId) {
            // Menghapus kelas select-readonly pada elemen
            $("#" + selectId).removeClass('select-readonly');

            // Jika elemen adalah select, hilangkan atribut readonly
            $("#" + selectId).prop('readonly', false);

            // Jika elemen adalah input text, hilangkan readonly atau disable
            $("#" + selectId).prop('disabled', false);
        }

        function toggleButtons(buttonSelectors, enable) {
            buttonSelectors.forEach(function(selector) {
                $(selector).prop('disabled', !enable);
            });
        }

        $no_faktur.on('change', function() {
            unsetSelectReadonly("kode-customer");
            unsetSelectReadonly("jenis-transaksi");
            unsetSelectReadonly("tgl-faktur");
            toggleButtons(['#btnInput', '#btnBatal'], true); // disable
            // toggleButtons(['#btnEdit'], true); // enable


        });


        function loadDataTransaksi(data) {
            renderDetailHTML();
            renderFooterHTML();
            originalData = {
                header: {
                    no_faktur: data.no_faktur,
                    tgl_faktur: data.tgl_faktur,
                    kode_customer: data.kode_customer,
                    kode_jenis_transaksi: data.kode_jenis_transaksi,
                },
                detail_penjualan: data.detail_penjualan || [],
                footer: {
                    total_bruto: data.total_bruto,
                    total_diskon: data.total_diskon,
                    total_jumlah: data.total_jumlah
                }
            };
            // Isi ulang input form
            document.querySelector('input[name="no_faktur"]').value = data.no_faktur;
            document.querySelector('input[name="no_faktur_lama"]').value = data
                .no_faktur; // <== ini yang penting
            document.querySelector('input[name="tgl_faktur"]').value = data.tgl_faktur;
            document.querySelector('select[name="kode_customer"]').value = data.kode_customer;
            document.querySelector('select[name="kode_jenis_transaksi"]').value = data.kode_jenis_transaksi;
            $('#no-faktur').val(data.no_faktur);
            $('#tgl-faktur').val(data.tgl_faktur);
            $('#kode-customer').val(data.kode_customer).trigger('change');
            $('#jenis-transaksi').val(data.kode_jenis_transaksi).trigger('change');

            renderDetailBarang(originalData.detail_penjualan);
            $('#total-bruto').val(data.total_bruto);
            $('#total-diskon').val(data.total_diskon);
            $('#total-jumlah').val(data.total_jumlah);
            calculateFooter();
        }

        function renderDetailBarang(data) {
            if (!Array.isArray(data)) {
                console.error('Data bukan array atau data kosong!');
                return; // Keluar dari fungsi jika data tidak sesuai
            }

            const tbody = $('#detail-table-body');
            const hiddenContainer = $('#hidden-input-container');

            tbody.empty();
            hiddenContainer.empty();


            data.forEach((item, index) => {

                if (item && item.harga && item.qty) {
                    const bruto = item.harga * item.qty;
                    const jumlahDiskon = (item.diskon / 100) * bruto;
                    const total = bruto - jumlahDiskon;
                    tbody.append(`
                <tr data-kode="${item.kode_barang}" data-diskon="${item.diskon}" data-qty="${item.qty}" data-bruto="${bruto}" data-jumlah="${total}" data-nama="${item.nama_barang}" data-harga="${item.harga}">
                    <td>${item.no_faktur}</td>
                    <td>${item.kode_barang}</td>
                    <td>${item.nama_barang}</td>
                    <td class="text-right harga currency">${item.harga}</td>
                    <td class="text-right qty">${item.qty}</td>
                    <td class="text-right diskon qtyPercent">${item.diskon}</td>
                    <td class="text-right bruto currency">${bruto}</td>
                    <td class="text-right jumlah currency">${total}</td>
                </tr>
            `);
                    // Tambahkan hidden input
                    const hiddenInputs = `
                <div class="detail-barang">
                    <input type="hidden" name="detail[${index}][kode_barang]" value="${item.kode_barang}">
                    <input type="hidden" name="detail[${index}][harga]" value="${item.harga}">
                    <input type="hidden" name="detail[${index}][qty]" value="${item.qty}">
                    <input type="hidden" name="detail[${index}][diskon]" value="${item.diskon}">
                </div>
            `;
                    hiddenContainer.append(hiddenInputs);


                } else {
                    console.warn('Item data tidak lengkap:', item);
                }
            });
            initAutoNumericAll();
            initAutoNumeric(".qty", "qty");
            initAutoNumeric(".diskon", "qtyPercent");
            initAutoNumeric(".harga", "currency");
            initAutoNumeric(".bruto", "currency");
            initAutoNumeric(".jumlah", "currency");
            calculateFooter();
        }



        $btnInputHeader.on('click', function() {
            $('#btnBatal').data('mode', 'input');
            // Validasi sederhana, sesuaikan dengan kebutuhanmu
            if ($no_faktur.val() && $('#tgl-faktur').val() && $('#kode-customer').val() && $(
                    '#jenis-transaksi').val()) {
                // Tampilkan bagian detail
                $('#header').hide();
                $('#detail').show();
                $('#tabel-transaksi-barang').show();
                // Nonaktifkan input header agar tidak diubah lagi
                $('#no-faktur').prop('readonly', true);
                setSelectReadonly("tgl-faktur");
                setSelectReadonly("kode-customer");
                setSelectReadonly("jenis-transaksi");
                toggleButtons(['#btnInput'], false);
                toggleButtons(['#btnCari'], false);
                toggleButtons(['#btnBatal'], false);
            } else if (!$no_faktur.val()) {
                Swal.fire({
                    title: 'Error Validasi',
                    html: 'No Faktur wajib diisi!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } else if (!$('#tgl-faktur').val()) {
                Swal.fire({
                    title: 'Error Validasi',
                    html: 'Tanggal Faktur wajib diisi!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } else if (!$('#kode-customer').val()) {
                Swal.fire({
                    title: 'Error Validasi',
                    html: 'Kode Customer wajib diisi!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } else if (!$('#jenis-transaksi').val()) {
                Swal.fire({
                    title: 'Error Validasi',
                    html: 'Jenis Transaksi wajib dipilih!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });

        function resetFormDetail() {
            $('#kode-select').val(null).trigger('change');
            $('#qty').val(1);
            $('#diskon').val(0);
            $('#nama-barang').val('');
            AutoNumeric.getAutoNumericElement("#harga-barang")?.set('');
            AutoNumeric.getAutoNumericElement("#bruto")?.set('');
            AutoNumeric.getAutoNumericElement("#jumlah")?.set('');

            // reset footer
            AutoNumeric.getAutoNumericElement("#total-bruto")?.set('');
            AutoNumeric.getAutoNumericElement("#total-diskon")?.set('');
            AutoNumeric.getAutoNumericElement("#total-jumlah")?.set('');

            $('#hidden-total-bruto').val('');
            $('#hidden-total-diskon').val('');
            $('#hidden-total-jumlah').val('');
            $('#detail-table-body').empty();
            $('#hidden-input-container').empty();

        }

        function resetHeader() {
            $('#no-faktur').val(null).trigger('change');
            $('#tgl-faktur').val('');
            $('#kode-customer').val('');
            $('#jenis-transaksi').val('');
        }

        function simpanDetailBarang() {
            const index = $('.detail-barang').length;
            const kodeBarang = $('#kode-select').val().toUpperCase();
            const namaBarang = $('#nama-barang').val();
            const harga = AutoNumeric.getNumber('#harga-barang');
            const qty = AutoNumeric.getNumber('#qty');
            const diskon = AutoNumeric.getNumber('#diskon');
            const bruto = harga * qty;
            const jumlahDiskon = (diskon / 100) * bruto;
            const total = bruto - jumlahDiskon;
            const noFaktur = $('#no-faktur').val().toUpperCase();
            AutoNumeric.getAutoNumericElement("#bruto")?.set(bruto);
            AutoNumeric.getAutoNumericElement("#jumlah")?.set(total);
            const row =
                `
                    <tr>
                        <td>${noFaktur}</td>
                        <td>${kodeBarang}</td>
                        <td>${namaBarang}</td>
                        <td class="text-right currency harga">${harga}</td>
                        <td class="text-right qty">${qty}</td>
                        <td class="text-right diskon qtyPercent">${diskon}</td>
                        <td class="text-right bruto currency">${bruto}</td>
                        <td class="text-right jumlah currency">${total}</td>
                    </tr>
                `;

            $('#detail-table-body').append(row);
            const hiddenInputs =
                `
                    <div class="detail-barang">
                        <input type="hidden" name="detail[${index}][kode_barang]" value="${kodeBarang}">
                        <input type="hidden" name="detail[${index}][harga]" value="${harga}">
                        <input type="hidden" name="detail[${index}][qty]" value="${qty}">
                        <input type="hidden" name="detail[${index}][diskon]" value="${diskon}">
                    </div>
                `;
            $('#hidden-input-container').append(hiddenInputs);

            initAutoNumericAll();
            calculateFooter();
        }

        function toTitleCase(str) {
            return str.replace(/\w\S*/g, function(txt) {
                return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
            });
        }

        function calculateFooter() {
            let totalBruto = 0;
            let totalDiskon = 0;

            $('#detail-table-body tr').each(function() {
                const harga = parseFloat($(this).find('.harga').text()
                    .replace('Rp', '') // Hapus simbol "Rp"
                    .replace(/\./g, '') // Hapus pemisah ribuan (titik)
                    .replace(',', '.') // Ganti koma desimal dengan titik
                );
                const qty = parseFloat($(this).find('.qty').text());
                const diskon = parseFloat($(this).find('.diskon').text().replace('%', ''));
                const bruto = harga * qty;
                const potongan = (diskon / 100) * bruto;

                totalBruto += bruto;
                totalDiskon += potongan;
            });



            const totalJumlah = totalBruto - totalDiskon;
            AutoNumeric.getAutoNumericElement("#total-bruto")?.set(totalBruto);
            AutoNumeric.getAutoNumericElement("#total-diskon")?.set(totalDiskon);
            AutoNumeric.getAutoNumericElement("#total-jumlah")?.set(totalJumlah);

            document.getElementById('hidden-total-bruto').value = totalBruto;
            document.getElementById('hidden-total-diskon').value = totalDiskon;
            document.getElementById('hidden-total-jumlah').value = totalJumlah;

        }

        function calculate() {
            const harga = AutoNumeric.getNumber("#harga-barang"); // ambil nilai numerik asli
            const qty = AutoNumeric.getNumber("#qty");
            const discount = AutoNumeric.getNumber("#diskon");

            const bruto = harga * qty;
            const jumlahDiskon = (discount / 100) * bruto;
            const total = bruto - jumlahDiskon;


            // Format angka hasil jika ingin ditampilkan rapi
            AutoNumeric.getAutoNumericElement("#bruto")?.set(bruto);
            AutoNumeric.getAutoNumericElement("#jumlah")?.set(total);
        }

        $('#form-penjualan').submit(function(e) {
            e.preventDefault();

            calculateFooter();

            let formData = $(this).serialize();
            const formMode = $('#form_mode').val();
            const noFaktur = $('#no-faktur').val();
            let url = '/penjualan/save'; // Default untuk store (create)
            let method = 'POST'; // Default method

            if (formMode === 'update') {
                url = `/penjualan/update/${noFaktur}`; // Route untuk update penjualan
                method = 'PUT'; // Gunakan PUT untuk update
            }
            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses',
                        text: response.message,
                    }).then(() => {
                        window.location.href =
                            '/penjualan/create'; // redirect manual
                    });
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON.message;
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan',
                        text: errorMessage,
                    });
                }
            });
        });

        $('#btnCari').on('click', function() {
            $('#modalCari').modal('show');
        });


        $('#modalCari').on('shown.bs.modal', function() {
            $('#cariNoFaktur').select2({
                dropdownParent: $('#modalCari'), // agar dropdown tampil dalam modal
                placeholder: 'Cari No Faktur',
                ajax: {
                    url: '/penjualan/search',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(item => ({
                                id: item.no_faktur,
                                text: `${item.no_faktur} - ${item.nama_customer} (${item.tanggal})`
                            }))
                        };
                    }
                }
            });
        });
        let originalData = {};
        $('#modal-submit').on('click', function(e) {
            $('#form_mode').val('edit');

            e.preventDefault();
            const noFaktur = $('#cariNoFaktur').val();

            $.ajax({
                url: `/penjualan/${noFaktur}`,
                type: 'GET',
                success: function(res) {
                    window.dataDariDatabase = res;

                    $('#detail-table-body').empty();
                    $('#modalCari').modal('hide');
                    $('#tabel-transaksi-barang').show();
                    loadDataTransaksi(res);
                    toggleButtons(['#btnEdit', '#btnHapus', '#btnPrint', '#btnPreview',
                            '#btnCSV'
                        ],
                        true);
                    setSelectReadonly("no-faktur");
                    $('#footer').show();
                    $('#detail').show();
                    AutoNumeric.getAutoNumericElement('#total-bruto')?.set(res.total_bruto);
                    AutoNumeric.getAutoNumericElement('#total-diskon')?.set(res
                        .total_diskon);
                    AutoNumeric.getAutoNumericElement('#total-jumlah')?.set(res
                        .total_jumlah);
                },
                error: function() {
                    alert("Transaksi tidak ditemukan!");
                }

            });


            initAutoNumeric("#total-bruto", "currency");
            initAutoNumeric("#total-jumlah", "currency");
            initAutoNumeric("#total-diskon", "currency");


            $btnHapusHeader.on('click', function() {
                const noFaktur = $('#no-faktur').val();

                if (!noFaktur) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops!',
                        text: 'Tidak ada transaksi yang dipilih untuk dihapus.'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: `Transaksi ${noFaktur} akan dihapus.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/penjualan/${noFaktur}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                                    .attr(
                                        'content')
                            },
                            success: function(res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: res.message
                                }).then(() => {
                                    location
                                        .reload(); // atau reset form
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Gagal menghapus transaksi.'
                                });
                            }
                        });
                    }
                });
            });
        });


        $(document).on('click', '#btn-hapus', function() {
            if (selectedRow) {
                selectedRow.remove();
                selectedRow = null;
                calculateFooter();
                // Reset form detail jika perlu
                $('#kode-select').val(null).trigger('change');
                $('#nama-barang').val('');
                $('#qty').val(1);
                AutoNumeric.getAutoNumericElement('#diskon')?.set(0);
                AutoNumeric.getAutoNumericElement('#harga-barang')?.set('');


                AutoNumeric.getAutoNumericElement('#bruto')?.set('');
                AutoNumeric.getAutoNumericElement('#jumlah')?.set('');
                // Matikan tombol kembali jika tidak ada baris
                toggleButtons(['#btn-hapus', '#btn-input', '#btn-batal'], false);
            }
        });

        let selectedRow = null;
        $btnEditHeader.on('click', function() {
            // Saat klik "Edit" atau load data untuk update
            $('#form_mode').val('update');
            $('#btnBatal').data('mode', 'edit');
            toggleButtons(["#btnEdit", '#btnHapus'], false);
            toggleButtons(["#btnBatal", "#btnSimpan"], true);
            unsetSelectReadonly("no-faktur");
            unsetSelectReadonly("tgl-faktur");
            unsetSelectReadonly("kode-customer");
            unsetSelectReadonly("jenis-transaksi");

            $('#detail-table-body').on('click', 'tr', function() {
                selectedRow = $(this);
                toggleButtons(['#btn-input'], false);
                toggleButtons(['#btn-hapus'], true);

                // Ambil data yang terkait dengan tr
                const kode = $(this).data('kode');
                const nama = $(this).data('nama');
                const qty = $(this).data('qty');
                const diskon = $(this).data('diskon');
                const harga = $(this).data('harga');
                const bruto = $(this).data('bruto');
                const jumlah = $(this).data('jumlah');
                const newOption = new Option(kode, kode, true,
                    true); // text, value, defaultSelected, selected
                $('#kode-select').append(newOption).trigger('change');

                $('#kode-select').select2({
                    placeholder: 'Ketik untuk mencari kode barang...',
                    allowClear: true,
                    minimumInputLength: 1, // Minimal 1 karakter baru search
                    ajax: {
                        url: '{{ route('barang.search') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                search: params.term,
                                _token: '{{ csrf_token() }}' // Tambahkan token CSRF
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: $.map(data, function(item) {
                                    const kode = item.kode_barang
                                        .toUpperCase();
                                    return {
                                        id: item.kode_barang,
                                        text: kode,
                                        data: item
                                    };
                                })
                            };
                        },
                        cache: true
                    }
                }).on('select2:select', function(e) {
                    unsetSelectReadonly("qty");
                    unsetSelectReadonly("diskon");
                    toggleButtons(['#btn-batal'], true); // enable
                    toggleButtons(['#btn-input'], true); // enable
                    const data = e.params.data.data;

                    // Isi input nama barang dan harga
                    $('#nama-barang').val(toTitleCase(data.nama_barang));
                    initAutoNumeric("#harga-barang", "currency").set(data.harga_barang);
                });
                $('#kode-select, #qty, #diskon').on('change', function() {
                    toggleButtons(['#btn-simpan'], true);
                });
                $('.btn-plus, .btn-minus, .btn-plus-diskon, .btn-minus-diskon').on('click',
                    function() {
                        toggleButtons(['#btn-simpan'], true);
                    });
                $('#btn-simpan').on('click', function() {

                    const kodeBarangForm = $('#kode-select').val();
                    const namaBarangForm = $('#nama-barang').val();
                    const harga = AutoNumeric.getNumber('#harga-barang') || 0;
                    const qty = AutoNumeric.getNumber('#qty') || 0;
                    const diskon = AutoNumeric.getNumber('#diskon') || 0;
                    const noFaktur = $('#no-faktur').val();

                    const bruto = harga * qty;
                    const jumlahDiskon = (diskon / 100) * bruto;
                    const total = bruto - jumlahDiskon;

                    // Update baris yang sesuai di tabel
                    $('#detail-table-body tr').each(function() {
                        const $row = $(this);
                        if ($row.data('kode') === kodeBarangForm) {
                            $row.find('td:eq(0)').text(noFaktur);
                            $row.find('td:eq(1)').text(kodeBarangForm);
                            $row.find('td:eq(2)').text(namaBarangForm);
                            $row.find('.harga').text(harga);
                            AutoNumeric.getAutoNumericElement($row.find('.qty')[
                                0]).set(qty);
                            AutoNumeric.getAutoNumericElement($row.find(
                                '.diskon')[0]).set(diskon);

                            $row.find('.bruto').text(bruto);
                            $row.find('.jumlah').text(total);
                        }
                        $('#kode-select').val(null).trigger('change');
                        $('#qty').val(1);
                        $('#diskon').val(0);
                        $('#nama-barang').val('');
                        $('#harga-barang').val('');
                        $('#bruto').val('');
                        $('#jumlah').val('');
                        AutoNumeric.getAutoNumericElement("#harga-barang")?.set(
                            '');
                        AutoNumeric.getAutoNumericElement("#bruto")?.set('');
                        AutoNumeric.getAutoNumericElement("#jumlah")?.set('');
                        toggleButtons(['#btnSimpan'], true);
                        toggleButtons(['#btn-input', 'btn-batal'], false);
                        setSelectReadonly("qty");
                        setSelectReadonly("diskon");
                    });

                    // Sekarang kumpulkan semua data dari tabel
                    const updatedData = [];

                    $('#detail-table-body tr').each(function() {
                        const $row = $(this);
                        const hargaRaw = $row.find('.harga').text()
                            .replace('Rp', '').replace(/\./g, '').replace(',',
                                '.');

                        const qtyInput = $row.find('.qty')[0];
                        const diskonInput = $row.find('.diskon')[0];

                        const item = {
                            kode_barang: $row.find('td:eq(1)').text(),
                            no_faktur: $row.find('td:eq(0)').text(),
                            nama_barang: $row.find('td:eq(2)').text(),
                            harga: parseFloat(hargaRaw) || 0,
                            qty: AutoNumeric.getNumber(qtyInput),
                            diskon: AutoNumeric.getNumber(diskonInput),
                        };

                        const bruto = item.harga * item.qty;
                        const jumlahDiskon = (item.diskon / 100) * bruto;
                        const total = bruto - jumlahDiskon;

                        item.bruto = bruto;
                        item.total = total;

                        updatedData.push(item);

                    });

                    $('#hidden-input-container').empty();

                    // Tambahkan ulang data yang sudah diperbarui
                    updatedData.forEach((item, index) => {
                        const hiddenInputs = `
        <div class="detail-barang">
            <input type="hidden" name="detail[${index}][kode_barang]" value="${item.kode_barang}">
            <input type="hidden" name="detail[${index}][harga]" value="${item.harga}">
            <input type="hidden" name="detail[${index}][qty]" value="${item.qty}">
            <input type="hidden" name="detail[${index}][diskon]" value="${item.diskon}">
        </div>
    `;
                        $('#hidden-input-container').append(hiddenInputs);
                    });
                    initAutoNumeric(".harga", "currency");
                    initAutoNumeric(".bruto", "currency");
                    initAutoNumeric(".jumlah", "currency");
                    calculateFooter();
                });





                // Set data di input lain
                $('#nama-barang').val(nama);
                AutoNumeric.getAutoNumericElement('#harga-barang')?.set(harga);
                AutoNumeric.getAutoNumericElement('#bruto')?.set(bruto);
                AutoNumeric.getAutoNumericElement('#jumlah')?.set(jumlah);
                initAutoNumeric('#qty', 'qty');
                // Inisialisasi AutoNumeric untuk diskon (percent)
                initAutoNumeric('#diskon', 'qtyPercent');
                AutoNumeric.getAutoNumericElement('#qty')?.set(qty);
                AutoNumeric.getAutoNumericElement('#diskon')?.set(diskon);

                // Ubah elemen form agar dapat diedit
                unsetSelectReadonly("kode-select");
                unsetSelectReadonly("qty");
                unsetSelectReadonly("diskon");

                $('#qty, #diskon').on('change', function() {
                    calculate();
                });


            });

        });

        function renderDetailHTML() {
            $('#detail').html(
                `
                            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                    <div>
                        <button id="btn-input" type="button" class="btn btn-light btn-sm" disabled>
                            Input
                        </button>
                        <button id="btn-hapus" type="button" class="btn btn-light btn-sm" disabled>
                            Hapus
                        </button>
                        <button id="btn-simpan" type="button" class="btn btn-light btn-sm" disabled>
                            Simpan
                        </button>
                        <button id="btn-batal" type="button" class="btn btn-light btn-sm" disabled>
                            Batal
                        </button>
                        <button id="btn-header" type="button" class="btn btn-light btn-sm">
                            Header
                        </button>
                    </div>
                </div>
                <div class="form-row d-flex flex-wrap mb-3 align-items-end">
                    <div class="col-auto mb-3" style="min-width: 160px; max-width: 180px;">
                        <label for="kode-select">Kode Barang</label>
                        <select name="kode-barang" id="kode-select" class="form-control select2" readonly>
                            <option value=""></option>
                        </select>
                    </div>
                    <div class="col-auto mb-3" style="min-width: 160px; max-width: 180px;">
                        <label for="nama-barang">Nama Barang</label>
                        <input type="text" class="form-control" name="nama_barang" id="nama-barang" readonly />
                    </div>
                    <div class="col-auto mb-3" style="min-width: 140px; max-width: 160px;">
                        <label for="harga-barang">Harga Barang</label>
                        <input type="text" class="form-control text-right currency" name="harga_barang"
                            id="harga-barang" readonly />
                    </div>

                    <!-- QTY -->
                    <div class="col-auto mb-3" style="min-width: 100px; max-width: 120px;">
                        <label for="qty">QTY</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <button class="btn btn-outline-secondary btn-minus px-2" type="button">-</button>
                            </div>
                            <input type="text" class="form-control text-right px-1 qty select-readonly"
                                name="qty" id="qty" value="1" />
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary btn-plus px-2" type="button">+</button>
                            </div>
                        </div>
                    </div>

                    <!-- Diskon -->
                    <div class="col-auto mb-3" style="min-width: 100px; max-width: 120px;">
                        <label for="diskon">Diskon %</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <button class="btn btn-outline-secondary btn-minus-diskon px-2"
                                    type="button">-</button>
                            </div>
                            <input type="text" class="form-control text-right px-1 qtyPercent select-readonly"
                                name="diskon" id="diskon" value="0" />
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary btn-plus-diskon px-2"
                                    type="button">+</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-auto mb-3" style="min-width: 120px; max-width: 140px;">
                        <label for="bruto">Bruto</label>
                        <input type="text" class="form-control text-right currency" name="bruto" id="bruto"
                            readonly />
                    </div>
                    <div class="col-auto mb-3" style="min-width: 120px; max-width: 140px;">
                        <label for="jumlah">Jumlah</label>
                        <input type="text" class="form-control text-right currency" name="jumlah" id="jumlah"
                            readonly />
                    </div>
                </div>
            </div>                                                
`)

        }

        function renderFooterHTML() {
            $('#footer').html(
                `
            <div class="card-body">
                <div class="row text-right">
                    <div class="col-md-9"><strong>TOTAL BRUTO</strong></div>
                    <div class="col-md-3">
                        <input type="hidden" name="total_bruto" id="hidden-total-bruto" />
                        <input type="text" class="form-control text-right currency" id="total-bruto" readonly />
                    </div>

                    <div class="col-md-9"><strong>TOTAL DISKON</strong></div>
                    <div class="col-md-3">
                        <input type="hidden" name="total_diskon" id="hidden-total-diskon" />
                        <input type="text" class="form-control text-right currency" id="total-diskon" readonly />
                    </div>
                    <div class="col-md-9"><strong>TOTAL JUMLAH</strong></div>
                    <div class="col-md-3">
                        <input type="hidden" name="total_jumlah" id="hidden-total-jumlah" />
                        <input type="text" class="form-control text-right currency" id="total-jumlah" readonly />
                    </div>
                </div>
            </div>
            `
            )
        }
        $('#btnPreview').on('click', function() {
            let formMode = $('#form_mode').val();

            let details = [];
            if (formMode === 'create') {
                $('#detail-table-body tr').each(function() {
                    let noFaktur = $(this).find('td:eq(0)').text().trim();
                    let kodeBarang = $(this).find('td:eq(1)').text().trim();
                    let namaBarang = $(this).find('td:eq(2)').text().trim();
                    let harga = parseFloat($(this).find('td.harga').attr('value')) || 0;
                    let qty = parseFloat($(this).find('td.qty').attr('value')) || 0;
                    let diskon = parseFloat($(this).find('td.diskon').attr('value')) || 0;
                    let bruto = parseFloat($(this).find('td.bruto').attr('value')) || 0;
                    let jumlah = parseFloat($(this).find('td.jumlah').attr('value')) || 0;

                    details.push({
                        no_faktur: noFaktur,
                        kode_barang: kodeBarang,
                        nama_barang: namaBarang,
                        harga: harga,
                        qty: qty,
                        diskon: diskon,
                        bruto: bruto,
                        jumlah: jumlah
                    });
                });
            } else {
                details = window.dataDariDatabase?.detail_penjualan || [];
            }

            let formData = {
                no_faktur: $('#no-faktur').val(),
                tanggal: $('#tgl-faktur').val(),
                customer: {
                    kode_customer: $('#kode-customer').val(),
                    nama_customer: $('#kode-customer option:selected').data('nama')
                },
                jenis_transaksi: {
                    kode: $('#jenis-transaksi').val(),
                    nama: $('#jenis-transaksi option:selected').text()
                },
                detail: details,
                total_bruto: parseFloat($('#hidden-total-bruto').val()),
                total_diskon: parseFloat($('#hidden-total-diskon').val()),
                total_jumlah: parseFloat($('#hidden-total-jumlah').val())
            };
            $.ajax({
                url: '{{ route('penjualan.preview') }}',
                method: 'POST',
                data: {
                    data: formData,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Buat instance modal baru setiap kali
                    const modal = $('#modalPreview').clone().removeAttr('id');

                    // Isi konten modal
                    modal.find('.modal-content').html(response);

                    // Tambahkan event handler untuk cleanup
                    modal.on('hidden.bs.modal', function() {
                        modal.remove();
                        $('body').removeClass('modal-open');
                        $('.modal-backdrop').remove();
                    });

                    // Tampilkan modal
                    modal.modal('show');

                    // Simpan referensi modal
                    window.currentPreviewModal = modal;
                }
            });
        });

        $('#btnPrint').on('click', function(e) {
            e.preventDefault();
            let formMode = $('#form_mode').val();

            let details = [];
            if (formMode === 'create') {
                $('#detail-table-body tr').each(function() {
                    let noFaktur = $(this).find('td:eq(0)').text().trim();
                    let kodeBarang = $(this).find('td:eq(1)').text().trim();
                    let namaBarang = $(this).find('td:eq(2)').text().trim();
                    let harga = parseFloat($(this).find('td.harga').attr('value')) || 0;
                    let qty = parseFloat($(this).find('td.qty').attr('value')) || 0;
                    let diskon = parseFloat($(this).find('td.diskon').attr('value')) || 0;
                    let bruto = parseFloat($(this).find('td.bruto').attr('value')) || 0;
                    let jumlah = parseFloat($(this).find('td.jumlah').attr('value')) || 0;

                    details.push({
                        no_faktur: noFaktur,
                        kode_barang: kodeBarang,
                        nama_barang: namaBarang,
                        harga: harga,
                        qty: qty,
                        diskon: diskon,
                        bruto: bruto,
                        jumlah: jumlah
                    });
                });
            } else {
                details = window.dataDariDatabase?.detail_penjualan || [];
            }

            let formData = {
                no_faktur: $('#no-faktur').val(),
                tanggal: $('#tgl-faktur').val(),
                customer: {
                    kode_customer: $('#kode-customer').val(),
                    nama_customer: $('#kode-customer option:selected').data('nama')
                },
                jenis_transaksi: {
                    kode: $('#jenis-transaksi').val(),
                    nama: $('#jenis-transaksi option:selected').text()
                },
                detail: details,
                total_bruto: parseFloat($('#hidden-total-bruto').val()),
                total_diskon: parseFloat($('#hidden-total-diskon').val()),
                total_jumlah: parseFloat($('#hidden-total-jumlah').val())
            };

            // Mengirim data ke server untuk print tanpa preview
            $.ajax({
                url: '{{ route('penjualan.print') }}',
                method: 'POST',
                data: {
                    data: formData,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {

                    // Membuka jendela baru untuk print
                    const printWindow = window.open('', '_blank', 'width=1200,height=600');
                    printWindow.document.write(response); // Render halaman untuk pencetakan
                    printWindow.document.close();
                    printWindow.onload = function() {
                        printWindow
                            .print(); // Memanggil fungsi print setelah dokumen dimuat
                    };
                }

            });
        });

        $('#btnCSV').on('click', function(e) {
            e.preventDefault();

            const noFaktur = $('#cariNoFaktur').val(); // Mengambil nilai noFaktur dari select2

            if (noFaktur) {
                window.location.href = `/penjualan/export-csv/${noFaktur}`;
            } else {
                alert("Pilih No Faktur terlebih dahulu!");
            }
        });








    });
</script>
@endpush
