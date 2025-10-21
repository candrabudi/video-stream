@extends('template.app')

@section('content')
    <div class="max-w-6xl mx-auto py-6 px-2 lg:flex lg:gap-6">

        <!-- Main Video Area -->
        <div class="flex-1">
            <div class="video-player mb-4">
                @if ($video->video_path)
                    <video controls poster="{{ $video->thumbnail ? asset('storage/' . $video->thumbnail) : '' }}"
                        class="w-full rounded-lg">
                        <source src="{{ asset('storage/' . $video->video_path) }}" type="video/mp4">
                        Browsermu tidak mendukung video tag.
                    </video>
                @elseif($video->video_url)
                    <iframe width="100%" height="480" src="{{ $video->video_url }}" frameborder="0"
                        allowfullscreen></iframe>
                @else
                    <p class="text-white">Video tidak tersedia</p>
                @endif
            </div>

            <div class="video-meta text-gray-300 mb-6">
                <h2 class="text-2xl font-semibold text-white mb-2">{{ $video->title }}</h2>
                <p><strong>Channel:</strong> {{ $video->channel->name ?? '-' }}</p>
                <p><strong>Kategori:</strong> {{ $video->category->name ?? 'Uncategorized' }}</p>
                <p><strong>Views:</strong> {{ number_format($video->views_count) }}</p>
                <p class="mt-4">{{ $video->description ?? '-' }}</p>
            </div>
        </div>

        <!-- Sidebar: Latest Videos -->
        <div class="w-full lg:w-80">
            <h3 class="text-white font-semibold mb-3">Latest Videos</h3>
            <div class="flex flex-col gap-4">
                @foreach (\App\Models\Video::latest()->take(10)->get() as $v)
                    <a href="{{ route('homepage.video.show', $v->id) }}"
                        class="flex gap-3 hover:bg-gray-800 rounded-lg p-2 transition">
                        <div class="w-32 flex-shrink-0 relative">
                            <img src="{{ $v->thumbnail ? asset('storage/' . $v->thumbnail) : '' }}" alt="{{ $v->title }}"
                                class="w-full h-20 object-cover rounded-md">
                            <span
                                class="absolute bottom-1 right-1 bg-black text-white text-xs px-1 rounded">{{ $v->duration ?? '00:00' }}</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-white text-sm font-semibold line-clamp-2">{{ $v->title }}</h4>
                            <p class="text-gray-400 text-xs">{{ $v->channel->name ?? '-' }}</p>
                            <p class="text-gray-400 text-xs">{{ number_format($v->views_count) }} views</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

    </div>
@endsection
