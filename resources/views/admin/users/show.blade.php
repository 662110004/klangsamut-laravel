<x-app-layout>
    <div class="content-container">

        <div class="book-show-header-actions">
            <div class="left-actions">
                <a href="{{ session()->get('bookmark_url.users', route('admin.users.index')) }}" class="btn btn-secondary">
                    &larr; Back to List
                </a>
            </div>

            <div class="right-actions">
                @auth
                @if(Auth::id() === $user->id)
                <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profile</a>

                @elseif(Auth::user()->role == 'admin')
                <div x-data="{ showConfirm: false }" class="modal-delete-wrapper">
                    <button @click.prevent="showConfirm = true" class="btn btn-danger">Delete This User</button>
                    <div x-show="showConfirm" class="modal-overlay" @click.away="showConfirm = false" x-cloak>
                        <div class="modal-content">
                            <p>Are you sure you want to delete '{{ $user->name }}'?</p>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="modal-delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Yes, Delete</button>
                            </form>
                            <button @click="showConfirm = false" class="btn btn-secondary">Cancel</button>
                        </div>
                    </div>
                </div>
                @endif
                @endauth
            </div>
        </div>

        <div class="book-details-wrapper" style="padding-top: 1.5rem;">
            <h1>{{ $user->name }}</h1>
            <div class="book-show-author">
                Role: <span>{{ $user->role }}</span>
            </div>

            <div class="book-show-section">
                <h3>User Details</h3>
                <table class="table">
                    <tbody>
                        <tr>
                            <th style="width: 200px;">Email Address</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>Joined On</th>
                            <td>{{ $user->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated</th>
                            <td>{{ $user->updated_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        </table>
    </div>
    @if(Auth::id() !== $user->id)

    <div class="book-show-section profile-section">
        <h2>Change Role</h2>

        <form method="POST" action="{{ route('admin.users.update-role', $user) }}">
            @csrf
            <div class="form-group">
                <label for="role" class="form-label">User Role</label>

                <select id="role" name="role" class="form-control" style="max-width: 400px;">
                    <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>
                        User
                    </option>
                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>
                        Admin
                    </option>
                </select>

                @error('role')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Role</button>
            </div>
        </form>
    </div>
    @endif
</x-app-layout>