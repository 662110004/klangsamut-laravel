@php session()->put('bookmark_url.return_to_list', request()->fullUrl()); @endphp

<x-user-layout>
    <div class="content-container">

        <div class="page-header">
            <h1>Browse All Books</h1>
        </div>

        <form
            action="{{ route('books.index') }}"
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

        <table class="table">
            <div class="paginate">
                {{ $books->links() }}
            </div>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Categories</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($books as $book)
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
                    <td><a href="{{ route('books.show', $book->code) }}" class="table-link">{{ $book->title }}</a></td>
                    <td><a href="{{ route('authors.show', $book->author->code) }}" class="table-link">{{ $book->author->pseudonym }}</a></td>
                    <td>
                        <div class="category-pills">
                            @forelse($book->categories as $category)
                            <a href="{{ route('categories.show', $category->code) }}" class="table-link">{{ $category->name }}</a>
                            @empty
                            <span class="text-muted">None</span>
                            @endforelse
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">No books available at the moment.</td>
                </tr>
                @endforelse
            </tbody>
        </table>



    </div>
</x-user-layout>