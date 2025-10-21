@extends('bo.template.app')
@section('title', 'Dashboard Video')

@section('content')
    <div class="d-flex align-items-center justify-content-between my-4 page-header-breadcrumb flex-wrap gap-2">
        <div>
            <p class="fw-medium fs-20 mb-0">Selamat datang di <strong>Video Studio 👋</strong></p>
            <p class="fs-13 text-muted mb-0">Pantau performa video, channel, dan statistik tontonanmu.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card custom-card text-center">
                <div class="card-body">
                    <span class="avatar avatar-rounded bg-primary-transparent mb-2">
                        <i class="ri-movie-fill fs-20"></i>
                    </span>
                    <h4 class="fw-semibold mb-0">{{ $totalVideos }}</h4>
                    <p class="text-muted mb-0">Total Video</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card custom-card text-center">
                <div class="card-body">
                    <span class="avatar avatar-rounded bg-success-transparent mb-2">
                        <i class="ri-user-voice-fill fs-20"></i>
                    </span>
                    <h4 class="fw-semibold mb-0">{{ $totalChannels }}</h4>
                    <p class="text-muted mb-0">Total Channel</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card custom-card text-center">
                <div class="card-body">
                    <span class="avatar avatar-rounded bg-warning-transparent mb-2">
                        <i class="ri-eye-fill fs-20"></i>
                    </span>
                    <h4 class="fw-semibold mb-0">{{ number_format($totalViews) }}</h4>
                    <p class="text-muted mb-0">Total Views</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card custom-card text-center">
                <div class="card-body">
                    <span class="avatar avatar-rounded bg-info-transparent mb-2">
                        <i class="ri-folder-video-fill fs-20"></i>
                    </span>
                    <h4 class="fw-semibold mb-0">{{ $totalCategories }}</h4>
                    <p class="text-muted mb-0">Total Kategori</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Statistik Views -->
        <div class="col-xl-8">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Statistik Penayangan (7 Hari Terakhir)</div>
                </div>
                <div class="card-body">
                    <div id="viewsChart"></div>
                </div>
            </div>
        </div>

        <!-- Top Channel -->
        <div class="col-xl-4">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Top Channel Berdasarkan Views</div>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach ($topChannels as $ch)
                            <li class="list-group-item d-flex align-items-center gap-2">
                                <img src="{{ $ch->avatar ?? asset('assets/images/default-avatar.png') }}"
                                    class="avatar avatar-md avatar-rounded" alt="avatar">
                                <div class="flex-fill">
                                    <span class="fw-medium d-block">{{ $ch->name }}</span>
                                    <span class="text-muted fs-12">{{ number_format($ch->total_views) }} views</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Video Terbaru</div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table text-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Thumbnail</th>
                                    <th>Judul</th>
                                    <th>Channel</th>
                                    <th>Kategori</th>
                                    <th>Status</th>
                                    <th>Views</th>
                                    <th>Upload</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($latestVideos as $video)
                                    <tr>
                                        <td><img src="{{ $video->thumbnail }}" width="60" class="rounded-2"></td>
                                        <td>{{ $video->title }}</td>
                                        <td>{{ $video->channel->name }}</td>
                                        <td>{{ $video->category->name ?? '-' }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $video->status === 'published' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($video->status) }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($video->views_count) }}</td>
                                        <td>{{ $video->uploaded_at?->format('d M Y') ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Belum ada video</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
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
                fontFamily: 'Nunito, sans-serif'
            },
            series: [{
                name: 'Views',
                data: @json($chartData)
            }],
            xaxis: {
                categories: @json($chartLabels),
                title: {
                    text: 'Tanggal'
                }
            },
            yaxis: {
                title: {
                    text: 'Jumlah Views'
                }
            },
            colors: ['#4e73df'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            fill: {
                opacity: 0.2,
                type: 'gradient'
            }
        };
        new ApexCharts(document.querySelector("#viewsChart"), options).render();
    </script>
@endpush
