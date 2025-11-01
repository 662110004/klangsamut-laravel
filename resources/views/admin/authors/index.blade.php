@php
session()->put('bookmark_url.return_to_list', request()->fullUrl());
@endphp

<x-app-layout>
    <div class="container">
        <h1>Manage Authors</h1>
        <form
            action="{{ route('admin.authors.index') }}"
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
        <a href="{{ route('admin.authors.create') }}" class="btn btn-primary">Add New Author</a>

        <div class="paginate">
            {{ $authors->links() }}
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Code</th>
                    <th>Pseudonym or Name</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($authors as $author)
                <tr>
                    <td>
                        @if($author->image_path)
                        <a href="{{ route('authors.show', $author->code) }}">
                            <img src="{{ asset('storage/' . $author->image_path) }}" alt="{{ $author->pseudonym }}" style="max-width: 100px; object-fit: cover; border-radius: 4px;">
                        </a>
                        @else
                        <span style="color: var(--color-text-light);">No Image</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('authors.show', $author->code) }}" class="table-link">
                            {{ $author->code }}
                        </a>
                    </td>
                    <td>{{ $author->pseudonym }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">No authors found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>


    </div>
</x-app-layout>