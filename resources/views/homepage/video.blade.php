@extends('template.app')

@section('content')
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <div class="bg-gray-50 min-h-screen pt-4 pb-12">
        <div class="w-full mx-auto px-4 lg:px-8 xl:px-16 lg:flex lg:gap-8">
            <div class="flex-1 lg:max-w-[calc(100%-384px)]">
                <div class="video-player mb-4 aspect-video bg-black rounded-xl shadow-lg overflow-hidden">
                    @if ($video->video_path)
                        <video controls poster="{{ $video->thumbnail ? asset('storage/' . $video->thumbnail) : '' }}"
                            class="w-full h-full object-cover">
                            <source src="{{ asset('storage/' . $video->video_path) }}" type="video/mp4">
                            Browsermu tidak mendukung video tag.
                        </video>
                    @elseif($video->video_url)
                        <iframe width="100%" height="100%" src="{{ $video->video_url }}" frameborder="0" allowfullscreen
                            class="w-full h-full"></iframe>
                    @else
                        <div class="flex items-center justify-center w-full h-full text-white text-lg">Video tidak tersedia
                        </div>
                    @endif
                </div>

                <div class="video-header mb-6">
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-3 leading-snug">
                        {{ $video->title ?? 'Judul Video Tidak Ditemukan' }}</h1>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b pb-4">
                        <div class="flex items-center mb-3 sm:mb-0">
                            <div
                                class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center text-white font-semibold text-lg mr-3">
                                {{ substr($video->channel->name ?? 'C', 0, 1) }}</div>
                            <div>
                                <p class="font-semibold text-gray-900 leading-tight">
                                    {{ $video->channel->name ?? 'Channel Anonim' }}</p>
                                <p class="text-sm text-gray-600">{{ number_format(rand(100, 5000)) }} subscribers</p>
                            </div>
                            <button
                                class="ml-4 px-3 py-1 bg-red-600 text-white font-medium text-sm rounded-full hover:bg-red-700 transition">Subscribe</button>
                        </div>
                        <div class="flex gap-2 text-sm text-gray-700">
                            @if ($video->video_path)
                                <a href="{{ asset('storage/' . $video->video_path) }}" download
                                    class="flex items-center bg-red-600 text-white rounded-full p-1.5 px-3 hover:bg-red-700 transition shadow">
                                    <i class='bx bx-download text-lg mr-1'></i> Download
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-gray-100 p-4 rounded-xl space-y-4 hover:bg-gray-200 transition">
                    <div>
                        <p class="text-sm font-semibold text-gray-900 mb-1">
                            {{ number_format($video->views_count ?? 0) }} views
                            <span class="mx-1">•</span>
                            {{ $video->category->name ?? 'Uncategorized' }}
                            <span class="mx-1">•</span>
                            {{ \Carbon\Carbon::parse($video->created_at ?? now())->diffForHumans() }}
                        </p>
                        <div class="text-sm text-gray-800" id="description-wrapper">
                            <p id="description-text" class="line-clamp-3">{!! trim($video->description ?? '<em>Tidak ada deskripsi yang tersedia untuk video ini.</em>') !!}</p>
                            <button id="toggle-description"
                                class="mt-2 text-blue-600 font-semibold hover:underline hidden">Show More</button>
                        </div>
                    </div>

                    @if (!empty($video->report_link))
                        @php
                            $decoded = html_entity_decode($video->report_link);
                            $decoded = preg_replace('/<\/?p[^>]*>/', "\n", $decoded);
                            $decoded = str_replace(['<br>', '<br/>', '<br />'], "\n", $decoded);
                            $text = strip_tags($decoded);
                            $text = preg_replace('/(https?:\/\/)(?=.*https?:\/\/)/i', "$1\n", $text);
                            preg_match_all('/https?:\/\/[^\s"\'<>]+/i', $text, $matches);
                            $links = array_unique($matches[0]);
                        @endphp

                        @if (count($links))
                            <div class="report-link mt-3">
                                <h4 class="text-sm font-semibold text-gray-900 mb-2">Laporan Terkait:</h4>
                                <div class="grid sm:grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach ($links as $url)
                                        <a href="{{ html_entity_decode($url) }}" target="_blank"
                                            class="flex items-center gap-3 bg-white border border-gray-200 rounded-lg p-3 shadow-sm hover:shadow-md hover:border-red-500 transition group">
                                            <div
                                                class="w-10 h-10 flex items-center justify-center rounded-full bg-red-100 text-red-600 group-hover:bg-red-600 group-hover:text-white transition">
                                                <i class='bx bx-link text-xl'></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-medium text-gray-900 group-hover:text-red-600 transition">
                                                    {{ parse_url($url, PHP_URL_HOST) ?? 'Link' }}</p>
                                                <p class="text-xs text-gray-500 truncate max-w-[240px]">
                                                    {{ html_entity_decode($url) }}</p>
                                            </div>
                                            <i
                                                class='bx bx-link-external ml-auto text-gray-400 group-hover:text-red-500'></i>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <div class="w-full lg:w-96 mt-8 lg:mt-0">
                <h3 class="text-gray-900 font-semibold mb-3 border-b pb-2 hidden lg:block">Video Terkait</h3>
                <div class="flex flex-col gap-3">
                    @foreach (\App\Models\Video::latest()->take(10)->get() as $v)
                        <a href="{{ route('homepage.video.show', $v->id) }}"
                            class="flex gap-3 items-start w-full transition">
                            <div
                                class="w-[160px] h-[90px] flex-shrink-0 relative overflow-hidden rounded-lg bg-gray-300 cursor-pointer thumbnail-wrapper">
                                <img src="{{ $v->thumbnail ? asset('storage/' . $v->thumbnail) : 'https://placehold.co/160x90/e0e0e0/555555?text=NO+THUMB' }}"
                                    alt="{{ $v->title }}" class="w-full h-full object-cover">
                                <div class="play-overlay"><i class='bx bx-play-circle'></i></div>
                                <span
                                    class="absolute bottom-1 right-1 bg-black bg-opacity-80 text-white text-xs px-1 py-0.5 rounded-sm font-medium">{{ $v->duration ?? '00:00' }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-gray-900 text-sm font-semibold line-clamp-2 leading-snug">
                                    {{ $v->title }}</h4>
                                <p class="text-gray-600 text-xs mt-1 leading-tight">{{ $v->channel->name ?? 'Channel' }}
                                </p>
                                <p class="text-gray-600 text-xs">{{ number_format($v->views_count) }} views</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const descText = document.getElementById('description-text');
            const toggleBtn = document.getElementById('toggle-description');
            const lineHeight = parseInt(window.getComputedStyle(descText).lineHeight);
            const maxHeight = lineHeight * 3;
            if (descText.scrollHeight > maxHeight) toggleBtn.classList.remove('hidden');
            toggleBtn.addEventListener('click', function() {
                descText.classList.toggle('line-clamp-3');
                toggleBtn.textContent = descText.classList.contains('line-clamp-3') ? 'Show More' :
                    'Show Less';
            });
        });
    </script>

    <style>
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .play-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            justify-content: center;
            align-items: center;
            pointer-events: none;
            border-radius: 0.5rem;
            opacity: 0;
            transition: opacity 0.2s, transform 0.2s;
        }

        .thumbnail-wrapper:hover .play-overlay {
            opacity: 1;
            transform: scale(1.05);
        }

        .play-overlay i {
            font-size: 36px;
            color: rgba(255, 255, 255, 0.9);
        }
    </style>
@endsection
