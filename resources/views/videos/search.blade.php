@extends('template.app')
@section('content')

<div class="bg-white min-h-screen">
    <div class="max-w-[1800px] mx-auto px-4 lg:px-6 py-4">
        
        <div class="mb-6 flex items-center gap-3 border-b border-gray-200 pb-4">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <h1 class="text-xl text-gray-800">
                Hasil pencarian untuk: <span class="font-semibold">"{{ $query }}"</span>
            </h1>
        </div>

        <div class="flex gap-6">
            <div class="flex-1 space-y-4">
                @forelse ($videos as $video)
                    <a href="{{ route('homepage.video.show', $video->id) }}" 
                       class="flex gap-4 hover:bg-gray-50 rounded-xl transition duration-150 p-2 group">
                    
                        <div class="relative w-[360px] aspect-video bg-gray-200 rounded-xl overflow-hidden flex-shrink-0">
                            <img src="{{ asset('/storage/'.$video->thumbnail ?? 'https://frompaddocktoplate.com.au/wp-content/uploads/2021/10/no-thumbnail.png') }}" 
                                 alt="{{ $video->title }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @if($video->duration)
                                <span class="absolute bottom-2 right-2 bg-black bg-opacity-90 text-white text-xs font-semibold px-2 py-1 rounded">
                                    {{ $video->duration }}
                                </span>
                            @endif
                        </div>

                        <div class="flex-1 py-1">
                            <h2 class="text-lg font-medium text-gray-900 line-clamp-2 mb-2 group-hover:text-gray-900">
                                {{ $video->title }}
                            </h2>

                            <div class="flex items-center gap-2 text-sm text-gray-600 mb-3">
                                <span>{{ number_format($video->views_count) }} views</span>
                                <span class="text-gray-400">•</span>
                                <span>{{ $video->uploaded_at ? $video->uploaded_at->diffForHumans() : '' }}</span>
                            </div>

                            <!-- Channel Info -->
                            <div class="flex items-center gap-3 mb-3">
                                <img src="{{ asset('/storage/'.$video->channel->avatar ?? 'https://upload.wikimedia.org/wikipedia/commons/7/7c/Profile_avatar_placeholder_large.png?20150327203541') }}" 
                                     alt="{{ $video->channel->name }}" 
                                     class="w-9 h-9 rounded-full object-cover">
                                <span class="text-sm text-gray-600 font-medium">{{ $video->channel->name }}</span>
                            </div>

                            <!-- Description -->
                           <p class="text-sm text-gray-600 line-clamp-2 leading-relaxed">{{ Str::limit(strip_tags($video->description), 120, '...') }}</p>

                        </div>
                    </a>
                @empty
                    <div class="text-center py-20">
                        <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <h3 class="text-xl font-medium text-gray-700 mb-2">Tidak ada hasil ditemukan</h3>
                        <p class="text-gray-500">Coba gunakan kata kunci yang berbeda atau lebih umum</p>
                    </div>
                @endforelse
            </div>

            <!-- SIDEBAR RECOMMENDATIONS -->
            <div class="hidden xl:block w-[400px] flex-shrink-0">
                <div class="sticky top-4">
                    @foreach ($recommendations as $rec)
                        <a href="{{ route('homepage.video.show', $rec->id) }}" 
                           class="flex gap-2 mb-3 hover:bg-gray-50 p-2 rounded-lg transition group">
                            
                            <!-- Thumbnail -->
                            <div class="relative w-[168px] aspect-video bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                <img src="{{ asset('/storage/'.$rec->thumbnail ?? 'https://frompaddocktoplate.com.au/wp-content/uploads/2021/10/no-thumbnail.png') }}" 
                                     alt="{{ $rec->title }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                @if($rec->duration)
                                    <span class="absolute bottom-1 right-1 bg-black bg-opacity-90 text-white text-[11px] font-semibold px-1.5 py-0.5 rounded">
                                        {{ $rec->duration }}
                                    </span>
                                @endif
                            </div>

                            <!-- Video Info -->
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 line-clamp-2 mb-1 leading-snug">
                                    {{ $rec->title }}
                                </h4>
                                <p class="text-xs text-gray-600 mb-0.5">{{ $rec->channel->name }}</p>
                                <div class="flex items-center gap-1 text-xs text-gray-500">
                                    <span>{{ number_format($rec->views_count) }} views</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($videos->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $videos->links() }}
        </div>
        @endif
    </div>
</div>

@endsection