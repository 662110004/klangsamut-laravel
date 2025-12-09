<x-app-layout>
    <div class="container">
        <h1>Add New Category</h1>

        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="code" class="form-label">Code</label>
                <input type="text" id="code" name="code" class="form-control" value="{{ old('code') }}" required>
                @error('code')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                @error('name')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create</button>
                <a href="{{ session()->get('bookmark_url.categories', route('admin.categories.index')) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>