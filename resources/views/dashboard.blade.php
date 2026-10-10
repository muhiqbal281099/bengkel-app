<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Bengkel Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --glass-bg: rgba(30, 41, 59, 0.7);
            --glass-border: rgba(255, 255, 255, 0.1);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --primary: #3b82f6;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
        }

        .navbar {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--glass-border);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h2 {
            margin: 0;
            background: linear-gradient(to right, #60a5fa, #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .badge {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
            padding: 0.3rem 0.8rem;
            border-radius: 99px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .btn-logout {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.2);
        }

        .main-content {
            padding: 3rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .card h3 {
            margin-top: 0;
            font-size: 1.5rem;
        }

        .card p {
            color: var(--text-muted);
            line-height: 1.6;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <h2>Bengkel Pro</h2>
        <div class="nav-actions">
            <span>Halo, <strong>{{ auth()->user()->name }}</strong></span>
            <span class="badge">Role: {{ strtoupper(auth()->user()->role) }}</span>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </nav>

    <div class="main-content">
        @if(session('success'))
            <div class="card" style="border-color: #22c55e; background: rgba(34, 197, 94, 0.05);">
                <p style="color: #22c55e; margin:0;">{{ session('success') }}</p>
            </div>
        @endif

        <div class="card">
            <h3>Selamat Datang di Dashboard Utama</h3>
            <p>Ini adalah halaman inti di mana antarmuka akan menyesuaikan berdasarkan hak akses (role) yang kamu miliki. Saat ini kamu login sebagai <strong>{{ auth()->user()->role }}</strong>.</p>
        </div>
    </div>

</body>
</html>
