@extends('template.app')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="max-w-12xl mx-auto p-6">

        <div class="bg-white shadow rounded-xl p-5 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h1 class="text-2xl font-bold text-red-600">Manajemen Video</h1>

                <div class="flex flex-wrap gap-3 items-center w-full sm:w-auto">
                    <a href="{{ route('videos.create') }}"
                        class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg shadow">
                        Tambah Video
                    </a>

                    {{-- Filter Channel --}}
                    <select id="filterChannel" class="border rounded-lg px-3 py-2 flex-1 min-w-[200px] sm:min-w-[250px]">
                        <option value="">Semua Channel</option>
                        @foreach ($channels as $channel)
                            <option value="{{ $channel->id }}">{{ $channel->name }}</option>
                        @endforeach
                    </select>

                    {{-- Filter Kategori --}}
                    <select id="filterCategory" class="border rounded-lg px-3 py-2 flex-1 min-w-[200px] sm:min-w-[250px]">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div id="videoGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <template id="skeletonCard">
                <div class="bg-white shadow rounded-lg overflow-hidden animate-pulse">
                    <div class="h-48 bg-gray-200"></div>
                    <div class="p-4 space-y-2">
                        <div class="h-5 bg-gray-200 rounded w-3/4"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                    </div>
                </div>
            </template>
        </div>

        <div id="toast-container" class="fixed bottom-5 right-5 space-y-2 z-50"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

        document.addEventListener('DOMContentLoaded', () => {
            let page = 1;
            let loading = false;
            let finished = false;
            let channelFilter = '';
            let categoryFilter = '';

            const filterChannel = document.getElementById('filterChannel');
            const filterCategory = document.getElementById('filterCategory');
            const grid = document.getElementById('videoGrid');

            function toast(msg, isError = false) {
                const box = document.createElement('div');
                box.className = (isError ? "bg-red-700" : "bg-black") + " text-white px-4 py-2 rounded-lg shadow";
                box.textContent = msg;
                document.getElementById('toast-container').appendChild(box);
                setTimeout(() => box.remove(), 3000);
            }

            function escapeHTML(text) {
                return text?.replace(/</g, "&lt;").replace(/>/g, "&gt;") ?? '';
            }

            function showSkeleton(count = 6) {
                const template = document.getElementById('skeletonCard');
                if (!template) return;
                for (let i = 0; i < count; i++) {
                    grid.appendChild(template.content.cloneNode(true));
                }
            }

            function fetchVideos(reset = false) {
                if (loading || finished) return;
                loading = true;

                if (reset) {
                    page = 1;
                    finished = false;
                    grid.innerHTML = '';
                }

                showSkeleton(6);

                const params = new URLSearchParams({
                    page,
                    channel: channelFilter,
                    category: categoryFilter
                });

                axios.get(`/videos/list?${params.toString()}`)
                    .then(res => {
                        grid.querySelectorAll(".animate-pulse").forEach(el => el.remove());
                        const data = res.data ?? [];

                        if (!data.length) {
                            finished = true;
                            if (page === 1) {
                                grid.innerHTML =
                                    `<p class="col-span-full text-center text-gray-500 py-10">Belum ada video.</p>`;
                            }
                            return;
                        }

                        data.forEach(v => {
                            const card = document.createElement('div');
                            card.className =
                                'bg-white shadow rounded-lg overflow-hidden hover:shadow-lg transition flex flex-col';
                            card.innerHTML = `
                        <div class="relative">
                            <img src="${v.thumbnail ? '/storage/' + v.thumbnail : 'https://via.placeholder.com/400x200'}" class="w-full h-48 object-cover">
                            <span class="absolute top-2 right-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">${v.duration ?? '00:00'}</span>
                        </div>
                        <div class="p-4 flex flex-col flex-1 justify-between">
                            <h3 class="font-bold text-lg mb-1 line-clamp-2">${escapeHTML(v.title)}</h3>
                            <p class="text-gray-500 text-sm mb-1">Channel: ${escapeHTML(v.channel?.name ?? '-')}</p>
                            <p class="text-gray-500 text-sm mb-2">Status: ${escapeHTML(v.status)}</p>
                            <div class="flex justify-end gap-2 mt-3">
                                <a href="/videos/${v.id}/edit" class="text-blue-600 hover:text-blue-800"><i class="ri-edit-line text-xl"></i></a>
                                <button onclick="deleteVideo(${v.id})" class="text-red-600 hover:text-red-800"><i class="ri-delete-bin-6-line text-xl"></i></button>
                            </div>
                        </div>
                    `;
                            grid.appendChild(card);
                        });

                        page++;
                    })
                    .catch(() => toast('Gagal memuat video', true))
                    .finally(() => loading = false);
            }

            window.deleteVideo = function(id) {
                Swal.fire({
                    title: 'Yakin hapus video ini?',
                    text: 'Tindakan ini tidak bisa dibatalkan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post(`/videos/${id}/delete`)
                            .then(() => {
                                Swal.fire('Berhasil!', 'Video berhasil dihapus.', 'success');
                                fetchVideos(true);
                            })
                            .catch(() => Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus.',
                                'error'));
                    }
                });
            }

            filterChannel.addEventListener('change', function() {
                channelFilter = this.value;
                fetchVideos(true);
            });

            filterCategory.addEventListener('change', function() {
                categoryFilter = this.value;
                fetchVideos(true);
            });

            window.addEventListener('scroll', function() {
                if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 300) {
                    fetchVideos();
                }
            });

            fetchVideos();
        });
    </script>
@endsection
