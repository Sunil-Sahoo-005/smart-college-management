<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="keyword" content="" />
    <meta name="author" content="flexilecode" />

    <title>Duralux || Dashboard</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/favicon.ico') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendors.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/daterangepicker.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/theme.min.css') }}" />

    @vite('resources/css/app.css')

    <style>
        body.modal-open .nxl-container,
        body.modal-open .nxl-header,
        body.modal-open .nxl-navigation,
        body.modal-open .page-header {
            filter: none !important;
        }
    </style>

</head>

<body>

    @include('elements.teacherheader')
    @include('elements.teachersidebar')

    <!-- Content Wrapper. Contains page content -->
    <main class="nxl-container">
        @yield('content')

        <footer class="footer">
            <p class="fs-11 text-muted fw-medium text-uppercase mb-0 copyright">
                <span>Copyright ©</span>
                <script>
                    document.write(new Date().getFullYear());
                </script>
            </p>
            <p><span>By: <a target="_blank" href="https://wrapbootstrap.com/user/theme_ocean"
                        target="_blank">theme_ocean</a></span> • <span>Distributed by: <a target="_blank"
                        href="https://themewagon.com" target="_blank">ThemeWagon</a></span></p>
            <div class="d-flex align-items-center gap-4">
                <a href="javascript:void(0);" class="fs-11 fw-semibold text-uppercase">Help</a>
                <a href="javascript:void(0);" class="fs-11 fw-semibold text-uppercase">Terms</a>
                <a href="javascript:void(0);" class="fs-11 fw-semibold text-uppercase">Privacy</a>
            </div>
        </footer>
        <!-- [ Footer ] end -->
    </main>

    <script src="{{ asset('assets/vendors/js/vendors.min.js') }}"></script>

    <script src="{{ asset('assets/vendors/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/circle-progress.min.js') }}"></script>

    <script src="{{ asset('assets/js/common-init.min.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard-init.min.js') }}"></script>

    <script src="{{ asset('assets/js/theme-customizer-init.min.js') }}"></script>

    @vite('resources/js/app.js')

</body>

</html>
