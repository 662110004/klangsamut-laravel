<?php

// --- ส่วนนำเข้า (Imports) ---
// ส่วนนี้คือการ 'use' หรือนำเข้า Class ต่างๆ ที่จำเป็นต้องใช้ในไฟล์นี้
// เพื่อให้ Laravel รู้จักชื่อย่อของ Class เหล่านั้น (เช่น Route, BookController, Auth)
// ไฟล์ที่เกี่ยวข้อง:
// app/Http/Controllers/ProfileController.php
// app/Http/Controllers/BookController.php
// app/Http/Controllers/AuthorController.php
// app/Http/Controllers/CategoryController.php
// app/Http/Controllers/UserController.php
// app/Models/Book.php
// app/Models/Author.php
// app/Models/Category.php
// app/Models/User.php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Models\Book;
use App\Models\Author;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// --- Public Routes (ทุกคนเข้าได้) ---

// 1. (ใหม่) หน้าแรก (Landing Page)
Route::get('/', function () {
    // ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่
    if (Auth::check()) {
        // ถ้า Log in แล้ว
        // ตรวจสอบ Role จาก Model User
        if (Auth::user()->role == 'admin') {
            // ถ้าเป็น 'admin' ให้ redirect ไปยัง Route ที่ชื่อ 'dashboard'
            return redirect()->route('dashboard');
        } else {
            // ถ้าเป็น Role อื่น (เช่น 'user') ให้ redirect ไปหน้า 'books.index'
            return redirect()->route('books.index'); // User ธรรมดาไปหน้าหนังสือ
        }
    }
    // ถ้าเป็น Guest (ยังไม่ Log in)
    // ให้แสดง View (หน้าเว็บ) ที่ไฟล์ welcome.blade.php
    // ไฟล์ที่เกี่ยวข้อง: resources/views/welcome.blade.php
    return view('welcome');
})->name('home'); // ตั้งชื่อ Route นี้ว่า 'home'

// 2. (ใหม่) ย้ายหน้าหนังสือไปที่ /books
// หน้าแสดงรายการหนังสือทั้งหมด (สาธารณะ)
// เมื่อเข้า /books จะไปเรียก method 'index' ใน BookController
// ไฟล์ที่เกี่ยวข้อง: app/Http/Controllers/BookController.php (method 'index')
Route::get('/books', [BookController::class, 'index'])->name('books.index');

// หน้าแสดงรายละเอียดหนังสือ 1 เล่ม (สาธารณะ)
// {book:code} คือการใช้ Route Model Binding โดยใช้คอลัมน์ 'code' แทน 'id'
// เมื่อเข้า /books/xxxx จะไปเรียก method 'show' ใน BookController
// ไฟล์ที่เกี่ยวข้อง: app/Http/Controllers/BookController.php (method 'show')
Route::get('/books/{book:code}', [BookController::class, 'show'])->name('books.show');

// หน้าแสดงรายละเอียดผู้แต่ง 1 คน (สาธารณะ)
// ไฟล์ที่เกี่ยวข้อง: app/Http/Controllers/AuthorController.php (method 'show')
Route::get('/authors/{author:code}', [App\Http\Controllers\AuthorController::class, 'show'])->name('authors.show');

// หน้าแสดงรายละเอียดหมวดหมู่ 1 หมวด (สาธารณะ)
// ไฟล์ที่เกี่ยวข้อง: app/Http/Controllers/CategoryController.php (method 'show')
Route::get('/categories/{category:code}', [App\Http\Controllers\CategoryController::class, 'show'])->name('categories.show');

