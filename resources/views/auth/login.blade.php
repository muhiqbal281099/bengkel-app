<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $setting = \App\Models\BengkelSetting::getSettings();
    @endphp
    <title>Login Petugas - {{ $setting->nama_bengkel }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-body: #f1f5f9;
            --surface-card: #ffffff;
            --border-color: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #475569;
            --text-dim: #64748b;
            
            --primary: #1e40af;
            --primary-blue: #2563eb;
            --primary-light: rgba(37, 99, 235, 0.1);
            
            --input-bg: #ffffff;
            --input-border: #cbd5e1;
            
            --shadow-card: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            --error: #dc2626;
        }

        [data-theme="dark"] {
            --bg-body: #0b1120;
            --surface-card: #131c31;
            --border-color: rgba(255, 255, 255, 0.1);
            --text-main: #f8fafc;
            --text-muted: #cbd5e1;
            --text-dim: #94a3b8;
            
            --primary: #3b82f6;
            --primary-blue: #60a5fa;
            --primary-light: rgba(59, 130, 246, 0.2);
            
            --input-bg: rgba(15, 23, 42, 0.7);
            --input-border: rgba(255, 255, 255, 0.15);
            
            --shadow-card: 0 20px 40px -10px rgba(0, 0, 0, 0.6);
            --error: #ef4444;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
            transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }

        body {
            background-color: var(--bg-body);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--text-main);
            padding: 1.5rem;
            position: relative;
        }

        .top-bar-nav {
            position: absolute;
            top: 1.5rem;
            left: 1.5rem;
            right: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-back {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 0.9rem;
            border-radius: 10px;
            background: var(--surface-card);
            border: 1px solid var(--border-color);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .btn-back:hover {
            color: var(--primary-blue);
            border-color: var(--primary-blue);
        }

        .btn-theme-toggle {
            background: var(--surface-card);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 0.5rem 0.9rem;
            border-radius: 10px;
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .login-container {
            width: 100%;
            max-width: 440px;
            padding: 2.5rem 2.2rem;
            background: var(--surface-card);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            box-shadow: var(--shadow-card);
            margin-top: 2rem;
        }

        .logo-area {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-icon-wrapper {
            margin-bottom: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .logo-img {
            width: 64px;
            height: 64px;
            object-fit: contain;
            border-radius: 14px;
            background: #fff;
            padding: 4px;
            border: 1px solid var(--border-color);
        }

        .logo-default {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--primary), var(--primary-blue));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.25);
        }

        .logo-area h1 {
            font-size: 1.6rem;
            font-weight: 900;
            color: var(--text-main);
            margin-bottom: 0.3rem;
            letter-spacing: -0.5px;
        }

        .logo-area p {
            color: var(--text-muted);
            font-size: 0.92rem;
            font-weight: 500;
        }

        .input-group {
            margin-bottom: 1.3rem;
        }

        .input-group label {
            display: block;
            margin-bottom: 0.45rem;
            font-size: 0.88rem;
            color: var(--text-muted);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-group input {
            width: 100%;
            padding: 0.85rem 1.1rem;
            background: var(--input-bg);
            border: 1.5px solid var(--input-border);
            border-radius: 12px;
            color: var(--text-main);
            font-size: 0.98rem;
            transition: all 0.2s ease;
        }

        .input-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            cursor: pointer;
        }

        .checkbox-group input {
            accent-color: var(--primary);
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .checkbox-group label {
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
        }

        .btn-submit {
            width: 100%;
            padding: 0.95rem;
            background: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.25);
        }

        .btn-submit:hover {
            opacity: 0.92;
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .alert {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #b91c1c;
            padding: 0.9rem 1.1rem;
            border-radius: 12px;
            margin-bottom: 1.4rem;
            font-size: 0.88rem;
            font-weight: 600;
        }

        [data-theme="dark"] .alert {
            background: rgba(220, 38, 38, 0.2);
            border-color: rgba(220, 38, 38, 0.4);
            color: #fca5a5;
        }

        .login-footer-hint {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.82rem;
            color: var(--text-dim);
            line-height: 1.4;
        }
    </style>
</head>
<body>

    <!-- TOP NAVIGATION -->
    <div class="top-bar-nav">
        <a href="{{ route('home') }}" class="btn-back">
            ← Monitor Antrean
        </a>

        <button type="button" class="btn-theme-toggle" onclick="toggleTheme()" title="Ubah Mode Terang / Gelap">
            <span id="themeIcon">🌙</span>
            <span id="themeLabel">Mode Gelap</span>
        </button>
    </div>

    <!-- LOGIN CARD -->
    <div class="login-container">
        <div class="logo-area">
            <div class="logo-icon-wrapper">
                @if($setting->logo_url)
                    <img src="{{ $setting->logo_url }}" alt="Logo" class="logo-img">
                @else
                    <div class="logo-default">🔧</div>
                @endif
            </div>
            <h1>{{ $setting->nama_bengkel }}</h1>
            <p>Portal Masuk Staf & Operasional Bengkel</p>
        </div>

        @if($errors->any())
            <div class="alert">
                ⚠️ {{ $errors->first() }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert" style="background:#dcfce7; border-color:#86efac; color:#15803d;">
                ✅ {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            
            <div class="input-group">
                <label for="email">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@bengkel.com">
            </div>

            <div class="input-group">
                <label for="password">Kata Sandi</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Ingat Saya di Perangkat Ini</label>
            </div>

            <button type="submit" class="btn-submit">Masuk ke Sistem Bengkel</button>
        </form>

        <div class="login-footer-hint">
            Akses dibatasi untuk staf berwenang (Admin, Kasir, Frontdesk, Montir).
        </div>
    </div>

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
</body>
</html>
