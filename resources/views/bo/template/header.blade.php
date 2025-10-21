<header class="app-header sticky" id="header">
    <div class="main-header-container container-fluid">
        <div class="header-content-left">
            <div class="header-element">
                <div class="horizontal-logo">
                    <a href="{{ url('/') }}" class="header-logo">
                        <img src="https://cdn-icons-png.flaticon.com/128/837/837560.png" alt="logo"
                            class="desktop-logo">
                        <img src="https://cdn-icons-png.flaticon.com/128/837/837560.png" alt="logo"
                            class="toggle-dark">
                        <img src="https://cdn-icons-png.flaticon.com/128/837/837560.png" alt="logo"
                            class="desktop-dark">
                        <img src="https://cdn-icons-png.flaticon.com/128/837/837560.png" alt="logo"
                            class="toggle-logo">
                        <img src="https://cdn-icons-png.flaticon.com/128/837/837560.png" alt="logo"
                            class="toggle-white">
                        <img src="https://cdn-icons-png.flaticon.com/128/837/837560.png" alt="logo"
                            class="desktop-white">
                    </a>
                </div>
            </div>

            <div class="header-element mx-lg-0 mx-2">
                <a aria-label="Hide Sidebar"
                    class="sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle"
                    data-bs-toggle="sidebar" href="javascript:void(0);">
                    <span></span>
                </a>
            </div>

            <div class="header-element header-search d-md-block d-none my-auto auto-complete-search"></div>
        </div>

        <ul class="header-content-right">
            <li class="header-element d-md-none d-block">
                <a href="javascript:void(0);" class="header-link" data-bs-toggle="modal"
                    data-bs-target="#header-responsive-search">
                    <i class="bi bi-search header-link-icon"></i>
                </a>
            </li>

            <li class="header-element header-fullscreen">
                <a onclick="openFullscreen();" href="javascript:void(0);" class="header-link">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 full-screen-open header-link-icon"
                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 full-screen-close header-link-icon d-none"
                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 9V4.5M9 9H4.5M9 9 3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5 5.25 5.25" />
                    </svg>
                </a>
            </li>

            <li class="header-element dropdown">
                <a href="javascript:void(0);" class="header-link dropdown-toggle" id="mainHeaderProfile"
                    data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <div>
                            <img src="{{ asset('assets/images/faces/15.jpg') }}" alt="img"
                                class="avatar custom-header-avatar avatar-rounded">
                        </div>
                    </div>
                </a>
                <ul class="main-header-dropdown dropdown-menu pt-0 overflow-hidden header-profile-dropdown dropdown-menu-end"
                    aria-labelledby="mainHeaderProfile">
                    <li>
                        <div class="dropdown-item text-center border-bottom">
                            <span class="fw-medium">{{ Auth::user()->name }}</span>
                            <span class="d-block fs-12 text-muted">{{ Auth::user()->role }}</span>
                        </div>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" id="logoutButton">
                            <i
                                class="ri-door-lock-line lh-1 p-1 rounded-circle bg-primary-transparent text-primary me-2 fs-14"></i>
                            Log Out
                        </a>
                    </li>
                    <script>
                        const logoutBtn = document.getElementById('logoutButton');

                        logoutBtn.addEventListener('click', function(e) {
                            e.preventDefault();

                            const btn = this;
                            btn.classList.add('disabled');
                            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content');

                            axios.post('/logout')
                                .then(res => {
                                    const toastEl = document.getElementById('loginToast');
                                    const toast = new bootstrap.Toast(toastEl, {
                                        delay: 3000
                                    });
                                    document.getElementById('toastMessage').innerText = res.data.message || 'Logout berhasil';
                                    toastEl.className = `toast align-items-center text-white bg-success border-0`;
                                    toast.show();

                                    setTimeout(() => {
                                        window.location.href = res.data.redirect || '/login';
                                    }, 500);
                                })
                                .catch(err => {
                                    console.error(err);
                                    const toastEl = document.getElementById('loginToast');
                                    const toast = new bootstrap.Toast(toastEl, {
                                        delay: 3000
                                    });
                                    document.getElementById('toastMessage').innerText = 'Gagal logout';
                                    toastEl.className = `toast align-items-center text-white bg-danger border-0`;
                                    toast.show();
                                    btn.classList.remove('disabled');
                                });
                        });
                    </script>

                </ul>
            </li>
        </ul>
    </div>
</header>
