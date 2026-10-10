<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Frontdesk') - {{ \App\Models\BengkelSetting::getSettings()->nama_bengkel }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        /* TEMA STANDAR LAYANAN PUBLIK: DEFAULT LIGHT MODE */
        :root {
            --bg-body: #f8fafc;
            --surface: #ffffff;
            --surface-secondary: #f1f5f9;
            --border-color: #e2e8f0;
            --border-hover: #93c5fd;

            --text-main: #0f172a;
            --text-muted: #475569;
            --text-dim: #64748b;

            --primary: #1e40af;
            --primary-blue: #2563eb;
            --primary-light: rgba(37, 99, 235, 0.1);

            --success: #16a34a;
            --warning: #d97706;
            --danger: #dc2626;
            --info: #0284c7;

            --accent: #0891b2; /* Teal untuk Petugas */
            --accent-light: rgba(8, 145, 178, 0.12);

            --input-bg: #ffffff;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 15px -3px rgba(0,0,0,0.06);
            --shadow-lg: 0 10px 25px -5px rgba(0,0,0,0.08);
        }

        [data-theme="dark"] {
            --bg-body: #0b1120;
            --surface: #131c31;
            --surface-secondary: #1a2642;
            --border-color: rgba(255,255,255,0.1);
            --border-hover: rgba(96,165,250,0.35);

            --text-main: #f8fafc;
            --text-muted: #cbd5e1;
            --text-dim: #94a3b8;

            --primary: #3b82f6;
            --primary-blue: #60a5fa;
            --primary-light: rgba(59,130,246,0.2);

            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #06b6d4;

            --accent: #22d3ee;
            --accent-light: rgba(34,211,238,0.15);

            --input-bg: rgba(15,23,42,0.7);
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.2);
            --shadow-md: 0 4px 20px rgba(0,0,0,0.4);
            --shadow-lg: 0 15px 30px rgba(0,0,0,0.6);
        }

        * { box-sizing: border-box; margin: 0; padding: 0;
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease; }

        body { background-color: var(--bg-body); color: var(--text-main); min-height: 100vh; display: flex; flex-direction: column; }

        /* NAVBAR */
        .navbar { background: var(--surface); border-bottom: 2px solid var(--border-color); padding: 0.85rem 2.2rem;
            position: sticky; top: 0; z-index: 50; display: flex; align-items: center; justify-content: space-between;
            box-shadow: var(--shadow-sm); }

        .navbar-brand { display: flex; align-items: center; gap: 0.85rem; text-decoration: none; }

        .brand-logo-img { width: 40px; height: 40px; border-radius: 10px; object-fit: contain;
            background: #fff; padding: 2px; border: 1px solid var(--border-color); }

        .brand-default-icon { width: 40px; height: 40px;
            background: linear-gradient(135deg, #0891b2, #0e7490);
            border-radius: 10px; display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem; box-shadow: 0 3px 8px rgba(8,145,178,0.3); }

        .brand-text h1 { font-size: 1.25rem; font-weight: 800; letter-spacing: -0.3px; color: var(--accent); margin: 0; line-height: 1.1; }
        .brand-text span { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 1.2px; color: var(--text-dim); font-weight: 700; }

        .navbar-nav { display: flex; align-items: center; gap: 0.4rem; list-style: none; }

        .nav-link { display: flex; align-items: center; gap: 0.4rem; color: var(--text-muted); text-decoration: none;
            padding: 0.5rem 0.95rem; border-radius: 10px; font-size: 0.92rem; font-weight: 600; transition: all 0.2s ease; }
        .nav-link:hover { color: var(--accent); background: var(--accent-light); }
        .nav-link.active { color: var(--accent); background: var(--accent-light); border: 1px solid rgba(8,145,178,0.3); font-weight: 700; }

        .navbar-right { display: flex; align-items: center; gap: 1rem; }

        .btn-theme-toggle { background: var(--surface-secondary); border: 1px solid var(--border-color);
            color: var(--text-main); padding: 0.45rem 0.85rem; border-radius: 10px; font-size: 0.82rem;
            font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem; }
        .btn-theme-toggle:hover { border-color: var(--accent); color: var(--accent); }

        .user-pill { display: flex; align-items: center; gap: 0.65rem; background: var(--surface-secondary);
            border: 1px solid var(--border-color); padding: 0.35rem 0.8rem; border-radius: 99px; }
        .user-avatar { width: 26px; height: 26px; border-radius: 50%; background: var(--accent);
            display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800; color: white; }
        .user-name { font-size: 0.85rem; font-weight: 700; color: var(--text-main); }
        .role-badge { background: var(--accent-light); color: var(--accent); border: 1px solid rgba(8,145,178,0.25);
            padding: 0.12rem 0.5rem; border-radius: 99px; font-size: 0.68rem; font-weight: 800;
            letter-spacing: 0.5px; text-transform: uppercase; }

        .btn-logout { background: rgba(220,38,38,0.1); color: var(--danger); border: 1px solid rgba(220,38,38,0.25);
            padding: 0.45rem 0.9rem; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 0.82rem; }
        .btn-logout:hover { background: var(--danger); color: #fff; }

        /* CONTAINER */
        .container { max-width: 1320px; width: 100%; margin: 0 auto; padding: 2rem; flex: 1; }

        .page-header { display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 1.8rem; flex-wrap: wrap; gap: 1rem; }
        .page-title h2 { font-size: 1.75rem; font-weight: 800; letter-spacing: -0.5px; color: var(--text-main); margin-bottom: 0.2rem; }
        .page-title p { color: var(--text-muted); font-size: 0.95rem; }
        .page-actions { display: flex; align-items: center; gap: 0.8rem; }

        /* BUTTONS */
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
            padding: 0.65rem 1.3rem; border-radius: 10px; font-size: 0.92rem; font-weight: 600;
            text-decoration: none; cursor: pointer; border: 1px solid transparent; transition: all 0.2s ease; }
        .btn-primary { background: var(--primary); color: #fff; box-shadow: 0 2px 6px rgba(30,64,175,0.2); }
        .btn-primary:hover { background: #1d4ed8; transform: translateY(-1px); }
        .btn-accent { background: var(--accent); color: #fff; box-shadow: 0 2px 6px rgba(8,145,178,0.25); }
        .btn-accent:hover { background: #0e7490; transform: translateY(-1px); }
        .btn-secondary { background: var(--surface-secondary); color: var(--text-main); border-color: var(--border-color); }
        .btn-secondary:hover { border-color: var(--accent); color: var(--accent); }
        .btn-success { background: var(--success); color: #fff; }
        .btn-warning { background: var(--warning); color: #fff; }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-info { background: var(--info); color: #fff; }
        .btn-sm { padding: 0.38rem 0.75rem; font-size: 0.82rem; border-radius: 8px; }

        /* CARD */
        .card { background: var(--surface); border: 1px solid var(--border-color); border-radius: 16px;
            padding: 1.8rem; box-shadow: var(--shadow-md); margin-bottom: 1.8rem; }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.4rem; }
        .card-header h3 { font-size: 1.25rem; font-weight: 800; color: var(--text-main); }

        /* ALERTS */
        .alert { padding: 0.9rem 1.3rem; border-radius: 12px; margin-bottom: 1.5rem;
            display: flex; align-items: center; justify-content: space-between; font-size: 0.92rem; font-weight: 500; }
        .alert-success { background: #dcfce7; border: 1px solid #86efac; color: #15803d; }
        [data-theme="dark"] .alert-success { background: rgba(22,163,74,0.2); border-color: rgba(22,163,74,0.4); color: #86efac; }
        .alert-danger { background: #fee2e2; border: 1px solid #fca5a5; color: #b91c1c; }
        [data-theme="dark"] .alert-danger { background: rgba(220,38,38,0.2); border-color: rgba(220,38,38,0.4); color: #fca5a5; }
        .alert-dismiss { background: transparent; border: none; color: currentColor; cursor: pointer; font-size: 1.1rem; opacity: 0.7; }

        /* TABLES */
        .table-responsive { width: 100%; overflow-x: auto; }
        .table { width: 100%; border-collapse: collapse; color: var(--text-main); }
        .table th { background: var(--surface-secondary); color: var(--text-muted); text-align: left;
            padding: 0.85rem 1rem; font-size: 0.82rem; font-weight: 800; text-transform: uppercase;
            letter-spacing: 0.8px; border-bottom: 2px solid var(--border-color); }
        .table td { padding: 0.95rem 1rem; border-bottom: 1px solid var(--border-color); font-size: 0.92rem; vertical-align: middle; }
        .table tr:last-child td { border-bottom: none; }
        .table tbody tr:hover { background: var(--surface-secondary); }

        /* BADGES */
        .badge { display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.25rem 0.65rem;
            border-radius: 99px; font-size: 0.76rem; font-weight: 800; }
        .badge-waiting  { background: #fef3c7; color: #b45309; border: 1px solid #fcd34d; }
        .badge-checking { background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }
        .badge-service  { background: #ede9fe; color: #6d28d9; border: 1px solid #c4b5fd; }
        .badge-completed{ background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .badge-cancelled{ background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; }
        [data-theme="dark"] .badge-waiting   { background: rgba(180,83,9,0.2);   color: #fcd34d; border-color: rgba(252,211,77,0.3); }
        [data-theme="dark"] .badge-checking  { background: rgba(29,78,216,0.25); color: #93c5fd; border-color: rgba(147,197,253,0.3); }
        [data-theme="dark"] .badge-service   { background: rgba(109,40,217,0.2); color: #c4b5fd; border-color: rgba(196,181,253,0.3); }
        [data-theme="dark"] .badge-completed { background: rgba(22,163,74,0.2);  color: #86efac; border-color: rgba(134,239,172,0.3); }
        [data-theme="dark"] .badge-cancelled { background: rgba(100,116,139,0.2);color: #94a3b8; border-color: rgba(148,163,184,0.3); }

        /* STAT CARDS */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; margin-bottom: 1.8rem; }
        .stat-card { background: var(--surface); border: 1px solid var(--border-color); border-radius: 14px;
            padding: 1.2rem 1.4rem; box-shadow: var(--shadow-sm); text-align: center; }
        .stat-card .stat-num { font-size: 2rem; font-weight: 900; line-height: 1; }
        .stat-card .stat-label { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;
            color: var(--text-dim); margin-top: 0.35rem; }

        /* FORM */
        .form-group { margin-bottom: 1.25rem; }
        .form-label { display: block; font-size: 0.88rem; font-weight: 700; color: var(--text-muted);
            margin-bottom: 0.45rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-control { width: 100%; padding: 0.7rem 1rem; background: var(--input-bg); color: var(--text-main);
            border: 1.5px solid var(--border-color); border-radius: 10px; font-size: 0.95rem; font-family: inherit;
            transition: border-color 0.2s ease, box-shadow 0.2s ease; }
        .form-control:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-light); }
        .form-control.is-invalid { border-color: var(--danger); }
        .invalid-feedback { color: var(--danger); font-size: 0.82rem; margin-top: 0.3rem; }
        select.form-control { cursor: pointer; }
        textarea.form-control { resize: vertical; min-height: 90px; }

        /* MODAL */
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6);
            backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 100; padding: 1rem; }
        .modal-overlay.active { display: flex; }
        .modal-box { background: var(--surface); border: 1px solid var(--border-color); border-radius: 18px;
            max-width: 520px; width: 100%; padding: 2rem; box-shadow: var(--shadow-lg); }

        .footer { text-align: center; padding: 1.5rem; color: var(--text-dim); font-size: 0.85rem;
            border-top: 1px solid var(--border-color); background: var(--surface); margin-top: auto; }

        @media (max-width: 950px) {
            .navbar { padding: 0.85rem 1.2rem; flex-wrap: wrap; gap: 0.8rem; }
            .navbar-nav { order: 3; width: 100%; overflow-x: auto; padding-top: 0.5rem; }
            .container { padding: 1.5rem 1rem; }
        }
    </style>
    @yield('styles')
</head>
<body>

    @php $bSetting = \App\Models\BengkelSetting::getSettings(); @endphp

    <!-- TOP NAVBAR -->
    <nav class="navbar">
        <a href="{{ route('petugas.dashboard') }}" class="navbar-brand">
            @if($bSetting->logo_url)
                <img src="{{ $bSetting->logo_url }}" alt="Logo" class="brand-logo-img">
            @else
                <div class="brand-default-icon">🖥️</div>
            @endif
            <div class="brand-text">
                <h1>{{ $bSetting->nama_bengkel }}</h1>
                <span>Panel Frontdesk</span>
            </div>
        </a>

        <ul class="navbar-nav">
            <li>
                <a href="{{ route('petugas.dashboard') }}"
                   class="nav-link {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}">
                    📋 Daftar Antrean
                </a>
            </li>
            <li>
                <a href="{{ route('petugas.antrean.create') }}"
                   class="nav-link {{ request()->routeIs('petugas.antrean.create') ? 'active' : '' }}">
                    ➕ Daftarkan Kendaraan
                </a>
            </li>
            <li>
                <a href="{{ route('home') }}" target="_blank" class="nav-link" title="Buka layar monitor antrean">
                    🖥️ Monitor Publik ↗
                </a>
            </li>
        </ul>

        <div class="navbar-right">
            <!-- THEME TOGGLE -->
            <button type="button" class="btn-theme-toggle" onclick="toggleTheme()" title="Ubah Mode Terang / Gelap">
                <span id="themeIcon">🌙</span>
                <span id="themeLabel">Mode Gelap</span>
            </button>

            <div class="user-pill">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <span class="role-badge">Frontdesk</span>
            </div>

            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </nav>

    <!-- CONTENT -->
    <main class="container">
        @if(session('success'))
            <div class="alert alert-success">
                <span>✅ {{ session('success') }}</span>
                <button class="alert-dismiss" onclick="this.parentElement.remove()">✕</button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">
                <span>⚠️ {{ session('error') }}</span>
                <button class="alert-dismiss" onclick="this.parentElement.remove()">✕</button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="footer">
        {{ $bSetting->nama_bengkel }} &bull; Panel Petugas Frontdesk
    </footer>

    <script>
        function initTheme() {
            const savedTheme = localStorage.getItem('app-theme') || 'light';
            setTheme(savedTheme);
        }
        function setTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('app-theme', theme);
            const icon = document.getElementById('themeIcon');
            const label = document.getElementById('themeLabel');
            if (theme === 'dark') {
                if (icon) icon.textContent = '☀️';
                if (label) label.textContent = 'Mode Terang';
            } else {
                if (icon) icon.textContent = '🌙';
                if (label) label.textContent = 'Mode Gelap';
            }
        }
        function toggleTheme() {
            const current = document.documentElement.getAttribute('data-theme') || 'light';
            setTheme(current === 'dark' ? 'light' : 'dark');
        }
        initTheme();
    </script>
    @yield('scripts')
</body>
</html>
