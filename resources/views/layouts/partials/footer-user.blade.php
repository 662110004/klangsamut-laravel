<footer class="app-footer">
    <div>
        &copy; {{ date('Y') }} KlangSamut. All rights reserved.
    </div>
    <div class="footer-links">
        <a href="{{ route('user.home') }}">Welcome</a> 
        <a href="{{ route('books.index') }}">Browse Books</a>
    </div>
</footer>