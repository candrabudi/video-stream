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

    public function create()
    {
        return view('bo.videos.create');
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

        $thumbnailPath = $request->hasFile('thumbnail')
            ? $request->file('thumbnail')->store('videos', 'public')
            : null;

        $videoPath = $request->file('video_file')->store('videos', 'public');

        $video = Video::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'channel_id' => $validated['channel_id'],
            'category_id' => $validated['category_id'] ?? null,
            'thumbnail' => $thumbnailPath,
            'video_path' => $videoPath,
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
            if ($video->thumbnail && Storage::disk('public')->exists($video->thumbnail)) {
                Storage::disk('public')->delete($video->thumbnail);
            }
            $thumbnailPath = $request->file('thumbnail')->store('videos', 'public');
        }

        if ($request->hasFile('video_file')) {
            if ($video->video_path && Storage::disk('public')->exists($video->video_path)) {
                Storage::disk('public')->delete($video->video_path);
            }
            $videoPath = $request->file('video_file')->store('videos', 'public');
            $video->video_path = $videoPath;
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
