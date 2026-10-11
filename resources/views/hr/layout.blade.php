<!DOCTYPE html>
<html lang="zxx" class="app-skin-light minimenu">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="keyword" content="" />
    <meta name="author" content="flexilecode" />

    <title>Duralux || Dashboard</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets\images\logo-abb.jpg') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendors.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/daterangepicker.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/theme.min.css') }}" />
<meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')

    <style>
         @media (max-width: 1199.98px) {
            .nxl-content.without-header .main-content {
                position: relative;
            }

            .nxl-content.without-header .main-content .content-sidebar {
                position: absolute;
                top: 0;
                left: 0;
                z-index: 1026;
                max-width: 300px;
                opacity: 0;
                visibility: hidden;
                transform: translateX(-100%);
                transition: all 0.3s ease;
                box-shadow: none;
            }

            .nxl-content.without-header .main-content .content-sidebar.app-sidebar-open {
                opacity: 1;
                visibility: visible;
                transform: translateX(0);
                box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
            }

            .nxl-content.without-header .main-content .content-sidebar .content-sidebar-header,
            .nxl-content.without-header .main-content .content-sidebar .content-sidebar-body {
                position: relative;
                z-index: 2;
                background: #fff;
            }

            .content-sidebar-overlay {
                position: fixed;
                inset: 0;
                z-index: 1025;
                background: rgba(0, 0, 0, 0.2);
            }
        }
    </style>
    {{-- <style>
        body.modal-open .nxl-container,
        body.modal-open .nxl-header,
        body.modal-open .nxl-navigation,
        body.modal-open .page-header {
            filter: none !important;
        }
        @media (max-width: 1199.98px) {
            .nxl-content.without-header .main-content {
                position: relative;
            }

            .nxl-content.without-header .main-content .content-sidebar {
                position: absolute;
                top: 0;
                left: 0;
                z-index: 1026;
                max-width: 300px;
                opacity: 0;
                visibility: hidden;
                transform: translateX(-100%);
                transition: all 0.3s ease;
                box-shadow: none;
            }

            .nxl-content.without-header .main-content .content-sidebar.app-sidebar-open {
                opacity: 1;
                visibility: visible;
                transform: translateX(0);
                box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
            }

            .nxl-content.without-header .main-content .content-sidebar .content-sidebar-header,
            .nxl-content.without-header .main-content .content-sidebar .content-sidebar-body {
                position: relative;
                z-index: 2;
                background: #fff;
            }

            .content-sidebar-overlay {
                position: fixed;
                inset: 0;
                z-index: 1025;
                background: rgba(0, 0, 0, 0.2);
            }
        }
        /* Responsive sidebar header */
        @media (min-width: 1200px) {
            .nxl-container .nxl-content .main-content .content-sidebar .content-sidebar-header {
                position: fixed;
                top: 80px;
                width: 298px;
            }
        }

        @media (min-width: 768px) and (max-width: 1199px) {
            .nxl-container .nxl-content .main-content .content-sidebar .content-sidebar-header {
                position: fixed;
                top: 80px;
                width: 20%;
            }
        }

        @media (max-width: 767px) {
            .nxl-container .nxl-content .main-content .content-sidebar .content-sidebar-header {
                position: static;
                width: 100%;
            }
        }
        .nxl-container .nxl-content .main-content .content-sidebar {
            top: 75px;
        }

        .main-content .content-sidebar .content-sidebar-body .nxl-content-sidebar-item {
            position: fixed;
        }

        /* Fix for sidebar and content scrolling */
        .nxl-content.without-header {
            height: 100vh;
            overflow: hidden;
        }

        .nxl-content.without-header .main-content {
            height: 100%;
        }

        .nxl-content.without-header .content-sidebar {
            height: 100%;
            overflow: hidden;
        }

        .nxl-content.without-header .content-area {
            height: 100%;
            overflow: auto;
            position: relative;
            top: 95px;
        }

        .nxl-content.without-header .content-area-header {
            position: fixed;
            top: 80px;
            left: 0;
            /* left: 580px; */
            right: 0;
            z-index: 10;
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            width: auto;
            padding: 12px 20px;
        }

        /* Large screens */
        @media (min-width: 1200px) {
            .nxl-content.without-header .content-area-header {
                top: 80px;
                padding: 12px 24px;
            }
        }

        /* Tablet */
        @media (max-width: 991px) {
            .nxl-content.without-header .content-area-header {
                top: 70px;
                padding: 10px 16px;
            }
        }

        /* Mobile */
        @media (max-width: 767px) {
            .nxl-content.without-header .content-area-header {
                top: 60px;
                padding: 10px 12px;
            }

            .nxl-content.without-header .content-area-header h6 {
                font-size: 14px;
            }

            .nxl-content.without-header .content-area-header p {
                font-size: 11px;
            }
        }

        .nxl-content.without-header .content-area-body {
            overflow: visible;
            padding-top: 0;
        }
    </style> --}}
    @stack('styles')
</head>

<body>

    @include('elements.hrheader')
    @include('elements.hrsidebar')

    <!-- Content Wrapper. Contains page content -->
    <main class="nxl-container">
        @yield('content')
        <!-- [ Footer ] end -->
    </main>
    <script src="{{ asset('assets/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/circle-progress.min.js') }}"></script>

    <script src="{{ asset('assets/js/common-init.min.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard-init.min.js') }}"></script>

    <script src="{{ asset('assets/js/theme-customizer-init.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>

    @vite('resources/js/app.js')
    @stack('scripts')
</body>

</html>
