@extends('template.app')
@section('title', 'Dashboard Video')

@section('content')
    <div class="min-h-screen bg-gray-50 text-gray-800 p-4 sm:p-6 lg:p-8">
        <div class="flex flex-wrap items-center justify-between my-4 gap-2 border-b border-gray-200 pb-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Selamat datang di <span class="text-red-600">Video
                        Waskita</span> 👋</h1>
                <p class="text-sm text-gray-500 mt-1">Pantau jumlah video yang diunggah untuk kebutuhan monitoring dan
                    evaluasi.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
            <div class="bg-white shadow-lg border border-gray-100 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold uppercase text-gray-500">Total Video</span>
                    <i class="ri-movie-fill text-2xl text-red-600"></i>
                </div>
                <h4 class="text-4xl font-extrabold mt-2 text-gray-900">{{ $totalVideos }}</h4>
                <p class="text-sm text-gray-500 mt-1">Video Diunggah</p>
            </div>

            <div class="bg-white shadow-lg border border-gray-100 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold uppercase text-gray-500">Total Channel</span>
                    <i class="ri-user-voice-fill text-2xl text-green-600"></i>
                </div>
                <h4 class="text-4xl font-extrabold mt-2 text-gray-900">{{ $totalChannels }}</h4>
                <p class="text-sm text-gray-500 mt-1">Channel Aktif</p>
            </div>

            <div class="bg-white shadow-lg border border-gray-100 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold uppercase text-gray-500">Total Kategori</span>
                    <i class="ri-folder-video-fill text-2xl text-blue-600"></i>
                </div>
                <h4 class="text-4xl font-extrabold mt-2 text-gray-900">{{ $totalCategories }}</h4>
                <p class="text-sm text-gray-500 mt-1">Kategori Tersedia</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mt-10">
            <div class="col-span-2 bg-white shadow-lg border border-gray-100 rounded-xl overflow-hidden">
                <div class="border-b border-gray-200 p-4">
                    <h2 class="font-bold text-lg text-gray-800">Statistik Upload (7 Hari Terakhir)</h2>
                    <p class="text-xs text-gray-500">Tren upload harian</p>
                </div>
                <div class="p-4">
                    <div id="uploadsChart"></div>
                </div>
            </div>

            <div class="bg-white shadow-lg border border-gray-100 rounded-xl overflow-hidden">
                <div class="border-b border-gray-200 p-4">
                    <h2 class="font-bold text-lg text-gray-800">Top Channel</h2>
                    <p class="text-xs text-gray-500">Berdasarkan Total Uploads</p>
                </div>

                <ul class="divide-y divide-gray-200">
                    @foreach ($topChannels as $ch)
                        <li class="flex items-center p-3 gap-3">
                            <div
                                class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-xs font-semibold text-gray-700 uppercase">
                                {{ substr($ch->name, 0, 2) }}
                            </div>
                            <div class="flex-grow">
                                <p class="font-medium text-gray-900 truncate">{{ $ch->name }}</p>
                                <p class="text-xs text-gray-500">{{ $ch->total_uploads }} uploads</p>
                            </div>
                            <i class="ri-upload-cloud-fill text-blue-500 text-lg"></i>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="bg-white shadow-lg border border-gray-100 rounded-xl mt-10 overflow-hidden">
            <div class="border-b border-gray-200 p-4">
                <h2 class="font-bold text-lg text-gray-800">Video Terbaru</h2>
                <p class="text-xs text-gray-500">Daftar video yang baru saja diunggah</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-gray-600 uppercase tracking-wider">
                        <tr>
                            <th class="p-3">Thumbnail</th>
                            <th class="p-3">Judul</th>
                            <th class="p-3">Channel</th>
                            <th class="p-3">Kategori</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Upload</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($latestVideos as $video)
                            <tr class="hover:bg-gray-100">
                                <td class="p-3">
                                    <img src="{{ asset('storage/' . $video->thumbnail) }}"
                                        class="w-24 h-14 object-cover rounded-md">
                                </td>
                                <td class="p-3 font-medium text-gray-900 truncate">{{ $video->title }}</td>
                                <td class="p-3 text-gray-600">{{ $video->channel->name }}</td>
                                <td class="p-3 text-gray-600">{{ $video->category->name ?? '-' }}</td>
                                <td class="p-3">
                                    <span
                                        class="px-3 py-1 text-xs font-semibold rounded-full {{ $video->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">
                                        {{ ucfirst($video->status) }}
                                    </span>
                                </td>
                                <td class="p-3 text-gray-600">{{ $video->uploaded_at?->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center p-6 text-gray-400">Belum ada video yang diunggah.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script>
        var options = {
            chart: {
                type: 'area',
                height: 300,
                toolbar: {
                    show: false
                }
            },
            series: [{
                name: 'Uploads',
                data: @json($chartData)
            }],
            xaxis: {
                categories: @json($chartLabels)
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                opacity: 0.3,
                type: 'gradient'
            },
            colors: ['#2563eb']
        };
        new ApexCharts(document.querySelector("#uploadsChart"), options).render();
    </script>
@endpush
