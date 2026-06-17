<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sign in') — {{ setting('app_name', config('app.name')) }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --fmc-primary: {{ setting('theme_primary_color', '#2563EB') }}; }
        body { font-family:'Inter',system-ui,sans-serif; min-height:100vh; display:grid; place-items:center;
            background: radial-gradient(1200px 600px at 10% -10%, #3b82f6 0%, transparent 50%),
                        radial-gradient(1000px 500px at 110% 110%, #1d4ed8 0%, transparent 45%),
                        #0f2a66; }
        .auth-card { width:100%; max-width:430px; border:0; border-radius:1.1rem;
            box-shadow:0 25px 60px -20px rgba(2,6,23,.6); }
        .brand-badge { width:54px;height:54px;border-radius:14px;background:var(--fmc-primary);color:#fff;
            display:grid;place-items:center;font-size:1.6rem;margin:0 auto 0.75rem; }
        .btn-primary { --bs-btn-bg:var(--fmc-primary); --bs-btn-border-color:var(--fmc-primary);
            --bs-btn-hover-bg:#1d4ed8; --bs-btn-hover-border-color:#1d4ed8; }
        .form-label { font-weight:600; font-size:.85rem; }
    </style>
</head>
<body>
    <div class="p-3 w-100 d-flex justify-content-center">
        <div class="card auth-card">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="brand-badge"><i class="bi bi-mortarboard-fill"></i></div>
                    <h1 class="h4 fw-bold mb-1">{{ setting('app_name', 'Fix My Class') }}</h1>
                    <p class="text-muted small mb-0">{{ setting('app_tagline', 'Coaching Management SaaS Platform') }}</p>
                </div>
                @yield('content')
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
