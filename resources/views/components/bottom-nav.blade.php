<nav class="navbar navbar-light bg-white border-top fixed-bottom d-md-none px-2 py-1">
    <div class="container-fluid justify-content-around">
        <a href="/home" class="nav-link text-center {{ Request::is('home') ? 'text-primary' : 'text-muted' }}">
            <i class="bi bi-house-door fs-5 d-block"></i>
            <span class="d-block" style="font-size: 10px;">Home</span>
        </a>
        <a href="/scan" class="nav-link text-center {{ Request::is('scan') ? 'text-primary' : 'text-muted' }}">
            <i class="bi bi-qr-code-scan fs-5 d-block"></i>
            <span class="d-block" style="font-size: 10px;">Scan</span>
        </a>
        <a href="/expenses" class="nav-link text-center {{ Request::is('expenses') ? 'text-primary' : 'text-muted' }}">
            <i class="bi bi-credit-card fs-5 d-block"></i>
            <span class="d-block" style="font-size: 10px;">Expenses</span>
        </a>
        <a href="/analytics" class="nav-link text-center {{ Request::is('analytics') ? 'text-primary' : 'text-muted' }}">
            <i class="bi bi-graph-up-arrow fs-5 d-block"></i>
            <span class="d-block" style="font-size: 10px;">Analytics</span>
        </a>
    </div>
</nav>
