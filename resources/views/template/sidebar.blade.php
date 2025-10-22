<aside id="sidebar"
    class="sidebar sidebar-mini fixed left-0 top-14 bottom-0 bg-white border-r border-gray-200 overflow-y-auto z-40">
    <nav class="p-2" style="margin-top: 25px;">
        <a href="/" class="nav-item active">
            <svg class="nav-icon w-6 h-6 text-gray-800" fill="currentColor" viewBox="0 0 24 24">
                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
            </svg>
            <span class="nav-text">Beranda</span>
        </a>
        <a href="#" class="nav-item">
            <svg class="nav-icon w-6 h-6 text-gray-800" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M10 14.65v-5.3L15 12l-5 2.65zm7.77-4.33c-.77-.32-1.2-.5-1.2-.5L18 9.06c1.84-.96 2.53-3.23 1.56-5.06s-3.24-2.53-5.07-1.56L6 6.94c-1.29.68-2.07 2.04-2 3.49.07 1.42.93 2.67 2.22 3.25.03.01 1.2.5 1.2.5L6 14.93c-1.83.97-2.53 3.24-1.56 5.07.97 1.83 3.24 2.53 5.07 1.56l8.5-4.5c1.29-.68 2.06-2.04 1.99-3.49-.07-1.42-.94-2.68-2.23-3.25z" />
            </svg>
            <span class="nav-text">Shorts</span>
        </a>
        <a href="#" class="nav-item">
            <svg class="nav-icon w-6 h-6 text-gray-800" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M18.7 8.7H5.3V7h13.4v1.7zm-1.7-5H7v1.6h10V3.7zm3.3 8.3v6.7c0 1-.7 1.6-1.6 1.6H5.3c-1 0-1.6-.7-1.6-1.6V12c0-1 .7-1.7 1.6-1.7h13.4c1 0 1.6.8 1.6 1.7zm-5 3.3l-5-2.7V18l5-2.7z" />
            </svg>
            <span class="nav-text">Subscription</span>
        </a>

        <div class="h-px bg-gray-200 my-3"></div>

        <a href="#" class="nav-item">
            <svg class="nav-icon w-6 h-6 text-gray-800" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M14.97 16.95L10 13.87V7h2v5.76l4.03 2.49-1.06 1.7zM12 3c-4.96 0-9 4.04-9 9s4.04 9 9 9 9-4.04 9-9-4.04-9-9-9m0-2c6.08 0 11 4.92 11 11s-4.92 11-11 11S1 18.08 1 12 5.92 1 12 1z" />
            </svg>
            <span class="nav-text">Riwayat</span>
        </a>
        <a href="#" class="nav-item">
            <svg class="nav-icon w-6 h-6 text-gray-800" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M18.4 5.6v12.8H5.6V5.6h12.8zm0-2H5.6C4.7 3.6 4 4.3 4 5.2v13.2c0 .9.7 1.6 1.6 1.6h12.8c.9 0 1.6-.7 1.6-1.6V5.2c0-.9-.7-1.6-1.6-1.6z" />
                <path d="M10 15l5.2-3.2L10 8.7z" />
            </svg>
            <span class="nav-text">Playlist</span>
        </a>
        <a href="#" class="nav-item">
            <svg class="nav-icon w-6 h-6 text-gray-800" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 4.33l7 6.12V20H5V10.45l7-6.12M12 2L2 11h3v10h14V11h3L12 2z" />
            </svg>
            <span class="nav-text">Tonton Nanti</span>
        </a>
        <a href="#" class="nav-item">
            <svg class="nav-icon w-6 h-6 text-gray-800" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M18.77,11h-4.23l1.52-4.94C16.38,5.03,15.54,4,14.38,4c-0.58,0-1.14,0.24-1.52,0.65L7,11H3v10h4h1h9.43 c1.06,0,1.98-0.67,2.19-1.61l1.34-6C21.23,12.15,20.18,11,18.77,11z M7,20H4v-8h3V20z M19.98,13.17l-1.34,6 C18.54,19.65,18.03,20,17.43,20H8v-8.61l5.6-6.06C13.79,5.12,14.08,5,14.38,5c0.26,0,0.5,0.11,0.63,0.3 c0.07,0.1,0.15,0.26,0.09,0.47l-1.52,4.94L13.18,12h1.35h4.23c0.41,0,0.8,0.17,1.03,0.46C19.92,12.61,20.05,12.86,19.98,13.17z" />
            </svg>
            <span class="nav-text">Video Disukai</span>
        </a>
    </nav>
</aside>

<!-- Toast Container -->
<div id="toast-container" class="fixed bottom-5 right-5 z-50 space-y-2"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarItems = document.querySelectorAll('#sidebar .nav-item');

    sidebarItems.forEach(item => {
        const text = item.querySelector('.nav-text').textContent.trim();

        // Beranda tetap bisa diklik
        if(text !== 'Beranda') {
            item.addEventListener('click', function(e) {
                e.preventDefault(); // cegah navigasi
                showToast('Halaman ini sedang maintenance.');
            });
        }
    });

    function showToast(message, duration = 3000) {
        const container = document.getElementById('toast-container');
        if(!container) return;

        const toast = document.createElement('div');
        toast.className = 'bg-gray-800 text-white px-4 py-2 rounded shadow-lg opacity-0 transform translate-y-2 transition-all duration-300';
        toast.textContent = message;

        container.appendChild(toast);

        // Trigger animasi masuk
        requestAnimationFrame(() => {
            toast.classList.remove('opacity-0', 'translate-y-2');
        });

        // Hilang otomatis setelah durasi
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-2');
            toast.addEventListener('transitionend', () => toast.remove());
        }, duration);
    }
});
</script>
