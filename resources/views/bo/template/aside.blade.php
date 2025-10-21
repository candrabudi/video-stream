<aside class="app-sidebar sticky" id="sidebar">
    <div class="main-sidebar-header">
        <a href="{{ route('dashboard') }}" class="header-logo">
            <img src="{{ asset('homepage/logo/logo.png') }}" alt="logo" class="desktop-logo">
            <img src="{{ asset('homepage/logo/logo.png') }}" alt="logo" class="toggle-dark">
            <img src="{{ asset('homepage/logo/logo.png') }}" alt="logo" class="desktop-dark">
            <img src="{{ asset('homepage/logo/logo.png') }}" alt="logo" class="toggle-logo">
            <img src="{{ asset('homepage/logo/logo.png') }}" alt="logo" class="toggle-white">
            <img src="{{ asset('homepage/logo/logo.png') }}" alt="logo" class="desktop-white">
        </a>
    </div>

    <div class="main-sidebar" id="sidebar-scroll">
        <nav class="main-menu-container nav nav-pills flex-column sub-open">
            <ul class="main-menu">

                <!-- DASHBOARD -->
                <li class="slide__category"><span class="category-name">Main</span></li>
                <li class="slide">
                    <a href="{{ route('dashboard') }}" class="side-menu__item">
                        <i class="ri-dashboard-line side-menu__icon"></i>
                        <span class="side-menu__label">Dashboard</span>
                    </a>
                </li>

                <!-- VIDEO MANAGEMENT -->
                <li class="slide__category"><span class="category-name">Video Management</span></li>

                <li class="slide">
                    <a href="{{ route('videos.index') }}" class="side-menu__item">
                        <i class="ri-video-line side-menu__icon"></i>
                        <span class="side-menu__label">Semua Video</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="{{ route('videos.create') }}" class="side-menu__item">
                        <i class="ri-upload-cloud-line side-menu__icon"></i>
                        <span class="side-menu__label">Upload Video</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="{{ route('categories.index') }}" class="side-menu__item">
                        <i class="ri-folder-line side-menu__icon"></i>
                        <span class="side-menu__label">Kategori Video</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="{{ route('channels.index') }}" class="side-menu__item">
                        <i class="ri-mic-line side-menu__icon"></i>
                        <span class="side-menu__label">Channel</span>
                    </a>
                </li>

                <!-- VIEW ANALYTICS -->
                <li class="slide__category"><span class="category-name">Analytics</span></li>

                <li class="slide">
                    <a href="{{ route('analytics.overview') }}" class="side-menu__item">
                        <i class="ri-bar-chart-2-line side-menu__icon"></i>
                        <span class="side-menu__label">Ringkasan Statistik</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="{{ route('views.index') }}" class="side-menu__item">
                        <i class="ri-eye-line side-menu__icon"></i>
                        <span class="side-menu__label">Perhitungan Views</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="{{ route('seo.index') }}" class="side-menu__item">
                        <i class="ri-search-line side-menu__icon"></i>
                        <span class="side-menu__label">SEO Management</span>
                    </a>
                </li>

                <!-- USER & SETTINGS -->
                <li class="slide__category"><span class="category-name">Pengaturan</span></li>

                <li class="slide">
                    <a href="{{ route('users.index') }}" class="side-menu__item">
                        <i class="ri-user-settings-line side-menu__icon"></i>
                        <span class="side-menu__label">Manajemen User</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="{{ route('settings.index') }}" class="side-menu__item">
                        <i class="ri-settings-3-line side-menu__icon"></i>
                        <span class="side-menu__label">Pengaturan Sistem</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="{{ route('profile.index') }}" class="side-menu__item">
                        <i class="ri-account-circle-line side-menu__icon"></i>
                        <span class="side-menu__label">Profil Saya</span>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>
