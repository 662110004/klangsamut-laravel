<?php

namespace App\Http\Controllers;

// --- Imports ---
// นำเข้า Model ที่จำเป็น (Category)
use App\Models\Category;
// นำเข้า Request เพื่อรับข้อมูลจากฟอร์มและ URL
use Illuminate\Http\Request;
// นำเข้า Rule สำหรับสร้าง Validation Rule ที่ซับซ้อน (เช่น unique->ignore)
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * แสดงหน้ารายการหมวดหมู่ทั้งหมด (ตาราง CRUD ฝั่ง Admin)
     *
     * (ฟังก์ชันนี้ถูกเรียกโดย Route::resource 'categories'
     * ซึ่งจะ map ไปที่ GET /admin/categories)
     * ดูไฟล์: routes/web.php (ในกลุ่ม 'role:admin')
     */
    public function index(Request $request)
    {
        // 1. ดึงคำค้นหา (ถ้ามี) จาก URL (เช่น ?search=...)
        $search = $request->input('search');

        // 2. เริ่มสร้าง Query โดยใช้ Category Model
        $categories = Category::query()
            // 3. (Conditional Query) ถ้า $search มีค่า (ไม่ว่าง)
            ->when($search, function ($query, $search) {
                // ให้เพิ่มเงื่อนไขการค้นหา
                return $query->where('name', 'like', "%{$search}%") // ค้นหาจาก 'ชื่อ' (name)
                    ->orWhere('code', 'like', "%{$search}%"); // หรือค้นหาจาก 'รหัส' (code)
            })
            // 4. แบ่งหน้า (Pagination) แสดงผลหน้าละ 10 รายการ
            ->paginate(10)
            // 5. ให้ลิงก์แบ่งหน้า (1, 2, 3...) ยังคงมีคำค้นหาต่อท้าย
            ->withQueryString();

        // 6. ส่งข้อมูล $categories ไปยัง View ของ Admin
        return view('admin.categories.index', compact('categories')); // ไฟล์: resources/views/admin/categories/index.blade.php
    }

    /**
     * แสดงฟอร์มสำหรับ "สร้าง" หมวดหมู่ใหม่
     *
     * (ฟังก์ชันนี้ถูกเรียกโดย Route::resource 'categories'
     * ซึ่งจะ map ไปที่ GET /admin/categories/create)
     */
    public function create()
    {
        // 1. ดึงหมวดหมู่ทั้งหมดมา
        // (ไฟล์ Migration 2025_10_29_090504_add_parent_id_to_categories_table.php
        // บอกว่ามีการเพิ่ม 'parent_id' ดังนั้น การดึงมาทั้งหมดนี้
        // น่าจะเพื่อใช้สร้าง Dropdown "หมวดหมู่หลัก" (Parent Category))
        $categories = Category::all();

        // 2. ส่งหน้า View ที่มีฟอร์มเปล่าๆ กลับไป
        return view('admin.categories.create', compact('categories')); // ไฟล์: resources/views/admin/categories/create.blade.php
    }

    /**
     * "บันทึก" หมวดหมู่ใหม่ลงฐานข้อมูล (หลังจากกด Submit จากหน้า Create)
     *
     * (ฟังก์ชันนี้ถูกเรียกโดย Route::resource 'categories'
     * ซึ่งจะ map ไปที่ POST /admin/categories)
     */
    public function store(Request $request)
    {
        // 1. ตรวจสอบความถูกต้องของข้อมูล (Validation)
        $request->validate([
            'name' => 'required|string|max:255|unique:categories', // ต้องมี, เป็นข้อความ, ไม่เกิน 255 ตัว, ห้ามซ้ำ
            'code' => 'required|string|max:50|unique:categories',  // ต้องมี, เป็นข้อความ, ไม่เกิน 50 ตัว, ห้ามซ้ำ
            // 'parent_id' (ถ้ามี) ควรจะเพิ่ม validation 'nullable|exists:categories,id' ด้วย
        ]);

        // 2. สร้างข้อมูลลงตาราง 'categories'
        // (ใช้ Mass Assignment จาก $request->all() เพราะใน Model 'Category.php'
        // ได้กำหนด 'fillable' ไว้แล้ว)
        Category::create($request->all());

        // 3. กลับไปหน้า Admin Index พร้อมข้อความแจ้งเตือน
        return redirect(session()->get('bookmark_url.categories', route('admin.categories.index')))
            ->with('success', 'Category created successfully.');
    }

    /**
     * แสดงหน้ารายละเอียดหมวดหมู่ 1 หมวด (ฝั่ง Public)
     * (Category $category) คือ Route Model Binding - Laravel จะหาหมวดหมู่จาก 'code' ใน URL ให้เราอัตโนมัติ
     *
     * (ฟังก์ชันนี้ถูกเรียกโดย Route ที่เราสร้างเองใน routes/web.php
     * คือ GET /categories/{category:code})
     */
    public function show(Category $category)
    {
        // 1. โหลดข้อมูล 'books' (หนังสือทั้งหมดในหมวดนี้) มาเตรียมไว้
        // 'books' คือชื่อฟังก์ชัน 'belongsToMany' ใน Model 'Category.php'
        $category->load('books');

        // 2. ส่งข้อมูล $category (รวมหนังสือ) ไปยัง View
        return view('categories.show', compact('category')); // ไฟล์: resources/views/categories/show.blade.php
    }

    /**
     * แสดงฟอร์มสำหรับ "แก้ไข" หมวดหมู่
     *
     * (ฟังก์ชันนี้ถูกเรียกโดย Route::resource 'categories'
     * ซึ่งจะ map ไปที่ GET /admin/categories/{category}/edit)
     */
    public function edit(Category $category)
    {
        // 1. ดึงหมวดหมู่ทั้งหมดมาเพื่อใช้ใน Dropdown (เลือก Parent Category)
        $allCategories = Category::all();

        // 2. ส่งข้อมูล $category (หมวดที่จะแก้) และ $allCategories (สำหรับ Dropdown) ไปยัง View
        return view('admin.categories.edit', compact('category', 'allCategories')); // ไฟล์: resources/views/admin/categories/edit.blade.php
    }

    /**
     * "อัปเดต" หมวดหมู่ในฐานข้อมูล (หลังจากกด Submit จากหน้า Edit)
     *
     * (ฟังก์ชันนี้ถูกเรียกโดย Route::resource 'categories'
     * ซึ่งจะ map ไปที่ PUT/PATCH /admin/categories/{category})
     */
    public function update(Request $request, Category $category)
    {
        // 1. ตรวจสอบข้อมูล (Validation)
        $request->validate([
            // (สำคัญ) Rule::unique('categories')->ignore($category->id)
            // หมายความว่า 'name' ต้องไม่ซ้ำกับใคร "ยกเว้น" (ignore) ID ของตัวเอง
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->ignore($category->id)
            ],
            // 'code' ก็ต้องใช้ Rule 'ignore' เหมือนกัน
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('categories')->ignore($category->id)
            ]
        ]);

        // 2. อัปเดตข้อมูล (Mass Assignment)
        $category->update($request->all());

        // 3. กลับไปหน้า Show (ฝั่ง Public) ของหมวดหมู่นี้
        return redirect(session()->get('bookmark_url.categories_show', route('categories.show', $category->code)))
            ->with('success', 'Category updated successfully.');
    }

    /**
     * "ลบ" หมวดหมู่ออกจากฐานข้อมูล
     *
     * (ฟังก์ชันนี้ถูกเรียกโดย Route::resource 'categories'
     * ซึ่งจะ map ไปที่ DELETE /admin/categories/{category})
     */
    public function destroy(Category $category)
    {
        // --- (สำคัญ) การป้องกันการลบ ---
        // นี่คือการตรวจสอบ "ระดับแอปพลิเคชัน" (Application-Level)
        // เพื่อป้องกันไม่ให้ลบหมวดหมู่ที่มีหนังสือผูกอยู่

        // 1. ตรวจสอบว่ามีหนังสือ (books) ที่ผูกกับหมวดหมู่นี้หรือไม่
        $bookCount = $category->books()->count();

        // 2. ถ้ามี (มากกว่า 0)
        if ($bookCount > 0) {
            // ให้ Redirect กลับไป พร้อมข้อความ Error (ไม่ทำการลบ)
            return redirect()->route('admin.categories.index')->with('error', 'Cannot delete, this category is in use.');
        }

        // 3. (ถ้า $bookCount == 0) ลบข้อมูลหมวดหมู่ออกจาก Database
        // (หมายเหตุ: การลบนี้จะไปลบข้อมูลใน 'book_category' ด้วย
        // เพราะใน Migration 'create_book_category_table.php'
        // เราตั้ง 'onDelete('cascade')' ไว้)
        $category->delete();

        // 4. ส่งกลับไปหน้า index พร้อม notification
        return redirect(session()->get('bookmark_url.categories', route('admin.categories.index')))
            ->with('success', 'Category deleted successfully.');
    }
}
