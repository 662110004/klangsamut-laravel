@extends('layouts.user') @section('content')

<div class="dashboard-card" style="margin-bottom: 2rem;">
    <h1>Welcome, {{ Auth::user()->name }}!</h1>
    <p class="mt-1rem text-muted">
        Explore the KlangSamut book encyclopedia.
    </p>
</div>

<div class="book-carousel-section">

    <div class="book-carousel-header">
        <h2>Recently Added Books</h2>
        <a href="{{ route('books.index') }}" class="btn btn-secondary">View All</a>
    </div>

    <div class="book-carousel-container">
        @forelse($recentBooks as $book)
        <a href="{{ route('books.show', $book->code) }}" class="book-card">
            <div class="book-card-cover">
                @if($book->image_path)
                <img src="{{ asset('storage/' . $book->image_path) }}" alt="{{ $book->title }} Cover">
                @else
                <div class="book-card-cover-placeholder">No Cover</div>
                @endif
            </div>
            <div class="book-card-title">{{ $book->title }}</div>
            <div class="book-card-author">{{ $book->author->pseudonym }}</div>
        </a>
        @empty
        <p class="text-muted">No books have been added yet.</p>
        @endforelse
    </div>
</div>

@endsection