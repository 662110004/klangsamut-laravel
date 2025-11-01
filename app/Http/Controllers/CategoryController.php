<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search'); // 3. ดึงคำค้นหา

        // 4. แก้ไข Query
        $categories = Category::query()
            ->when($search, function ($query, $search) {
                // ค้นหาใน 'name' หรือ 'code'
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->paginate(10)
            ->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'code' => 'required|string|max:50|unique:categories',
        ]);

        Category::create($request->all());

        return redirect(session()->get('bookmark_url.categories', route('admin.categories.index')))
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        // โหลดหนังสือที่เกี่ยวข้องกับหมวดหมู่นี้มาด้วย
        $category->load('books');

        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        // 3. ดึงหมวดหมู่ทั้งหมดมาเพื่อใช้ใน Dropdown
        $allCategories = Category::all();
        return view('admin.categories.edit', compact('category', 'allCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([

            // เปลี่ยน 'name' ให้ใช้ Rule class
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->ignore($category->id)
            ],

            // เพิ่ม 'code' โดยใช้ Rule class
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('categories')->ignore($category->id)
            ]
        ]);

        $category->update($request->all());

        return redirect(session()->get('bookmark_url.categories_show', route('categories.show', $category->code)))
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // (ควรเพิ่ม: ตรวจสอบว่ามีหนังสือใช้หมวดหมู่นี้หรือไม่)
        // $bookCount = $category->books()->count();
        // if ($bookCount > 0) {
        //     return redirect()->route('admin.categories.index')->with('error', 'Cannot delete, this category is in use.');
        // }

        // ลบข้อมูล
        $category->delete();

        // ส่งกลับไปหน้า index พร้อม notification
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }
}
