<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ setting('app_name', config('app.name')) }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.11/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.1/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">

    <style>
        :root {
            --fmc-primary: {{ setting('theme_primary_color', '#2563EB') }};
            --fmc-primary-dark: color-mix(in srgb, var(--fmc-primary) 85%, #000);
            --fmc-primary-subtle: color-mix(in srgb, var(--fmc-primary) 12%, #fff);
            --fmc-sidebar-w: 264px;
            --bs-primary: var(--fmc-primary);
            --bs-primary-rgb: 37, 99, 235;
            --bs-link-color: var(--fmc-primary);
            --bs-body-font-family: 'Inter', system-ui, sans-serif;
        }
        body { background: #f1f5f9; font-family: 'Inter', system-ui, sans-serif; }
        [data-bs-theme="dark"] body { background: #0b1220; }

        .btn-primary { --bs-btn-bg: var(--fmc-primary); --bs-btn-border-color: var(--fmc-primary);
            --bs-btn-hover-bg: var(--fmc-primary-dark); --bs-btn-hover-border-color: var(--fmc-primary-dark);
            --bs-btn-active-bg: var(--fmc-primary-dark); --bs-btn-active-border-color: var(--fmc-primary-dark); }
        .btn-outline-primary { --bs-btn-color: var(--fmc-primary); --bs-btn-border-color: var(--fmc-primary);
            --bs-btn-hover-bg: var(--fmc-primary); --bs-btn-hover-border-color: var(--fmc-primary);
            --bs-btn-active-bg: var(--fmc-primary); --bs-btn-active-border-color: var(--fmc-primary); }
        .text-bg-primary-subtle { background: var(--fmc-primary-subtle); color: var(--fmc-primary-dark); }
        [data-bs-theme="dark"] .text-bg-primary-subtle { background: color-mix(in srgb, var(--fmc-primary) 25%, #0b1220); color: #93c5fd; }

        /* ---- Sidebar ---- */
        .fmc-sidebar { position: fixed; inset: 0 auto 0 0; width: var(--fmc-sidebar-w); z-index: 1040;
            background: linear-gradient(180deg, #0f2a66 0%, #102f7a 45%, #1d4ed8 130%);
            color: #cbd5e1; display: flex; flex-direction: column; transition: transform .25s ease; }
        .fmc-sidebar .brand { display:flex; align-items:center; gap:.65rem; padding:1.15rem 1.25rem;
            color:#fff; text-decoration:none; font-weight:800; font-size:1.08rem; letter-spacing:.2px; }
        .fmc-sidebar .brand .logo-badge { width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,.14);
            display:grid;place-items:center;font-size:1.2rem; }
        .fmc-sidebar .nav-label { font-size:.68rem; text-transform:uppercase; letter-spacing:.12em;
            color:#7c9bd6; padding:1rem 1.4rem .35rem; }
        .fmc-sidebar .nav-link { color:#c7d6f2; padding:.55rem 1.05rem; margin:.1rem .75rem; border-radius:.55rem;
            display:flex; align-items:center; gap:.7rem; font-size:.9rem; font-weight:500; }
        .fmc-sidebar .nav-link i { font-size:1.05rem; width:1.25rem; text-align:center; opacity:.9; }
        .fmc-sidebar .nav-link:hover { background:rgba(255,255,255,.08); color:#fff; }
        .fmc-sidebar .nav-link.active { background:#fff; color:var(--fmc-primary-dark); font-weight:600;
            box-shadow:0 6px 18px rgba(2,6,23,.25); }
        .fmc-sidebar .sidebar-scroll { overflow-y:auto; flex:1; padding-bottom:1rem; scrollbar-width:thin; }

        /* ---- Main / navbar ---- */
        .fmc-main { margin-left: var(--fmc-sidebar-w); min-height:100vh; display:flex; flex-direction:column; transition: margin .25s ease; }
        .fmc-navbar { background:var(--bs-body-bg); border-bottom:1px solid var(--bs-border-color);
            position:sticky; top:0; z-index:1030; }
        .fmc-content { padding:1.5rem; flex:1; }

        /* ---- Cards / widgets ---- */
        .card { border:0; border-radius:.9rem; box-shadow:0 1px 3px rgba(15,23,42,.07), 0 8px 24px -16px rgba(15,23,42,.12); }
        [data-bs-theme="dark"] .card { box-shadow:0 1px 3px rgba(0,0,0,.5); }
        .stat-card .stat-icon { width:46px;height:46px;border-radius:.8rem;display:grid;place-items:center;font-size:1.25rem; }
        .stat-card h3 { font-weight:800; margin:0; }
        .badge-status { font-weight:600; }
        .text-bg-success-subtle { background:#dcfce7; color:#15803d; }
        .text-bg-danger-subtle { background:#fee2e2; color:#b91c1c; }
        .text-bg-warning-subtle { background:#fef3c7; color:#b45309; }
        .text-bg-info-subtle { background:#e0f2fe; color:#0369a1; }
        .text-bg-secondary-subtle { background:#e2e8f0; color:#334155; }
        [data-bs-theme="dark"] .text-bg-success-subtle { background:#14532d; color:#86efac; }
        [data-bs-theme="dark"] .text-bg-danger-subtle { background:#7f1d1d; color:#fca5a5; }
        [data-bs-theme="dark"] .text-bg-warning-subtle { background:#78350f; color:#fcd34d; }
        [data-bs-theme="dark"] .text-bg-info-subtle { background:#0c4a6e; color:#7dd3fc; }
        [data-bs-theme="dark"] .text-bg-secondary-subtle { background:#1e293b; color:#cbd5e1; }

        /* ---- Tables ---- */
        .table thead th { font-size:.72rem; text-transform:uppercase; letter-spacing:.06em; color:#64748b;
            border-bottom-width:1px; font-weight:700; white-space:nowrap; }
        .table td { vertical-align:middle; font-size:.9rem; }
        div.dataTables_wrapper div.dataTables_processing { background:var(--bs-body-bg); border-radius:.5rem; }

        /* ---- Forms ---- */
        .form-label { font-weight:600; font-size:.85rem; }
        .required:after { content:" *"; color:#dc2626; }
        .select2-container .select2-selection--single, .select2-container .select2-selection--multiple { min-height:38px; }

        .avatar-sm { width:36px;height:36px;border-radius:50%;object-fit:cover; }
        .avatar-lg { width:96px;height:96px;border-radius:50%;object-fit:cover; }

        @media (max-width: 991.98px) {
            .fmc-sidebar { transform: translateX(-100%); }
            .fmc-sidebar.show { transform: none; }
            .fmc-main { margin-left:0; }
            .fmc-content { padding:1rem; }
        }
        .sidebar-backdrop { display:none; position:fixed; inset:0; background:rgba(2,6,23,.5); z-index:1035; }
        .sidebar-backdrop.show { display:block; }
    </style>
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    <style>
        #fmcLeafletMap { z-index: 0; }
        .leaflet-control-attribution { font-size: 10px; }
    </style>
    @stack('styles')
</head>
<body>

@include('layouts.partials.sidebar')
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<div class="fmc-main">
    @include('layouts.partials.navbar')

    <main class="fmc-content">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if ($errors->any() && ! session('success'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Please fix the following:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="px-4 py-3 text-muted small border-top">
        © {{ date('Y') }} {{ setting('app_name', 'Fix My Class') }} · v1.0
    </footer>
</div>

{{-- ====== Global Map Picker Modal ====== --}}
<div class="modal fade" id="mapPickerModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2 border-bottom">
                <h6 class="modal-title fw-semibold"><i class="bi bi-map me-2 text-primary"></i>Select Location on Map</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-2 border-bottom bg-light d-flex gap-2 align-items-center">
                    <input type="text" id="mapSearchInput" class="form-control form-control-sm"
                           placeholder="Search city, address, place…" style="flex:1">
                    <button type="button" class="btn btn-sm btn-primary px-3" id="mapSearchBtn">
                        <i class="bi bi-search"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="mapMyLocationBtn" title="Use my current location">
                        <i class="bi bi-geo-alt-fill"></i> My Location
                    </button>
                </div>
                <div id="fmcLeafletMap" style="height:420px; width:100%;"></div>
                <div class="px-3 py-2 border-top bg-light d-flex align-items-center gap-2 small">
                    <i class="bi bi-cursor text-muted"></i>
                    <span class="text-muted">Click on map or drag the pin to select location.</span>
                    <span class="ms-auto fw-semibold text-primary" id="mapPickerCoords">No location selected</span>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" id="mapPickerConfirmBtn">
                    <i class="bi bi-check-lg me-1"></i>Use This Location
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.11/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.11/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.1/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.49.1"></script>

<script>
    // ---- Global AJAX / CSRF ----
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    @auth
    @if(auth()->user()->hasRole('City Admin') && auth()->user()->city)
    window.fmcUserCity = {
        name: '{{ auth()->user()->city->name }}',
        state: '{{ auth()->user()->city->state }}',
    };
    @else
    window.fmcUserCity = null;
    @endif
    @endauth

    // ---- Theme (dark / light) ----
    (function () {
        const stored = localStorage.getItem('fmc-theme')
            || '{{ setting('theme_default_mode', 'light') }}';
        const apply = (mode) => {
            const resolved = mode === 'system'
                ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
                : mode;
            document.documentElement.setAttribute('data-bs-theme', resolved);
            const icon = document.getElementById('themeIcon');
            if (icon) icon.className = resolved === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars';
        };
        apply(stored);
        window.fmcToggleTheme = function () {
            const current = document.documentElement.getAttribute('data-bs-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            localStorage.setItem('fmc-theme', next);
            apply(next);
        };
    })();

    // ---- Mobile sidebar ----
    $('#sidebarToggle').on('click', function () {
        $('.fmc-sidebar').toggleClass('show');
        $('#sidebarBackdrop').toggleClass('show');
    });
    $('#sidebarBackdrop').on('click', function () {
        $('.fmc-sidebar').removeClass('show');
        $(this).removeClass('show');
    });

    // ---- Toast helper ----
    window.fmcToast = function (message, icon = 'success') {
        Swal.fire({ toast: true, position: 'top-end', icon, title: message,
            showConfirmButton: false, timer: 3000, timerProgressBar: true });
    };

    // ---- Delete helper (SweetAlert + AJAX) ----
    window.fmcDelete = function (url, tableSelector = '.fmc-datatable') {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This record will be deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Yes, delete it',
        }).then((result) => {
            if (!result.isConfirmed) return;
            $.ajax({ url, type: 'DELETE' })
                .done((res) => {
                    fmcToast(res.message || 'Deleted.');
                    $(tableSelector).each(function () {
                        const dt = $(this).DataTable();
                        if (dt) dt.ajax.reload(null, false);
                    });
                })
                .fail((xhr) => fmcToast(xhr.responseJSON?.message || 'Delete failed.', 'error'));
        });
    };

    // ---- Generic POST action helper ----
    window.fmcPost = function (url, confirmText = null, tableSelector = '.fmc-datatable') {
        const run = () => $.post(url)
            .done((res) => {
                fmcToast(res.message || 'Done.');
                $(tableSelector).each(function () {
                    const dt = $(this).DataTable();
                    if (dt) dt.ajax.reload(null, false);
                });
            })
            .fail((xhr) => fmcToast(xhr.responseJSON?.message || 'Action failed.', 'error'));

        if (!confirmText) return run();

        Swal.fire({ title: confirmText, icon: 'question', showCancelButton: true, confirmButtonText: 'Confirm' })
            .then((r) => { if (r.isConfirmed) run(); });
    };

    // ---- DataTable defaults ----
    $.extend(true, $.fn.dataTable.defaults, {
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        dom: "<'row align-items-center mb-2'<'col-sm-6'l><'col-sm-6 text-end'>>t<'row align-items-center mt-3'<'col-sm-6'i><'col-sm-6'p>>",
        language: { processing: '<div class="spinner-border text-primary spinner-border-sm"></div> Loading…' },
    });

    // ---- Select2 default ----
    $(function () {
        $('.select2').each(function () {
            $(this).select2({ theme: 'bootstrap-5', width: '100%',
                dropdownParent: $(this).closest('.modal').length ? $(this).closest('.modal') : $(document.body) });
        });
    });

    // ---- Unread notifications badge ----
    $(function () {
        const badge = $('#notifBadge');
        if (!badge.length) return;
        $.get('{{ route('admin.notifications.unread') }}')
            .done((res) => {
                if (res.count > 0) badge.text(res.count > 99 ? '99+' : res.count).removeClass('d-none');
                const list = $('#notifList');
                if (!res.notifications.length) return;
                list.empty();
                res.notifications.forEach((n) => {
                    list.append(`<a class="dropdown-item small py-2" href="${n.url || '#'}">
                        <div class="fw-semibold">${n.title}</div>
                        <div class="text-muted text-truncate" style="max-width:260px">${n.body}</div>
                        <div class="text-muted" style="font-size:.7rem">${n.time}</div></a>`);
                });
            });
    });
</script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ===== Global Map Picker =====
(function () {
    let _map = null, _marker = null;
    let _pickedLat = null, _pickedLng = null;
    let _latSel = null, _lngSel = null;

    const mapEl    = document.getElementById('mapPickerModal');
    const coordsEl = document.getElementById('mapPickerCoords');

    function updateDisplay() {
        if (_pickedLat !== null) {
            coordsEl.textContent = _pickedLat.toFixed(6) + ', ' + _pickedLng.toFixed(6);
        } else {
            coordsEl.textContent = 'No location selected';
        }
    }

    function placeMarker(lat, lng) {
        _pickedLat = lat; _pickedLng = lng;
        if (_marker) {
            _marker.setLatLng([lat, lng]);
        } else {
            _marker = L.marker([lat, lng], { draggable: true }).addTo(_map);
            _marker.on('dragend', function () {
                const p = _marker.getLatLng();
                _pickedLat = p.lat; _pickedLng = p.lng;
                updateDisplay();
            });
        }
        updateDisplay();
    }

    function clearMarker() {
        if (_marker) { _map.removeLayer(_marker); _marker = null; }
        _pickedLat = null; _pickedLng = null;
        updateDisplay();
    }

    // Init map lazily on first open
    function initMap() {
        if (_map) return;
        _map = L.map('fmcLeafletMap', { zoomControl: true }).setView([20.5937, 78.9629], 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19,
        }).addTo(_map);
        _map.on('click', function (e) { placeMarker(e.latlng.lat, e.latlng.lng); });
    }

    // Public API
    window.fmcOpenMapPicker = function (latSelector, lngSelector) {
        _latSel = latSelector;
        _lngSel = lngSelector;

        const currentLat = parseFloat($(latSelector).val()) || null;
        const currentLng = parseFloat($(lngSelector).val()) || null;

        // nested modal z-index fix
        $(mapEl).one('show.bs.modal', function () {
            const openModals = document.querySelectorAll('.modal.show');
            if (openModals.length) {
                const maxZ = Math.max(...[...openModals].map(m => parseInt(m.style.zIndex) || 1055));
                mapEl.style.zIndex = maxZ + 20;
                setTimeout(() => {
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    if (backdrops.length > 1) {
                        backdrops[backdrops.length - 1].style.zIndex = maxZ + 10;
                    }
                }, 10);
            }
        });

        $(mapEl).one('shown.bs.modal', function () {
            initMap();
            _map.invalidateSize();
            clearMarker();
            if (currentLat && currentLng) {
                placeMarker(currentLat, currentLng);
                _map.setView([currentLat, currentLng], 15);
            } else if (window.fmcUserCity) {
                const q = window.fmcUserCity.name + ', ' + window.fmcUserCity.state + ', India';
                $('#mapSearchInput').val(window.fmcUserCity.name);
                fetch('https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent(q) + '&format=json&limit=1', {
                    headers: { 'Accept-Language': 'en' }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.length) {
                        _map.setView([parseFloat(data[0].lat), parseFloat(data[0].lon)], 13);
                    } else {
                        _map.setView([20.5937, 78.9629], 5);
                    }
                })
                .catch(() => _map.setView([20.5937, 78.9629], 5));
            } else {
                _map.setView([20.5937, 78.9629], 5);
            }
            if (!window.fmcUserCity) $('#mapSearchInput').val('');
        });

        new bootstrap.Modal(mapEl).show();
    };

    // Restore body scroll when nested map modal closes
    mapEl.addEventListener('hidden.bs.modal', function () {
        if (document.querySelector('.modal.show')) {
            document.body.classList.add('modal-open');
            document.body.style.overflow = 'hidden';
        }
    });

    // Confirm button
    document.getElementById('mapPickerConfirmBtn').addEventListener('click', function () {
        if (_pickedLat === null) {
            alert('Pehle map par ek location select karein.');
            return;
        }
        $(_latSel).val(_pickedLat.toFixed(7));
        $(_lngSel).val(_pickedLng.toFixed(7));
        bootstrap.Modal.getInstance(mapEl).hide();
    });

    // Search via Nominatim (free, no API key)
    function doSearch() {
        const q = $('#mapSearchInput').val().trim();
        if (!q) return;
        const btn = $('#mapSearchBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        fetch('https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent(q) + '&format=json&limit=1', {
            headers: { 'Accept-Language': 'en' }
        })
        .then(r => r.json())
        .then(data => {
            btn.prop('disabled', false).html('<i class="bi bi-search"></i>');
            if (data.length) {
                const lat = parseFloat(data[0].lat), lng = parseFloat(data[0].lon);
                _map.setView([lat, lng], 14);
                placeMarker(lat, lng);
            } else {
                alert('Location nahi mili. Alag search try karein.');
            }
        })
        .catch(() => btn.prop('disabled', false).html('<i class="bi bi-search"></i>'));
    }

    $('#mapSearchBtn').on('click', doSearch);
    $('#mapSearchInput').on('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); doSearch(); } });

    // My Location
    $('#mapMyLocationBtn').on('click', function () {
        if (!navigator.geolocation) { alert('Geolocation support nahi hai is browser mein.'); return; }
        const btn = $(this).prop('disabled', true);
        navigator.geolocation.getCurrentPosition(function (pos) {
            btn.prop('disabled', false);
            const lat = pos.coords.latitude, lng = pos.coords.longitude;
            _map.setView([lat, lng], 16);
            placeMarker(lat, lng);
        }, function () {
            btn.prop('disabled', false);
            alert('Location access deny kar di gayi hai.');
        });
    });
})();
</script>
@stack('scripts')
</body>
</html>
