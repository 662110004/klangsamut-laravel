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