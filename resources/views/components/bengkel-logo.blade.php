@props(['setting' => null, 'size' => 48, 'showText' => false])

@php
    $bSetting = $setting ?? \App\Models\BengkelSetting::getSettings();
@endphp

<div class="bengkel-logo-wrapper" style="display: inline-flex; align-items: center; gap: 0.75rem;">
    @if($bSetting->logo_url)
        <img src="{{ $bSetting->logo_url }}" 
             alt="{{ $bSetting->nama_bengkel }}" 
             style="width: {{ $size }}px; height: {{ $size }}px; object-fit: contain; border-radius: 12px; background: #fff; padding: 2px; border: 1px solid var(--border-color, #e2e8f0);">
    @else
        <!-- DEFAULT LOGO RESMI BENGKEL PRO (SVG Modern) -->
        <div style="width: {{ $size }}px; height: {{ $size }}px; border-radius: 12px; background: linear-gradient(135deg, #1e40af, #2563eb); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25); flex-shrink: 0;">
            <svg width="{{ $size * 0.6 }}" height="{{ $size * 0.6 }}" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Icon Wrench & Gear -->
                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.77 3.77Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="18" cy="18" r="3" stroke="#93c5fd" stroke-width="2"/>
            </svg>
        </div>
    @endif

    @if($showText)
        <div class="bengkel-logo-text">
            <h2 style="font-size: 1.25rem; font-weight: 800; margin: 0; color: var(--text-main); line-height: 1.1;">
                {{ $bSetting->nama_bengkel }}
            </h2>
            @if($bSetting->alamat_bengkel)
                <p style="margin: 0.15rem 0 0 0; font-size: 0.8rem; color: var(--text-muted); line-height: 1.2;">
                    📍 {{ $bSetting->alamat_bengkel }}
                </p>
            @endif
        </div>
    @endif
</div>
