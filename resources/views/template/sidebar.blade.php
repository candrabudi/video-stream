<link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">

<aside id="sidebar"
    class="sidebar sidebar-mini fixed left-0 top-14 bottom-0 bg-white border-r border-gray-200 overflow-y-auto z-40">
    <nav class="p-2 mt-6 space-y-2">
        <a href="/dashboard"
            class="nav-item flex items-center gap-3 p-2 rounded hover:bg-gray-100 {{ Request::is('dashboard') ? 'bg-gray-100 font-semibold' : '' }}">
            <i class="ri-dashboard-line text-gray-800 text-xl"></i>
            <span class="nav-text">Dashboard</span>
        </a>

        <a href="/"
            class="nav-item flex items-center gap-3 p-2 rounded hover:bg-gray-100 {{ Request::is('/') ? 'bg-gray-100 font-semibold' : '' }}">
            <i class="ri-home-4-line text-gray-800 text-xl"></i>
            <span class="nav-text">Beranda</span>
        </a>

        <a href="/videos"
            class="nav-item flex items-center gap-3 p-2 rounded hover:bg-gray-100 {{ Request::is('videos') ? 'bg-gray-100 font-semibold' : '' }}">
            <i class="ri-video-line text-gray-800 text-xl"></i>
            <span class="nav-text">Data Video</span>
        </a>

        <a href="/videos/create"
            class="nav-item flex items-center gap-3 p-2 rounded hover:bg-gray-100 {{ Request::is('videos/create') ? 'bg-gray-100 font-semibold' : '' }}">
            <i class="ri-upload-cloud-line text-gray-800 text-xl"></i>
            <span class="nav-text">Upload Video</span>
        </a>

        <a href="/channels"
            class="nav-item flex items-center gap-3 p-2 rounded hover:bg-gray-100 {{ Request::is('channels*') ? 'bg-gray-100 font-semibold' : '' }}">
            <i class="ri-tv-2-line text-gray-800 text-xl"></i>
            <span class="nav-text">Channel</span>
        </a>

        <a href="/categories"
            class="nav-item flex items-center gap-3 p-2 rounded hover:bg-gray-100 {{ Request::is('categories*') ? 'bg-gray-100 font-semibold' : '' }}">
            <i class="ri-layout-grid-line text-gray-800 text-xl"></i>
            <span class="nav-text">Kategori</span>
        </a>

        @if (Auth::user())     
            @if (Auth::user()->role == 'super_admin')
                <a href="/users"
                    class="nav-item flex items-center gap-3 p-2 rounded hover:bg-gray-100 {{ Request::is('users*') ? 'bg-gray-100 font-semibold' : '' }}">
                    <i class="ri-user-settings-line text-gray-800 text-xl"></i>
                    <span class="nav-text">Kelola User</span>
                </a>
            @endif
        @endif

    </nav>
</aside>
