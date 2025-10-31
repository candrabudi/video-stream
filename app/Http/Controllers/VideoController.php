<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Channel;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoController extends Controller
{
    public function index()
    {
        $channels = Channel::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('videos.index', compact('channels', 'categories'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $videos = Video::with(['channel'])
            ->when($query, function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhereHas('channel', fn ($qc) => $qc->where('name', 'like', "%{$query}%"));
            })
            ->where('status', 'published')
            ->latest()
            ->paginate(9);

        $recommendations = Video::with('channel')
            ->where('status', 'published')
            ->inRandomOrder()
            ->take(5)
            ->get();

        return view('videos.search', compact('videos', 'recommendations', 'query'));
    }

    public function apiSearch(Request $request)
    {
        $secret = $request->header('X-API-SECRET');
        $storedSecret = config('app.api_secret');

        if (!$storedSecret || !$secret || !hash_equals($storedSecret, $secret)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        if ($request->ip() !== null) {
            if (cache()->has('rl_'.$request->ip())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too Many Requests',
                ], 429);
            }
            cache()->put('rl_'.$request->ip(), true, now()->addSeconds(1));
        }

        $validated = $request->validate([
            'query' => 'nullable|string|max:100',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $query = strip_tags($validated['query'] ?? '');
        $limit = $validated['limit'] ?? 12;

        try {
            $videos = Video::with('channel')
                ->where('status', 'published')
                ->when($query, function ($q) use ($query) {
                    $q->where(function ($x) use ($query) {
                        $x->where('title', 'like', "%{$query}%")
                          ->orWhere('description', 'like', "%{$query}%")
                          ->orWhereHas('channel', fn ($c) => $c->where('name', 'like', "%{$query}%")
                          )
                          ->orWhereRaw('MATCH(title, description) AGAINST(? IN BOOLEAN MODE)', [$query]);
                    });
                })
                ->selectRaw('
                videos.*,
                (CASE
                    WHEN title LIKE ? THEN 50
                    WHEN description LIKE ? THEN 30
                    ELSE 10
                END) as priority
            ', ["%{$query}%", "%{$query}%"])
                ->orderBy('priority', 'desc')
                ->paginate($limit);

            return response()->json([
                'success' => true,
                'query' => $query,
                'pagination' => [
                    'total' => $videos->total(),
                    'per_page' => $videos->perPage(),
                    'current_page' => $videos->currentPage(),
                    'last_page' => $videos->lastPage(),
                ],
                'data' => $videos->items(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
            ], 500);
        }
    }

    public function listData(Request $request)
    {
        $search = $request->input('search');
        $channelId = $request->input('channel');
        $categoryId = $request->input('category');
        $perPage = 9;
        $page = $request->input('page', 1);

        $query = Video::with(['channel', 'category', 'creator'])
            ->when($search, function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('channel', function ($qc) use ($search) {
                      $qc->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('creator', function ($qc) use ($search) {
                      $qc->where('name', 'like', "%{$search}%");
                  });
            })
            ->when($channelId, fn ($q) => $q->where('channel_id', $channelId))
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->orderBy('uploaded_at', 'desc');

        $videos = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        return response()->json($videos);
    }

    public function create()
    {
        $channels = Channel::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('videos.create', compact('channels', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'channel_id' => 'required|exists:channels,id',
            'category_id' => 'nullable|exists:categories,id',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'video_file' => 'required|file|mimes:mp4,mov,mkv|max:512000',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'report_link' => 'nullable|max:1000',
        ]);

        $videoPath = $request->file('video_file')->store('videos', 'public');
        $videoFullPath = storage_path('app/public/'.$videoPath);

        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('videos', 'public');
        } else {
            $thumbnailName = pathinfo($videoPath, PATHINFO_FILENAME).'.jpg';
            $thumbnailPath = 'videos/'.$thumbnailName;
            $thumbnailFullPath = storage_path('app/public/'.$thumbnailPath);
            $cmd = 'ffmpeg -i '.escapeshellarg($videoFullPath).' -ss 00:00:00 -vframes 1 '.escapeshellarg($thumbnailFullPath);
            exec($cmd);
        }

        $duration = null;
        $cmdDuration = 'ffprobe -i '.escapeshellarg($videoFullPath).' -show_entries format=duration -v quiet -of csv="p=0"';
        $durationSec = shell_exec($cmdDuration);
        if ($durationSec) {
            $durationSec = floatval($durationSec);
            $hours = floor($durationSec / 3600);
            $minutes = floor(($durationSec % 3600) / 60);
            $seconds = floor($durationSec % 60);
            $duration = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        Video::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'channel_id' => $validated['channel_id'],
            'category_id' => $validated['category_id'] ?? null,
            'thumbnail' => $thumbnailPath,
            'video_path' => $videoPath,
            'duration' => $duration,
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'uploaded_at' => now(),
            'created_by' => Auth::user()->id,
            'report_link' => $validated['report_link'] ?? null,
        ]);

        return redirect()->route('videos.index')->with('success', 'Video berhasil dibuat');
    }

    public function edit($id)
    {
        $video = Video::findOrFail($id);
        $channels = Channel::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('videos.edit', compact('video', 'channels', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $video = Video::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'channel_id' => 'required|exists:channels,id',
            'category_id' => 'nullable|exists:categories,id',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'video_file' => 'nullable|file|mimes:mp4,mov,mkv|max:512000',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'report_link' => 'nullable|max:1000',
        ]);

        $thumbnailPath = $video->thumbnail;

        if ($request->hasFile('thumbnail')) {
            Storage::disk('public')->delete($thumbnailPath);
            $thumbnailPath = $request->file('thumbnail')->store('videos', 'public');
        }

        if ($request->hasFile('video_file')) {
            Storage::disk('public')->delete($video->video_path);
            $videoPath = $request->file('video_file')->store('videos', 'public');
            $videoFullPath = storage_path("app/public/$videoPath");

            if (!$request->hasFile('thumbnail')) {
                $thumbnailName = pathinfo($videoPath, PATHINFO_FILENAME).'.jpg';
                $thumbnailPath = "videos/$thumbnailName";
                exec("ffmpeg -i '$videoFullPath' -ss 00:00:00 -vframes 1 '".storage_path("app/public/$thumbnailPath")."'");
            }

            $durationSec = floatval(shell_exec("ffprobe -i '$videoFullPath' -show_entries format=duration -v quiet -of csv='p=0'"));
            $video->video_path = $videoPath;
            $video->duration = gmdate('H:i:s', $durationSec);
        }

        $video->update([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'channel_id' => $validated['channel_id'],
            'category_id' => $validated['category_id'],
            'thumbnail' => $thumbnailPath,
            'description' => $validated['description'],
            'status' => $validated['status'],
            'report_link' => $validated['report_link'],
        ]);

        return redirect()->route('videos.index')->with('success', 'Video berhasil diperbarui');
    }

    public function destroy($id)
    {
        $video = Video::findOrFail($id);
        if ($video->thumbnail && Storage::disk('public')->exists($video->thumbnail)) {
            Storage::disk('public')->delete($video->thumbnail);
        }
        if ($video->video_path && Storage::disk('public')->exists($video->video_path)) {
            Storage::disk('public')->delete($video->video_path);
        }
        $video->delete();

        return redirect()->route('videos.index')->with('success', 'Video berhasil dihapus');
    }
}
