<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class AuthorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $authors = Author::query() // Use Author model
            ->when($search, function ($query, $search) {
                // Search the correct 'pseudonym' column
                return $query->where('pseudonym', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->paginate(5)
            ->withQueryString();

        return view('admin.authors.index', compact('authors'));
    }

    public function create()
    {
        return view('admin.authors.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'pseudonym' => 'required|string|max:255|unique:authors',
            'code' => 'required|string|max:50|unique:authors',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'biography' => 'nullable|string',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            // 2. ถ้ามีรูป, ให้เก็บใน 'public/images/authors'
            $imagePath = $request->file('image')->store('images/authors', 'public');
        }

        // 3. สร้างข้อมูลพร้อม path รูป
        Author::create([
            'pseudonym' => $validatedData['pseudonym'],
            'code' => $validatedData['code'],
            'image_path' => $imagePath,
            'biography' => $validatedData['biography'],
        ]);

        return redirect(session()->get('bookmark_url.authors', route('admin.authors.index')))
            ->with('success', 'Author created successfully.');
    }

    public function edit(Author $author)
    {
        return view('admin.authors.edit', compact('author'));
    }

    public function update(Request $request, Author $author)
    {
        $validatedData = $request->validate([
            'pseudonym' => ['required', 'string', 'max:255', Rule::unique('authors')->ignore($author->id)],
            'code' => ['required', 'string', 'max:50', Rule::unique('authors')->ignore($author->id)],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'biography' => 'nullable|string',
        ]);

        $imagePath = $author->image_path; // 1. ใช้รูปเก่าเป็นค่าเริ่มต้น

        if ($request->hasFile('image')) {
            // 2. ถ้ามีรูปใหม่
            // 2.1 ลบรูปเก่า (ถ้ามี)
            if ($author->image_path) {
                Storage::disk('public')->delete($author->image_path);
            }
            // 2.2 อัปโหลดรูปใหม่
            $imagePath = $request->file('image')->store('images/authors', 'public');
        }

        // 3. อัปเดตข้อมูล
        $author->update([
            'pseudonym' => $validatedData['pseudonym'],
            'code' => $validatedData['code'],
            'image_path' => $imagePath,
            'biography' => $validatedData['biography'],
        ]);

        return redirect(session()->get('bookmark_url.authors_show', route('authors.show', $author->code)))
            ->with('success', 'Author updated successfully.');
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

    // ฟังก์ชัน show() เราไม่ได้ใช้ใน Admin (resource) แต่ปล่อยว่างไว้ได้
    public function show(Author $author)
    {
        // โหลดหนังสือที่เกี่ยวข้องกับผู้แต่งนี้มาด้วย
        $author->load('books');

        return view('authors.show', compact('author'));
    }
}
