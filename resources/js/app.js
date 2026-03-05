import './bootstrap';
// Alpine.js is managed by Livewire internally.

// ── Dark mode ────────────────────────────────────────────────────────────────
// Default: dark ON (null = no preference stored → dark).
function resolveDark() {
    var cookie = document.cookie.match(/darkMode=([^;]+)/);
    if (cookie) return cookie[1] !== 'false';
    var stored = localStorage.getItem('darkMode');
    return stored !== null ? stored === 'true' : true;
}

function applyDark(isDark) {
    document.documentElement.classList.toggle('dark', isDark);
    document.documentElement.style.backgroundColor = isDark ? '#080c18' : '#F7F8FA';
    localStorage.setItem('darkMode', isDark);
    document.cookie = 'darkMode=' + isDark + ';path=/;max-age=31536000;SameSite=Lax';
}

// Global Alpine store — survives wire:navigate morphdom re-inits.
document.addEventListener('alpine:init', () => {
    Alpine.store('theme', {
        dark: resolveDark(),
        toggle() {
            this.dark = !this.dark;
            applyDark(this.dark);
        },
    });

    Alpine.store('sidebar', {
        desktopOpen: true,
        mobileOpen: false,
    });
});

// MutationObserver: re-applies dark class the instant morphdom removes it from <html>.
// Fires as a microtask — before the browser paints the next frame.
new MutationObserver(() => {
    const isDark = resolveDark();
    if (isDark && !document.documentElement.classList.contains('dark')) {
        document.documentElement.classList.add('dark');
        document.documentElement.style.backgroundColor = '#080c18';
    } else if (!isDark && document.documentElement.classList.contains('dark')) {
        document.documentElement.classList.remove('dark');
        document.documentElement.style.backgroundColor = '#F7F8FA';
    }
}).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

// ── Page content transition ──────────────────────────────────────────────────
// Only fades #page-content — sidebar and navbar stay fixed and visible.
document.addEventListener('livewire:navigate', () => {
    const el = document.getElementById('page-content');
    if (el) {
        el.style.transition = 'none';
        el.style.opacity    = '0';
    }
});

document.addEventListener('livewire:navigated', () => {
    // Restore scroll position saved before a language change redirect.
    const savedY = sessionStorage.getItem('restoreScroll');
    if (savedY !== null) {
        window.scrollTo({ top: parseInt(savedY), behavior: 'instant' });
        sessionStorage.removeItem('restoreScroll');
    }

    const el = document.getElementById('page-content');
    if (el) {
        requestAnimationFrame(() => {
            el.style.transition = 'opacity 200ms ease-in';
            el.style.opacity    = '1';
        });
    }
});
