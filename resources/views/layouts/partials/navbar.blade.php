<header class="fmc-navbar">
    <div class="d-flex align-items-center gap-2 px-3 px-lg-4 py-2">
        <button class="btn btn-light d-lg-none" id="sidebarToggle" type="button" aria-label="Toggle sidebar">
            <i class="bi bi-list fs-5"></i>
        </button>

        <div class="d-none d-md-block">
            <div class="fw-bold">@yield('title', 'Dashboard')</div>
            <div class="text-muted small">{{ now()->format(setting('date_format', 'd M Y')) }}</div>
        </div>

        <div class="ms-auto d-flex align-items-center gap-2">
            <button class="btn btn-light" type="button" onclick="fmcToggleTheme()" aria-label="Toggle theme">
                <i id="themeIcon" class="bi bi-moon-stars"></i>
            </button>

            <div class="dropdown">
                <button class="btn btn-light position-relative" data-bs-toggle="dropdown" aria-label="Notifications">
                    <i class="bi bi-bell"></i>
                    <span id="notifBadge" class="d-none position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem"></span>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow" style="width:320px">
                    <div class="dropdown-header d-flex justify-content-between align-items-center">
                        <span>Notifications</span>
                        <a href="#" class="small" onclick="$.post('{{ route('admin.notifications.read-all') }}').done(() => location.reload()); return false;">Mark all read</a>
                    </div>
                    <div id="notifList" style="max-height:320px;overflow:auto">
                        <div class="dropdown-item text-muted small">No new notifications</div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-center small" href="{{ route('admin.notifications.index') }}">View all</a>
                </div>
            </div>

            <div class="dropdown">
                <button class="btn btn-light d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                    <img src="{{ auth()->user()->avatar_url }}" alt="" class="avatar-sm">
                    <span class="d-none d-md-inline fw-semibold small">{{ auth()->user()->name }}</span>
                    <i class="bi bi-chevron-down small"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow">
                    <div class="dropdown-header">
                        <div class="fw-semibold">{{ auth()->user()->name }}</div>
                        <div class="small text-muted">{{ auth()->user()->roles->pluck('name')->join(', ') }}</div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('admin.profile.edit') }}"><i class="bi bi-person me-2"></i>My Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
