<?php

namespace App\Http\Controllers;

// --- Imports ---
// นำเข้า Model ที่จำเป็น (Book, Author, Category)
use App\Models\Book;
use App\Models\Author;
use App\Models\Category;
// นำเข้า Request เพื่อรับข้อมูลจากฟอร์มและ URL
use Illuminate\Http\Request;
// นำเข้า Rule สำหรับสร้าง Validation Rule ที่ซับซ้อน (เช่น unique->ignore)
use Illuminate\Validation\Rule;
// นำเข้า Storage Facade เพื่อจัดการไฟล์ (ลบ/อัปโหลดรูปภาพ)
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    // --- Public Functions (สำหรับผู้ใช้ทั่วไป) ---

    /**
     * แสดงหน้ารายการหนังสือทั้งหมด (ฝั่ง Public)
     * ถูกเรียกโดย Route: GET /books
     */
    public function index(Request $request)
    {
        // 1. ดึงคำค้นหา (ถ้ามี) จาก URL (เช่น ?search=...)
        $search = $request->input('search');

        // 2. เริ่มสร้าง Query
        $books = Book::query()
            // 3. Eager Loading: โหลด 'author' และ 'category' (ความสัมพันธ์เก่า?) มาพร้อมกัน
            // เพื่อป้องกันปัญหา N+1 Query (ที่ทำให้เว็บช้า)
            ->with('author', 'category')
            // 4. (Conditional Query) ถ้า $search มีค่า (ไม่ว่าง)
            ->when($search, function ($query, $search) {
                // ให้เพิ่มเงื่อนไขการค้นหา
                return $query->where('title', 'like', "%{$search}%") // ค้นหาจากชื่อหนังสือ
                    ->orWhere('code', 'like', "%{$search}%") // ค้นหาจากรหัส
                    ->orWhereHas('author', function ($q) use ($search) { // ค้นหาในตาราง 'authors'
                        $q->where('pseudonym', 'like', "%{$search}%");
                    })
                    ->orWhereHas('categories', function ($q) use ($search)  { // ค้นหาในตาราง 'categories'
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            // 5. เรียงลำดับตาม 'code'
            ->orderBy('code')
            // 6. แบ่งหน้า (Pagination) แสดงผลหน้าละ 5 รายการ
            ->paginate(5)
            // 7. ให้ลิงก์แบ่งหน้า (1, 2, 3...) ยังคงมีคำค้นหาต่อท้าย (เช่น ?search=...&page=2)
            ->withQueryString();

        // 8. ส่งข้อมูล $books ไปยัง View
        return view('books.index', compact('books')); // ไฟล์: resources/views/books/index.blade.php
    }

    /**
     * แสดงหน้ารายละเอียดหนังสือ 1 เล่ม (ฝั่ง Public)
     * (Book $book) คือ Route Model Binding - Laravel จะหาหนังสือจาก 'code' ใน URL ให้เราอัตโนมัติ
     * ถูกเรียกโดย Route: GET /books/{book:code}
     */
    public function show(Book $book)
    {
        // 'load()' คือการ Eager Load ข้อมูลที่เกี่ยวข้อง (ผู้แต่ง, หมวดหมู่)
        // หลังจากที่ได้ $book มาแล้ว
        $book->load('author', 'categories');

        // ส่งข้อมูล $book เล่มเดียวนี้ไปยัง View
        return view('books.show', compact('book')); // ไฟล์: resources/views/books/show.blade.php
    }

    // --- Admin Functions (สำหรับผู้ดูแลระบบ) ---

    /**
     * แสดงหน้ารายการหนังสือ (ตาราง CRUD ฝั่ง Admin)
     * ถูกเรียกโดย Route: GET /admin/books
     */
    public function adminIndex(Request $request)
    {
        // การทำงานเหมือนฟังก์ชัน index() ด้านบน แต่ใช้ความสัมพันธ์ 'categories' (พหูพจน์)
        $search = $request->input('search');

        $books = Book::query()
            ->with('author', 'categories') // 1. โหลดความสัมพันธ์ 'categories' (พหูพจน์)
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('author', function ($q) use ($search) {
                        $q->where('pseudonym', 'like', "%{$search}%");
                    })
                    ->orWhereHas('categories', function ($q) use ($search) { // 2. ค้นหาจาก 'categories' (พหูพจน์)
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->orderBy('code')
            ->paginate(5)
            ->withQueryString();

        // ส่งข้อมูลไปยัง View ของ Admin
        return view('admin.books.index', compact('books')); // ไฟล์: resources/views/admin/books/index.blade.php
    }

    /**
     * แสดงฟอร์มสำหรับ "สร้าง" หนังสือใหม่
     * ถูกเรียกโดย Route: GET /admin/books/create
     */
    public function create()
    {
        // 1. ดึงข้อมูล Authors ทั้งหมดเพื่อใช้ใน <select>
        $authors = Author::all();
        // 2. ดึงข้อมูล Categories ทั้งหมดเพื่อใช้ใน Checkbox
        $categories = Category::all();

        // 3. ส่งข้อมูล $authors และ $categories ไปยัง View
        return view('admin.books.create', compact('authors', 'categories')); // ไฟล์: resources/views/admin/books/create.blade.php
    }

    /**
     * "บันทึก" หนังสือใหม่ลงฐานข้อมูล (หลังจากกด Submit จากหน้า Create)
     * ถูกเรียกโดย Route: POST /admin/books
     */
    public function store(Request $request)
    {
        // 1. ตรวจสอบความถูกต้องของข้อมูล (Validation)
        $validatedData = $request->validate([
            'title' => 'required|string|max:255|unique:books', // ต้องมี, เป็นข้อความ, ไม่เกิน 255 ตัว, ห้ามซ้ำ
            'code' => 'required|string|max:50|unique:books',  // ต้องมี, เป็นข้อความ, ไม่เกิน 50 ตัว, ห้ามซ้ำ
            'author_id' => 'required|exists:authors,id',      // ต้องมี, และ ID ต้องมีอยู่จริงในตาราง authors
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // ไม่บังคับ, ต้องเป็นไฟล์ภาพ, นามสกุลที่อนุญาต, ขนาดไม่เกิน 2MB
            'description' => 'nullable|string',               // ไม่บังคับ, เป็นข้อความ
            'synopsis' => 'nullable|string',                  // ไม่บังคับ, เป็นข้อความ
            'categories' => 'required|array',                 // ต้องมี (อย่างน้อย 1), ต้องเป็น Array (มาจาก Checkbox)
            'categories.*' => 'exists:categories,id'          // ทุกค่าใน Array 'categories' ต้องมี ID อยู่จริงในตาราง categories
        ]);

        // 2. จัดการการอัปโหลดรูปภาพ
        $imagePath = null; // ค่าเริ่มต้น (ถ้าไม่
        if ($request->hasFile('image')) {
            // ถ้ามีการอัปโหลดไฟล์ 'image'
            // ให้เก็บไฟล์ใน 'storage/app/public/images/books'
            // และเก็บ "ที่อยู่" ของไฟล์ไว้ใน $imagePath
            $imagePath = $request->file('image')->store('images/books', 'public');
        }

        // 3. สร้างหนังสือ (Book) ลงในตาราง 'books'
        $book = Book::create([
            'title' => $validatedData['title'],
            'code' => $validatedData['code'],
            'author_id' => $validatedData['author_id'],
            'image_path' => $imagePath, // บันทึกที่อยู่ของรูปภาพ (หรือ null)
            'description' => $validatedData['description'],
            'synopsis' => $validatedData['synopsis'],
        ]);

        // 4.บันทึกความสัมพันธ์ Many-to-Many
        // นำ Array ของ Category ID ที่ได้ ($validatedData['categories'])
        // ไป "แนบ" (attach) กับ $book ที่เพิ่งสร้าง
        // Laravel จะบันทึกข้อมูลลงในตารางกลาง (book_category) ให้เอง
        $book->categories()->attach($validatedData['categories']);

        // 5. กลับไปหน้า Admin Index พร้อมข้อความแจ้งเตือน
        return redirect(session()->get('bookmark_url.books', route('admin.books.admin_index')))
            ->with('success', 'Book created successfully.');
    }

    /**
     * แสดงฟอร์มสำหรับ "แก้ไข" หนังสือ
     * (Book $book) คือ Route Model Binding
     * ถูกเรียกโดย Route: GET /admin/books/{book}/edit
     */
    public function edit(Book $book)
    {
        // 1. ดึงข้อมูล Authors และ Categories ทั้งหมดสำหรับฟอร์ม
        $authors = Author::all();
        $categories = Category::all();

        // 2. ส่งข้อมูล $book (เล่มที่จะแก้), $authors, และ $categories ไปยัง View
        return view('admin.books.edit', compact('book', 'authors', 'categories')); // ไฟล์: resources/views/admin/books/edit.blade.php
    }

    /**
     * "อัปเดต" หนังสือในฐานข้อมูล (หลังจากกด Submit จากหน้า Edit)
     * ถูกเรียกโดย Route: PUT /admin/books/{book}
     */
    public function update(Request $request, Book $book)
    {
        // 1. ตรวจสอบข้อมูล (Validation)
        $validatedData = $request->validate([
            // (สำคัญ) Rule::unique('books')->ignore($book->id)
            // หมายความว่า 'title' ต้องไม่ซ้ำกับใคร "ยกเว้น" (ignore) ID ของตัวเอง (เล่มที่กำลังแก้อยู่)
            'title' => ['required', 'string', 'max:255', Rule::unique('books')->ignore($book->id)],
            'code' => ['required', 'string', 'max:50', Rule::unique('books')->ignore($book->id)],
            'author_id' => 'required|exists:authors,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // ถ้ามีรูปใหม่มา
            'description' => 'nullable|string',
            'synopsis' => 'nullable|string',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id'
        ]);

        // 2. จัดการการอัปโหลด/ลบ รูปภาพ
        $imagePath = $book->image_path; // 2.1 ดึงที่อยู่รูปเก่ามาเก็บไว้ก่อน
        if ($request->hasFile('image')) {
            // 2.2 ถ้ามีการอัปโหลด "รูปใหม่"
            if ($book->image_path) {
                // 2.3 และ "รูปเก่า" มีอยู่
                // ให้ลบ "รูปเก่า" ออกจาก Storage
                Storage::disk('public')->delete($book->image_path);
            }
            // 2.4 บันทึก "รูปใหม่" และอัปเดต $imagePath
            $imagePath = $request->file('image')->store('images/books', 'public');
        }

        // 3. อัปเดตข้อมูลในตาราง 'books'
        $book->update([
            'title' => $validatedData['title'],
            'code' => $validatedData['code'],
            'author_id' => $validatedData['author_id'],
            'image_path' => $imagePath, // ใช้ $imagePath (อาจเป็นของใหม่ หรือของเดิม)
            'description' => $validatedData['description'],
            'synopsis' => $validatedData['synopsis'],
        ]);

        // 4.อัปเดตความสัมพันธ์ Many-to-Many
        // ใช้ "sync()" แทน "attach()"
        // sync() จะจัดการให้เองว่า ID ไหนต้อง "ลบ", ID ไหนต้อง "เพิ่ม"
        // เพื่อให้ข้อมูลในตารางกลาง ตรงกับ Array ($validatedData['categories']) ที่ส่งมาล่าสุด
        $book->categories()->sync($validatedData['categories']);

        // 5. กลับไปหน้า Show ของเล่มนี้ พร้อมข้อความแจ้งเตือน
        return redirect(session()->get('bookmark_url.books_show', route('books.show', $book->code)))
            ->with('success', 'Book updated successfully.');
    }

    /**
     * "ลบ" หนังสือออกจากฐานข้อมูล
     * ถูกเรียกโดย Route: DELETE /admin/books/{book}
     */
    public function destroy(Book $book)
    {
        // 1. ลบไฟล์รูปภาพของ "หนังสือ" นี้ออกจาก Storage ก่อน
        if ($book->image_path) { 
            Storage::disk('public')->delete($book->image_path);
        }

        // 2. ลบข้อมูล "หนังสือ" ออกจาก Database
        // (ความสัมพันธ์ใน 'book_category' จะถูกลบอัตโนมัติ
        // เพราะเราตั้ง 'onDelete('cascade')' ไว้ใน Migration)
        $book->delete();

        // 3. กลับไปหน้า Admin Index พร้อมข้อความแจ้งเตือน
        return redirect(session()->get('bookmark_url.books', route('admin.books.admin_index'))) 
            ->with('success', 'Book deleted successfully.'); 
    }
}