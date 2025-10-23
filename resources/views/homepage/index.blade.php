@extends('template.app')

@push('styles')
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .play-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            pointer-events: none;
            border-radius: .5rem;
        }

        .play-overlay i {
            font-size: 48px;
            color: rgba(255, 255, 255, 0.9);
            opacity: 0;
            transition: opacity .2s, transform .2s;
        }

        .video-card:hover .play-overlay i {
            opacity: 1;
            transform: scale(1.1);
        }

        .duration-badge {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 12px;
        }

        .p-3 {
            padding: 12px;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .category-chip {
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 9999px;
            border: 1px solid #ccc;
            font-size: 14px;
            background-color: white;
            min-width: 140px;
        }

        .category-chip.active {
            background-color: #ef4444;
            color: white;
            border-color: #ef4444;
        }

        .thumbnail-wrapper {
            cursor: pointer;
            position: relative;
        }
    </style>
@endpush

@section('content')
    <div class="mb-4 overflow-x-auto">
        <div class="flex gap-3 whitespace-nowrap px-2">
            <button class="category-chip active" onclick="filterCategory(this, 'all')">Semua</button>
            @foreach (\App\Models\Category::orderBy('name')->get() as $category)
                <button class="category-chip" onclick="filterCategory(this, '{{ $category->slug }}')">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
    </div>

    <div id="videoGrid" class="video-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"></div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        let allVideos = [];
        let filteredVideos = [];
        let currentCategory = 'all';

        function toIndoTime(t) {
            if (!t) return '-';
            return t
                .replace(/days?/gi, 'hari')
                .replace(/day/gi, 'hari')
                .replace(/hours?/gi, 'jam')
                .replace(/minutes?/gi, 'menit')
                .replace(/seconds?/gi, 'detik')
                .replace(/ago/gi, 'lalu')
                .replace(/just now/gi, 'baru saja');
        }

        function fetchVideos() {
            axios.get('/get-videos')
                .then(res => {
                    allVideos = res.data.map(v => {
                        let desc = v.description ? v.description.replace(/<[^>]+>/g, '') : '-';
                        if (desc.length > 120) desc = desc.substring(0, 120) + '...';
                        return {
                            id: v.id,
                            title: v.title,
                            channel: v.channel || '-',
                            time: toIndoTime(v.time),
                            duration: v.duration || '00:00',
                            thumbnail: v.thumbnail ||
                                'https://frompaddocktoplate.com.au/wp-content/uploads/2021/10/no-thumbnail.png',
                            category: v.category || 'uncategorized',
                            description: desc
                        };
                    });
                    filteredVideos = [...allVideos];
                    renderVideos(filteredVideos);
                });
        }

        function renderVideos(videosToRender) {
            const grid = document.getElementById('videoGrid');
            if (!videosToRender || videosToRender.length === 0) {
                grid.innerHTML = '<p class="text-center text-gray-500">Belum ada video</p>';
                return;
            }

            grid.innerHTML = videosToRender.map(video => `
            <div class="video-card">
                <div class="thumbnail-wrapper relative" data-id="${video.id}">
                    <img src="${video.thumbnail}" alt="${video.title}" class="thumbnail w-full object-cover rounded-lg">
                    <div class="play-overlay">
                        <i class='bx bx-play-circle'></i>
                    </div>
                    <span class="duration-badge">${video.duration}</span>
                </div>
                <div class="p-3">
                    <h3 class="font-semibold text-sm line-clamp-2 mb-1">
                        <a href="/get-videos/${video.id}" class="hover:underline text-gray-900">${video.title}</a>
                    </h3>
                    <p class="text-gray-500 text-xs mb-1 line-clamp-2">${video.description}</p>
                    <p class="text-gray-400 text-xs">${video.channel} • ${video.time}</p>
                </div>
            </div>
        `).join('');

            document.querySelectorAll('.thumbnail-wrapper').forEach(wrapper => {
                wrapper.addEventListener('click', () => {
                    window.location.href = `/get-videos/${wrapper.getAttribute('data-id')}`;
                });
            });
        }

        function filterCategory(button, categorySlug) {
            currentCategory = categorySlug;
            document.querySelectorAll('.category-chip').forEach(chip => chip.classList.remove('active'));
            button.classList.add('active');
            filteredVideos = allVideos.filter(video => categorySlug === 'all' || video.category === categorySlug);
            renderVideos(filteredVideos);
        }

        document.addEventListener('DOMContentLoaded', fetchVideos);
        renderVideos(filteredVideos);
    </script>
@endpush
