<x-app-layout>
    <div class="container">
        <div class="breadcrumbs">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <span class="separator">/</span>
            <a href="{{ route('admin.categories.index') }}">Manage Categories</a>
            <span class="separator">/</span>
            <span>Edit Category</span>
        </div>
        <h1>Edit Category</h1>

        <form action="{{ route('admin.categories.update', $category) }}" method="POST" x-data="{ showConfirm: false }">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="code" class="form-label">Code</label>
                <input type="text" id="code" name="code" class="form-control" value="{{ old('code', $category->code) }}" required>
                @error('code')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
                @error('name')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="button" @click.prevent="showConfirm = true" class="btn btn-primary">
                    Update
                </button>
                <a href="{{ session()->get('bookmark_url.categories_show', route('categories.show', $category->code)) }}" class="btn btn-secondary">
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