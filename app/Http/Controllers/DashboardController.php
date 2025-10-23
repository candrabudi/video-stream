<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Channel;
use App\Models\Video;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalChannels = Channel::count();
        $totalVideos = Video::count();
        $totalCategories = Category::count();

        $latestVideos = Video::with('channel', 'category')
            ->latest('uploaded_at')
            ->take(5)
            ->get();

        $uploadsPerDay = Video::select(
            DB::raw('DATE(uploaded_at) as date'),
            DB::raw('count(*) as uploads')
        )
            ->where('uploaded_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $chartLabels = $uploadsPerDay->pluck('date')->map(fn ($d) => date('d M', strtotime($d)));
        $chartData = $uploadsPerDay->pluck('uploads');

        $topChannels = Channel::select('channels.*', DB::raw('COUNT(videos.id) as total_uploads'))
            ->join('videos', 'channels.id', '=', 'videos.channel_id')
            ->groupBy('channels.id')
            ->orderByDesc('total_uploads')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalChannels',
            'totalVideos',
            'totalCategories',
            'latestVideos',
            'chartLabels',
            'chartData',
            'topChannels'
        ));
    }
}
