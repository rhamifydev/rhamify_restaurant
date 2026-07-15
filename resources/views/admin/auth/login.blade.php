<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>{{ config('settings.site_name') }} | Admin Login</title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('admin/assets/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/modules/fontawesome/css/all.min.css') }}">

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('admin/assets/modules/bootstrap-social/bootstrap-social.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/toastr.min.css') }}">
    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/components.css') }}">
    <style>
        :root {
            --admin-auth-primary: {{ config('settings.site_color', '#ed7011') }};
            --admin-auth-dark: #17130f;
            --admin-auth-line: #efe4dc;
        }

        body {
            min-height: 100vh;
            background:
                linear-gradient(135deg, rgba(23, 19, 15, .88), rgba(23, 19, 15, .72)),
                url('{{ asset('frontend/images/login_bg.jpg') }}') center/cover no-repeat;
        }

        .admin-auth {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 32px 0;
        }

        .admin-auth__shell {
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 10px;
            background: #fffaf7;
            box-shadow: 0 24px 70px rgba(0, 0, 0, .28);
        }

        .admin-auth__brand {
            height: 100%;
            min-height: 520px;
            padding: 42px;
            background: var(--admin-auth-dark);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .admin-auth__logo {
            width: 170px;
            max-height: 72px;
            object-fit: contain;
            object-position: left center;
        }

        .admin-auth__brand h1 {
            margin: 34px 0 14px;
            color: #fff;
            font-size: 34px;
            line-height: 1.15;
            font-weight: 900;
        }

        .admin-auth__brand p {
            max-width: 360px;
            color: rgba(255, 255, 255, .72);
            font-size: 15px;
            line-height: 1.8;
        }

        .admin-auth__badge {
            display: inline-flex;
            align-items: center;
            width: max-content;
            gap: 8px;
            padding: 8px 13px;
            border-radius: 999px;
            background: rgba(237, 112, 17, .16);
            color: var(--admin-auth-primary);
            font-weight: 800;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .admin-auth__form {
            min-height: 520px;
            display: flex;
            align-items: center;
            padding: 42px;
        }

        .admin-auth .card {
            width: 100%;
            margin: 0;
            border: 0;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
        }

        .admin-auth .card.card-primary {
            border-top: 0;
        }

        .admin-auth .card-header {
            display: block;
            min-height: auto;
            padding: 0 0 26px;
            border-bottom: 0;
            background: transparent;
        }

        .admin-auth .card-header h4 {
            margin-bottom: 8px;
            color: var(--admin-auth-dark);
            font-size: 28px;
            font-weight: 900;
        }

        .admin-auth .card-header p {
            margin: 0;
            color: #756b63;
            line-height: 1.6;
        }

        .admin-auth .card-body {
            padding: 0;
        }

        .admin-auth label {
            color: var(--admin-auth-dark);
            font-weight: 800;
        }

        .admin-auth .form-control {
            height: 48px !important;
            border-color: var(--admin-auth-line);
            border-radius: 8px;
            background: #fff;
        }

        .admin-auth .form-control:focus {
            border-color: var(--admin-auth-primary);
            box-shadow: 0 0 0 3px rgba(237, 112, 17, .14) !important;
        }

        .admin-auth .btn-primary {
            height: 48px;
            border: 0;
            border-radius: 8px;
            background: var(--admin-auth-primary);
            color: #111;
            font-weight: 900;
            box-shadow: 0 12px 24px rgba(237, 112, 17, .24);
        }

        .admin-auth .btn-primary:hover {
            background: #d7630d !important;
            color: #111 !important;
        }

        .admin-auth a {
            color: var(--admin-auth-primary);
            font-weight: 800;
        }

        .admin-auth .custom-control-label {
            color: #615a54;
            font-weight: 700;
        }

        @media (max-width: 767.98px) {
            .admin-auth {
                padding: 18px 0;
            }

            .admin-auth__brand {
                min-height: auto;
                padding: 28px;
            }

            .admin-auth__brand h1 {
                font-size: 26px;
            }

            .admin-auth__form {
                min-height: auto;
                padding: 28px;
            }
        }
    </style>
    <!-- Start GA -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-94034622-3');
    </script>
    <!-- /END GA -->
</head>

<body>
    <div id="app">
        <section class="admin-auth">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10 col-xl-9">
                        <div class="admin-auth__shell">
                            <div class="row no-gutters">
                                <div class="col-md-5">
                                    <div class="admin-auth__brand">
                                        <div>
                                            <img src="{{ asset(config('settings.logo')) }}" alt="{{ config('settings.site_name') }}" class="admin-auth__logo">
                                            <h1>{{ config('settings.site_name') }} Admin</h1>
                                            <p>Manage orders, products, reservations, and restaurant content from one focused workspace.</p>
                                        </div>
                                        <span class="admin-auth__badge"><i class="fas fa-shield-alt"></i> Secure access</span>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="admin-auth__form">
                                        <div class="card card-primary">
                            <div class="card-header">
                                <h4>Login</h4>
                                <p>Welcome back. Sign in to continue.</p>
                            </div>

                            <div class="card-body">
                                <form action="{{ route('login') }}" method="POST" class="needs-validation"
                                    novalidate="">
                                    @csrf
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input id="email" type="email" class="form-control" name="email"
                                            tabindex="1" required autofocus value="{{ old('email') }}">
                                        <div class="invalid-feedback">
                                            Please fill in your email
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="d-block">
                                            <label for="password" class="control-label">Password</label>
                                            <div class="float-right">
                                                <a href="{{ route('admin.forget-password') }}" class="text-small">
                                                    Forgot Password?
                                                </a>
                                            </div>
                                        </div>
                                        <input id="password" type="password" class="form-control" name="password"
                                            tabindex="2" required>
                                        <div class="invalid-feedback">
                                            please fill in your password
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="remember" class="custom-control-input"
                                                tabindex="3" id="remember-me">
                                            <label class="custom-control-label" for="remember-me">Remember Me</label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                                            Login
                                        </button>
                                    </div>
                                </form>


                            </div>
                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- General JS Scripts -->
    <script src="{{ asset('admin/assets/modules/jquery.min.js') }}"></script>
    <script src="{{ asset('admin/assets/modules/popper.js') }}"></script>
    <script src="{{ asset('admin/assets/modules/tooltip.js') }}"></script>
    <script src="{{ asset('admin/assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('admin/assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('admin/assets/modules/moment.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/stisla.js') }}"></script>

    <!-- JS Libraies -->
    <script src="{{ asset('admin/assets/js/toastr.min.js') }}"></script>
    <!-- Page Specific JS File -->

    <!-- Template JS File -->
    <script src="{{ asset('admin/assets/js/scripts.js') }}"></script>
    <script src="{{ asset('admin/assets/js/custom.js') }}"></script>

    <!-- show dynamic validation message-->
    <script>
        toastr.options.progressBar = true;

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}")
            @endforeach
        @endif
    </script>
</body>

</html>
