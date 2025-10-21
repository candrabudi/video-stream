<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-vertical-style="overlay" data-theme-mode="light"
    data-header-styles="light" data-menu-styles="light" data-toggled="close">

<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="Description" content="Codeigniter Bootstrap Responsive Admin Web Dashboard Template">
    <meta name="Author" content="Spruko Technologies Private Limited">
    <meta name="keywords"
        content="admin panel template, admin panel bootstrap template, bootstrap, bootstrap admin template, bootstrap codeigniter, bootstrap dashboard, bootstrap framework template, codeigniter template, code igniter, codeigniter admin, codeigniter dashboard, codeigniter ui, framework codeigniter, php framework codeigniter, template codeigniter.">
    <title>Login | Dian Farm</title>
    <link rel="icon" href="{{ asset('assets/images/brand-logos/favicon.ico') }}" type="image/x-icon">
    <script src="{{ asset('assets/js/authentication-main.js') }}"></script>
    <link id="style" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>

<body class="authentication-background">

    <div class="offcanvas offcanvas-end" tabindex="-1" id="switcher-canvas" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title text-default" id="offcanvasRightLabel">Switcher</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <nav class="border-bottom border-block-end-dashed">
                <div class="nav nav-tabs nav-justified" id="switcher-main-tab" role="tablist">
                    <button class="nav-link active" id="switcher-home-tab" data-bs-toggle="tab"
                        data-bs-target="#switcher-home" type="button" role="tab" aria-controls="switcher-home"
                        aria-selected="true">Theme Styles</button>
                    <button class="nav-link" id="switcher-profile-tab" data-bs-toggle="tab"
                        data-bs-target="#switcher-profile" type="button" role="tab"
                        aria-controls="switcher-profile" aria-selected="false">Theme Colors</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <!-- [tab content omitted for brevity; unchanged, no comments left] -->
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center align-items-center authentication authentication-basic h-100">
            <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-8 col-12">
                <div class="card custom-card my-4">
                    <div class="card-body p-5">
                        <div class="mb-4 d-flex justify-content-center">
                            <a href="index.html">
                                <img src="https://cdn-icons-png.flaticon.com/128/837/837560.png" alt="logo"
                                    class="desktop-logo">
                                <img src="https://cdn-icons-png.flaticon.com/128/837/837560.png" alt="logo"
                                    class="desktop-white">
                            </a>
                        </div>
                        <p class="h5 mb-2 text-center">Masuk</p>
                        <p class="text-muted mb-4 text-center">Kelola Peternakan Dian Farm</p>
                        <div class="row gy-3">
                            <div class="col-xl-12">
                                <label for="signin-username" class="form-label text-default">User Name</label>
                                <input type="text" class="form-control" id="signin-username" placeholder="user name">
                            </div>
                            <div class="col-xl-12 mb-2">
                                <label for="signin-password" class="form-label text-default d-block">Password</label>
                                <div class="position-relative">
                                    <input type="password" class="form-control create-password-input"
                                        id="signin-password" placeholder="password">
                                    <a href="javascript:void(0);" class="show-password-button text-muted"
                                        onclick="togglePassword('signin-password', this)" id="button-addon2">
                                        <i class="ri-eye-off-line align-middle"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid mt-3">
                            <button id="signin-button" class="btn btn-primary">Sign In</button>
                        </div>

                        <!-- Toast di pojok kanan atas -->
                        <div class="position-fixed top-0 end-0 p-3" style="z-index:1080">
                            <div id="loginToast" class="toast align-items-center text-white border-0" role="alert"
                                aria-live="assertive" aria-atomic="true">
                                <div class="d-flex">
                                    <div class="toast-body" id="toastMessage">...</div>
                                    <button type="button" class="btn-close btn-close-white me-2 m-auto"
                                        data-bs-dismiss="toast" aria-label="Tutup"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/show-password.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute(
            'content');

        const toastEl = document.getElementById('loginToast');
        const toast = new bootstrap.Toast(toastEl, {
            delay: 3000
        });

        function showToast(message, bg = 'bg-primary') {
            document.getElementById('toastMessage').innerText = message;
            toastEl.className = `toast align-items-center text-white ${bg} border-0`;
            toast.show();
        }

        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('ri-eye-off-line');
                icon.classList.add('ri-eye-line');
            } else {
                input.type = 'password';
                icon.classList.remove('ri-eye-line');
                icon.classList.add('ri-eye-off-line');
            }
        }

        document.getElementById('signin-button').addEventListener('click', function(e) {
            e.preventDefault();

            const username = document.getElementById('signin-username').value.trim();
            const password = document.getElementById('signin-password').value;

            if (!username || !password) {
                showToast('Username dan password wajib diisi', 'bg-danger');
                return;
            }

            const btn = this;
            btn.disabled = true;
            btn.innerText = 'Signing in...';

            axios.post('/login', {
                    username,
                    password
                })
                .then(res => {
                    const redirectUrl = res.data.redirect || '/dashboard';
                    showToast(res.data.message || 'Login berhasil!', 'bg-success');
                    setTimeout(() => window.location.href = redirectUrl, 500);
                })
                .catch(err => {
                    console.error(err);
                    if (err.response && err.response.data && err.response.data.message) {
                        showToast(err.response.data.message, 'bg-danger');
                    } else {
                        showToast('Gagal login', 'bg-danger');
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerText = 'Sign In';
                });
        });
    </script>

</body>

</html>
