<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    // --- Public Functions ---
    public function index(Request $request)
    {
        $search = $request->input('search'); // 2. ดึงคำค้นหา

        $books = Book::query()
            ->with('author', 'category')
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('author', function ($q) use ($search) {
                        $q->where('pseudonym', 'like', "%{$search}%");
                    })
                    ->orWhereHas('categories', function ($q) use ($search)  {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->orderBy('code')
            ->paginate(5)
            ->withQueryString();
        return view('books.index', compact('books')); // หน้า Public
    }

    public function show(Book $book)
    {
        // 'load()' คือการดึงข้อมูลที่เกี่ยวข้อง (author, categories)
        // มาเตรียมไว้ให้ View
        $book->load('author', 'categories');

        return view('books.show', compact('book'));
    }

    // --- Admin Functions ---
    public function adminIndex(Request $request)
    {
        $search = $request->input('search');

        $books = Book::query()
            ->with('author', 'categories') // 1. ต้องเป็น 'categories' (พหูพจน์)
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('author', function ($q) use ($search) {
                        $q->where('pseudonym', 'like', "%{$search}%");
                    })
                    ->orWhereHas('categories', function ($q) use ($search) { // 2. ต้องเป็น 'categories' (พหูพจน์)
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->orderBy('code')
            ->paginate(5)
            ->withQueryString();

        return view('admin.books.index', compact('books'));
    }

    public function create()
    {
        $authors = Author::all();
        $categories = Category::all();
        // ต้อง return view() เท่านั้น
        return view('admin.books.create', compact('authors', 'categories'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255|unique:books',
            'code' => 'required|string|max:50|unique:books',
            'author_id' => 'required|exists:authors,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
            'synopsis' => 'nullable|string',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images/books', 'public');
        }

        $book = Book::create([
            'title' => $validatedData['title'],
            'code' => $validatedData['code'],
            'author_id' => $validatedData['author_id'],
            'image_path' => $imagePath,
            'description' => $validatedData['description'],
            'synopsis' => $validatedData['synopsis'],
        ]);

        $book->categories()->attach($validatedData['categories']);

        return redirect(session()->get('bookmark_url.books', route('admin.books.admin_index')))
            ->with('success', 'Book created successfully.');
    }

    public function edit(Book $book)
    {
        $authors = Author::all();
        $categories = Category::all();
        return view('admin.books.edit', compact('book', 'authors', 'categories'));
    }

    public function update(Request $request, Book $book)
    {
        $validatedData = $request->validate([
            'title' => ['required', 'string', 'max:255', Rule::unique('books')->ignore($book->id)],
            'code' => ['required', 'string', 'max:50', Rule::unique('books')->ignore($book->id)],
            'author_id' => 'required|exists:authors,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
            'synopsis' => 'nullable|string',
            'categories' => 'required|array', // <-- 1. เปลี่ยนเป็น 'categories' (array)
            'categories.*' => 'exists:categories,id'
        ]);

        // ... (Logic การอัปโหลด/ลบรูปภาพ ... เหมือนเดิม) ...
        $imagePath = $book->image_path;
        if ($request->hasFile('image')) {
            if ($book->image_path) {
                Storage::disk('public')->delete($book->image_path);
            }
            $imagePath = $request->file('image')->store('images/books', 'public');
        }

        // 2. อัปเดตข้อมูลหนังสือ
        $book->update([
            'title' => $validatedData['title'],
            'code' => $validatedData['code'],
            'author_id' => $validatedData['author_id'],
            'image_path' => $imagePath,
            'description' => $validatedData['description'],
            'synopsis' => $validatedData['synopsis'],
        ]);

        // 3. "Sync" หมวดหมู่ (ลบของเก่า/เพิ่มของใหม่ อัตโนมัติ)
        $book->categories()->sync($validatedData['categories']);

        return redirect(session()->get('bookmark_url.books_show', route('books.show', $book->code)))
            ->with('success', 'Book updated successfully.');
    }

    public function destroy(Author $author)
    {
        // 1. (สำคัญ) ลบไฟล์รูปภาพออกจาก Storage ก่อน
        if ($author->image_path) {
            Storage::disk('public')->delete($author->image_path);
        }

        // 2. ลบข้อมูลออกจาก Database
        $author->delete();

        return redirect(session()->get('bookmark_url.authors', route('admin.authors.index')))
            ->with('success', 'Author deleted successfully.');
    }
}
