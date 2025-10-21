<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Channel;
use App\Models\Video;
use App\Models\VideoView;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total data
        $totalChannels = Channel::count();
        $totalVideos = Video::count();
        $totalCategories = Category::count();
        $totalViews = VideoView::count();

        // Video terbaru
        $latestVideos = Video::with('channel', 'category')
            ->latest('uploaded_at')
            ->take(5)
            ->get();

        // Statistik views 7 hari terakhir
        $viewsPerDay = VideoView::select(
            DB::raw('DATE(viewed_at) as date'),
            DB::raw('count(*) as views')
        )
            ->where('viewed_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Format data untuk chart
        $chartLabels = $viewsPerDay->pluck('date')->map(fn ($d) => date('d M', strtotime($d)));
        $chartData = $viewsPerDay->pluck('views');

        // Top 5 channel berdasarkan total views
        $topChannels = Channel::select('channels.*', DB::raw('SUM(videos.views_count) as total_views'))
            ->join('videos', 'channels.id', '=', 'videos.channel_id')
            ->groupBy('channels.id')
            ->orderByDesc('total_views')
            ->take(5)
            ->get();

        return view('bo.dashboard.index', compact(
            'totalChannels',
            'totalVideos',
            'totalCategories',
            'totalViews',
            'latestVideos',
            'chartLabels',
            'chartData',
            'topChannels'
        ));
    }
}
