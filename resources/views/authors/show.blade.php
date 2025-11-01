@php
session()->put('bookmark_url.authors_show', request()->fullUrl());

$layoutComponent = (Auth::check() && Auth::user()->role == 'admin')
? 'app-layout' // (Layout ของ Admin)
: 'user-layout'; // (Layout ของ User)
@endphp

<x-dynamic-component :component="$layoutComponent">

    <div class="content-container">

        <div class="book-show-header-actions">
            <div class="left-actions">
                <a href="{{ session()->get('bookmark_url.return_to_list', 
    (Auth::check() && Auth::user()->role == 'admin') ? route('admin.authors.index') : route('books.index')) }}"
                    class="btn btn-secondary">
                    &larr; Back
                </a>
            </div>

            <div class="right-actions">
                @auth
                @if(Auth::user()->role == 'admin')
                <a href="{{ route('admin.authors.edit', $author) }}" class="btn btn-primary">Edit</a>

                <div x-data="{ showConfirm: false }" class="modal-delete-wrapper">
                    <button @click.prevent="showConfirm = true" class="btn btn-danger">Delete</button>
                    <div x-show="showConfirm" class="modal-overlay" @click.away="showConfirm = false" x-cloak>
                        <div class="modal-content">
                            <p>Are you sure you want to delete '{{ $author->pseudonym }}'?</p>
                            <form method="POST" action="{{ route('admin.authors.destroy', $author) }}" class="modal-delete-form">
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
                @if($author->image_path)
                <img src="{{ asset('storage/' . $author->image_path) }}" alt="{{ $author->pseudonym }} Cover">
                @else
                <div class="book-cover-placeholder">No Cover Image</div>
                @endif
            </div>

            <div class="book-details-wrapper">
                <h1>{{ $author->pseudonym }}</h1>
                <div class="book-show-author">
                    @if(Auth::check() && Auth::user()->role == 'admin')
                    Code: <span>{{ $author->code }}</span>
                    @endif
                </div>

                @if($author->biography)
                <div class="book-show-section">
                    <h3>About the Author</h3>
                    <pre class="book-show-synopsis">{{ $author->biography }}</pre>
                </div>
                @endif

                <div class="book-show-section">
                    <h3>Books by this Author</h3>

                    <table class="table">

                        <tbody>
                            @forelse($author->books as $book)
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
                                    <div class="category-pills">
                                        @foreach($book->categories as $category)
                                        <a href="{{ route('categories.show', $category->code) }}" class="table-link">{{ $category->name }}</a>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                @if(Auth::check() && Auth::user()->role == 'admin')
                                <td colspan="4">No books found for this author.</td>
                                @else
                                <td colspan="3">No books found for this author.</td>
                                @endif
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</x-dynamic-component>