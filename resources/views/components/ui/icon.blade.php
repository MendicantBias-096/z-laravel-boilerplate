@props(['name'])

@php
$icons = [
    'home' => '<path stroke-linecap="round" stroke-linejoin="round" d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline stroke-linecap="round" stroke-linejoin="round" points="9 22 9 12 15 12 15 22"/>',

    'users' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path stroke-linecap="round" stroke-linejoin="round" d="M22 21v-2a4 4 0 0 0-3-3.87"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13a4 4 0 0 1 0 7.75"/>',

    'layout-grid' => '<rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/>',

    'link' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>',

    'list' => '<line stroke-linecap="round" x1="8" x2="21" y1="6" y2="6"/><line stroke-linecap="round" x1="8" x2="21" y1="12" y2="12"/><line stroke-linecap="round" x1="8" x2="21" y1="18" y2="18"/><line stroke-linecap="round" x1="3" x2="3.01" y1="6" y2="6"/><line stroke-linecap="round" x1="3" x2="3.01" y1="12" y2="12"/><line stroke-linecap="round" x1="3" x2="3.01" y1="18" y2="18"/>',

    'bar-chart-2' => '<line stroke-linecap="round" x1="18" x2="18" y1="20" y2="10"/><line stroke-linecap="round" x1="12" x2="12" y1="20" y2="4"/><line stroke-linecap="round" x1="6" x2="6" y1="20" y2="14"/>',

    'shield-check' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4"/>',

    'bell' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path stroke-linecap="round" stroke-linejoin="round" d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>',

    'settings' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/>',

    'circle-user' => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="10" r="3"/><path stroke-linecap="round" stroke-linejoin="round" d="M7 20.662V19a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1.662"/>',

    'chevron-down' => '<path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/>',

    'chevron-right' => '<path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/>',

    'menu' => '<line stroke-linecap="round" x1="4" x2="20" y1="6" y2="6"/><line stroke-linecap="round" x1="4" x2="20" y1="12" y2="12"/><line stroke-linecap="round" x1="4" x2="20" y1="18" y2="18"/>',

    'x' => '<path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18"/><path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12"/>',

    'sun' => '<circle cx="12" cy="12" r="4"/><path stroke-linecap="round" d="M12 2v2"/><path stroke-linecap="round" d="M12 20v2"/><path stroke-linecap="round" d="m4.93 4.93 1.41 1.41"/><path stroke-linecap="round" d="m17.66 17.66 1.41 1.41"/><path stroke-linecap="round" d="M2 12h2"/><path stroke-linecap="round" d="M20 12h2"/><path stroke-linecap="round" d="m6.34 17.66-1.41 1.41"/><path stroke-linecap="round" d="m19.07 4.93-1.41 1.41"/>',

    'moon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>',

    'log-out' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline stroke-linecap="round" stroke-linejoin="round" points="16 17 21 12 16 7"/><line stroke-linecap="round" x1="21" x2="9" y1="12" y2="12"/>',

    'loader-circle' => '<path stroke-linecap="round" d="M21 12a9 9 0 1 1-6.219-8.56"/>',

    'heart' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>',

    'plus'             => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>',
    'ellipsis-vertical'   => '<circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/>',
    'search'              => '<circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35"/>',
    'check'               => '<path stroke-linecap="round" stroke-linejoin="round" d="M20 6 9 17l-5-5"/>',
    'sliders-horizontal'  => '<line stroke-linecap="round" x1="21" x2="14" y1="4" y2="4"/><line stroke-linecap="round" x1="10" x2="3" y1="4" y2="4"/><line stroke-linecap="round" x1="21" x2="12" y1="12" y2="12"/><line stroke-linecap="round" x1="8" x2="3" y1="12" y2="12"/><line stroke-linecap="round" x1="21" x2="16" y1="20" y2="20"/><line stroke-linecap="round" x1="12" x2="3" y1="20" y2="20"/><circle cx="12" cy="4" r="2"/><circle cx="10" cy="12" r="2"/><circle cx="16" cy="20" r="2"/>',

    'pencil'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>',

    'trash-2'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/><path stroke-linecap="round" stroke-linejoin="round" d="M10 11v6M14 11v6"/>',

    'rotate-ccw' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v5h5"/>',

    'information-circle' => '<circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8h.01"/>',

    // ── Aliases HeroIcons → Lucide ───────────────────────────────────────
    // Permiten seguir usando los nombres HeroIcons en sidebar, navbar y menu.php

    'bolt'                      => '<polygon stroke-linecap="round" stroke-linejoin="round" points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>',
    'zap'                       => '<polygon stroke-linecap="round" stroke-linejoin="round" points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>',
    'x-mark'                    => '<path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18"/><path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12"/>',
    'bars-3'                    => '<line stroke-linecap="round" x1="4" x2="20" y1="6" y2="6"/><line stroke-linecap="round" x1="4" x2="20" y1="12" y2="12"/><line stroke-linecap="round" x1="4" x2="20" y1="18" y2="18"/>',
    'cog-6-tooth'               => '<path stroke-linecap="round" stroke-linejoin="round" d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/>',
    'arrow-right-on-rectangle'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline stroke-linecap="round" stroke-linejoin="round" points="16 17 21 12 16 7"/><line stroke-linecap="round" x1="21" x2="9" y1="12" y2="12"/>',
    'squares-2x2'               => '<rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/>',
    'queue-list'                => '<line stroke-linecap="round" x1="8" x2="21" y1="6" y2="6"/><line stroke-linecap="round" x1="8" x2="21" y1="12" y2="12"/><line stroke-linecap="round" x1="8" x2="21" y1="18" y2="18"/><line stroke-linecap="round" x1="3" x2="3.01" y1="6" y2="6"/><line stroke-linecap="round" x1="3" x2="3.01" y1="12" y2="12"/><line stroke-linecap="round" x1="3" x2="3.01" y1="18" y2="18"/>',
    'chart-bar'                 => '<line stroke-linecap="round" x1="18" x2="18" y1="20" y2="10"/><line stroke-linecap="round" x1="12" x2="12" y1="20" y2="4"/><line stroke-linecap="round" x1="6" x2="6" y1="20" y2="14"/>',
    'user-circle'               => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="10" r="3"/><path stroke-linecap="round" stroke-linejoin="round" d="M7 20.662V19a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1.662"/>',
];
@endphp

<svg
    xmlns="http://www.w3.org/2000/svg"
    fill="none"
    viewBox="0 0 24 24"
    stroke-width="2"
    stroke="currentColor"
    {{ $attributes->merge(['class' => 'size-5']) }}
>
    {!! $icons[$name] ?? '' !!}
</svg>
