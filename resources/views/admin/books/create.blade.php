<x-app-layout>
    <div class="container">
        <div class="breadcrumbs">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <span class="separator">/</span>
            <a href="{{ route('admin.books.admin_index') }}">Manage Books</a>
            <span class="separator">/</span>
            <span>Create New</span>
        </div>
        <h1>Add New Book</h1>

        <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="code" class="form-label">Code</label>
                <input type="text" id="code" name="code" class="form-control" value="{{ old('code') }}" required>
                @error('code')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="title" class="form-label">Title</label>
                <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}">
                @error('title')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="author_id" class="form-label">Author</label>
                <select name="author_id" id="author_id" class="form-control">
                    <option value="">-- Select Author --</option>
                    @foreach($authors as $author)
                    <option value="{{ $author->id }}" {{ old('author_id') == $author->id ? 'selected' : '' }}>
                        {{ $author->code }}: {{ $author->pseudonym }}
                    </option>
                    @endforeach
                </select>
                @error('author_id')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Categories</label>
                <div class="checkbox-list-container">
                    <div class="checkbox-list">
                        @foreach($categories as $category)
                        <div class="checkbox-list-item">
                            <input type="checkbox"
                                name="categories[]"
                                value="{{ $category->id }}"
                                id="category-{{ $category->id }}"
                                {{ (collect(old('categories'))->contains($category->id)) ? 'checked' : '' }}>
                            <label for="category-{{ $category->id }}">
                                <span class="highlight-text">{{ $category->code }}</span>: {{ $category->name }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                @error('categories')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control" rows="5">{{ old('description') }}</textarea>
                @error('description')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="synopsis" class="form-label">Synopsis</label>
                <textarea id="synopsis" name="synopsis" class="form-control" rows="8">{{ old('synopsis') }}</textarea>
                @error('synopsis')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="image" class="form-label">Book Image (Not more than 2048 KB)</label>
                <input type="file" id="image" name="image" class="form-control">
                @error('image')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>



            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create</button>
                <a href="{{ session()->get('bookmark_url.books', route('admin.books.admin_index')) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>