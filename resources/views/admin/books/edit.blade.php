<x-app-layout>
    <div class="container">
        <h1>Edit Book</h1>

        <form action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data" x-data="{ showConfirm: false }">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="code" class="form-label">Code</label>
                <input type="text" id="code" name="code" class="form-control" value="{{ old('code', $book->code) }}" required>
                @error('code')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="title" class="form-label">Title</label>
                <input type="text" id="title" name="title" class="form-control" value="{{ old('title', $book->title) }}">
                @error('title')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="author_id" class="form-label">Author</label>
                <select id="author_id" name="author_id" class="form-control">
                    <option value="">-- Select Author --</option>
                    @foreach($authors as $author)
                    <option value="{{ $author->id }}" {{ old('author_id', $book->author_id) == $author->id ? 'selected' : '' }}>
                        {{ $author->pseudonym }}
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
                                {{-- 1. เช็ก old() ก่อน --}}
                                @if(old('categories'))
                                {{ (collect(old('categories'))->contains($category->id)) ? 'checked' : '' }}
                                {{-- 2. ถ้าไม่มี old() ให้เช็กข้อมูลจาก DB --}}
                                @else
                                {{ ($book->categories->contains($category->id)) ? 'checked' : '' }}
                                @endif>
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
                <textarea id="description" name="description" class="form-control" rows="5">{{ old('description', $book->description) }}</textarea>
                @error('description')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="synopsis" class="form-label">Synopsis</label>
                <textarea id="synopsis" name="synopsis" class="form-control" rows="8">{{ old('synopsis', $book->synopsis) }}</textarea>
                @error('synopsis')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="image" class="form-label">Book Image (To replace, upload a new one. A maximum of 2048 KB)</label>

                @if($book->image_path)
                <div style="margin-bottom: 1rem;">
                    <img src="{{ asset('storage/' . $book->image_path) }}" alt="{{ $book->pseudonym }}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                </div>
                @endif

                <input type="file" id="image" name="image" class="form-control">
                @error('image')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="button" @click.prevent="showConfirm = true" class="btn btn-primary">
                    Update
                </button>
                <a href="{{ session()->get('bookmark_url.books_show', route('books.show', $book->code)) }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>

            <div x-show="showConfirm" class="modal-overlay" @click.away="showConfirm = false" x-cloak>
                <div class="modal-content">
                    <p>Are you sure you want to save these changes?</p>

                    <button type="submit" class="btn btn-primary">Yes, Save Changes</button>

                    <button type="button" @click="showConfirm = false" class="btn btn-secondary">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>