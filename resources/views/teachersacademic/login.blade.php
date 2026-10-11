<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="keyword" content="">
    <meta name="author" content="theme_ocean">

    <title>Duralux || Login Creative</title>

    {{-- <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico">

    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">

    <link rel="stylesheet" type="text/css" href="assets/vendors/css/vendors.min.css">

    <link rel="stylesheet" type="text/css" href="assets/css/theme.min.css"> --}}
 <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/favicon.ico') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendors.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/daterangepicker.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/theme.min.css') }}" />
</head>

<body>

    <main class="auth-creative-wrapper">
        <div class="auth-creative-inner">
            <div class="creative-card-wrapper">
                <div class="card my-4 overflow-hidden" style="z-index: 1">
                    <div class="row flex-1 g-0">
                        <div class="col-lg-6 h-100 my-auto order-1 order-lg-0">
                            <div
                                class="wd-50 bg-white p-2 rounded-circle shadow-lg position-absolute translate-middle top-50 start-50 d-none d-lg-block">
                                <img src="{{ asset('assets/images/logo-abbr.png') }}" alt="" class="img-fluid">
                            </div>
                            <div class="creative-card-body card-body p-sm-5">
                                <h2 class="fs-20 fw-bolder mb-4">Login</h2>
                                <h4 class="fs-13 fw-bold mb-2">Login to your account</h4>
                                <p class="fs-12 fw-medium text-muted">Thank you for get back <strong>Nelel</strong> web
                                    applications, let's access our the best recommendation for you.</p>
                                <form action="{{ route('teacher.teachersacademic.logins') }}" class="w-100 mt-4 pt-2" method="POST">
                                    @csrf

                                    <div class="mb-4">
                                        <input type="text" class="form-control" name="username"
                                            placeholder="Email or Username" value="prasanta" required>
                                    </div>

                                    <div class="mb-3">
                                        <input type="password" class="form-control" name="password"
                                            placeholder="Password" value="12345678" required>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="rememberMe"
                                                    name="remember">
                                                <label class="custom-control-label c-pointer" for="rememberMe">Remember
                                                    Me</label>
                                            </div>
                                        </div>
                                        <div>
                                            {{-- <a href="{{ route('password.request') }}" class="fs-11 text-primary">Forget password?</a> --}}
                                        </div>
                                    </div>

                                    <div class="mt-5">
                                        <button type="submit" class="btn btn-lg btn-primary w-100">Login</button>
                                    </div>
                                </form>

                            </div>
                        </div>
                        <div class="col-lg-6 bg-primary order-0 order-lg-1">
                            <div class="h-100 d-flex align-items-center justify-content-center">
                                <img src="{{ asset('assets/images/auth/auth-user.png') }}" alt="" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <div class="theme-customizer">
        <div class="customizer-handle">
            <a href="javascript:void(0);" class="cutomizer-open-trigger bg-primary">
                <i class="feather-settings"></i>
            </a>
        </div>
        <div class="customizer-sidebar-wrapper">
            <div
                class="customizer-sidebar-header px-4 ht-80 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Theme Settings</h5>
                <a href="javascript:void(0);" class="cutomizer-close-trigger d-flex">
                    <i class="feather-x"></i>
                </a>
            </div>
            <div class="customizer-sidebar-body position-relative p-4" data-scrollbar-target="#psScrollbarInit">
                <!--! BEGIN: [Skins] !-->
                <div class="position-relative px-3 pb-3 pt-4 mt-3 mb-5 border border-gray-2 theme-options-set">
                    <label
                        class="py-1 px-2 fs-8 fw-bold text-uppercase text-muted text-spacing-2 bg-white border border-gray-2 position-absolute rounded-2 options-label"
                        style="top: -12px">Skins</label>
                    <div class="row g-2 theme-options-items app-skin" id="appSkinList">
                        <div class="col-6 text-center position-relative single-option light-button active">
                            <input type="radio" class="btn-check" id="app-skin-light" name="app-skin" value="1"
                                data-app-skin="app-skin-light">
                            <label
                                class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label"
                                for="app-skin-light">Light</label>
                        </div>
                        <div class="col-6 text-center position-relative single-option dark-button">
                            <input type="radio" class="btn-check" id="app-skin-dark" name="app-skin" value="2"
                                data-app-skin="app-skin-dark">
                            <label
                                class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label"
                                for="app-skin-dark">Dark</label>
                        </div>
                    </div>
                </div>
               
            </div>
            <div class="customizer-sidebar-footer px-4 ht-60 border-top d-flex align-items-center gap-2">
                <div class="flex-fill w-50">
                    <a href="javascript:void(0);" class="btn btn-danger"
                        data-style="reset-all-common-style">Reset</a>
                </div>
                <div class="flex-fill w-50">
                    <a href="https://www.themewagon.com/themes/Duralux-admin" target="_blank"
                        class="btn btn-primary">Download</a>
                </div>
            </div>
        </div>
    </div>
    <!--! ================================================================ !-->
    <!--! [End] Theme Customizer !-->
    <!--! ================================================================ !-->
    <!--! ================================================================ !-->
    <!--! Footer Script !-->
    <!--! ================================================================ !-->
    <!--! BEGIN: Vendors JS !-->
    <script src="assets/vendors/js/vendors.min.js"></script>
    <!-- vendors.min.js {always must need to be top} -->
    <!--! END: Vendors JS !-->
    <!--! BEGIN: Apps Init  !-->
    <script src="assets/js/common-init.min.js"></script>
    <!--! END: Apps Init !-->
    <!--! BEGIN: Theme Customizer  !-->
    <script src="assets/js/theme-customizer-init.min.js"></script>
    <!--! END: Theme Customizer !-->
</body>

</html>
