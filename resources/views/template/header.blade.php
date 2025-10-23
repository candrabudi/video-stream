<header class="header fixed top-0 left-0 right-0 z-50">
    <div class="flex items-center justify-between px-4 py-3">
        <div class="flex items-center gap-4">
            <button onclick="toggleSidebar()" class="icon-button">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <a href="/" class="logo">
                <div class="logo-icon">
                    <img src="{{ asset('homepage/logo/logo.png') }}" alt="Logo Waskita">
                </div>
                <span class="gradient-text hidden sm:block">Waskita</span>
            </a>
        </div>

        <div class="search-container hidden md:flex items-center">
            <form action="{{ route('videos.search') }}" method="GET"
                class="flex items-center bg-gray-100 rounded-full overflow-hidden">
                <input type="text" name="query" id="searchInput"
                    placeholder="Cari video, channel, atau kategori..."
                    class="px-4 py-2 text-sm text-gray-700 bg-transparent outline-none w-64 md:w-80 focus:w-96 transition-all duration-300">
                <button type="submit" class="px-3 text-gray-600 hover:text-gray-800 transition duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </form>
        </div>

        <div class="flex items-center gap-2">
            <button class="icon-button md:hidden" onclick="toggleMobileSearch()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>

            <button class="icon-button hidden md:flex">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </button>

            <button class="icon-button">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </button>

            <div class="relative">
    <!-- Tombol Profil -->
    <button id="profileButton" onclick="toggleDropdown()"
        class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold text-sm shadow-md transition duration-150 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50">
        {{ strtoupper(substr(Auth::user()->username, 0, 2)) }}
    </button>

    <!-- Dropdown Profil -->
    <div id="profileDropdown"
        class="absolute right-0 top-12 mt-1 w-56 bg-white rounded-xl shadow-2xl py-2 hidden ring-1 ring-black ring-opacity-5 transition transform origin-top-right duration-200">
        
        <!-- Info User -->
        <div class="px-4 py-3 border-b border-gray-100 mb-1">
            <h4 class="text-sm text-gray-600">{{ Auth::user()->username }}</h4>
        </div>

        <!-- Pengaturan -->
        <a href="#" onclick="handleSetting(); return false;"
            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition duration-150 rounded-lg mx-2">
            <i class="ri-settings-3-line text-lg"></i>
            Pengaturan
        </a>

        <!-- Logout -->
        <a href="#" onclick="handleLogout(); return false;"
            class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition duration-150 rounded-lg mx-2">
            <i class="ri-logout-box-r-line text-lg"></i>
            Keluar
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    // Toggle dropdown profil
    function toggleDropdown() {
        document.getElementById("profileDropdown").classList.toggle("hidden");
    }

    // Alert pengaturan
    function handleSetting() {
        alert("Menu Pengaturan sedang dikembangkan 😄");
    }

    // Logout via Axios
    function handleLogout() {
        axios.post("{{ route('logout') }}", {}, {
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json',
            }
        })
        .then(response => {
            if (response.data.redirect) {
                window.location.href = response.data.redirect;
            }
        })
        .catch(error => {
            console.error("Logout error:", error);
            alert("Logout gagal, silakan coba lagi.");
        });
    }

    // Tutup dropdown saat klik di luar
    window.addEventListener("click", function(event) {
        const dropdown = document.getElementById("profileDropdown");
        const button = document.getElementById("profileButton");
        if (!button.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add("hidden");
        }
    });
</script>


        </div>
    </div>

    <div id="mobileSearch" class="hidden px-4 pb-3">
        <div class="search-container w-full flex items-center bg-gray-100 rounded-full overflow-hidden">
            <input type="text" id="mobileSearchInput" placeholder="Cari..." class="search-input">
            <button onclick="searchVideosMobile()"
                class="px-3 text-gray-600 hover:text-gray-800 transition duration-150 search-button">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>
        </div>
    </div>
</header>

<script>
    function toggleDropdown() {
        const dropdown = document.getElementById('profileDropdown');
        dropdown.classList.toggle('hidden');
    }

    window.onclick = function(event) {
        const profileButton = document.getElementById('profileButton');
        if (profileButton && !profileButton.contains(event.target)) {
            const dropdown = document.getElementById('profileDropdown');
            if (dropdown && !dropdown.classList.contains('hidden') && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        }
    }
</script>
