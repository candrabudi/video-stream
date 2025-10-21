<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Waskita</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
            color: #111111;
        }

        .sidebar {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: transform;
            background: #ffffff;
            border-right: 1px solid #e0e0e0;
        }

        .sidebar-mini {
            width: 72px;
        }

        .sidebar-full {
            width: 240px;
        }

        .main-content {
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .main-mini {
            margin-left: 72px;
        }

        .main-full {
            margin-left: 240px;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: 0;
                top: 56px;
                height: calc(100vh - 56px);
                z-index: 40;
                transform: translateX(-100%);
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0 !important;
            }
        }

        .video-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
        }

        .video-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .thumbnail-wrapper {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%;
            overflow: hidden;
            background: #e0e0e0;
            border-radius: 12px 12px 0 0;
        }

        .thumbnail {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .play-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .video-card:hover .play-overlay {
            opacity: 1;
        }

        .play-button {
            width: 64px;
            height: 64px;
            background: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: scale(0.8);
            transition: transform 0.3s ease;
        }

        .video-card:hover .play-button {
            transform: scale(1);
        }

        .duration-badge {
            position: absolute;
            bottom: 8px;
            right: 8px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .search-container {
            position: relative;
            max-width: 600px;
            width: 100%;
        }

        .search-input {
            width: 100%;
            background: #ffffff;
            border: 1px solid #cccccc;
            color: #111111;
            padding: 10px 48px 10px 16px;
            border-radius: 40px;
            outline: none;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: #3ea6ff;
            box-shadow: 0 0 0 1px #3ea6ff;
        }

        .search-input::placeholder {
            color: #888888;
        }

        .search-button {
            position: absolute;
            right: 4px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #666666;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 20px;
            transition: all 0.2s ease;
        }

        .search-button:hover {
            color: #111111;
            background: #e0e0e0;
        }

        .category-chip {
            padding: 8px 16px;
            background: #f0f0f0;
            color: #111111;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .category-chip:hover {
            background: #d9d9d9;
        }

        .category-chip.active {
            background: #3ea6ff;
            color: white;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 24px;
            padding: 10px 12px;
            color: #111111;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.2s ease;
            cursor: pointer;
            position: relative;
        }

        .nav-item:hover {
            background: #f0f0f0;
        }

        .nav-item.active {
            background: #e0e0e0;
            font-weight: 500;
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 24px;
            background: #3ea6ff;
            border-radius: 0 2px 2px 0;
        }

        .sidebar-mini .nav-item {
            justify-content: center;
            gap: 0;
        }

        .sidebar-mini .nav-text {
            display: none;
        }

        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .avatar:hover {
            transform: scale(1.1);
        }

        .channel-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            flex-shrink: 0;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
        }

        @media (max-width: 1400px) {
            .video-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .video-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }

        .icon-button {
            padding: 8px;
            border-radius: 50%;
            background: transparent;
            border: none;
            color: #111111;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-button:hover {
            background: #f0f0f0;
        }

        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.2);
            z-index: 35;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f5f5f5;
        }

        ::-webkit-scrollbar-thumb {
            background: #cccccc;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #b3b3b3;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            border-bottom: 1px solid #e0e0e0;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .video-card {
            animation: slideIn 0.5s ease forwards;
        }

        .video-card:nth-child(1) {
            animation-delay: 0.05s;
        }

        .video-card:nth-child(2) {
            animation-delay: 0.1s;
        }

        .video-card:nth-child(3) {
            animation-delay: 0.15s;
        }

        .video-card:nth-child(4) {
            animation-delay: 0.2s;
        }

        .video-card:nth-child(5) {
            animation-delay: 0.25s;
        }

        .video-card:nth-child(6) {
            animation-delay: 0.3s;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            font-size: 20px;
            color: #111111;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background: #3ea6ff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
    @stack('styles')
</head>

<body>
    @include('template.header')

    <div class="overlay" id="overlay" onclick="closeMobileSidebar()"></div>
    @include('template.sidebar')
    <main id="mainContent" class="main-content main-mini pt-20 px-6 pb-8">
        @yield('content')
    </main>
    @stack('scripts')
    <script>
        let sidebarExpanded = false;

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const overlay = document.getElementById('overlay');

            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('active');
            } else {
                sidebarExpanded = !sidebarExpanded;
                if (sidebarExpanded) {
                    sidebar.classList.remove('sidebar-mini');
                    sidebar.classList.add('sidebar-full');
                    mainContent.classList.remove('main-mini');
                    mainContent.classList.add('main-full');
                } else {
                    sidebar.classList.remove('sidebar-full');
                    sidebar.classList.add('sidebar-mini');
                    mainContent.classList.remove('main-full');
                    mainContent.classList.add('main-mini');
                }
            }
        }

        function closeMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
        }

        function toggleMobileSearch() {
            const mobileSearch = document.getElementById('mobileSearch');
            mobileSearch.classList.toggle('hidden');
        }

        document.getElementById('searchInput')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') searchVideos();
        });

        document.getElementById('mobileSearchInput')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') searchVideosMobile();
        });

        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            if (window.innerWidth > 768) {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
            }
        });

        renderVideos(filteredVideos);
    </script>
</body>

</html>
