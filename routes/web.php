<?php

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
    if (Auth::check()) {
        // ถ้า Log in แล้ว
        if (Auth::user()->role == 'admin') {
            return redirect()->route('dashboard');
        } else {
            return redirect()->route('books.index'); // User ธรรมดาไปหน้าหนังสือ
        }
    }
    // ถ้าเป็น Guest (ยังไม่ Log in)
    return view('welcome');
})->name('home');

// 2. (ใหม่) ย้ายหน้าหนังสือไปที่ /books
Route::get('/books', [BookController::class, 'index'])->name('books.index');

Route::get('/books/{book:code}', [BookController::class, 'show'])->name('books.show');

Route::get('/authors/{author:code}', [App\Http\Controllers\AuthorController::class, 'show'])->name('authors.show');

Route::get('/categories/{category:code}', [App\Http\Controllers\CategoryController::class, 'show'])->name('categories.show');

// --- Protected Routes (Admin เท่านั้น) ---
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // CRUD สำหรับ Books (เราจะสร้างเองเพราะ public index/show อยู่ข้างนอก)
    Route::get('/books', [BookController::class, 'adminIndex'])->name('books.admin_index');
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/update-role', [UserController::class, 'updateRole'])
        ->name('users.update-role');

    // CRUD สำหรับ Authors (ใช้ resource)
    Route::resource('authors', AuthorController::class);

    // CRUD สำหรับ Categories (ใช้ resource)
    Route::resource('categories', CategoryController::class);
});

Route::get('/dashboard', function () {
    // 1. ดึงข้อมูลนับจำนวน
    $bookCount = Book::count();
    $authorCount = Author::count();
    $categoryCount = Category::count();
    $userCount = User::count();

    // 2. ส่งข้อมูลไปยัง View
    return view('dashboard', [
        'bookCount' => $bookCount,
        'authorCount' => $authorCount,
        'categoryCount' => $categoryCount,
        'userCount' => $userCount
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/home', function () {
    $recentBooks = Book::with('author')->latest()->take(10)->get();

    return view('user-home', compact('recentBooks'));
})->middleware(['auth'])->name('user.home');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
