<nav class="col-md-3 col-lg-2 d-none d-md-block bg-white sidebar border-end vh-100 position-sticky top-0 p-3">
    <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
        <img class="fs-4 me-2" width="50" src="{{asset('icons/logo.png')}}" alt="ExpenseTracker Logo">
        <span class="fw-bold fs-5">ExpenseTracker</span>
    </div>
    <ul class="nav nav-pills flex-column mb-auto gap-2">
        <li class="nav-item">
            <a href="/home" class="nav-link d-flex align-items-center gap-2 {{ Request::is('home') ? 'active' : 'text-dark' }}">
                <i class="bi bi-house-door"></i> Home
            </a>
        </li>
        <li class="nav-item">
            <a href="/scan" class="nav-link d-flex align-items-center gap-2 {{ Request::is('scan') ? 'active' : 'text-dark' }}">
                <i class="bi bi-qr-code-scan"></i> Scan
            </a>
        </li>
        <li class="nav-item">
            <a href="/expenses" class="nav-link d-flex align-items-center gap-2 {{ Request::is('expenses') ? 'active' : 'text-dark' }}">
                <i class="bi bi-credit-card"></i> Expenses
            </a>
        </li>
        <li class="nav-item">
            <a href="/analytics" class="nav-link d-flex align-items-center gap-2 {{ Request::is('analytics') ? 'active' : 'text-dark' }}">
                <i class="bi bi-graph-up-arrow"></i> Analytics
            </a>
        </li>
    </ul>
    <div class="mt-auto border-top pt-2">
        <!-- Keep the form hidden and self-contained -->
        <form action="{{ route('logout') }}" method="GET" id="logout-form" class="d-none">
            @csrf
        </form>
        
        <!-- This anchor is now visible to users -->
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link text-danger d-flex align-items-center gap-2 p-2 rounded">
            <i class="bi bi-box-arrow-left"></i> Sign Out
        </a>
    </div>
    
</nav>
