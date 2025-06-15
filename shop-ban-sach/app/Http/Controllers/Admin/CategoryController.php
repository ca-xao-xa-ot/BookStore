<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('books')->paginate(10);
        return view('admin.pages.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.pages.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required|string|max:255|unique:categories',
                'description' => 'nullable|string',
            ],
            [
                'name.required' => 'Tên danh mục là bắt buộc.',
                'name.unique' => 'Tên danh mục đã tồn tại.',
                'description.string' => 'Mô tả phải là một chuỗi.',
            ]
        );

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tạo danh mục thành công',
                'redirect' => route('admin.categories.index')
            ]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Tạo danh mục thành công');
    }

    public function edit(Category $category)
    {
        return view('admin.pages.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật danh mục thành công',
                'redirect' => route('admin.categories.index')
            ]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Xóa danh mục thành công');
    }
}
