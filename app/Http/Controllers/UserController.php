<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index');
    }

    public function list()
    {
        $users = User::latest()->get();

        return response()->json(['data' => $users]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'role' => 'required|in:super_admin,user',
            'password' => 'required|string|min:6',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('users', 'public');
        }

        $user = User::create(array_merge($validated, [
            'profile_picture' => $path,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dibuat',
            'data' => $user,
        ]);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        return response()->json(['data' => $user]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,'.$user->id,
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,'.$user->id,
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'role' => 'required|in:super_admin,user',
            'password' => 'nullable|string|min:6',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = $user->profile_picture;
        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('users', 'public');
        }

        if (!empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->update(array_merge($validated, [
            'profile_picture' => $path,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diperbarui',
            'data' => $user,
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
            Storage::disk('public')->delete($user->profile_picture);
        }
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus',
        ]);
    }
}
