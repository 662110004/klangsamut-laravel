@php
session()->put('bookmark_url.return_to_list', request()->fullUrl());
@endphp

<x-app-layout>
    <div class="container">

        <h1>Manage Users</h1>

        <form
            action="{{ route('admin.users.index') }}"
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
                {{ $users->links() }}
            </div>
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Name</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                <tr>
                    <td><a href="{{ route('admin.users.show', $user) }}" class="table-link">{{ $user->email }}</a></td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->role }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>



    </div>
</x-app-layout>