<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChannelController extends Controller
{
    // 1. Tampilkan halaman index
    public function index()
    {
        return view('channels.index');
    }

    // 2. List data (AJAX)
    public function listData()
    {
        $channels = Channel::latest()->get();

        return response()->json($channels);
    }

    // 3. Simpan channel baru
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
            'subscribers' => 0,
        ]);

        return response()->json(['success' => true, 'message' => 'Channel berhasil dibuat', 'data' => $channel]);
    }

    // 4. Ambil data 1 channel (edit)
    public function edit($id)
    {
        $channel = Channel::findOrFail($id);

        return response()->json($channel);
    }

    // 5. Update channel
    public function update(Request $request, $id)
    {
        $channel = Channel::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:channels,name,'.$channel->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
            'subscribers' => 'nullable|numeric|min:0',
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
            'subscribers' => $validated['subscribers'] ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Channel berhasil diperbarui',
            'data' => $channel,
        ]);
    }

    // 6. Hapus channel
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
