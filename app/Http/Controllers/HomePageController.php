<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    public function index()
    {
        return view('homepage.index');
    }

    public function getVideos(Request $request)
    {
        $category = $request->query('category');

        $query = Video::with(['channel', 'category'])->latest();

        if ($category && $category !== 'all') {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('slug', $category);
            });
        }

        $videos = $query->get()->map(function ($video) {
            return [
                'id' => $video->id,
                'title' => $video->title,
                'channel' => $video->channel->name ?? '-',
                'views' => $video->views_count,
                'time' => $video->uploaded_at ? Carbon::parse($video->uploaded_at)->diffForHumans() : '-',
                'duration' => $video->duration ?? '-',
                'thumbnail' => $video->thumbnail ? asset('storage/'.$video->thumbnail) : null,
                'gradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                'category' => $video->category->slug ?? 'uncategorized',
            ];
        });

        return response()->json($videos);
    }

    public function showVideo($id)
    {
        $video = Video::with(['channel', 'category'])->findOrFail($id);

        return view('homepage.video', compact('video'));
    }
}
