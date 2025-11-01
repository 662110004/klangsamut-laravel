@php
session()->put('bookmark_url.books_show', request()->fullUrl());

$layoutComponent = (Auth::check() && Auth::user()->role == 'admin')
? 'app-layout' // (Layout ของ Admin)
: 'user-layout'; // (Layout ของ User)
@endphp

<x-dynamic-component :component="$layoutComponent">

    <div class="content-container">

        <div class="book-show-header-actions">

            <div class="left-actions">
                <a href="{{ session()->get('bookmark_url.return_to_list', 
    (Auth::check() && Auth::user()->role == 'admin') ? route('admin.books.admin_index') : route('books.index')) }}"
                    class="btn btn-secondary">
                    &larr; Back
                </a>
            </div>

            <div class="right-actions">
                @auth
                @if(Auth::user()->role == 'admin')

                <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-primary">Edit</a>

                <div x-data="{ showConfirm: false }" class="modal-delete-wrapper">
                    <button @click.prevent="showConfirm = true" class="btn btn-danger">Delete</button>
                    <div x-show="showConfirm" class="modal-overlay" @click.away="showConfirm = false" x-cloak>
                        <div class="modal-content">
                            <p>Are you sure you want to delete '{{ $book->title }}'?</p>
                            <form method="POST" action="{{ route('admin.books.destroy', $book) }}" class="modal-delete-form">
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
        <div class="book-show-grid">

            <div class="book-cover-wrapper">
                @if($book->image_path)
                <img src="{{ asset('storage/' . $book->image_path) }}" alt="{{ $book->title }} Cover">
                @else
                <div class="book-cover-placeholder">No Cover Image</div>
                @endif
            </div>

            <div class="book-details-wrapper">

                <h1>{{ $book->title }}</h1>

                <div class="book-show-author">
                    <a href="{{ route('authors.show', $book->author) }}" class="table-link">
                        by <span>{{ $book->author->pseudonym }}</span>
                    </a>
                </div>

                <div class="book-show-section">
                    <h3>Genres</h3>
                    <div class="category-pills">
                        @forelse($book->categories as $category)
                        <a href="{{ route('categories.show', $category->code) }}" class="table-link">
                            <span class="category-pill">{{ $category->name }}</span>
                        </a>
                        @empty
                        <span class="text-muted">None</span>
                        @endforelse
                    </div>
                </div>

                <div class="book-show-section">
                    <h3>Description</h3>
                    <pre class="book-show-synopsis">{{ $book->description ?? 'No description available.' }}</pre>
                </div>

                <div class="book-show-section">
                    <h3>Synopsis</h3>
                    <pre class="book-show-synopsis">{{ $book->synopsis ?? 'No synopsis available.' }}</pre>
                </div>

            </div>
        </div>
    </div>

</x-dynamic-component>