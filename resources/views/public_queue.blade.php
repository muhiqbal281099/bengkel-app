<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Antrean - {{ $setting->nama_bengkel }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        /* TEMA LAYANAN PUBLIK: DEFAULT LIGHT MODE */
        :root {
            --bg-body: #f1f5f9;
            --surface-header: #ffffff;
            --surface-card: #ffffff;
            --surface-card-sub: #f8fafc;
            --border-color: #cbd5e1;
            --border-card: #e2e8f0;

            --text-main: #0f172a;
            --text-muted: #475569;
            --text-dim: #64748b;

            --primary: #1e40af;
            --primary-blue: #2563eb;
            --primary-bg: #eff6ff;
            --primary-border: #bfdbfe;

            --status-waiting-bg: #fef3c7;
            --status-waiting-text: #b45309;
            --status-waiting-border: #fcd34d;

            --status-checking-bg: #ffedd5;
            --status-checking-text: #c2410c;
            --status-checking-border: #fdba74;

            --status-inservice-bg: #dbeafe;
            --status-inservice-text: #1d4ed8;
            --status-inservice-border: #93c5fd;

            --status-completed-bg: #dcfce7;
            --status-completed-text: #15803d;
            --status-completed-border: #86efac;

            --shadow-header: 0 2px 10px rgba(0, 0, 0, 0.05);
            --shadow-card: 0 4px 15px -3px rgba(0, 0, 0, 0.07);
        }

        /* DARK MODE OVERRIDES */
        [data-theme="dark"] {
            --bg-body: #0b1120;
            --surface-header: #131c31;
            --surface-card: #18233c;
            --surface-card-sub: #0f172a;
            --border-color: rgba(255, 255, 255, 0.12);
            --border-card: rgba(255, 255, 255, 0.08);

            --text-main: #f8fafc;
            --text-muted: #cbd5e1;
            --text-dim: #94a3b8;

            --primary: #3b82f6;
            --primary-blue: #60a5fa;
            --primary-bg: rgba(59, 130, 246, 0.15);
            --primary-border: rgba(59, 130, 246, 0.3);

            --status-waiting-bg: rgba(245, 158, 11, 0.2);
            --status-waiting-text: #fcd34d;
            --status-waiting-border: rgba(245, 158, 11, 0.4);

            --status-checking-bg: rgba(249, 115, 22, 0.2);
            --status-checking-text: #fdba74;
            --status-checking-border: rgba(249, 115, 22, 0.4);

            --status-inservice-bg: rgba(37, 99, 235, 0.2);
            --status-inservice-text: #93c5fd;
            --status-inservice-border: rgba(37, 99, 235, 0.4);

            --status-completed-bg: rgba(34, 197, 94, 0.2);
            --status-completed-text: #86efac;
            --status-completed-border: rgba(34, 197, 94, 0.4);

            --shadow-header: 0 4px 20px rgba(0, 0, 0, 0.3);
            --shadow-card: 0 8px 25px -5px rgba(0, 0, 0, 0.4);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            transition: background-color 0.25s ease, color 0.25s ease, border-color 0.25s ease;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* HEADER */
        .header {
            background: var(--surface-header);
            border-bottom: 2px solid var(--border-color);
            padding: 1.2rem 3rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-header);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1.4rem;
        }

        .bengkel-logo {
            width: 68px;
            height: 68px;
            border-radius: 14px;
            object-fit: contain;
            background: #fff;
            border: 1px solid var(--border-color);
            padding: 4px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
            flex-shrink: 0;
        }

        .bengkel-default-logo {
            width: 68px;
            height: 68px;
            border-radius: 14px;
            background: linear-gradient(135deg, #1e40af, #2563eb);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
            flex-shrink: 0;
        }

        .bengkel-info h1 {
            margin: 0;
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--primary);
            line-height: 1.15;
            letter-spacing: -0.5px;
        }

        .bengkel-info .alamat {
            margin: 0.25rem 0 0 0;
            color: var(--text-muted);
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .bengkel-info .kontak {
            margin-top: 0.15rem;
            color: var(--text-dim);
            font-size: 0.85rem;
            font-weight: 600;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .time-display {
            text-align: right;
        }

        .time-display .clock {
            font-size: 2.3rem;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1;
            font-variant-numeric: tabular-nums;
            letter-spacing: -1px;
        }

        .time-display .date {
            color: var(--text-muted);
            font-size: 0.92rem;
            font-weight: 500;
            margin-top: 0.3rem;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* THEME TOGGLE BUTTON */
        .btn-theme-toggle {
            background: var(--surface-card-sub);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 0.65rem 1rem;
            border-radius: 12px;
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .btn-theme-toggle:hover {
            border-color: var(--primary-blue);
            color: var(--primary-blue);
        }

        .btn-login {
            background: var(--primary);
            color: #ffffff;
            border: none;
            padding: 0.7rem 1.4rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 2px 8px rgba(30, 64, 175, 0.25);
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .btn-login:hover {
            opacity: 0.92;
            transform: translateY(-1px);
        }

        /* MAIN CONTENT */
        .main-content {
            flex: 1;
            padding: 2.2rem 3rem;
            display: flex;
            flex-direction: column;
            gap: 1.8rem;
        }

        .queue-summary-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--surface-card);
            border: 1px solid var(--border-card);
            padding: 1rem 1.8rem;
            border-radius: 16px;
            box-shadow: var(--shadow-card);
            flex-wrap: wrap;
            gap: 1rem;
        }

        .summary-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .summary-badges {
            display: flex;
            align-items: center;
            gap: 1.2rem;
            flex-wrap: wrap;
        }

        .legend-pill {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.88rem;
            font-weight: 600;
        }

        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        /* GRID CARDS */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
            gap: 1.6rem;
        }

        .queue-card {
            background: var(--surface-card);
            border: 2px solid var(--border-card);
            border-radius: 20px;
            padding: 1.8rem;
            box-shadow: var(--shadow-card);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .queue-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
        }

        .queue-card.status-waiting::before {
            background: #f59e0b;
        }

        .queue-card.status-checking::before {
            background: #f97316;
        }

        .queue-card.status-inservice::before {
            background: #2563eb;
        }

        .queue-card.status-completed::before {
            background: #16a34a;
        }

        .queue-card-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.2rem;
        }

        .queue-number-box {
            display: flex;
            flex-direction: column;
        }

        .queue-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 800;
            color: var(--text-dim);
        }

        .queue-number {
            font-size: 4rem;
            font-weight: 900;
            line-height: 1;
            color: var(--primary);
            letter-spacing: -2px;
            margin-top: 0.2rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.55rem 1.1rem;
            border-radius: 99px;
            font-weight: 800;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .status-waiting .status-badge {
            background: var(--status-waiting-bg);
            color: var(--status-waiting-text);
            border: 1px solid var(--status-waiting-border);
        }

        .status-checking .status-badge {
            background: var(--status-checking-bg);
            color: var(--status-checking-text);
            border: 1px solid var(--status-checking-border);
        }

        .status-inservice .status-badge {
            background: var(--status-inservice-bg);
            color: var(--status-inservice-text);
            border: 1px solid var(--status-inservice-border);
        }

        .status-completed .status-badge {
            background: var(--status-completed-bg);
            color: var(--status-completed-text);
            border: 1px solid var(--status-completed-border);
        }

        .queue-details {
            background: var(--surface-card-sub);
            border-radius: 14px;
            padding: 1.1rem;
            display: flex;
            flex-direction: column;
            gap: 0.7rem;
            border: 1px solid var(--border-card);
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.95rem;
        }

        .detail-label {
            color: var(--text-dim);
            font-weight: 500;
        }

        .detail-value {
            font-weight: 700;
            color: var(--text-main);
        }

        .plat-nomor {
            font-size: 1.25rem;
            font-weight: 900;
            letter-spacing: 1px;
            color: var(--text-main);
            background: var(--primary-bg);
            border: 1px solid var(--primary-border);
            padding: 0.2rem 0.6rem;
            border-radius: 8px;
        }

        /* EMPTY STATE */
        .empty-state {
            text-align: center;
            padding: 6rem 2rem;
            background: var(--surface-card);
            border-radius: 24px;
            border: 2px dashed var(--border-card);
            box-shadow: var(--shadow-card);
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .empty-state h2 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--text-muted);
            font-size: 1.05rem;
        }

        /* RUNNING TEXT TICKER */
        .ticker-wrapper {
            background: var(--surface-header);
            border-top: 2px solid var(--border-color);
            padding: 0.75rem 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            overflow: hidden;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.04);
            margin-top: auto;
        }

        .ticker-label {
            background: var(--primary);
            color: #fff;
            padding: 0.35rem 0.8rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            flex-shrink: 0;
        }

        .ticker-content {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .ticker-text {
            display: inline-block;
            animation: marquee 25s linear infinite;
        }

        @keyframes marquee {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        @media (max-width: 900px) {
            .header {
                padding: 1rem 1.5rem;
                flex-wrap: wrap;
                gap: 1rem;
            }

            .header-right {
                width: 100%;
                justify-content: space-between;
            }

            .main-content {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>

    <!-- TOP HEADER LAYANAN PUBLIK -->
    <header class="header">
        <div class="header-left">
            @if($setting->logo_url)
                <img src="{{ $setting->logo_url }}" alt="Logo {{ $setting->nama_bengkel }}" class="bengkel-logo">
            @else
                <div class="bengkel-default-logo">
                    <svg width="42" height="42" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.77 3.77Z"
                            stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <circle cx="18" cy="18" r="3" stroke="#bfdbfe" stroke-width="2" />
                    </svg>
                </div>
            @endif

            <div class="bengkel-info">
                <h1>{{ $setting->nama_bengkel }}</h1>
                <p class="alamat">
                    <span>📍</span> {{ $setting->alamat_bengkel ?? 'Sistem Antrean Layanan Bengkel Terpadu' }}
                </p>
                @if($setting->telepon_bengkel)
                    <p class="kontak">📞 Layanan Pelanggan: {{ $setting->telepon_bengkel }}</p>
                @endif
            </div>
        </div>

        <div class="header-right">
            <div class="time-display">
                <div class="clock" id="clock">00:00:00</div>
                <div class="date" id="date">Hari, Tanggal</div>
            </div>

            <div class="header-actions">
                <!-- TOMBOL THEME TOGGLE (LIGHT / DARK) -->
                <button type="button" class="btn-theme-toggle" id="themeToggleBtn" onclick="toggleTheme()"
                    title="Ubah Mode Terang / Gelap">
                    <span id="themeIcon">🌙</span>
                    <span id="themeLabel">Mode Gelap</span>
                </button>

                <a href="{{ route('login') }}" class="btn-login">
                    <span>🔐</span> Staff Login
                </a>
            </div>
        </div>
    </header>

    <!-- CONTENT -->
    <main class="main-content">

        <!-- STATUS SUMMARY BAR -->
        <div class="queue-summary-bar">
            <div class="summary-title">
                <span>📋</span> Status Antrean Hari Ini: <strong>{{ $queues->count() }} Kendaraan Aktif</strong>
            </div>

            <div class="summary-badges">
                <div class="legend-pill">
                    <span class="legend-dot" style="background: #f59e0b;"></span>
                    <span>Menunggu (Waiting)</span>
                </div>
                <div class="legend-pill">
                    <span class="legend-dot" style="background: #f97316;"></span>
                    <span>Pengecekan (Checking)</span>
                </div>
                <div class="legend-pill">
                    <span class="legend-dot" style="background: #2563eb;"></span>
                    <span>Dikerjakan (In Service)</span>
                </div>
                <div class="legend-pill">
                    <span class="legend-dot" style="background: #16a34a;"></span>
                    <span>Selesai (Completed)</span>
                </div>
            </div>
        </div>

        @if($queues->count() > 0)
            <div class="grid-container">
                @foreach($queues as $q)
                    @php
                        $statusClass = match ($q->status) {
                            'waiting' => 'status-waiting',
                            'checking' => 'status-checking',
                            'in_service' => 'status-inservice',
                            'completed' => 'status-completed',
                            default => 'status-waiting'
                        };

                        $statusText = match ($q->status) {
                            'waiting' => 'Menunggu Giliran',
                            'checking' => 'Pengecekan Kendaraan',
                            'in_service' => 'Sedang Diservis',
                            'completed' => 'Selesai Servis',
                            default => strtoupper($q->status)
                        };
                    @endphp

                    <div class="queue-card {{ $statusClass }}">
                        <div class="queue-card-top">
                            <div class="queue-number-box">
                                <span class="queue-label">Nomor Antrean</span>
                                <div class="queue-number">{{ $q->nomor_antrean }}</div>
                            </div>
                            <span class="status-badge">
                                {{ $statusText }}
                            </span>
                        </div>

                        <div class="queue-details">
                            <div class="detail-item">
                                <span class="detail-label">Nomor Polisi:</span>
                                <span class="plat-nomor">{{ $q->kendaraan->no_polisi ?? '-' }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Kendaraan:</span>
                                <span class="detail-value">{{ $q->kendaraan->merk ?? '' }}
                                    {{ $q->kendaraan->model ?? '' }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Layanan:</span>
                                <span class="detail-value"
                                    style="color: var(--primary);">{{ $q->service->nama_layanan ?? 'Servis Umum' }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Mekanik/Montir:</span>
                                <span class="detail-value">{{ $q->montor->name ?? 'Belum Ditugaskan' }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon"></div>
                <h2>Belum Ada Antrean Aktif Saat Ini</h2>
                <p>Silakan daftarkan kendaraan Anda di loket meja depan untuk mendapatkan nomor antrean servis.</p>
            </div>
        @endif

    </main>

    <!-- TICKER RUNNING TEXT -->
    @if($setting->running_text)
        <div class="ticker-wrapper">
            <span class="ticker-label">Pengumuman</span>
            <div class="ticker-content">
                <span class="ticker-text">{{ $setting->running_text }}</span>
            </div>
        </div>
    @endif

    <script>
        // THEME MANAGEMENT (DEFAULT: LIGHT)
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
            const next = current === 'dark' ? 'light' : 'dark';
            setTheme(next);
        }

        // Initialize Theme immediately
        initTheme();

        // REAL-TIME CLOCK
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
            document.getElementById('clock').textContent = timeString;

            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const dateString = now.toLocaleDateString('id-ID', options);
            document.getElementById('date').textContent = dateString;
        }

        setInterval(updateTime, 1000);
        updateTime();

        // Auto Refresh halaman setiap 30 detik untuk memperbarui antrean
        setTimeout(function () {
            window.location.reload();
        }, 30000);
    </script>
</body>

</html>