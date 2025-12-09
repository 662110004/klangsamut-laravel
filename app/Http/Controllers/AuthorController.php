<?php

namespace App\Http\Controllers;

// --- Imports ---
// นำเข้า Model ที่จำเป็น (Author)
use App\Models\Author;
// นำเข้า Request เพื่อรับข้อมูลจากฟอร์มและ URL
use Illuminate\Http\Request;
// นำเข้า Rule สำหรับสร้าง Validation Rule ที่ซับซ้อน (เช่น unique->ignore)
use Illuminate\Validation\Rule;
// นำเข้า Storage Facade เพื่อจัดการไฟล์ (ลบ/อัปโหลดรูปภาพ)
use Illuminate\Support\Facades\Storage;

class AuthorController extends Controller
{
    /**
     * แสดงหน้ารายการผู้แต่งทั้งหมด (ตาราง CRUD ฝั่ง Admin)
     *
     * (ฟังก์ชันนี้ถูกเรียกโดย Route::resource 'authors'
     * ซึ่งจะ map ไปที่ GET /admin/authors)
     * ดูไฟล์: routes/web.php (ในกลุ่ม 'role:admin')
     */
    public function index(Request $request)
    {
        // 1. ดึงคำค้นหา (ถ้ามี) จาก URL (เช่น ?search=...)
        $search = $request->input('search');

        // 2. เริ่มสร้าง Query โดยใช้ Author Model
        $authors = Author::query()
            // 3. (Conditional Query) ถ้า $search มีค่า (ไม่ว่าง)
            ->when($search, function ($query, $search) {
                // ให้เพิ่มเงื่อนไขการค้นหา
                return $query->where('pseudonym', 'like', "%{$search}%") // ค้นหาจาก 'นามแฝง' (pseudonym)
                    ->orWhere('code', 'like', "%{$search}%"); // หรือค้นหาจาก 'รหัส' (code)
            })
            // 4. แบ่งหน้า (Pagination) แสดงผลหน้าละ 5 รายการ
            ->paginate(5)
            // 5. ให้ลิงก์แบ่งหน้า (1, 2, 3...) ยังคงมีคำค้นหาต่อท้าย
            ->withQueryString();

        // 6. ส่งข้อมูล $authors ไปยัง View ของ Admin
        return view('admin.authors.index', compact('authors')); // ไฟล์: resources/views/admin/authors/index.blade.php
    }

    /**
     * แสดงฟอร์มสำหรับ "สร้าง" ผู้แต่งใหม่
     *
     * (ฟังก์ชันนี้ถูกเรียกโดย Route::resource 'authors'
     * ซึ่งจะ map ไปที่ GET /admin/authors/create)
     */
    public function create()
    {
        // ส่งหน้า View ที่มีฟอร์มเปล่าๆ กลับไป
        return view('admin.authors.create'); // ไฟล์: resources/views/admin/authors/create.blade.php
    }

    /**
     * "บันทึก" ผู้แต่งใหม่ลงฐานข้อมูล (หลังจากกด Submit จากหน้า Create)
     *
     * (ฟังก์ชันนี้ถูกเรียกโดย Route::resource 'authors'
     * ซึ่งจะ map ไปที่ POST /admin/authors)
     */
    public function store(Request $request)
    {
        // 1. ตรวจสอบความถูกต้องของข้อมูล (Validation)
        $validatedData = $request->validate([
            'pseudonym' => 'required|string|max:255|unique:authors', // ต้องมี, เป็นข้อความ, ไม่เกิน 255 ตัว, ห้ามซ้ำ
            'code' => 'required|string|max:50|unique:authors',      // ต้องมี, เป็นข้อความ, ไม่เกิน 50 ตัว, ห้ามซ้ำ
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // ไม่บังคับ, ต้องเป็นไฟล์ภาพ, ขนาดไม่เกิน 2MB
            'biography' => 'nullable|string',                      // ไม่บังคับ, เป็นข้อความ (ชีวประวัติ)
        ]);

        // 2. จัดการการอัปโหลดรูปภาพ
        $imagePath = null; // ค่าเริ่มต้น (ถ้าไม่
        if ($request->hasFile('image')) {
            // 2. ถ้ามีรูป, ให้เก็บใน 'storage/app/public/images/authors'
            $imagePath = $request->file('image')->store('images/authors', 'public');
        }

        // 3. สร้างข้อมูลผู้แต่ง (Author) ลงในตาราง 'authors'
        Author::create([
            'pseudonym' => $validatedData['pseudonym'],
            'code' => $validatedData['code'],
            'image_path' => $imagePath, // บันทึกที่อยู่ของรูปภาพ (หรือ null)
            'biography' => $validatedData['biography'],
        ]);

        // 4. กลับไปหน้า Admin Index พร้อมข้อความแจ้งเตือน
        return redirect(session()->get('bookmark_url.authors', route('admin.authors.index')))
            ->with('success', 'Author created successfully.');
    }

