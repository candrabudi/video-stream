<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChannelController extends Controller
{
    public function index()
    {
        return view('channels.index');
    }

    public function listData()
    {
        $channels = Channel::with('creator')->latest()->get();

        return response()->json(['data' => $channels]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:channels,name',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
        ]);

        $path = null;
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('channels', 'public');
        }

        $channel = Channel::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'avatar' => $path,
            'description' => $validated['description'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Channel berhasil dibuat',
            'data' => $channel->load('creator'),
        ]);
    }

    public function edit($id)
    {
        $channel = Channel::with('creator')->findOrFail($id);

        return response()->json(['data' => $channel]);
    }

    public function update(Request $request, $id)
    {
        $channel = Channel::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:channels,name,'.$channel->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
        ]);

        $path = $channel->avatar;
        if ($request->hasFile('avatar')) {
            if ($channel->avatar && Storage::disk('public')->exists($channel->avatar)) {
                Storage::disk('public')->delete($channel->avatar);
            }
            $path = $request->file('avatar')->store('channels', 'public');
        }

        $channel->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'avatar' => $path,
            'description' => $validated['description'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Channel berhasil diperbarui',
            'data' => $channel->load('creator'),
        ]);
    }

    public function destroy($id)
    {
        $channel = Channel::findOrFail($id);
        if ($channel->avatar && Storage::disk('public')->exists($channel->avatar)) {
            Storage::disk('public')->delete($channel->avatar);
        }
        $channel->delete();

        return response()->json(['success' => true, 'message' => 'Channel berhasil dihapus']);
    }
}
