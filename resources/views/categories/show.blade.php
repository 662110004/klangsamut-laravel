@php
// สร้าง Bookmark ใหม่สำหรับหน้า Show นี้
session()->put('bookmark_url.categories_show', request()->fullUrl());

$layoutComponent = (Auth::check() && Auth::user()->role == 'admin')
? 'app-layout'
: 'user-layout';
@endphp

<x-dynamic-component :component="$layoutComponent">

    <div class="content-container">

        <div class="book-show-header-actions">
            <div class="left-actions">
                <a href="{{ session()->get('bookmark_url.return_to_list', 
    (Auth::check() && Auth::user()->role == 'admin') ? route('admin.categories.index') : route('books.index')) }}"
                    class="btn btn-secondary">
                    &larr; Back
                </a>
            </div>

            <div class="right-actions">
                @auth
                @if(Auth::user()->role == 'admin')
                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary">Edit</a>

                <div x-data="{ showConfirm: false }" class="modal-delete-wrapper">
                    <button @click.prevent="showConfirm = true" class="btn btn-danger">Delete</button>
                    <div x-show="showConfirm" class="modal-overlay" @click.away="showConfirm = false" x-cloak>
                        <div class="modal-content">
                            <p>Are you sure you want to delete '{{ $category->name }}'?</p>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="modal-delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Yes, Delete</button>
                            </form>
                            <button @click="showConfirm = false" class="btn btn-secondary">Cancel</button>
                        </div>
                    </div>
                </div>
                @endif
                @endauth
            </div>
        </div>

        <div class="book-details-wrapper" style="padding-top: 1.5rem;">
            <h1>{{ $category->name }}</h1>
            @if(Auth::check() && Auth::user()->role == 'admin')
            <div class="book-show-author">
                Code: <span>{{ $category->code }}</span>
            </div>
            @endif

            <div class="book-show-section">
                <h3>Books in this Category</h3>
                <table class="table">
                    <tbody>
                        @forelse($category->books as $book)
                        <tr>
                            <td>
                                <a href="{{ route('books.show', $book->code) }}">
                                    @if($book->image_path)
                                    <img src="{{ asset('storage/' . $book->image_path) }}" alt="{{ $book->title }}" class="table-img" style="max-width: 150px; object-fit: cover; border-radius: 4px;">
                                    @else
                                    <div class="table-img-placeholder">No Image</div>
                                    @endif
                                </a>
                            </td>
                            @if(Auth::check() && Auth::user()->role == 'admin')
                            <td><a href="{{ route('books.show', $book->code) }}" class="table-link">{{ $book->code }}</a></td>
                            <td>{{ $book->title }}</td>
                            @else
                            <td><a href="{{ route('books.show', $book->code) }}" class="table-link">{{ $book->title }}</a></td>
                            @endif

                            <td>
                                <a href="{{ route('authors.show', $book->author) }}" class="table-link">
                                    {{ $book->author->pseudonym }}
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3">No books found in this category.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-dynamic-component>