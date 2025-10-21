<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // 1. Tampilkan halaman index
    public function index()
    {
        return view('bo.categories.index');
    }

    // 2. List data (AJAX)
    public function list()
    {
        $categories = Category::latest()->get();

        return response()->json($categories);
    }

    // 3. Simpan category baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'keywords' => 'nullable|string',
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'keywords' => $validated['keywords'] ?? null,
        ]);

        return response()->json(['success' => true, 'message' => 'Category berhasil dibuat', 'data' => $category]);
    }

    // 4. Ambil data 1 category (edit)
    public function edit($id)
    {
        $category = Category::findOrFail($id);

        return response()->json($category);
    }

    // 5. Update category
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$category->id,
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'keywords' => 'nullable|string',
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'keywords' => $validated['keywords'] ?? null,
        ]);

        return response()->json(['success' => true, 'message' => 'Category berhasil diperbarui', 'data' => $category]);
    }

    // 6. Hapus category
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['success' => true, 'message' => 'Category berhasil dihapus']);
    }
}
