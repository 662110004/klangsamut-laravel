@php
session()->put('bookmark_url.return_to_list', request()->fullUrl());
@endphp

<x-app-layout>
    <div class="container">
        <h1>Manage Books</h1>
        <form
            action="{{ route('admin.books.admin_index') }}"
            method="GET"
            class="search-form"
            x-data="{ searchQuery: '{{ request('search', '') }}' }">
            <div class="search-input-wrapper">

                <input
                    type="text"
                    name="search"
                    placeholder="Search by code or name..."
                    class="form-control"
                    x-model="searchQuery">

                <button
                    type="button"
                    class="search-clear-btn"
                    x-show="searchQuery.length > 0"
                    @click="searchQuery = ''; $nextTick(() => $el.closest('form').submit());"
                    x-cloak>
                    &times;
                </button>
                <!-- x-show="searchQuery.length > 0" คือคำสั่งที่บอกว่า จงแสดงปุ่มนี้ ก็ต่อเมื่อใน searchbar มีอักษร > 0 -->
                <!-- 
                @click="searchQuery = ''; $nextTick(() => $el.closest('form').submit());"  
                1. searchQuery = ''; คือ สั่งให้ล้างค่าตัวแปร searchQuery ให้กลายเป็นค่าว่าง
                2. $nextTick(() => ...) คือ สั่งให้ "รอ" จนกว่า Alpine.js จะอัปเดตหน้าจอ (ล้างข้อความ) เสร็จเรียบร้อยก่อน แล้วค่อยทำคำสั่งข้างใน
                3. $el คือ ปุ่ม "x"
                4. .closest('form') คือ ให้วิ่งหา <form> ที่อยู่ใกล้ที่สุดที่ครอบมันอยู่
                5. คือ สั่งให้ส่งฟอร์มนั้นทันที
                -->
                <!-- x-cloak คือ ทำหน้าที่ซ่อนปุ่มนี้ไว้ก่อนที่ Alpine.js จะโหลดเสร็จ เพื่อป้องกันไม่ให้ผู้ใช้เห็นปุ่ม "x" กระพริบแวบหนึ่งตอนโหลดหน้าเว็บ -->
            </div>

            <button type="submit" class="btn btn-primary">Search</button>

        </form>
        <a href="{{ route('admin.books.create') }}" class="btn btn-primary">Add New Book</a>

        <div class="paginate">
            {{ $books->links() }}

        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Code</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($books as $book)
                <tr>
                    <td>
                        @if($book->image_path)
                        <a href="{{ route('books.show', $book->code) }}">
                            <img src="{{ asset('storage/' . $book->image_path) }}" alt="{{ $book->pseudonym }}" style="max-width: 150px; object-fit: cover; border-radius: 4px;">
                        </a>
                        @else
                        <span style="color: var(--color-text-light);">No Image</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('books.show', $book->code) }}" class="table-link">
                            {{ $book->code }}
                        </a>
                    </td>
                    <td>{{ $book->title }}</td>
                    <td>
                        <a href="{{ route('authors.show', $book->author->code) }}" class="table-link">
                            {{ $book->author->pseudonym }}
                        </a>
                    </td>
                    <td>
                        <div class="category-pills">
                            @forelse($book->categories as $category)
                            <a href="{{ route('categories.show', $category->code) }}" class="table-link">
                                <span class="category-pill">{{ $category->name }}</span>
                            </a>
                            @empty
                            <span style="color: var(--color-text-light);">None</span>
                            @endforelse
                        </div>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="6">No books found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</x-app-layout>