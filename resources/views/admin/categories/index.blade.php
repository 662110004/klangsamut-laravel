@php
session()->put('bookmark_url.return_to_list', request()->fullUrl());
@endphp

<x-app-layout>
    <div class="container">
        <h1>Manage Categories</h1>
        <form
            action="{{ route('admin.categories.index') }}"
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
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Add New Category</a>

        <table class="table">
            <div class="paginate">
                {{ $categories->links() }}
            </div>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                <tr>
                    <td><a href="{{ route('categories.show', $category->code) }}" class="table-link">{{ $category->code }}</a></td>
                    <td>{{ $category->name }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3">No categories found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</x-app-layout>