// --- Protected Routes (Admin เท่านั้น) ---
// กลุ่มของ Route ที่ต้องผ่านการตรวจสอบสิทธิ์ก่อน
// middleware('auth'): ต้องล็อกอินก่อน (มาจาก Laravel)
// middleware('role:admin'): ต้องมี Role เป็น 'admin' (ตรวจสอบโดย Middleware ที่เราสร้างเอง)
// ไฟล์ที่เกี่ยวข้อง: app/Http/Middleware/CheckRole.php
// prefix('admin'): URL ของทุก Route ในกลุ่มนี้จะขึ้นต้นด้วย /admin (เช่น /admin/books)
// name('admin.'): ชื่อของทุก Route ในกลุ่มนี้จะขึ้นต้นด้วย admin. (เช่น admin.books.create)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // CRUD สำหรับ Books (เราจะสร้างเองเพราะ public index/show อยู่ข้างนอก)
    // ไฟล์ที่เกี่ยวข้อง: app/Http/Controllers/BookController.php
    Route::get('/books', [BookController::class, 'adminIndex'])->name('books.admin_index'); // หน้าจัดการหนังสือ (ของ Admin)
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create'); // หน้าฟอร์มสร้างหนังสือ
    Route::post('/books', [BookController::class, 'store'])->name('books.store'); // URL สำหรับรับข้อมูลจากฟอร์ม (สร้าง)
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit'); // หน้าฟอร์มแก้ไขหนังสือ
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update'); // URL สำหรับรับข้อมูลจากฟอร์ม (แก้ไข)
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy'); // URL สำหรับลบข้อมูล

    // CRUD สำหรับ Users
    // ไฟล์ที่เกี่ยวข้อง: app/Http/Controllers/UserController.php
    Route::get('/users', [UserController::class, 'index'])->name('users.index'); // หน้าจัดการผู้ใช้
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show'); // หน้าดูรายละเอียดผู้ใช้
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy'); // URL สำหรับลบผู้ใช้
    Route::post('/users/{user}/update-role', [UserController::class, 'updateRole']) // URL สำหรับอัปเดต Role
        ->name('users.update-role');

    // CRUD สำหรับ Authors (ใช้ resource)
    // Route::resource เป็นคำสั่งสร้างชุด Route CRUD (index, create, store, show, edit, update, destroy) ทั้งหมดอัตโนมัติ
    // ไฟล์ที่เกี่ยวข้อง: app/Http/Controllers/AuthorController.php (ทุก method ในนั้น)
    Route::resource('authors', AuthorController::class);

    // CRUD สำหรับ Categories (ใช้ resource)
    // ไฟล์ที่เกี่ยวข้อง: app/Http/Controllers/CategoryController.php (ทุก method ในนั้น)
    Route::resource('categories', CategoryController::class);
});

// --- Dashboard Route (สำหรับผู้ใช้ที่ล็อกอินแล้ว) ---
// หน้านี้มักจะเป็นหน้าหลัก "หลังบ้าน"
// middleware('auth', 'verified'): ต้องล็อกอิน และต้องยืนยันอีเมลแล้ว (ถ้าเปิดใช้งาน)
Route::get('/dashboard', function () {
    // 1. ดึงข้อมูลนับจำนวนจาก Models
    // ไฟล์ที่เกี่ยวข้อง: App/Models/Book.php, Author.php, Category.php, User.php
    $bookCount = Book::count();
    $authorCount = Author::count();
    $categoryCount = Category::count();
    $userCount = User::count();

    // 2. ส่งข้อมูลไปยัง View
    // ไฟล์ที่เกี่ยวข้อง: resources/views/dashboard.blade.php
    return view('dashboard', [
        'bookCount' => $bookCount,
        'authorCount' => $authorCount,
        'categoryCount' => $categoryCount,
        'userCount' => $userCount
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// --- User Home Route (สำหรับผู้ใช้ที่ล็อกอิน) ---
// หน้านี้เป็นหน้า Home สำหรับ User ทั่วไปที่ล็อกอินเข้ามา
// middleware('auth'): ขอแค่ล็อกอินก็เข้าได้
Route::get('/home', function () {
    // ดึงหนังสือล่าสุด 10 เล่ม พร้อมข้อมูลผู้แต่ง
    // ไฟล์ที่เกี่ยวข้อง: App/Models/Book.php
    $recentBooks = Book::with('author')->latest()->take(10)->get();

    // ส่งข้อมูลไปแสดงที่ View
    // ไฟล์ที่เกี่ยวข้อง: resources/views/user-home.blade.php
    return view('user-home', compact('recentBooks'));
})->middleware(['auth'])->name('user.home');

// --- Profile Routes (มาจาก Breeze) ---
// กลุ่ม Route นี้สำหรับจัดการโปรไฟล์ส่วนตัวของผู้ใช้ที่ล็อกอิน
// middleware('auth'): ขอแค่ล็อกอินก็เข้าได้ (ทั้ง user และ admin)
// ไฟล์ที่เกี่ยวข้อง: app/Http/Controllers/ProfileController.php
// ไฟล์ที่เกี่ยวข้อง: resources/views/profile/edit.blade.php
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit'); // หน้าแก้ไขโปรไฟล์
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update'); // URL รับข้อมูลอัปเดตโปรไฟล์
});

// --- Auth Routes File ---
// เรียกใช้ไฟล์ routes/auth.php
// ไฟล์นี้จะเก็บ Route ที่เกี่ยวกับการยืนยันตัวตนทั้งหมด (Login, Logout, Register, Forgot Password)
// ซึ่งถูกสร้างโดย 'laravel/breeze'
// ไฟล์ที่เกี่ยวข้อง: routes/auth.php
require __DIR__ . '/auth.php';
