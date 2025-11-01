<section>
    <header>
        <h2>
            {{ __('Delete Account') }}
        </h2>
        <p>
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <div x-data="{ showConfirm: false }">

        <button type="button" @click.prevent="showConfirm = true" class="btn btn-danger">
            {{ __('Delete Account') }}
        </button>

        <div x-show="showConfirm" class="modal-overlay" @click.away="showConfirm = false" x-cloak>
            <div class="modal-content">

                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <h2>
                        {{ __('Are you sure you want to delete your account?') }}
                    </h2>
                    <p>
                        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                    </p>

                    <div class="form-group">
                        <label for="password_delete" class="form-label">{{ __('Password') }}</label>
                        <input id="password_delete" name="password" type="password" class="form-control" placeholder="{{ __('Password') }}">
                        @error('password', 'userDeletion')
                        <div class="form-error" style="margin-top: 0.5rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-actions" style="justify-content: flex-end;">
                        <button type="button" @click="showConfirm = false" class="btn btn-secondary">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-danger">
                            {{ __('Delete Account') }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</section>