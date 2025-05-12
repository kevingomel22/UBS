<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'UBS')</title>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Load Select2 CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <!-- Jquery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!-- Auto Complete -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <link href="https://cdn.datatables.net/2.1.4/css/dataTables.dataTables.min.css" rel="stylesheet">

    @stack('styles') <!-- Untuk inject custom CSS dari halaman -->
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}">UBS</a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container py-4">
        @yield('content')
    </main>


    <script src="https://cdn.datatables.net/2.1.4/js/dataTables.min.js"></script>

    <!-- Popper.js + Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- AutoNumeric -->
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.6.0/dist/autoNumeric.min.js"></script>

    <!-- Inisialisasi AutoNumeric -->
    <script>
        function initAutoNumeric(id = '', type) {
            // Jika sudah ada AutoNumeric, hapus dulu instance-nya
            if (AutoNumeric.getAutoNumericElement(id)) {
                AutoNumeric.getAutoNumericElement(id).remove();
            }

            // Tentukan konfigurasi berdasarkan tipe
            let options = {};
            switch (type) {
                case "currency":
                    options = {
                        currencySymbol: "Rp ",
                        currencySymbolPlacement: "p",
                        decimalCharacter: ",",
                        digitGroupSeparator: ".",
                        decimalPlaces: 2,
                        allowDecimalPadding: "floats",
                        outputFormat: "number",
                        unformatOnSubmit: true
                    };
                    break;
                case "qty":
                    options = {
                        decimalCharacter: ".",
                        digitGroupSeparator: ",",
                        allowDecimalPadding: "floats",
                        outputFormat: "string",
                        unformatOnSubmit: true
                    };
                    break;
                case "qtyPercent":
                    options = {
                        alwaysAllowDecimalCharacter: true,
                        currencySymbol: "%",
                        currencySymbolPlacement: "s",
                        decimalCharacter: ",",
                        decimalPlacesRawValue: 2,
                        digitGroupSeparator: ".",
                        outputFormat: "string",
                        unformatOnSubmit: true
                    };
                    break;
            }

            // Inisialisasi baru
            return new AutoNumeric(id, options);
        }


        function initAutoNumericAll() {
            // document.addEventListener('DOMContentLoaded', function() {
            var currencyElements = document.querySelectorAll('.currency');
            var currencyQty = document.querySelectorAll('.qty');
            var currencyQtyPercent = document.querySelectorAll('.qtyPercent');

            currencyElements.forEach(function(element) {
                // Periksa apakah elemen tersebut sudah diinisialisasi sebelumnya
                if (!AutoNumeric.getAutoNumericElement(element)) {
                    new AutoNumeric(element, {
                        currencySymbol: "Rp ",
                        currencySymbolPlacement: "p",
                        decimalCharacter: ",",
                        digitGroupSeparator: ".",
                        decimalPlaces: 2,
                        allowDecimalPadding: "floats",
                        outputFormat: "number",
                        unformatOnSubmit: true
                    });
                }
            });

            currencyQty.forEach(function(element) {
                // Periksa apakah elemen tersebut sudah diinisialisasi sebelumnya
                if (!AutoNumeric.getAutoNumericElement(element)) {
                    new AutoNumeric(element, {
                        decimalCharacter: ".",
                        digitGroupSeparator: ",",
                        allowDecimalPadding: "floats",
                        outputFormat: "string",
                        unformatOnSubmit: true
                    });
                }
            });

            currencyQtyPercent.forEach(function(element) {
                // Periksa apakah elemen tersebut sudah diinisialisasi sebelumnya
                if (!AutoNumeric.getAutoNumericElement(element)) {
                    new AutoNumeric(element, {
                        alwaysAllowDecimalCharacter: true,
                        currencySymbol: "%",
                        currencySymbolPlacement: "s",
                        decimalCharacter: ",",
                        decimalPlacesRawValue: 2,
                        digitGroupSeparator: ".",
                        outputFormat: "string",
                        unformatOnSubmit: true
                    });
                }
            });

            document.body.addEventListener('focus', function(event) {
                if (event.target.classList.contains('currency')) {
                    // Periksa apakah elemen tersebut sudah diinisialisasi sebelumnya
                    if (!AutoNumeric.getAutoNumericElement(event.target)) {
                        new AutoNumeric(event.target, {

                            currencySymbolPlacement: "p",
                            decimalCharacter: ",",
                            digitGroupSeparator: ".",
                            decimalPlaces: 2,
                            allowDecimalPadding: "floats",
                            outputFormat: "number",
                            unformatOnSubmit: true
                        });
                    }
                }

                if (event.target.classList.contains('qty')) {
                    // Periksa apakah elemen tersebut sudah diinisialisasi sebelumnya
                    if (!AutoNumeric.getAutoNumericElement(event.target)) {
                        new AutoNumeric(event.target, {
                            decimalCharacter: ".",
                            digitGroupSeparator: ",",
                            allowDecimalPadding: "floats",
                            outputFormat: "string",
                            unformatOnSubmit: true
                        });
                    }
                }

                if (event.target.classList.contains('qtyPercent')) {
                    // Periksa apakah elemen tersebut sudah diinisialisasi sebelumnya
                    if (!AutoNumeric.getAutoNumericElement(event.target)) {
                        new AutoNumeric(event.target, {
                            alwaysAllowDecimalCharacter: true,
                            currencySymbol: "%",
                            currencySymbolPlacement: "s",
                            decimalCharacter: ",",
                            decimalPlacesRawValue: 2,
                            digitGroupSeparator: ".",
                            outputFormat: "string",
                            unformatOnSubmit: true
                        });
                    }
                }
            }, true);
            // });
        }


        $(document).ready(function() {
            initAutoNumericAll();
        });
    </script>
    @stack('js')

    @stack('scripts') <!-- Untuk inject JS tambahan dari halaman -->
</body>

</html>
