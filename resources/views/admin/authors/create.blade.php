<x-app-layout>
    <div class="container">
        <h1>Add New Author</h1>

        <form action="{{ route('admin.authors.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="code" class="form-label">Code</label>
                <input type="text" id="code" name="code" class="form-control" value="{{ old('code') }}" required>
                @error('code')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="pseudonym" class="form-label">Pseudonym or Name</label>
                <input type="text" id="pseudonym" name="pseudonym" class="form-control" value="{{ old('pseudonym') }}" required>
                @error('pseudonym')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="biography" class="form-label">Biography</label>
                <textarea id="biography" name="biography" class="form-control" rows="5">{{ old('biography') }}</textarea>
                @error('biography')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="image" class="form-label">Author Image</label>
                <input type="file" id="image" name="image" class="form-control">
                @error('image')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ session()->get('bookmark_url.authors', route('admin.authors.index')) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>