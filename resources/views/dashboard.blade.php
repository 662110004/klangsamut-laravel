<x-app-layout>
    <div class="container">

        <div class="dashboard-card">
            <h1>
                {{ __("Welcome to KlangSamut, ") }} {{ Auth::user()->name }}!
            </h1>
        </div>

        @if(Auth::user()->role == 'admin')
        <div class="stats-grid">

            <a href="{{ route('admin.books.admin_index') }}" class="stat-card">
                <div class="stat-card-label">Total Books</div>
                <span class="stat-card-number">{{ $bookCount }}</span>
            </a>

            <a href="{{ route('admin.authors.index') }}" class="stat-card">
                <div class="stat-card-label">Total Authors</div>
                <span class="stat-card-number">{{ $authorCount }}</span>
            </a>

            <a href="{{ route('admin.categories.index') }}" class="stat-card">
                <div class="stat-card-label">Total Categories</div>
                <span class="stat-card-number">{{ $categoryCount }}</span>
            </a>

            <a href="{{ route('admin.users.index') }}" class="stat-card">
                <div class="stat-card-label">Total Users</div>
                <span class="stat-card-number">{{ $userCount }}</span>
            </a>

        </div>
        @endif

    </div>
</x-app-layout>