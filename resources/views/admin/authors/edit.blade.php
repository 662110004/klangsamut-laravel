<x-app-layout>
    <div class="container">
        <h1>Edit Author</h1>

        <form action="{{ route('admin.authors.update', $author) }}" method="POST" enctype="multipart/form-data" x-data="{ showConfirm: false }">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="code" class="form-label">Code</label>
                <input type="text" id="code" name="code" class="form-control" value="{{ old('code', $author->code) }}" required>
                @error('code')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="pseudonym" class="form-label">Pseudonym or Name</label>
                <input type="text" id="pseudonym" name="pseudonym" class="form-control" value="{{ old('pseudonym', $author->pseudonym) }}">
                @error('pseudonym')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="biography" class="form-label">Biography</label>
                <textarea id="biography" name="biography" class="form-control" rows="5">{{ old('biography', $author->biography) }}</textarea>
                @error('biography')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="image" class="form-label">Author Image (Upload new to replace)</label>

                @if($author->image_path)
                <div style="margin-bottom: 1rem;">
                    <img src="{{ asset('storage/' . $author->image_path) }}" alt="{{ $author->pseudonym }}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
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
                <a href="{{ session()->get('bookmark_url.authors_show', route('authors.show', $author->code)) }}" class="btn btn-secondary">
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