    /**
     * แสดงฟอร์มสำหรับ "แก้ไข" ผู้แต่ง
     * (Author $author) คือ Route Model Binding - Laravel จะหาผู้แต่งจาก ID ใน URL ให้เราอัตโนมัติ
     *
     * (ฟังก์ชันนี้ถูกเรียกโดย Route::resource 'authors'
     * ซึ่งจะ map ไปที่ GET /admin/authors/{author}/edit)
     */
    public function edit(Author $author)
    {
        // ส่งข้อมูล $author (คนที่จะแก้) ไปยัง View เพื่อเติมข้อมูลเก่าในฟอร์ม
        return view('admin.authors.edit', compact('author')); // ไฟล์: resources/views/admin/authors/edit.blade.php
    }

    /**
     * "อัปเดต" ผู้แต่งในฐานข้อมูล (หลังจากกด Submit จากหน้า Edit)
     *
     * (ฟังก์ชันนี้ถูกเรียกโดย Route::resource 'authors'
     * ซึ่งจะ map ไปที่ PUT/PATCH /admin/authors/{author})
     */
    public function update(Request $request, Author $author)
    {
        // 1. ตรวจสอบข้อมูล (Validation)
        $validatedData = $request->validate([
            // (สำคัญ) Rule::unique('authors')->ignore($author->id)
            // หมายความว่า 'pseudonym' ต้องไม่ซ้ำกับใคร "ยกเว้น" (ignore) ID ของตัวเอง
            'pseudonym' => ['required', 'string', 'max:255', Rule::unique('authors')->ignore($author->id)],
            'code' => ['required', 'string', 'max:50', Rule::unique('authors')->ignore($author->id)],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // ถ้ามีรูปใหม่มา
            'biography' => 'nullable|string', // (Validation สำหรับ Biography)
        ]);

        // 2. จัดการการอัปโหลด/ลบ รูปภาพ
        $imagePath = $author->image_path; // 2.1 ดึงที่อยู่รูปเก่ามาเก็บไว้ก่อน
        if ($request->hasFile('image')) {
            // 2.2 ถ้ามีการอัปโหลด "รูปใหม่"
            if ($author->image_path) {
                // 2.3 และ "รูปเก่า" มีอยู่
                // ให้ลบ "รูปเก่า" ออกจาก Storage
                Storage::disk('public')->delete($author->image_path);
            }
            // 2.4 บันทึก "รูปใหม่" และอัปเดต $imagePath
            $imagePath = $request->file('image')->store('images/authors', 'public');
        }

        // 3. อัปเดตข้อมูลในตาราง 'authors'
        $author->update([
            'pseudonym' => $validatedData['pseudonym'],
            'code' => $validatedData['code'],
            'image_path' => $imagePath, // ใช้ $imagePath (อาจเป็นของใหม่ หรือของเดิม)
            'biography' => $validatedData['biography'], // (อัปเดต Biography)
        ]);

        // 4. กลับไปหน้า Show (ฝั่ง Public) ของผู้แต่งคนนี้
        return redirect(session()->get('bookmark_url.authors_show', route('authors.show', $author->code)))
            ->with('success', 'Author updated successfully.');
    }

    /**
     * "ลบ" ผู้แต่งออกจากฐานข้อมูล
     *
     * (ฟังก์ชันนี้ถูกเรียกโดย Route::resource 'authors'
     * ซึ่งจะ map ไปที่ DELETE /admin/authors/{author})
     */
    public function destroy(Author $author)
    {
        // (ข้อควรระวัง: ถ้าผู้แต่งคนนี้ยังมีหนังสือ (Books)
        // การลบจะล้มเหลวเพราะติด Foreign Key Constraint
        // ซึ่งเป็นสิ่งที่ดี เพื่อป้องกันข้อมูลเสียหาย)

        $bookCount = $author->books()->count();

        // 2. ถ้ามี (มากกว่า 0)
        if ($bookCount > 0) {
            // ให้ Redirect กลับไป พร้อมข้อความ Error (ไม่ทำการลบ)
            return redirect()->route('admin.authors.index')->with('error', 'Cannot delete, this author is in use.');
        }

        // 2. ลบข้อมูลผู้แต่งออกจาก Database
        $author->delete();

        // 3. กลับไปหน้า Admin Index พร้อมข้อความแจ้งเตือน
        return redirect(session()->get('bookmark_url.authors', route('admin.authors.index')))
            ->with('success', 'Author deleted successfully.');
    }

    /**
     * แสดงหน้ารายละเอียดผู้แต่ง 1 คน (ฝั่ง Public)
     * (ฟังก์ชันนี้ถูกเรียกโดย Route ที่เราสร้างเองใน routes/web.php
     * คือ GET /authors/{author:code})
     */
    public function show(Author $author)
    {
        // โหลดข้อมูล 'books' (หนังสือทั้งหมดของผู้แต่งคนนี้) มาเตรียมไว้
        // 'books' คือชื่อฟังก์ชัน 'hasMany' ใน Model 'Author.php'
        $author->load('books');

        // ส่งข้อมูล $author (รวมหนังสือ) ไปยัง View
        return view('authors.show', compact('author')); // ไฟล์: resources/views/authors/show.blade.php
    }
}
