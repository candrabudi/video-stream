<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoController extends Controller
{
    public function index()
    {
        return view('bo.videos.index');
    }

    public function listData()
    {
        $videos = Video::with(['channel', 'category'])->latest()->get();

        return response()->json($videos);
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

    public function searchApi(Request $request)
    {
        $query = $request->input('query');

        $videos = Video::with(['channel', 'category'])
            ->when($query, function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhereHas('channel', function ($q2) use ($query) {
                      $q2->where('name', 'like', "%{$query}%");
                  });
            })
            ->orderBy('uploaded_at', 'desc')
            ->take(15)
            ->get();

        $recommendations = Video::with('channel')
            ->inRandomOrder()
            ->take(5)
            ->get();

        return response()->json([
            'query' => $query,
            'results' => $videos,
            'recommendations' => $recommendations,
        ]);
    }

    public function create()
    {
        return view('videos.create');
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

        $video = Video::create([
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
            'views_count' => 0,
        ]);

        return redirect()->route('videos.index')->with('success', 'Video berhasil dibuat');
    }

    public function edit($id)
    {
        $video = Video::findOrFail($id);

        return view('bo.videos.edit', compact('video'));
    }

    public function update(Request $request, $id)
    {
        $video = Video::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'channel_id' => 'required|exists:channels,id',
            'category_id' => 'nullable|exists:categories,id',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'video_file' => 'nullable|file|mimes:mp4,mov,mkv|max:512000',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,published',
        ]);

        $thumbnailPath = $video->thumbnail;

        if ($request->hasFile('thumbnail')) {
            if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }
            $thumbnailPath = $request->file('thumbnail')->store('videos', 'public');
        }

        if ($request->hasFile('video_file')) {
            if ($video->video_path && Storage::disk('public')->exists($video->video_path)) {
                Storage::disk('public')->delete($video->video_path);
            }

            $videoPath = $request->file('video_file')->store('videos', 'public');
            $videoFullPath = storage_path('app/public/'.$videoPath);

            if (!$request->hasFile('thumbnail')) {
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

            $video->video_path = $videoPath;
            $video->duration = $duration;
        }

        $video->update([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'channel_id' => $validated['channel_id'],
            'category_id' => $validated['category_id'] ?? null,
            'thumbnail' => $thumbnailPath,
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
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
